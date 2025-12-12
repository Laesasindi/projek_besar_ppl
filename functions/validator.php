<?php
/**
 * FILE: functions/validator.php
 * FUNGSI: Fungsi-fungsi untuk validasi input dan data
 * DESKRIPSI: Memastikan data yang diproses aman dan valid
 * VERSION: 2.0 - Enhanced with additional validation functions
 */

/**
 * Membersihkan input dari karakter berbahaya
 * @param string $input Input dari user
 * @return string Input yang sudah dibersihkan
 */
function sanitizeInput($input) {
    if (!is_string($input)) {
        return '';
    }
    
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    // Remove any remaining dangerous characters
    $input = preg_replace('/[<>"\']/', '', $input);
    
    return $input;
}

/**
 * Validasi nama kota
 * @param string $city Nama kota
 * @return bool True jika valid
 */
function validateCityName($city) {
    if (empty($city) || !is_string($city)) {
        return false;
    }
    
    // City name should be 2-50 characters, letters, spaces, hyphens, apostrophes
    $pattern = '/^[a-zA-Z\s\-\'\.]{2,50}$/u';
    return preg_match($pattern, trim($city));
}

/**
 * Validasi nama negara
 * @param string $country Nama negara
 * @return bool True jika valid
 */
function validateCountryName($country) {
    if (empty($country) || !is_string($country)) {
        return false;
    }
    
    // Country name should be 2-60 characters, letters, spaces, hyphens
    $pattern = '/^[a-zA-Z\s\-]{2,60}$/u';
    return preg_match($pattern, trim($country));
}

/**
 * Validasi format JSON
 * @param string $json String JSON
 * @return bool True jika valid
 */
function isValidJson($json) {
    if (empty($json) || !is_string($json)) {
        return false;
    }
    
    json_decode($json);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Validasi response code HTTP
 * @param int $code HTTP status code
 * @return bool True jika code adalah success (200-299)
 */
function isSuccessResponse($code) {
    return is_numeric($code) && $code >= 200 && $code < 300;
}

/**
 * Validasi API key format
 * @param string $apiKey API key
 * @return bool True jika format valid
 */
function validateApiKeyFormat($apiKey) {
    if (empty($apiKey) || !is_string($apiKey)) {
        return false;
    }
    
    // API key should be alphanumeric, 20-50 characters
    $pattern = '/^[a-zA-Z0-9]{20,50}$/';
    return preg_match($pattern, $apiKey);
}

/**
 * Validasi koordinat latitude
 * @param float $lat Latitude
 * @return bool True jika valid
 */
function validateLatitude($lat) {
    return is_numeric($lat) && $lat >= -90 && $lat <= 90;
}

/**
 * Validasi koordinat longitude
 * @param float $lng Longitude
 * @return bool True jika valid
 */
function validateLongitude($lng) {
    return is_numeric($lng) && $lng >= -180 && $lng <= 180;
}

/**
 * Validasi email format
 * @param string $email Email address
 * @return bool True jika valid
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validasi URL format
 * @param string $url URL
 * @return bool True jika valid
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Sanitize array input
 * @param array $array Input array
 * @return array Sanitized array
 */
function sanitizeArray($array) {
    if (!is_array($array)) {
        return [];
    }
    
    $sanitized = [];
    foreach ($array as $key => $value) {
        $cleanKey = sanitizeInput($key);
        if (is_array($value)) {
            $sanitized[$cleanKey] = sanitizeArray($value);
        } else {
            $sanitized[$cleanKey] = sanitizeInput($value);
        }
    }
    
    return $sanitized;
}

/**
 * Validasi rate limiting
 * @param string $identifier Unique identifier (IP, user ID, etc)
 * @param int $maxRequests Maximum requests allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if within limits
 */
function checkRateLimit($identifier, $maxRequests = 60, $timeWindow = 3600) {
    if (!function_exists('apcu_fetch')) {
        return true; // Skip if APCu not available
    }
    
    $key = 'rate_limit_' . md5($identifier);
    $current = apcu_fetch($key);
    
    if ($current === false) {
        // First request
        apcu_store($key, 1, $timeWindow);
        return true;
    }
    
    if ($current >= $maxRequests) {
        return false; // Rate limit exceeded
    }
    
    // Increment counter
    apcu_inc($key);
    return true;
}

/**
 * Validasi CSRF token
 * @param string $token Token to validate
 * @param string $sessionToken Token from session
 * @return bool True if valid
 */
function validateCsrfToken($token, $sessionToken) {
    if (empty($token) || empty($sessionToken)) {
        return false;
    }
    
    return hash_equals($sessionToken, $token);
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validasi input berdasarkan tipe
 * @param mixed $input Input to validate
 * @param string $type Type of validation
 * @param array $options Additional options
 * @return bool True if valid
 */
function validateInput($input, $type, $options = []) {
    switch ($type) {
        case 'city':
            return validateCityName($input);
        case 'country':
            return validateCountryName($input);
        case 'email':
            return validateEmail($input);
        case 'url':
            return validateUrl($input);
        case 'latitude':
            return validateLatitude($input);
        case 'longitude':
            return validateLongitude($input);
        case 'api_key':
            return validateApiKeyFormat($input);
        case 'required':
            return !empty($input);
        case 'numeric':
            return is_numeric($input);
        case 'integer':
            return filter_var($input, FILTER_VALIDATE_INT) !== false;
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
        case 'boolean':
            return filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
        case 'length':
            $min = $options['min'] ?? 0;
            $max = $options['max'] ?? PHP_INT_MAX;
            $length = strlen($input);
            return $length >= $min && $length <= $max;
        default:
            return true;
    }
}

/**
 * Batch validation untuk multiple inputs
 * @param array $inputs Array of inputs to validate
 * @param array $rules Validation rules
 * @return array Validation results
 */
function validateBatch($inputs, $rules) {
    $results = [
        'valid' => true,
        'errors' => [],
        'sanitized' => []
    ];
    
    foreach ($rules as $field => $rule) {
        $input = $inputs[$field] ?? null;
        $type = $rule['type'] ?? 'required';
        $options = $rule['options'] ?? [];
        $required = $rule['required'] ?? true;
        
        // Check if field is required
        if ($required && empty($input)) {
            $results['valid'] = false;
            $results['errors'][$field] = 'Field is required';
            continue;
        }
        
        // Skip validation if field is empty and not required
        if (!$required && empty($input)) {
            $results['sanitized'][$field] = '';
            continue;
        }
        
        // Validate input
        if (!validateInput($input, $type, $options)) {
            $results['valid'] = false;
            $results['errors'][$field] = "Invalid {$type} format";
        }
        
        // Sanitize input
        $results['sanitized'][$field] = sanitizeInput($input);
    }
    
    return $results;
}
