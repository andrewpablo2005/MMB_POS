<?php
try {
    $dbh = new PDO('mysql:host=127.0.0.1;port=3306;dbname=mmbpos;charset=utf8mb4','root','', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    foreach ($dbh->query('SHOW TABLES') as $row) {
        if (stripos($row[0], 'return') !== false) {
            echo $row[0] . PHP_EOL;
        }
    }
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
