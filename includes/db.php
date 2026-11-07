<?php
mysqli_report(MYSQLI_REPORT_OFF);

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$dbName = getenv('DB_NAME') ?: 'vebra';

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    error_log('VEBRA database connection failed: ' . mysqli_connect_error());
    exit('Database connection failed. Please check your database settings.');
}

mysqli_set_charset($conn, 'utf8mb4');

function esc($value): string
{
    global $conn;
    return mysqli_real_escape_string($conn, (string) $value);
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

