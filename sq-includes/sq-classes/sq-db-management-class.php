<?php

/**
 * Database Management Class for SqueHub CMS
 *
 * @package SqueHub
 * @subpackage Database
 */
class sq_DataBaseManagement {
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
     * Method to create the users table if it doesn't exist
     *
     * SQL Query to create users table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            profile_picture VARCHAR(255),
            is_email_verified BOOLEAN DEFAULT 0,
            account_status ENUM('active', 'suspended', 'disabled') DEFAULT 'active',
            role_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES ".SQ_PREFIX."roles(role_id)
        )";

        $this->sq_dbc->exec($sql);
    }


    /**
     * Method to create the themes table if it doesn't exist
     *
     * SQL Query to create themes table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createThemesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."themes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            url VARCHAR(255),
            description TEXT,
            author_name VARCHAR(255),
            author_url VARCHAR(255),
            tags TEXT,
            version VARCHAR(20),
            requires_php_version VARCHAR(20),
            requires_cms_version VARCHAR(20),
            tested_up_to_cms_version VARCHAR(20),
            text_domain VARCHAR(255),
            license VARCHAR(255),
            license_url VARCHAR(255),
            file_path VARCHAR(255),
            is_active BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->sq_dbc->exec($sql);
    }

    /**
     * Method to create the plugins table if it doesn't exist
     *
     * SQL Query to create plugins table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createPluginsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."plugins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            url VARCHAR(255),
            description TEXT,
            author_name VARCHAR(255),
            author_url VARCHAR(255),
            tags TEXT,
            version VARCHAR(20),
            requires_php_version VARCHAR(20),
            requires_cms_version VARCHAR(20),
            tested_up_to_cms_version VARCHAR(20),
            text_domain VARCHAR(255),
            license VARCHAR(255),
            license_url VARCHAR(255),
            file_path VARCHAR(255),
            is_active BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->sq_dbc->exec($sql);
    }


 /**
     * Method to create the roles table if it doesn't exist
     *
     * SQL Query to create roles table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createRolesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."roles (
            role_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->sq_dbc->exec($sql);
    }


     /**
     * Method to create the categories table if it doesn't exist
     *
     * SQL Query to create categories table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createCategoriesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            parent_id INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (parent_id) REFERENCES ".SQ_PREFIX."categories(id)
        )";

        $this->sq_dbc->exec($sql);
    }

    /**
     * Method to create the tags table if it doesn't exist
     *
     * SQL Query to create tags table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createTagsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            parent_id INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (parent_id) REFERENCES ".SQ_PREFIX."tags(id)
        )";

        $this->sq_dbc->exec($sql);
    }

    /**
     * Method to create the post types table if it doesn't exist
     *
     * SQL Query to create post types table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createPostTypesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."post_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            parent_id INT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (parent_id) REFERENCES ".SQ_PREFIX."post_types(id)
        )";

        $this->sq_dbc->exec($sql);
    }

    /**
     * Method to create the posts table if it doesn't exist
     *
     * SQL Query to create posts table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createPostsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content TEXT,
            author_id INT NOT NULL,
            category_id INT NOT NULL,
            post_type_id INT NOT NULL,
            featured_image VARCHAR(255),
            status ENUM('draft', 'published', 'trash') DEFAULT 'draft', /* Added status field */
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, /* Added updated_at field */
            FOREIGN KEY (author_id) REFERENCES ".SQ_PREFIX."users(id),
            FOREIGN KEY (category_id) REFERENCES ".SQ_PREFIX."categories(id),
            FOREIGN KEY (post_type_id) REFERENCES ".SQ_PREFIX."post_types(id)
        )";

        $this->sq_dbc->exec($sql);

        // Create a pivot table for post-tag relationships
        $this->createPostTagPivotTable();

        // Create a pivot table for post-category relationships
        $this->createPostCategoryPivotTable();
    }

    /**
     * Method to create the post-tag pivot table if it doesn't exist
     *
     * @return void
     */
    private function createPostTagPivotTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."post_tag_pivot (
            post_id INT,
            tag_id INT,
            PRIMARY KEY (post_id, tag_id),
            FOREIGN KEY (post_id) REFERENCES ".SQ_PREFIX."posts(id),
            FOREIGN KEY (tag_id) REFERENCES ".SQ_PREFIX."tags(id)
        )";

        $this->sq_dbc->exec($sql);
    }

    /**
     * Method to create the post-category pivot table if it doesn't exist
     *
     * @return void
     */
    private function createPostCategoryPivotTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."post_category_pivot (
            post_id INT,
            category_id INT,
            PRIMARY KEY (post_id, category_id),
            FOREIGN KEY (post_id) REFERENCES ".SQ_PREFIX."posts(id),
            FOREIGN KEY (category_id) REFERENCES ".SQ_PREFIX."categories(id)
        )";

        $this->sq_dbc->exec($sql);
    }


        /**
     * Method to create the pages table if it doesn't exist
     *
     * SQL Query to create pages table
     *
     * @package SqueHub
     * @subpackage Database
     *
     * @return void
     */
    public function createPagesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content TEXT,
            author_id INT NOT NULL,
            parent_id INT,
            template VARCHAR(255),
            featured_image VARCHAR(255),
            status ENUM('draft', 'published', 'trash') DEFAULT 'draft', /* Added status field */
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, /* Added updated_at field */
            FOREIGN KEY (author_id) REFERENCES ".SQ_PREFIX."users(id),
            FOREIGN KEY (parent_id) REFERENCES ".SQ_PREFIX."pages(id)
        )";

        $this->sq_dbc->exec($sql);

        // Create a pivot table for page-tag relationships
        $this->createPageTagPivotTable();

        // Create a pivot table for page-category relationships
        $this->createPageCategoryPivotTable();
    }

    /**
     * Method to create the page-tag pivot table if it doesn't exist
     *
     * @return void
     */
    private function createPageTagPivotTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."page_tag_pivot (
            page_id INT,
            tag_id INT,
            PRIMARY KEY (page_id, tag_id),
            FOREIGN KEY (page_id) REFERENCES ".SQ_PREFIX."pages(id),
            FOREIGN KEY (tag_id) REFERENCES ".SQ_PREFIX."tags(id)
        )";

        $this->sq_dbc->exec($sql);
    }

    /**
     * Method to create the page-category pivot table if it doesn't exist
     *
     * @return void
     */
    private function createPageCategoryPivotTable() {
        $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."page_category_pivot (
            page_id INT,
            category_id INT,
            PRIMARY KEY (page_id, category_id),
            FOREIGN KEY (page_id) REFERENCES ".SQ_PREFIX."pages(id),
            FOREIGN KEY (category_id) REFERENCES ".SQ_PREFIX."categories(id)
        )";

        $this->sq_dbc->exec($sql);
    }


    /**
     * Method to execute custom SQL query for creating a new table.
     *
     * @param string $sql SQL query for creating the table.
     * @return void
     */
    public function sq_makeTable($sql) {
        // Add the SQ_PREFIX only to table names
        $sql = preg_replace('/\bCREATE TABLE IF NOT EXISTS `([a-zA-Z0-9_]+)`/', 'CREATE TABLE IF NOT EXISTS `'.SQ_PREFIX.'$1`', $sql);

        // Execute the modified SQL query
        $this->sq_dbc->exec($sql);
    }


    /**
     * Method to remove a table from the database.
     *
     * @param string $tableName Name of the table to remove.
     * @return void
     */
    public function sq_removeTable($tableName) {
        $sql = "DROP TABLE IF EXISTS ".SQ_PREFIX.$tableName;

        $this->sq_dbc->exec($sql);
    }

    /**
     * Method to modify specific fields in a table.
     *
     * @param string $tableName Name of the table to modify.
     * @param string $sql SQL query for modifying the fields.
     * @return void
     */
    public function sq_modifyTableFields($tableName, $sql) {
        $sql = "ALTER TABLE ".SQ_PREFIX.$tableName." ".$sql;

        $this->sq_dbc->exec($sql);
    }

    /**
 * Method to create the options table if it doesn't exist
 *
 * SQL Query to create options table
 *
 * @package SqueHub
 * @subpackage Database
 *
 * @return void
 */
