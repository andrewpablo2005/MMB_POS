<?php
try {
    $dbh = new PDO('mysql:host=127.0.0.1;port=3306;dbname=mmbpos;charset=utf8mb4','root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    foreach (array('return_transactions', 'return_items') as $table) {
        echo 'TABLE: ' . $table . PHP_EOL;
        foreach ($dbh->query('DESCRIBE ' . $table) as $row) {
            echo implode(' | ', array_values($row)) . PHP_EOL;
        }
        echo PHP_EOL;
    }
} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
