<?php 
/**
 * Class sq_ThemeManagement
 *
 * This class handles theme-related functionalities in SqueHub CMS.
 *
 * @package SqueHub
 * @subpackage ThemeManagement
 */
class sq_ThemeManagement {
    /**
     * @var PDO $sq_dbc Database connection object.
     */
    private $sq_dbc;

    /**
     * Constructor initializes ThemeManagement with a database connection.
     *
     * @param PDO $sq_dbc Database connection object.
     */
    public function __construct($sq_dbc) {
        $this->sq_dbc = $sq_dbc;
    }


    /**
     * Create the theme table in the database.
     */
    public function sq_createThemeTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS " . SQ_PREFIX . "theme (
                theme_id INT AUTO_INCREMENT PRIMARY KEY,
                theme_name VARCHAR(255) NOT NULL,
                theme_dir VARCHAR(255) NOT NULL,
                is_active INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                description TEXT,
                author VARCHAR(255),
                version VARCHAR(20),
                created_date DATE,
                updated_date DATE,
                tags VARCHAR(255),
                text_domain VARCHAR(255)
            )
        ";

        try {
            $this->sq_dbc->exec($query);
        } catch (PDOException $e) {
            // Handle any errors that occur during table creation
            //die("Error creating theme table: " . $e->getMessage());
        }
    }



    /**
     * Install a theme from a theme directory.
     *
     * @param string $themeFolder The folder name of the theme.
     */
    public function sq_installTheme($themeFolder) {
        // Check if theme.info file exists
        $themeInfoPath = SQ_THEME_DIR . $themeFolder . '/theme.info';

        if (file_exists($themeInfoPath)) {
            // Theme.info file found, parse theme information
            $themeInfo = $this->parseThemeInfo($themeInfoPath);

            // Install theme in database
            $this->sq_installThemeInDatabase($themeInfo, $themeFolder);

            // Optionally, you can copy theme files to the desired location
            // For example: copy(SQ_THEME_DIR . $themeFolder, NEW_THEME_DIR);

           // echo "Theme '{$themeInfo['name']}' installed successfully!";
        } else {
           // echo "Theme.info file not found for '{$themeFolder}' theme.";
        }
    }

    private function sq_installThemeInDatabase($themeInfo, $themeFolder) {
        // Check if a theme with the same theme_dir exists
        $query = "SELECT COUNT(*) FROM " . SQ_PREFIX . "theme WHERE theme_dir = :themeDir";
        $stmt = $this->sq_dbc->prepare($query);
        $stmt->execute([':themeDir' => $themeFolder]);
    
        $themeExists = (bool)$stmt->fetchColumn();
    
        if ($themeExists) {
            // Theme with the same theme_dir exists, update fields
            $query = "
                UPDATE " . SQ_PREFIX . "theme SET
                    theme_name = :themeName,
                    description = :description,
                    author = :author,
                    version = :version,
                    created_date = :created,
                    updated_date = :updated,
                    tags = :tags,
                    text_domain = :textDomain
                WHERE theme_dir = :themeDir
            ";
        } else {
            // Theme with the same theme_dir doesn't exist, insert a new entry
            $query = "
                INSERT INTO " . SQ_PREFIX . "theme 
                (theme_name, theme_dir, description, author, version, created_date, updated_date, tags, text_domain) 
                VALUES 
                (:themeName, :themeDir, :description, :author, :version, :created, :updated, :tags, :textDomain)
            ";
        }
    
        try {
            $stmt = $this->sq_dbc->prepare($query);
            $stmt->execute([
                ':themeName' => $themeInfo['name'],
                ':themeDir' => $themeFolder,
                ':description' => $themeInfo['description'],
                ':author' => $themeInfo['author'],
                ':version' => $themeInfo['version'],
                ':created' => $themeInfo['created'],
                ':updated' => $themeInfo['updated'],
                ':tags' => $themeInfo['tags'],
                ':textDomain' => $themeInfo['text-domain']
            ]);
        } catch (PDOException $e) {
           // die("Error installing/updating theme in database: " . $e->getMessage());
        }
    }
    

    /**
     * Check if a theme directory exists and remove it from the database if not.
     *
     * @param string $themeFolder The folder name of the theme.
     */
    public function sq_checkThemeDirectory($themeFolder) {
        $themePath = SQ_THEME_DIR . $themeFolder;

        if (!is_dir($themePath)) {
            // Theme directory doesn't exist, remove it from the database
            $this->sq_removeThemeFromDatabaseIfNotExists($themeFolder);
        }
    }

    /**
     * Remove a theme from the database if the theme directory doesn't exist.
     *
     * @param string $themeFolder The folder name of the theme.
     */
    public function sq_removeThemeFromDatabaseIfNotExists($themeFolder) {
        $query = "DELETE FROM " . SQ_PREFIX . "theme WHERE theme_dir = :themeFolder AND NOT EXISTS (SELECT 1 FROM " . SQ_PREFIX . "theme WHERE theme_dir = :themeFolder)";

        try {
            $stmt = $this->sq_dbc->prepare($query);
            $stmt->bindParam(':themeFolder', $themeFolder, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            // Handle any errors that occur during database operation
            die("Error removing theme from database: " . $e->getMessage());
        }
    }


    /**
     * Parse theme information from a theme.info file.
     *
     * @param string $themeInfoPath Path to the theme.info file.
     * @return array Theme information.
     */ 
    function parseThemeInfo($themeInfoPath) {
        $info = file($themeInfoPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $themeInfo = [];

        foreach ($info as $line) {
            list($key, $value) = explode(' = ', $line);
            $themeInfo[trim($key)] = trim($value);
        }

        return $themeInfo;
    }

    /**
     * Retrieve a list of installed themes from the database.
     *
     * @return array List of installed themes.
     */
    public function sq_getInstalledThemes() {
        // Implement logic to retrieve installed themes from the database using $this->sq_dbc (PDO object).
    }

    /**
     * Activate the selected theme in the database.
     *
     * @param int $themeId ID of the theme to activate.
     */
    public function sq_activateTheme($themeId) {
        // Implement logic to activate the selected theme in the database.
    }

    /**
     * Deactivate the selected theme in the database.
     *
     * @param int $themeId ID of the theme to deactivate.
     */
    public function sq_deactivateTheme($themeId) {
        // Implement logic to deactivate the selected theme in the database.
    }

    /**
     * Upload and install a new theme.
     *
     * @param string $themeFile Path to the uploaded theme file.
     */
    public function sq_uploadTheme($themeFile) {
        // Implement logic to upload and install a new theme.
    }

    /**
     * Delete a theme from the database and filesystem.
     *
     * @param int $themeId ID of the theme to delete.
     */
    public function sq_deleteTheme($themeId) {
        // Implement logic to delete a theme from the database and filesystem.
    }

    /**
     * Store and apply theme customizations.
     *
     * @param int $themeId ID of the theme to customize.
     * @param array $customizations Array of customizations.
     */
    public function sq_customizeTheme($themeId, $customizations) {
        // Implement logic to store and apply theme customizations.
    }

    /**
     * Retrieve the currently active theme from the database.
     *
     * @return array|null Associative array containing theme information if active theme is found, else null.
     */
    public function sq_getActiveTheme() {
        $query = "SELECT * FROM " . SQ_PREFIX . "theme WHERE is_active = 1 LIMIT 1";

        try {
            $stmt = $this->sq_dbc->query($query);
            $activeTheme = $stmt->fetch(PDO::FETCH_ASSOC);

            return $activeTheme ? $activeTheme : null;
        } catch (PDOException $e) {
            // Handle any errors that occur during database operation
            die("Error retrieving active theme from database: " . $e->getMessage());
        }
    }

    
    /**
     * Retrieve the directory of the currently active theme from the database.
     *
     * @return string|null Active theme directory if found, else null.
     */
    public function sq_getActiveThemeDir() {
        $query = "SELECT theme_dir FROM " . SQ_PREFIX . "theme WHERE is_active = 1 LIMIT 1";

        try {
            $stmt = $this->sq_dbc->query($query);
            $activeThemeDir = $stmt->fetchColumn();

            return $activeThemeDir ? $activeThemeDir : null;
        } catch (PDOException $e) {
            // Handle any errors that occur during database operation
            die("Error retrieving active theme directory from database: " . $e->getMessage());
        }
    }

    /**
     * Apply the active theme's assets (CSS, JS, etc.).
     */
    public function sq_applyTheme() {
        // Implement logic to apply the active theme's assets.
    }
}

/*
// Assuming $sq_dbc is your PDO database connection
$themeManagement = new sq_ThemeManagement($sq_dbc);

// Create the theme table
$themeManagement->sq_createThemeTable();


$themeDirectory = SQ_THEME_DIR; // Assuming SQ_THEME_DIR contains the path to your theme directory

// Get a list of all directories in the theme directory
$themeFolders = array_filter(glob($themeDirectory . '*'), 'is_dir');

foreach ($themeFolders as $themeFolder) {
    // Extract the folder name from the path
    $themeFolderName = basename($themeFolder);

    // Install the theme
    $themeManagement->sq_installTheme($themeFolderName);
}


// Assuming $themeFolderName is the folder name of the theme you want to remove
$themeManagement->sq_removeThemeFromDatabaseIfNotExists($themeFolderName);



// Retrieve the active theme
$activeTheme = $themeManagement->sq_getActiveTheme();

// Check if an active theme was found
if ($activeTheme !== null) {
    echo "Active Theme Name: " . $activeTheme['theme_name'] . "<br>";
    echo "Active Theme Author: " . $activeTheme['author'] . "<br>";
    echo "Active Theme Directory: " . $activeTheme['theme_dir'] . "<br>";
    echo "Active Theme Text Domain: " . $activeTheme['text_domain'] . "<br>";
    echo "Active Theme Tags: " . $activeTheme['tags'] . "<br>";


    // Add more information if needed
} else {
    echo "No active theme found.";
}


$activeThemeDir = $themeManagement->sq_getActiveThemeDir();


echo $activeThemeDir;
*/
// ...


