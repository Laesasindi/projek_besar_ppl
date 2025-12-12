<?php
/**
 * Simple test runner for GitHub Actions
 * Alternative to PHPUnit if there are permission issues
 */

echo "🧪 Running Weather App Tests...\n";

// Test 1: Check if all required files exist
echo "\n1. Testing file existence...\n";
$requiredFiles = [
    'index.php',
    'config.php',
    'functions/weather_api.php',
    'functions/geography_api.php',
    'functions/validator.php'
];

$filesExist = true;
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file missing\n";
        $filesExist = false;
    }
}

// Test 2: Check PHP syntax
echo "\n2. Testing PHP syntax...\n";
$syntaxOk = true;
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        $output = [];
        $returnCode = 0;
        exec("php -l $file 2>&1", $output, $returnCode);
        if ($returnCode === 0) {
            echo "✅ $file syntax OK\n";
        } else {
            echo "❌ $file syntax error: " . implode(' ', $output) . "\n";
            $syntaxOk = false;
        }
    }
}

// Test 3: Check if functions can be loaded
echo "\n3. Testing function loading...\n";
$functionsOk = true;
try {
    require_once 'config.php';
    echo "✅ config.php loaded\n";
    
    require_once 'functions/validator.php';
    echo "✅ validator.php loaded\n";
    
    require_once 'functions/weather_api.php';
    echo "✅ weather_api.php loaded\n";
    
    require_once 'functions/geography_api.php';
    echo "✅ geography_api.php loaded\n";
    
} catch (Exception $e) {
    echo "❌ Error loading functions: " . $e->getMessage() . "\n";
    $functionsOk = false;
}

// Test 4: Check validator functions
echo "\n4. Testing validator functions...\n";
$validatorOk = true;
try {
    if (function_exists('isValidJson')) {
        $testJson = '{"test": "value"}';
        if (isValidJson($testJson)) {
            echo "✅ isValidJson function works\n";
        } else {
            echo "❌ isValidJson function failed\n";
            $validatorOk = false;
        }
    } else {
        echo "❌ isValidJson function not found\n";
        $validatorOk = false;
    }
    
    if (function_exists('isSuccessResponse')) {
        if (isSuccessResponse(200) && !isSuccessResponse(404)) {
            echo "✅ isSuccessResponse function works\n";
        } else {
            echo "❌ isSuccessResponse function failed\n";
            $validatorOk = false;
        }
    } else {
        echo "❌ isSuccessResponse function not found\n";
        $validatorOk = false;
    }
    
} catch (Exception $e) {
    echo "❌ Error testing validator functions: " . $e->getMessage() . "\n";
    $validatorOk = false;
}

// Test 5: Check API key configuration
echo "\n5. Testing API configuration...\n";
$configOk = true;
try {
    if (defined('WEATHER_API_KEY')) {
        if (!empty(WEATHER_API_KEY) && WEATHER_API_KEY !== 'YOUR_API_KEY_HERE') {
            echo "✅ WEATHER_API_KEY is configured\n";
        } else {
            echo "⚠️ WEATHER_API_KEY needs to be set\n";
        }
    } else {
        echo "❌ WEATHER_API_KEY not defined\n";
        $configOk = false;
    }
} catch (Exception $e) {
    echo "❌ Error checking API configuration: " . $e->getMessage() . "\n";
    $configOk = false;
}

// Summary
echo "\n📊 Test Summary:\n";
echo "Files exist: " . ($filesExist ? "✅ PASS" : "❌ FAIL") . "\n";
echo "PHP syntax: " . ($syntaxOk ? "✅ PASS" : "❌ FAIL") . "\n";
echo "Functions load: " . ($functionsOk ? "✅ PASS" : "❌ FAIL") . "\n";
echo "Validator functions: " . ($validatorOk ? "✅ PASS" : "❌ FAIL") . "\n";
echo "API configuration: " . ($configOk ? "✅ PASS" : "⚠️ WARNING") . "\n";

$allTestsPassed = $filesExist && $syntaxOk && $functionsOk && $validatorOk;

if ($allTestsPassed) {
    echo "\n🎉 All tests passed! Weather App is ready.\n";
    exit(0);
} else {
    echo "\n❌ Some tests failed. Please check the issues above.\n";
    exit(1);
}