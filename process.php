<?php
/**
 * FILE: process.php
 * FUNGSI: Memproses request dari form dan memanggil API
 * DESKRIPSI: Menangani POST request, validasi input, dan menampilkan hasil
 */

require_once 'config.php';
require_once 'functions/weather_api.php';
require_once 'functions/geography_api.php';
require_once 'functions/validator.php';

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$apiType = $_POST['api_type'] ?? '';
$result = '';

try {
    switch ($apiType) {
        case 'weather':
            // Tugas Paskah - Weather API
            $city = sanitizeInput($_POST['city'] ?? '');
            
            if (empty($city)) {
                throw new Exception('Nama kota tidak boleh kosong!');
            }
            
            // Validasi API Key
            if (!validateApiKey()) {
                throw new Exception('API Key tidak valid atau belum diatur!');
            }
            
            // Panggil Weather API (implementasi Paskah)
            $weatherData = getWeatherData($city);
            $result = displayWeatherResult($weatherData);
            break;
            
        case 'geography':
            // Tugas Riza - Geography API
            $country = sanitizeInput($_POST['country'] ?? '');
            
            if (empty($country)) {
                throw new Exception('Nama negara tidak boleh kosong!');
            }
            
            // Panggil Geography API (implementasi Riza)
            $geoData = getGeographyData($country);
            $result = displayGeographyResult($geoData);
            break;
            

            
        default:
            throw new Exception('Tipe API tidak valid!');
    }
    
} catch (Exception $e) {
    $result = '<div class="alert alert-error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Simpan hasil ke session dan redirect
$_SESSION['result'] = $result;
header('Location: index.php');
exit;
