<?php
/**
 * FILE: functions/weather_api.php
 * FUNGSI: Menangani Weather API (OpenWeatherMap)
 * DESKRIPSI: Fungsi untuk mengambil dan memproses data cuaca
 * VERSION: 2.0 - Enhanced with better error handling and caching
 */

/**
 * Mengambil data cuaca dari OpenWeatherMap API
 * @param string $city Nama kota
 * @param array $options Opsi tambahan (units, lang, etc)
 * @return array Data cuaca
 * @throws Exception Jika request gagal
 */
function getWeatherData($city, $options = []) {
    // Validasi input
    if (empty($city) || !is_string($city)) {
        throw new Exception('Nama kota tidak valid');
    }
    
    // Default options
    $defaultOptions = [
        'units' => 'metric',
        'lang' => 'id',
        'cache' => true,
        'cache_duration' => 300 // 5 minutes
    ];
    $options = array_merge($defaultOptions, $options);
    
    // Check cache first
    $cacheKey = 'weather_' . md5(strtolower($city));
    if ($options['cache'] && function_exists('apcu_fetch')) {
        $cachedData = apcu_fetch($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }
    }
    
    // Build API URL
    $params = [
        'q' => $city,
        'appid' => '3e0c7b48086358f5ce90a70eb1d5620f',
        'units' => $options['units'],
        'lang' => $options['lang']
    ];
    $url = WEATHER_API_URL . '/weather?' . http_build_query($params);
    
    // Make API request
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT => 'Weather App/2.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Handle cURL errors
    if ($curlError) {
        throw new Exception("Koneksi gagal: $curlError");
    }
    
    // Handle HTTP errors
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        switch ($httpCode) {
            case 401:
                throw new Exception('API Key tidak valid');
            case 404:
                throw new Exception("Kota '$city' tidak ditemukan");
            case 429:
                throw new Exception('Terlalu banyak request, coba lagi nanti');
            case 500:
                throw new Exception('Server API bermasalah, coba lagi nanti');
            default:
                $errorMessage = $errorData['message'] ?? "HTTP Error $httpCode";
                throw new Exception("API Error: $errorMessage");
        }
    }
    
    // Parse JSON response
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Response API tidak valid');
    }
    
    // Validate required fields
    if (!isset($data['main']) || !isset($data['weather']) || !isset($data['name'])) {
        throw new Exception('Data cuaca tidak lengkap');
    }
    
    // Cache the result
    if ($options['cache'] && function_exists('apcu_store')) {
        apcu_store($cacheKey, $data, $options['cache_duration']);
    }
    
    return $data;
}

/**
 * Mengambil data cuaca untuk multiple cities
 * @param array $cities Daftar nama kota
 * @param array $options Opsi tambahan
 * @return array Data cuaca untuk semua kota
 */
function getMultipleWeatherData($cities, $options = []) {
    $results = [];
    $errors = [];
    
    foreach ($cities as $city) {
        try {
            $results[$city] = getWeatherData($city, $options);
        } catch (Exception $e) {
            $errors[$city] = $e->getMessage();
        }
    }
    
    return [
        'success' => count($results) > 0,
        'results' => $results,
        'errors' => $errors,
        'total_requested' => count($cities),
        'total_success' => count($results),
        'total_failed' => count($errors)
    ];
}

/**
 * Menampilkan hasil data cuaca dalam format HTML
 * @param array $data Data cuaca dari API
 * @param array $options Opsi tampilan
 * @return string HTML result
 */
