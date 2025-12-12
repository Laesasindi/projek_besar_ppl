<?php
/**
 * FILE: index.php
 * FUNGSI: Halaman utama aplikasi REST Client untuk API Cuaca dan Geografi
 * DESKRIPSI: Menampilkan dashboard cuaca modern dengan layout seperti gambar
 */

require_once 'config.php';
require_once 'functions/weather_api.php';
require_once 'functions/geography_api.php';
require_once 'functions/validator.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuaca - Weather Information</title>
    <link rel="stylesheet" href="assets/css/weather-card-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <!-- Header -->
        <header class="app-header">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <div class="brand-text">
                    <h1>Cuaca</h1>
                    <p>Weather Information</p>
                </div>
            </div>
            <nav class="main-nav">
                <a href="#" class="nav-item active" data-section="home">
                    <i class="fas fa-home"></i>
                    Home
                </a>
                <a href="#" class="nav-item" data-section="search">
                    <i class="fas fa-search"></i>
                    Search
                </a>
                <a href="#" class="nav-item" data-section="map">
                    <i class="fas fa-map"></i>
                    Peta Cuaca
                </a>
                <a href="#" class="nav-item" data-section="geography">
                    <i class="fas fa-globe"></i>
                    Geography
                </a>
            </nav>
        </header>

        <!-- Main Dashboard -->
        <main class="dashboard">
            <!-- Current Weather Card -->
            <div class="current-weather-card">
                <div class="current-weather-content">
                    <div class="weather-main">
                        <div class="location-info">
                            <h2 class="city-name">Jakarta</h2>
                            <p class="date-time">Wednesday, 12 December 2025</p>
                        </div>
                        
                        <div class="temperature-display">
                            <span class="main-temp">28°</span>
                            <div class="weather-icon-main">
                                <img src="assets/images/weather/partly-cloudy.svg" alt="Partly Cloudy">
                            </div>
                        </div>
                        
                        <div class="weather-status">
                            <p class="condition">Cloudy</p>
                            <p class="feels-like">Feels like <span>31°</span></p>
                        </div>
                    </div>
                    
                    <div class="weather-details">
                        <div class="detail-item">
                            <span class="detail-label">Visibility</span>
                            <span class="detail-value">10 km</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Pressure</span>
                            <span class="detail-value">1015 hPa</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">UV Index</span>
                            <span class="detail-value">4</span>
                        </div>
                    </div>
                </div>
                
                <!-- Hourly Forecast -->
                <div class="hourly-forecast">
                    <h3>Hourly Forecast</h3>
                    <div class="hourly-items">
                        <div class="hourly-item">
                            <img src="assets/images/weather/partly-cloudy.svg" alt="Weather">
                            <span class="hourly-temp">29°</span>
                            <span class="hourly-time">2 PM</span>
                        </div>
                        <div class="hourly-item">
                            <img src="assets/images/weather/partly-cloudy.svg" alt="Weather">
                            <span class="hourly-temp">28°</span>
                            <span class="hourly-time">3 PM</span>
                        </div>
                        <div class="hourly-item">
                            <img src="assets/images/weather/cloudy.svg" alt="Weather">
                            <span class="hourly-temp">27°</span>
                            <span class="hourly-time">4 PM</span>
                        </div>
                        <div class="hourly-item">
                            <img src="assets/images/weather/rainy.svg" alt="Weather">
                            <span class="hourly-temp">26°</span>
                            <span class="hourly-time">5 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 5 Day Forecast -->
            <div class="forecast-card">
                <h3>Next 5 Days</h3>
                <div class="forecast-list">
                    <div class="forecast-item">
                        <img src="assets/images/weather/partly-cloudy.svg" alt="Weather" class="forecast-icon">
                        <div class="forecast-info">
                            <span class="forecast-city">Bandung</span>
                            <span class="forecast-desc">2 - 6ay</span>
                        </div>
                        <div class="forecast-temps">
                            <span class="temp-high">26°</span>
                            <span class="temp-low">32°</span>
                        </div>
                    </div>
                    
                    <div class="forecast-item">
                        <img src="assets/images/weather/rainy.svg" alt="Weather" class="forecast-icon">
                        <div class="forecast-info">
                            <span class="forecast-city">Medan</span>
                            <span class="forecast-desc">2 - 6ay</span>
                        </div>
                        <div class="forecast-temps">
                            <span class="temp-high">24°</span>
                            <span class="temp-low">32°</span>
                        </div>
                    </div>
                    
                    <div class="forecast-item">
                        <img src="assets/images/weather/sunny.svg" alt="Weather" class="forecast-icon">
                        <div class="forecast-info">
                            <span class="forecast-city">Semarang</span>
                            <span class="forecast-desc">2 - 9ay</span>
                        </div>
                        <div class="forecast-temps">
                            <span class="temp-high">25°</span>
                            <span class="temp-low">33°</span>
                        </div>
                    </div>
                    
                    <div class="forecast-item">
                        <img src="assets/images/weather/cloudy.svg" alt="Weather" class="forecast-icon">
                        <div class="forecast-info">
                            <span class="forecast-city">Makassar</span>
                            <span class="forecast-desc">2 - 3ay</span>
                        </div>
                        <div class="forecast-temps">
                            <span class="temp-high">28°</span>
                            <span class="temp-low">33°</span>
                        </div>
                    </div>
                    
                    <div class="forecast-item">
                        <img src="assets/images/weather/cloudy.svg" alt="Weather" class="forecast-icon">
                        <div class="forecast-info">
                            <span class="forecast-city">Surabaya</span>
                            <span class="forecast-desc">2 - 3ay</span>
                        </div>
                        <div class="forecast-temps">
                            <span class="temp-high">28°</span>
                            <span class="temp-low">32°</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Hidden sections for other features -->
        <div class="hidden-sections" style="display: none;">
            <!-- Search Section -->
            <section id="search-section" class="content-section">
                <div class="search-container">
                    <h2>🔍 Cari Cuaca Kota</h2>
                    <form id="searchForm" method="POST" action="process.php">
                        <input type="hidden" name="api_type" value="weather">
                        <div class="search-box">
                            <input type="text" id="city-search" name="city" placeholder="Masukkan nama kota..." required>
                            <button type="submit" class="search-btn">Cari</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Geography Section -->
            <section id="geography-section" class="content-section">
                <div class="geography-container">
                    <h2>🌍 Informasi Geografi Negara</h2>
                    <form id="geoForm" method="POST" action="process.php">
                        <input type="hidden" name="api_type" value="geography">
                        <div class="geo-form">
                            <select id="country-select" name="country" required>
                                <option value="">-- Pilih Negara --</option>
                                <option value="Indonesia">🇮🇩 Indonesia</option>
                                <option value="Malaysia">🇲🇾 Malaysia</option>
                                <option value="Singapore">🇸🇬 Singapore</option>
                                <option value="Thailand">🇹🇭 Thailand</option>
                                <option value="Philippines">🇵🇭 Philippines</option>
                                <option value="Vietnam">🇻🇳 Vietnam</option>
                                <option value="Japan">🇯🇵 Japan</option>
                                <option value="South Korea">🇰🇷 South Korea</option>
                                <option value="China">🇨🇳 China</option>
                                <option value="India">🇮🇳 India</option>
                                <option value="Australia">🇦🇺 Australia</option>
                                <option value="United States">🇺🇸 United States</option>
                                <option value="United Kingdom">🇬🇧 United Kingdom</option>
                            </select>
                            <button type="submit" class="geo-btn">Cek Informasi</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script src="assets/js/weather-card-script.js"></script>
</body>
</html>