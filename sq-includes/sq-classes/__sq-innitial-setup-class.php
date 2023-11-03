<?php
/**
 * Configuration and Installation Class for SqueHub CMS
 *
 * @package SqueHub
 * @subpackage Installation
 */
class sq_CMS_Installation {
    private $sq_dbc; // Your database connection object goes here

    /**
     * Constructor to initialize the database connection
     *
     * @param PDO $sq_dbc Database connection object
     */
    public function __construct($sq_dbc) {
        $this->sq_dbc = $sq_dbc;
    }

/**
 * Method to store database details in ss-db.php file
 *
 * @param string $host Database host
 * @param string $name Database name
 * @param string $user Database username
 * @param string $pass Database password
 */
public function storeDatabaseDetails($host, $name, $user, $pass) {
    global $sq_dbc;
    
    // Check if the database connection is successful
    $connResult = $this->checkDatabaseConnection($host, $name, $user, $pass);
    
    if (strpos($connResult, 'Error') !== 0) {
        $content = "\n// Database Configuration\n";
        $content .= "\$sq_db_servername = '$host';\n";
        $content .= "\$sq_db_username = '$user';\n";
        $content .= "\$sq_db_password = '$pass';\n";
        $content .= "\$sq_db_name = '$name';\n";
        
        // Now, let's add the code to establish the PDO connection
        $content .= "try {\n";
        $content .= "    \$sq_dbc = new PDO(\"mysql:host=\$sq_db_servername;dbname=\$sq_db_name\", \$sq_db_username, \$sq_db_password);\n";
        $content .= "    \$sq_dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
        $content .= "    return 'Database details saved successfully!';\n";
        $content .= "} catch(PDOException \$e) {\n";
        $content .= "    return 'Error saving database details. ' . \$e->getMessage();\n";
        $content .= "}\n";
    
        $file = 'sq-config.php';
    
        if(file_put_contents($file, $content, FILE_APPEND | LOCK_EX) !== false) {
            return 'Database details saved successfully!';
        } else {
            return 'Error saving database details.';
        }
    } else {
        return $connResult; // Return the connection error message
    }
}    
   


    

    

    /**
     * Method to check if the provided database details are correct
     *
     * @param string $host Database host
     * @param string $name Database name
     * @param string $user Database username
     * @param string $pass Database password
     */
    public function checkDatabaseConnection($host, $name, $user, $pass) {
        global $sq_dbc;
        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
        try {
            $dbh = new PDO($dsn, $user, $pass);
            return 'Database connection successful!';
        } catch (PDOException $e) {
            return 'Error connecting to the database: ' . $e->getMessage();
        }
    }
}