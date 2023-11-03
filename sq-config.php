<?php
// sq-config.php

// Define the base URL of the project
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$subfolder = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . '://' . $host . $subfolder);



// SqueHub Prefix
define('SQ_PREFIX', 'sq_');          // Prefix for tables, classes, etc.


// Paths
define('SQ_THEME_DIR', __DIR__ . '/sq-content/themes/'); // Theme directory path
define('SQ_PLUGIN_DIR', __DIR__ . '/sq-content/plugins/'); // Plugin directory path
define('SQ_ADMIN_DIR', __DIR__ . '/sq-admin/'); // Admin panel directory path
define('SQ_INCLUDES_DIR', __DIR__ . '/sq-includes/'); // Include directory path
define('SQ_CLASSES_DIR', SQ_INCLUDES_DIR . '/sq-classes/'); // Class directory path
define('SQ_FUNCTIONS_DIR', SQ_INCLUDES_DIR . '/sq-functions/'); // functions directory path


// URLs
define('SQ_THEME_URL', '/sq-content/themes/'); // Theme directory URL
define('SQ_PLUGIN_URL', '/sq-content/plugins/'); // Plugin directory URL
define('SQ_ADMIN_URL', '/sq-admin/'); // Admin panel directory URL
define('SQ_INCLUDES_URL', '/sq-includes/'); // Include directory URL
define('SQ_CLASSES_URL', SQ_INCLUDES_URL . '/sq-classes/'); // Class directory URL
define('SQ_FUNCTIONS_URL', SQ_INCLUDES_URL . '/sq-functions/'); // functions directory URL


// Debugging
define('SQ_DEBUG', false); // Enable debugging mode

if (SQ_DEBUG) {
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Display a message for debugging mode
    echo 'SqueHub Debug Mode Is ON';
} else {
    // In a production environment, disable error reporting and don't display errors to users
    error_reporting(0);
    ini_set('display_errors', 0);
}


// Additional Configurations
define('SQ_MAX_UPLOAD_SIZE', '10M'); // Maximum allowed file upload size
define('SQ_CACHE_EXPIRATION', 3600); // Cache expiration time in seconds
define('SQ_DEFAULT_LANGUAGE', 'en_US'); // Default language for the CMS
// ...

// You can add more configuration settings as needed for your CMS



/**
 * Checks if the database is connected.
 * 
 * @return bool True if connected, false otherwise.
 */
function isSQ_DBConnected($sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name) {
    try {
        // Attempt to connect
        $sq_dbc = new mysqli($sq_db_servername, $sq_db_username, $sq_db_password, $sq_db_name);
    
        // Check connection
        if ($sq_dbc->connect_error) {
            return false;
        } else {
            $sq_dbc->close();
            return true;
        }
    } catch (Exception $e) {
        // Handle the exception (log it, etc.)
        return false;
    }
}





// Database Configuration
$sq_db_servername = 'localhost';
$sq_db_username = 'root';
$sq_db_password = '';
$sq_db_name = 'squehub_cms';
try {
    $sq_dbc = new PDO("mysql:host=$sq_db_servername;dbname=$sq_db_name", $sq_db_username, $sq_db_password);
    $sq_dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return 'Database details saved successfully!';
} catch(PDOException $e) {
    return 'Error saving database details. ' . $e->getMessage();
}
