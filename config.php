<?php
/**
 * FILE: config.php
 * FUNGSI: File konfigurasi untuk menyimpan API Keys dan pengaturan
 * DESKRIPSI: Berisi konstanta dan konfigurasi yang digunakan di seluruh aplikasi
 */

// Memulai session untuk menyimpan data sementara (hanya jika bukan CLI)
if (php_sapi_name() !== 'cli') {
    session_start();
}

// API Keys - Ganti dengan API key Anda sendiri
// OpenWeatherMap API: https://openweathermap.org/api
define('WEATHER_API_KEY', getenv('WEATHER_API_KEY') ?: '3e0c7b48086358f5ce90a70eb1d5620f');

// RestCountries API (tidak memerlukan API key)
define('RESTCOUNTRIES_API_URL', 'https://restcountries.com/v3.1');

// OpenWeatherMap API URL
define('WEATHER_API_URL', 'https://api.openweathermap.org/data/2.5');

// Pengaturan timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Fungsi untuk validasi API Key
 * @return bool
 */
function validateApiKey() {
    if (empty(WEATHER_API_KEY) || WEATHER_API_KEY === 'Y3e0c7b48086358f5ce90a70eb1d5620f') {
        return false;
    }
    return true;
}
