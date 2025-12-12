<?php
/**
 * FILE: functions/geography_api.php
 * FUNGSI: Menangani Geography API (RestCountries)
 * DESKRIPSI: Fungsi untuk mengambil dan memproses data geografi negara
 * VERSION: 2.0 - Enhanced with better error handling and additional features
 */

/**
 * Mengambil data geografi dari RestCountries API
 * @param string $country Nama negara
 * @param array $options Opsi tambahan
 * @return array Data geografi
 * @throws Exception Jika request gagal
 */
function getGeographyData($country, $options = []) {
    // Validasi input
    if (empty($country) || !is_string($country)) {
        throw new Exception('Nama negara tidak valid');
    }
    
    // Default options
    $defaultOptions = [
        'fullText' => false,
        'cache' => true,
        'cache_duration' => 3600, // 1 hour
        'fields' => null // null = all fields
    ];
    $options = array_merge($defaultOptions, $options);
    
    // Check cache first
    $cacheKey = 'geography_' . md5(strtolower($country));
    if ($options['cache'] && function_exists('apcu_fetch')) {
        $cachedData = apcu_fetch($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }
    }
    
    // Build API URL
    $endpoint = $options['fullText'] ? 'name' : 'name';
    $params = ['fullText' => $options['fullText'] ? 'true' : 'false'];
    
    if ($options['fields']) {
        $params['fields'] = implode(',', $options['fields']);
    }
    
    $url = RESTCOUNTRIES_API_URL . '/' . $endpoint . '/' . urlencode($country);
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    // Make API request
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT => 'Geography App/2.0',
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
        switch ($httpCode) {
            case 404:
                throw new Exception("Negara '$country' tidak ditemukan");
            case 429:
                throw new Exception('Terlalu banyak request, coba lagi nanti');
            case 500:
                throw new Exception('Server API bermasalah, coba lagi nanti');
            default:
                throw new Exception("API Error: HTTP Status Code $httpCode");
        }
    }
    
    // Parse JSON response
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Response API tidak valid');
    }
    
    // Validate response
    if (empty($data) || !is_array($data)) {
        throw new Exception("Data negara '$country' tidak ditemukan");
    }
    
    // RestCountries returns array, take first result
    $countryData = $data[0];
    
    // Validate required fields
    if (!isset($countryData['name'])) {
        throw new Exception('Data negara tidak lengkap');
    }
    
    // Cache the result
    if ($options['cache'] && function_exists('apcu_store')) {
        apcu_store($cacheKey, $countryData, $options['cache_duration']);
    }
    
    return $countryData;
}

/**
 * Mengambil data geografi untuk multiple countries
 * @param array $countries Daftar nama negara
 * @param array $options Opsi tambahan
 * @return array Data geografi untuk semua negara
 */
function getMultipleGeographyData($countries, $options = []) {
    $results = [];
    $errors = [];
    
    foreach ($countries as $country) {
        try {
            $results[$country] = getGeographyData($country, $options);
        } catch (Exception $e) {
            $errors[$country] = $e->getMessage();
        }
    }
    
    return [
        'success' => count($results) > 0,
        'results' => $results,
        'errors' => $errors,
        'total_requested' => count($countries),
        'total_success' => count($results),
        'total_failed' => count($errors)
    ];
}

/**
 * Mencari negara berdasarkan berbagai kriteria
 * @param array $criteria Kriteria pencarian
 * @return array Hasil pencarian
 */
function searchCountries($criteria) {
    $searchTypes = [
        'name' => 'name',
        'capital' => 'capital',
        'region' => 'region',
        'subregion' => 'subregion',
        'currency' => 'currency',
        'language' => 'lang'
    ];
    
    $results = [];
    
    foreach ($criteria as $type => $value) {
        if (!isset($searchTypes[$type]) || empty($value)) {
            continue;
        }
        
        try {
            $endpoint = $searchTypes[$type];
            $url = RESTCOUNTRIES_API_URL . '/' . $endpoint . '/' . urlencode($value);
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => 'Geography App/2.0',
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if ($data && is_array($data)) {
                    $results = array_merge($results, $data);
                }
            }
        } catch (Exception $e) {
            // Continue with other criteria
            continue;
        }
    }
    
    // Remove duplicates based on country code
    $uniqueResults = [];
    foreach ($results as $country) {
        $code = $country['cca3'] ?? $country['name']['common'] ?? '';
        if ($code && !isset($uniqueResults[$code])) {
            $uniqueResults[$code] = $country;
        }
    }
    
    return array_values($uniqueResults);
}

