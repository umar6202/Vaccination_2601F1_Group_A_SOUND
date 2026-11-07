<?php
require_once __DIR__ . '/includes/db.php';

$tables = ['users', 'genres', 'reviews', 'songs', 'videos', 'artists'];
foreach ($tables as $t) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM `$t`");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        echo $t . ': ' . $row['cnt'] . " rows\n";
    }
}

