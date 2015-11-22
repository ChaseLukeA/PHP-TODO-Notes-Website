<?php

try {

    $dsn = "mysql:host=$HOST:$PORT;dbname=$DBNAME";

    $pdo = new PDO($dsn, $USERNAME, $PASSWORD);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('SET NAMES "utf8"');
}
catch (PDOException $ex) {

    $errorMsg = "Error connecting to the database: " . $ex->getMessage();
    $_SESSION['action'] = "";

    include 'exception.php';
    exit();
}