/**
 * Menampilkan hasil data geografi dalam format HTML
 * @param array $data Data geografi dari API
 * @param array $options Opsi tampilan
 * @return string HTML result
 */
function displayGeographyResult($data, $options = []) {
    // Default options
    $defaultOptions = [
        'show_details' => true,
        'show_flag' => true,
        'show_coat_of_arms' => true,
        'compact_mode' => false
    ];
    $options = array_merge($defaultOptions, $options);
    
    // Extract data with null checks
    $commonName = htmlspecialchars($data['name']['common'] ?? 'Unknown');
    $officialName = htmlspecialchars($data['name']['official'] ?? $commonName);
    $capital = isset($data['capital']) && is_array($data['capital']) 
        ? htmlspecialchars($data['capital'][0]) 
        : 'Tidak tersedia';
    $population = isset($data['population']) ? number_format($data['population']) : 'Tidak tersedia';
    $area = isset($data['area']) ? number_format($data['area']) : 'Tidak tersedia';
    $region = htmlspecialchars($data['region'] ?? 'Tidak tersedia');
    $subregion = htmlspecialchars($data['subregion'] ?? 'Tidak tersedia');
    $flagUrl = $data['flags']['png'] ?? '';
    $coatOfArms = $data['coatOfArms']['png'] ?? '';
    
    // Process languages
    $languages = [];
    if (isset($data['languages']) && is_array($data['languages'])) {
        foreach ($data['languages'] as $lang) {
            $languages[] = htmlspecialchars($lang);
        }
    }
    $languagesStr = !empty($languages) ? implode(', ', $languages) : 'Tidak tersedia';
    
    // Process currencies
    $currencies = [];
    if (isset($data['currencies']) && is_array($data['currencies'])) {
        foreach ($data['currencies'] as $code => $currency) {
            $name = $currency['name'] ?? $code;
            $symbol = isset($currency['symbol']) ? ' (' . $currency['symbol'] . ')' : '';
            $currencies[] = htmlspecialchars($name . $symbol);
        }
    }
    $currenciesStr = !empty($currencies) ? implode(', ', $currencies) : 'Tidak tersedia';
    
    // Process timezones
    $timezones = 'Tidak tersedia';
    if (isset($data['timezones']) && is_array($data['timezones'])) {
        $timezones = implode(', ', array_map('htmlspecialchars', $data['timezones']));
    }
    
    // Process borders
    $borders = 'Tidak ada perbatasan darat';
    if (isset($data['borders']) && is_array($data['borders']) && !empty($data['borders'])) {
        $borders = implode(', ', array_map('htmlspecialchars', $data['borders']));
    }
    
    // Calculate population density
    $density = 0;
    if (isset($data['population']) && isset($data['area']) && $data['area'] > 0) {
        $density = round($data['population'] / $data['area'], 2);
    }
    
    // Compact mode for mobile
    if ($options['compact_mode']) {
        return generateCompactGeographyDisplay($data, $options);
    }
    
    // Full geography display
    $html = '
    <div class="geography-result-card" data-country="' . htmlspecialchars($commonName) . '">
        <div class="geography-header">
            <div class="country-info">
                <h2>🌍 ' . $commonName . '</h2>
                <p class="official-name">' . $officialName . '</p>
            </div>';
    
    if ($options['show_flag'] && $flagUrl) {
        $html .= '
            <div class="country-flag">
                <img src="' . $flagUrl . '" alt="Bendera ' . $commonName . '" class="flag-image" loading="lazy">
            </div>';
    }
    
    $html .= '
        </div>';
    
    if ($options['show_details']) {
        $html .= '
        <div class="geography-main-info">
            <div class="info-grid">
                <div class="info-section">
                    <h3>📍 Informasi Dasar</h3>
                    <div class="info-items">
                        <div class="info-item">
                            <span class="info-label">Ibu Kota:</span>
                            <span class="info-value">' . $capital . '</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Region:</span>
                            <span class="info-value">' . $region . '</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sub Region:</span>
                            <span class="info-value">' . $subregion . '</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>👥 Demografi</h3>
                    <div class="info-items">
                        <div class="info-item">
                            <span class="info-label">Populasi:</span>
                            <span class="info-value">' . $population . ' jiwa</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Luas Wilayah:</span>
                            <span class="info-value">' . $area . ' km²</span>
                        </div>';
        
        if ($density > 0) {
            $html .= '
                        <div class="info-item">
                            <span class="info-label">Kepadatan:</span>
                            <span class="info-value">' . number_format($density, 2) . ' jiwa/km²</span>
                        </div>';
        }
        
        $html .= '
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>🗣️ Bahasa & Mata Uang</h3>
                    <div class="info-items">
                        <div class="info-item">
                            <span class="info-label">Bahasa:</span>
                            <span class="info-value">' . $languagesStr . '</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Mata Uang:</span>
                            <span class="info-value">' . $currenciesStr . '</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>🌐 Informasi Lainnya</h3>
                    <div class="info-items">
                        <div class="info-item">
                            <span class="info-label">Zona Waktu:</span>
                            <span class="info-value">' . $timezones . '</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Negara Tetangga:</span>
                            <span class="info-value">' . $borders . '</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    // Show coat of arms if available and requested
    if ($options['show_coat_of_arms'] && $coatOfArms) {
        $html .= '
        <div class="coat-of-arms">
            <h4>Lambang Negara</h4>
            <img src="' . $coatOfArms . '" alt="Lambang ' . $commonName . '" class="coat-image" loading="lazy">
        </div>';
    }
    
    $html .= '
        <div class="geography-footer">
            <p>🌐 Data dari RestCountries API • Diperbarui: ' . date('d/m/Y H:i') . '</p>
        </div>
    </div>';
    
    return $html;
}

/**
 * Generate compact geography display for mobile/small screens
 * @param array $data Geography data
 * @param array $options Display options
 * @return string Compact HTML
 */
function generateCompactGeographyDisplay($data, $options = []) {
    $commonName = htmlspecialchars($data['name']['common'] ?? 'Unknown');
    $capital = isset($data['capital']) && is_array($data['capital']) 
        ? htmlspecialchars($data['capital'][0]) 
        : 'N/A';
    $population = isset($data['population']) ? number_format($data['population']) : 'N/A';
    $flagUrl = $data['flags']['png'] ?? '';
    
    return '
    <div class="geography-compact-card">
        <div class="compact-header">
            ' . ($flagUrl ? '<img src="' . $flagUrl . '" alt="Flag" class="compact-flag">' : '') . '
            <div class="compact-info">
                <h3>' . $commonName . '</h3>
                <p>Ibu Kota: ' . $capital . '</p>
                <p>Populasi: ' . $population . '</p>
            </div>
        </div>
    </div>';
}

/**
 * Get countries by region
 * @param string $region Region name
 * @return array Countries in the region
 */
function getCountriesByRegion($region) {
    try {
        $url = RESTCOUNTRIES_API_URL . '/region/' . urlencode($region);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Geography App/2.0',
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $data ?: [];
        }
        
        return [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get all available regions
 * @return array List of regions
 */
function getAvailableRegions() {
    return [
        'Africa' => 'Afrika',
        'Americas' => 'Amerika',
        'Asia' => 'Asia',
        'Europe' => 'Eropa',
        'Oceania' => 'Oseania'
    ];
}

/**
 * Validate geography API availability
 * @return bool True if API is accessible
 */
function validateGeographyApi() {
    try {
        $testData = getGeographyData('Indonesia', ['cache' => false]);
        return isset($testData['name']);
    } catch (Exception $e) {
        return false;
    }
}

?>