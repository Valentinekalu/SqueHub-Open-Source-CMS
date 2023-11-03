<?php 
/**
 * Class sq_AuthUrlSlugs
 *
 * This class handles authentication URL slugs in SqueHub CMS.
 *
 * @package SqueHub
 * @subpackage AuthUrlSlugs
 */
class sq_AuthUrlSlugs {
    /**
     * @var PDO $dbh Database connection object.
     */
    private $dbh;

    /**
     * Constructor initializes AuthUrlSlugs with a database connection.
     *
     * @param PDO $dbh Database connection object.
     */
    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

    /**
     * Initialize authentication URL slugs (login, register, reset password, etc.).
     */
    public function sq_initAuthUrlSlugs() {

        // Create the auth slugs table if it doesn't exist
        $this->sq_createAuthSlugsTable();

        $authUrlSlugs = [
            ['sq-login', 'Login', 'login'],
            ['sq-register', 'Register', 'register'],
            ['sq-reset-password', 'Reset Password', 'reset_password']
            // Add more authentication URL slugs as needed
        ];

        foreach ($authUrlSlugs as $slug) {
            $slugName = $slug[0];
            $slugTitle = $slug[1];
            $slugType = $slug[2];

            // Check if the slug already exists in the database
            if (!$this->sq_slugExists($slugName)) {
                // Create the slug in the database
                $this->sq_createSlug($slugName, $slugTitle, $slugType);
            }
        }

        // Delete slugs that don't exist in authUrlSlugs
        $existingSlugs = array_column($authUrlSlugs, 0);
        $this->sq_deleteSlugsWithoutType($existingSlugs);
    }

    /**
     * Create the slugs table if it doesn't exist.
     */
    private function sq_createAuthSlugsTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS " . SQ_PREFIX . "Auth_slugs (
                slug_id INT AUTO_INCREMENT PRIMARY KEY,
                slug_name VARCHAR(255) NOT NULL,
                slug_title VARCHAR(255) NOT NULL,
                slug_type VARCHAR(255) NOT NULL
            )
        ";

        try {
            $this->dbh->exec($query);
        } catch (PDOException $e) {
            die("Error creating slugs table: " . $e->getMessage());
        }
    }

    /**
     * Delete slugs from the database that don't have a corresponding type.
     *
     * @param array $existingSlugs Array of slugs that should exist.
     */
    private function sq_deleteSlugsWithoutType($existingSlugs) {
        $query = "DELETE FROM " . SQ_PREFIX . "auth_slugs WHERE slug_name NOT IN (" . implode(',', array_fill(0, count($existingSlugs), '?')) . ")";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute($existingSlugs);
        } catch (PDOException $e) {
            die("Error deleting slugs from database: " . $e->getMessage());
        }
    }

    /**
     * Delete a slug from the database based on its name.
     *
     * @param string $slugName The name of the slug to delete.
     */
    public function sq_deleteSlugByName($slugName) {
        $query = "DELETE FROM " . SQ_PREFIX . "auth_slugs WHERE slug_name = :slugName";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([':slugName' => $slugName]);
        } catch (PDOException $e) {
            die("Error deleting slug from database: " . $e->getMessage());
        }
    }

    /**
     * Check if a slug already exists in the database.
     *
     * @param string $slugName The name of the slug to check.
     * @return bool True if slug exists, false otherwise.
     */
    private function sq_slugExists($slugName) {
        $query = "SELECT COUNT(*) FROM " . SQ_PREFIX . "auth_slugs WHERE slug_name = :slugName";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([':slugName' => $slugName]);
            $count = $stmt->fetchColumn();
            return ($count > 0);
        } catch (PDOException $e) {
            die("Error checking slug existence in database: " . $e->getMessage());
        }
    }

    /**
     * Create a slug in the database.
     *
     * @param string $slugName The name of the slug.
     * @param string $slugTitle The title of the slug.
     * @param string $slugType The type of the slug (e.g., login, register, etc.).
     */
    private function sq_createSlug($slugName, $slugTitle, $slugType) {
        $query = "
            INSERT INTO " . SQ_PREFIX . "auth_slugs 
            (slug_name, slug_title, slug_type) 
            VALUES 
            (:slugName, :slugTitle, :slugType)
        ";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                ':slugName' => $slugName,
                ':slugTitle' => $slugTitle,
                ':slugType' => $slugType
            ]);
        } catch (PDOException $e) {
            die("Error inserting slug into database: " . $e->getMessage());
        }
    }

    /**
     * Retrieve a list of slugs from the database.
     *
     * @return array List of slugs.
     */
    public function sq_getSlugs() {
        $query = "SELECT * FROM " . SQ_PREFIX . "auth_slugs";

        try {
            $stmt = $this->dbh->query($query);
            $slugs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $slugs;
        } catch (PDOException $e) {
            die("Error retrieving slugs from database: " . $e->getMessage());
        }
    }

    
    // Add more methods for slug management as needed...
}

// Assuming $dbh is your PDO database connection
$authUrlSlugs = new sq_AuthUrlSlugs($dbh);

// Initialize authentication URL slugs
$authUrlSlugs->sq_initAuthUrlSlugs();
