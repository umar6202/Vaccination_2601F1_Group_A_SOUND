<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect('localhost', 'root', '', 'vebra');

if (!$conn) {
    exit('MYSQLI ERROR: ' . mysqli_connect_error());
}

echo "MySQLi connection successful<br>";
mysqli_set_charset($conn, 'utf8mb4');
echo "MySQLi charset utf8mb4 set successfully";
