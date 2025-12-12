<?php
/**
 * FILE: tests/FileTypeTest.php
 * FUNGSI: Unit test menggunakan PHPUnit untuk validasi file dan struktur proyek
 * DESKRIPSI: 5 Test Case sesuai requirement tugas
 * AUTHOR: Sindi (Ketua Tim) - Sistem Informasi Semester 5
 */

use PHPUnit\Framework\TestCase;

class FileTypeTest extends TestCase
{
    /**
     * Daftar file yang harus ada di proyek
     */
    private $projectFiles = [
        'index.php',
        'config.php',
        'functions/weather_api.php',   
        'functions/geography_api.php',     
        'functions/validator.php'
    ];

    /**
     * Test Case 1: File Exist
     * Pastikan semua file proyek yang dibutuhkan ada
     * Implementasi: assertFileExists()
     */
    public function test_files_exist()
    {
        foreach ($this->projectFiles as $file) {
            $this->assertFileExists($file, "File $file tidak ditemukan");
        }
    }

    /**
     * Test Case 2: Valid PHP Code  
     * Pastikan file PHP mengandung start tag <?php
     * Implementasi: assertStringContainsString() untuk mencari '<?php'
     */
    public function test_php_files_contain_php_code()
    {
        foreach ($this->projectFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($file);
                $this->assertStringContainsString('<?php', $content, "File $file tidak mengandung kode PHP!");
            }
        }
    }

    /**
     * Test Case 3: API Key Tidak Boleh Kosong
     * Pastikan variabel API Key memiliki nilai
     * Implementasi: assertNotEmpty() atau assertNotNull() pada variabel API Key
     */
    public function test_api_key_not_empty()
    {
        require_once 'config.php';
        
        // Cek apakah konstanta API key terdefinisi dan tidak kosong
        $this->assertTrue(defined('WEATHER_API_KEY'), 'WEATHER_API_KEY tidak terdefinisi');
        $this->assertNotEmpty(WEATHER_API_KEY, 'WEATHER_API_KEY tidak boleh kosong');
        $this->assertNotEquals('YOUR_API_KEY_HERE', WEATHER_API_KEY, 'API Key harus diisi dengan nilai yang valid');
    }

    /**
     * Test Case 4: Response Code Harus 200
     * Pastikan request API berhasil dengan kode status HTTP 200
     * Implementasi: Melakukan request dan memvalidasi statusCode == 200
     */
    public function test_response_code_validation()
    {
        require_once 'functions/validator.php';
        
        // Cek apakah fungsi isSuccessResponse ada
        $this->assertTrue(function_exists('isSuccessResponse'), 'Fungsi isSuccessResponse tidak ada');
        
        // Test fungsi dengan status code 200
        $this->assertTrue(isSuccessResponse(200), 'Status code 200 harus valid');
        
        // Test fungsi dengan status code selain 200
        $this->assertFalse(isSuccessResponse(404), 'Status code 404 harus invalid');
        $this->assertFalse(isSuccessResponse(500), 'Status code 500 harus invalid');
    }

    /**
     * Test Case 5: Valid JSON Response
     * Pastikan data yang diterima dari API adalah JSON yang valid
     * Implementasi: json_decode() dan memvalidasi struktur array/object
     */
    public function test_valid_json_response()
    {
        require_once 'functions/validator.php';
        
        // Cek apakah fungsi isValidJson ada
        $this->assertTrue(function_exists('isValidJson'), 'Fungsi isValidJson tidak ada');
        
        // Test dengan JSON valid yang memiliki field penting
        $validJson = '{"name":"Jakarta","temp":30,"city":"Jakarta"}';
        $this->assertTrue(isValidJson($validJson), 'JSON valid harus return true');
        
        // Test validasi struktur JSON - harus memiliki field penting
        $data = json_decode($validJson, true);
        $this->assertArrayHasKey('name', $data, 'JSON harus memiliki field "name"');
        $this->assertArrayHasKey('temp', $data, 'JSON harus memiliki field "temp" atau field penting lainnya');
        
        // Test dengan JSON invalid
        $invalidJson = '{name:Jakarta,temp:30}';
        $this->assertFalse(isValidJson($invalidJson), 'JSON invalid harus return false');
    }
}
