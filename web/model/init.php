<?php
try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWD);
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    }
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    exit("Failed to connect DB");
}