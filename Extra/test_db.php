
<?php
require_once __DIR__ . '/includes/db.php';

foreach (['songs', 'videos', 'artists'] as $table) {
    echo "<h3>" . htmlspecialchars($table) . "</h3>";

    $result = mysqli_query($conn, "DESCRIBE `$table`");

    if (!$result) {
        echo "Error: " . htmlspecialchars(mysqli_error($conn));
        continue;
    }

    echo "<pre>";
    while ($column = mysqli_fetch_assoc($result)) {
        echo htmlspecialchars($column['Field'])
            . " (" . htmlspecialchars($column['Type']) . ")\n";
    }
    echo "</pre>";
}
?>