public function createOptionsTable() {
    $sql = "CREATE TABLE IF NOT EXISTS ".SQ_PREFIX."options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        option_name VARCHAR(255) NOT NULL UNIQUE,
        option_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $this->sq_dbc->exec($sql);

    // Add default options
    $this->addOption('site_icon', 'sq-icon.png'); // Replace with your site's icon
    $this->addOption('site_logo', 'sq-logo.png'); // Replace with your site's logo
    $this->addOption('site_name', 'SqueHub CMS'); // Replace with your site's name
    $this->addOption('site_description', 'A powerful open-source CMS.'); // Replace with your site's description
    $this->addOption('admin_email', 'admin@example.com'); // Replace with your admin email
    $this->addOption('users_can_comment', '1'); // Default to allow comments
    $this->addOption('users_can_register', '1'); // Default to allow user registration
    $this->addOption('default_role', 'subscriber'); // Default user role
    $this->addOption('allow_html_in_comments', '0'); // Default to disallow HTML in comments
    $this->addOption('default_timezone', 'UTC'); // Default timezone
    $this->addOption('date_format', 'Y-m-d'); // Default date format
    $this->addOption('time_format', 'H:i:s'); // Default time format
    $this->addOption('enable_caching', '0'); // Default to disable caching
    $this->addOption('cache_lifetime', '3600'); // Default cache lifetime in seconds
    $this->addOption('enable_https', '0'); // Default to disable HTTPS
    $this->addOption('enable_maintenance_mode', '0'); // Default to disable maintenance mode
    $this->addOption('maintenance_mode_message', 'Under maintenance. Please check back later.'); // Default maintenance mode message
    $this->addOption('enable_google_analytics', '0'); // Default to disable Google Analytics
    $this->addOption('google_analytics_id', ''); // Google Analytics tracking ID (empty by default)
    $this->addOption('enable_facebook_pixel', '0'); // Default to disable Facebook Pixel
    $this->addOption('facebook_pixel_id', ''); // Facebook Pixel ID (empty by default)
    $this->addOption('enable_twitter_integration', '0'); // Default to disable Twitter integration
    $this->addOption('twitter_username', ''); // Twitter username (empty by default)
    $this->addOption('enable_social_sharing', '1'); // Default to enable social sharing buttons
    $this->addOption('enable_seo_features', '1'); // Default to enable SEO features
    $this->addOption('enable_sitemap_generation', '1'); // Default to enable sitemap generation
    $this->addOption('enable_xml_rpc', '0'); // Default to disable XML-RPC
    $this->addOption('enable_json_api', '0'); // Default to disable JSON API
    $this->addOption('custom_css', ''); // Custom CSS (empty by default)
    $this->addOption('custom_js', ''); // Custom JavaScript (empty by default)
    $this->addOption('enable_comments_moderation', '0'); // Default to disable comments moderation
    $this->addOption('enable_registration_captcha', '0'); // Default to disable registration captcha
    $this->addOption('enable_login_captcha', '0'); // Default to disable login captcha
    $this->addOption('enable_two_factor_auth', '0'); // Default to disable two-factor authentication
    $this->addOption('enable_user_profile_avatars', '1'); // Default to enable user profile avatars
    $this->addOption('enable_gravatar_integration', '1'); // Default to enable Gravatar integration
    $this->addOption('enable_user_profile_cover', '1'); // Default to enable user profile cover images
    $this->addOption('enable_user_profile_bio', '1'); // Default to enable user profile biographies
    $this->addOption('enable_user_profile_links', '1'); // Default to enable user profile links
    $this->addOption('enable_user_profile_social_links', '1'); // Default to enable user profile social links
    $this->addOption('custom_404_page', ''); // Custom 404 page (empty by default)

    // Add more default options as needed...
}

/**
 * Method to add an option to the options table. If the option already exists, it will update the value.
 *
 * @param string $optionName Name of the option.
 * @param string $optionValue Value of the option.
 * @return void
 */
public function addOption($optionName, $optionValue) {
    $sql = "INSERT INTO ".SQ_PREFIX."options (option_name, option_value) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)";
    $stmt = $this->sq_dbc->prepare($sql);
    $stmt->execute([$optionName, $optionValue]);
}



        // Methods for other tables go here...
    }

// Usage example:
/*
// Assuming you have a database connection object $sq_dbc
$dbManager = new sq_DataBaseManagement($sq_dbc);

// Create tables
$dbManager->createRolesTable();
$dbManager->createUsersTable();
$dbManager->createThemesTable();
$dbManager->createPluginsTable();
$dbManager->createCategoriesTable();
$dbManager->createTagsTable();
$dbManager->createPostTypesTable();
// Create posts table and pivot tables
$dbManager->createPostsTable();
// Create pages table and pivot tables
$dbManager->createPagesTable();
// Create options table and add default options
$dbManager->createOptionsTable();
// Create other tables as needed...

*/