function displayWeatherResult($data, $options = []) {
    // Default options
    $defaultOptions = [
        'show_details' => true,
        'show_forecast' => false,
        'compact_mode' => false
    ];
    $options = array_merge($defaultOptions, $options);
    
    // Extract data with null checks
    $cityName = htmlspecialchars($data['name'] ?? 'Unknown');
    $country = htmlspecialchars($data['sys']['country'] ?? '');
    $temp = round($data['main']['temp'] ?? 0);
    $feelsLike = round($data['main']['feels_like'] ?? 0);
    $humidity = $data['main']['humidity'] ?? 0;
    $pressure = $data['main']['pressure'] ?? 0;
    $windSpeed = isset($data['wind']['speed']) ? round($data['wind']['speed'] * 3.6) : 0;
    $windDir = $data['wind']['deg'] ?? 0;
    $visibility = isset($data['visibility']) ? round($data['visibility'] / 1000, 1) : 'N/A';
    $description = ucfirst($data['weather'][0]['description'] ?? 'Unknown');
    $icon = $data['weather'][0]['icon'] ?? '01d';
    $iconUrl = "https://openweathermap.org/img/wn/{$icon}@2x.png";
    
    // Convert wind direction
    $windDirection = getWindDirection($windDir);
    
    // Calculate local time
    $timezone = $data['timezone'] ?? 0;
    $localTime = date('H:i', time() + $timezone);
    $sunrise = isset($data['sys']['sunrise']) ? date('H:i', $data['sys']['sunrise'] + $timezone) : 'N/A';
    $sunset = isset($data['sys']['sunset']) ? date('H:i', $data['sys']['sunset'] + $timezone) : 'N/A';
    
    // Compact mode for mobile or small displays
    if ($options['compact_mode']) {
        return generateCompactWeatherDisplay($data, $options);
    }
    
    // Full weather display
    $html = '
    <div class="weather-result-card" data-city="' . htmlspecialchars($cityName) . '">
        <div class="weather-header">
            <div class="weather-location">
                <h2>🌍 ' . $cityName . ($country ? ', ' . $country : '') . '</h2>
                <p class="local-time">Waktu Lokal: ' . $localTime . '</p>
            </div>
            <div class="weather-icon-display">
                <img src="' . $iconUrl . '" alt="' . $description . '" class="weather-api-icon" loading="lazy">
            </div>
        </div>
        
        <div class="weather-main-info">
            <div class="temperature-section">
                <div class="main-temperature">' . $temp . '°C</div>
                <div class="weather-description">' . $description . '</div>
                <div class="feels-like">Terasa seperti ' . $feelsLike . '°C</div>
            </div>
        </div>';
    
    // Add detailed information if requested
    if ($options['show_details']) {
        $html .= '
        <div class="weather-details-grid">
            <div class="weather-detail-item">
                <div class="detail-icon">💧</div>
                <div class="detail-content">
                    <span class="detail-label">Kelembaban</span>
                    <span class="detail-value">' . $humidity . '%</span>
                </div>
            </div>
            
            <div class="weather-detail-item">
                <div class="detail-icon">🌬️</div>
                <div class="detail-content">
                    <span class="detail-label">Angin</span>
                    <span class="detail-value">' . $windSpeed . ' km/h ' . $windDirection . '</span>
                </div>
            </div>
            
            <div class="weather-detail-item">
                <div class="detail-icon">🔽</div>
                <div class="detail-content">
                    <span class="detail-label">Tekanan</span>
                    <span class="detail-value">' . $pressure . ' hPa</span>
                </div>
            </div>
            
            <div class="weather-detail-item">
                <div class="detail-icon">👁️</div>
                <div class="detail-content">
                    <span class="detail-label">Jarak Pandang</span>
                    <span class="detail-value">' . $visibility . ' km</span>
                </div>
            </div>
            
            <div class="weather-detail-item">
                <div class="detail-icon">🌅</div>
                <div class="detail-content">
                    <span class="detail-label">Matahari Terbit</span>
                    <span class="detail-value">' . $sunrise . '</span>
                </div>
            </div>
            
            <div class="weather-detail-item">
                <div class="detail-icon">🌇</div>
                <div class="detail-content">
                    <span class="detail-label">Matahari Terbenam</span>
                    <span class="detail-value">' . $sunset . '</span>
                </div>
            </div>
        </div>';
    }
    
    $html .= '
        <div class="weather-footer">
            <p>📡 Data dari OpenWeatherMap • Diperbarui: ' . date('d/m/Y H:i') . '</p>
        </div>
    </div>';
    
    return $html;
}

/**
 * Generate compact weather display for mobile/small screens
 * @param array $data Weather data
 * @param array $options Display options
 * @return string Compact HTML
 */
function generateCompactWeatherDisplay($data, $options = []) {
    $cityName = htmlspecialchars($data['name'] ?? 'Unknown');
    $temp = round($data['main']['temp'] ?? 0);
    $description = ucfirst($data['weather'][0]['description'] ?? 'Unknown');
    $icon = $data['weather'][0]['icon'] ?? '01d';
    $iconUrl = "https://openweathermap.org/img/wn/{$icon}.png";
    
    return '
    <div class="weather-compact-card">
        <div class="compact-header">
            <img src="' . $iconUrl . '" alt="' . $description . '" class="compact-icon">
            <div class="compact-info">
                <h3>' . $cityName . '</h3>
                <p>' . $temp . '°C - ' . $description . '</p>
            </div>
        </div>
    </div>';
}

/**
 * Convert wind direction degrees to compass direction
 * @param float $degrees Wind direction in degrees
 * @return string Compass direction
 */
function getWindDirection($degrees) {
    $directions = [
        'Utara', 'Utara-Timur Laut', 'Timur Laut', 'Timur-Timur Laut',
        'Timur', 'Timur-Tenggara', 'Tenggara', 'Selatan-Tenggara',
        'Selatan', 'Selatan-Barat Daya', 'Barat Daya', 'Barat-Barat Daya',
        'Barat', 'Barat-Barat Laut', 'Barat Laut', 'Utara-Barat Laut'
    ];
    
    $index = round($degrees / 22.5) % 16;
    return $directions[$index];
}

/**
 * Get weather condition in Indonesian
 * @param string $condition Weather condition code
 * @return string Indonesian weather condition
 */
function getWeatherConditionIndonesian($condition) {
    $conditions = [
        'clear sky' => 'Langit cerah',
        'few clouds' => 'Sedikit berawan',
        'scattered clouds' => 'Berawan sebagian',
        'broken clouds' => 'Berawan',
        'shower rain' => 'Hujan rintik',
        'rain' => 'Hujan',
        'thunderstorm' => 'Badai petir',
        'snow' => 'Salju',
        'mist' => 'Berkabut'
    ];
    
    return $conditions[strtolower($condition)] ?? ucfirst($condition);
}

/**
 * Validate weather API key
 * @return bool True if API key is valid
 */
function validateWeatherApiKey() {
    if (!defined('WEATHER_API_KEY') || empty(WEATHER_API_KEY)) {
        return false;
    }
    
    // Test API key with a simple request
    try {
        $testData = getWeatherData('Jakarta', ['cache' => false]);
        return isset($testData['name']);
    } catch (Exception $e) {
        return false;
    }
}

?>