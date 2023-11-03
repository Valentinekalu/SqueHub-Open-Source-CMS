<?php
// index.php

// Start session
session_start();

// Set maintenance mode
$ghostmood = 0; // Set maintenance mode to 1 (0 for off)

// Define admin URL
$getadminurl = '/admin/';



// Include SqueHub configuration
require_once 'sq-config.php';

// Include classes
$classesDirectory = SQ_CLASSES_DIR;
$classesfiles = scandir($classesDirectory);

// Loop through each class file and includes/require it if it's a PHP file
foreach ($classesfiles as $file) {
    $filePath = $classesDirectory . '/' . $file;

    if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $filePath;
    }
}

// Initialize router
$router = new sq_Router(); // Create an instance of the Router class

// Include functions files
$functionsDirectory = SQ_FUNCTIONS_DIR;
$functionsFiles = scandir($functionsDirectory);

// Loop through each functions file and include/require it if it's a PHP file
foreach ($functionsFiles as $file) {
    $filePath = $functionsDirectory . '/' . $file;

    if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $filePath;
    }
}

// admins dir files
$adminsDirectory = SQ_ADMIN_DIR;
$adminsFiles = scandir($adminsDirectory);

// Loop through each sq-admins file and include/require it if it's a PHP file
foreach ($adminsFiles as $file) {
    $filePath = $adminsDirectory . '/' . $file;

    if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $filePath;
    }
}




/*

try {
    // Attempt to connect
    $dbh = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($dbh->connect_error) {
        require_once './sq-includes/sq-functions/sq-route-stream/sq-configuration-stream.php';
        header('Location: /sq-configuration'); // Redirect to /sq-configuration
        exit();
    } else {
        require_once './sq-includes/sq-functions/sq-route-stream/user-route.php';
        $dbh->close();
    }
} catch (Exception $e) {
    require_once './sq-includes/sq-functions/sq-route-stream/sq-configuration-stream.php';
    header('Location: /sq-configuration'); // Redirect to /sq-configuration
    exit();
}

*/


/*
$sql = "CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


$dbManager->sq_makeTable($sql);

*/


// Modify the 'products' table to add a new column
//$sql = "ADD COLUMN `new_column` VARCHAR(255) NOT NULL AFTER `price`";
//$dbManager->sq_modifyTableFields('products', $sql);

// Modify the 'products' table to add a new column
//$sql = "ADD COLUMN `new_column1` VARCHAR(255) NOT NULL";
//$dbManager->sq_modifyTableFields('products', $sql);

// Remove a table named 'products'
//$dbManager->sq_removeTable('products');

// Handle the current request
$router->sq_route();



?>
