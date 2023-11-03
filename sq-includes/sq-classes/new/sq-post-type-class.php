<?php 
/**
 * Class sq_PostTypes
 *
 * This class handles Post Types in SqueHub CMS.
 *
 * @package SqueHub
 * @subpackage sq_PostTypes
 */
class sq_PostTypes {
    /**
     * @var PDO $dbh Database connection object.
     */
    private $dbh;

    /**
     * Constructor initializes sq_PostTypes with a database connection.
     *
     * @param PDO $dbh Database connection object.
     */
    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

    /**
     * Initialize core post types.
     */public function sq_initCorePostTypes() {
    // Create the post types table if it doesn't exist
    $this->sq_createPostTypesTable();

    $corePostTypes = [
        ['post', 'Post', 'post', null, 1], // Added null for parent post type, Set is_default to 1 for
        ['page', 'Page', 'page', null, 1], // Set is_default to 1 for 'page' post type
        // Add more core post types as needed
    ];

    foreach ($corePostTypes as $postType) {
        $postTypeSlug = $postType[0];
        $postTypeName = $postType[1];
        $postTypeDesc = $postType[2];
        $parentPostTypeID = $postType[3]; // Added parent post type ID
        $isDefault = $postType[4]; // Added is_default value

        // Check if the post type already exists in the database
        if (!$this->sq_postTypeExists($postTypeSlug)) {
            // Create the post type in the database
            $this->sq_insertPostType($postTypeSlug, $postTypeName, $postTypeDesc, $parentPostTypeID, $isDefault);
        }
    }
}


    /**
     * Create the post types table if it doesn't exist.
     */
    private function sq_createPostTypesTable() {
        $query = "
                CREATE TABLE IF NOT EXISTS " . SQ_PREFIX . "post_types (
                    post_type_id INT AUTO_INCREMENT PRIMARY KEY,
                    post_type_name VARCHAR(255) NOT NULL,
                    post_type_slug VARCHAR(255) NOT NULL,
                    post_type_desc TEXT,
                    is_default INT DEFAULT 0,  -- Added is_default field
                    parent_post_type_id INT,
                    FOREIGN KEY (parent_post_type_id) REFERENCES " . SQ_PREFIX . "post_types(post_type_id)
                )
        ";

        try {
            $this->dbh->exec($query);
        } catch (PDOException $e) {
            die("Error creating post types table: " . $e->getMessage());
        }
    }

    /**
     * Insert a post type into the database.
     *
     * @param string $postTypeSlug The slug of the post type.
     * @param string $postTypeName The name of the post type.
     * @param string $postTypeType The type of the post type (e.g., post, page, etc.).
     * @param string $postTypeDesc The description of the post type.
     * @param int|null $parentPostTypeID The ID of the parent post type (null if none).
     * @param int  $isDefault
     */
private function sq_insertPostType($postTypeSlug, $postTypeName,  $isDefault,$postTypeDesc = null,  $parentPostTypeID = null) {
        $query = "
            INSERT INTO " . SQ_PREFIX . "post_types 
            (post_type_slug, post_type_name, post_type_desc, parent_post_type_id, is_default) 
            VALUES 
            (:postTypeSlug, :postTypeName, :postTypeDesc, :parentPostTypeID, :isDefault)
        ";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                ':postTypeSlug' => $postTypeSlug,
                ':postTypeName' => $postTypeName,
                ':postTypeDesc' => $postTypeDesc,
                ':parentPostTypeID' => $parentPostTypeID,
                ':isDefault' => $isDefault,
            ]);
        } catch (PDOException $e) {
            die("Error inserting post type into database: " . $e->getMessage());
        }
    }

    /**
     * Check if a post type already exists in the database.
     *
     * @param string $postTypeSlug The slug of the post type.
     * @return bool True if post type exists, false otherwise.
     */
    public function sq_postTypeExists($postTypeSlug) {
        $query = "SELECT COUNT(*) FROM " . SQ_PREFIX . "post_types WHERE post_type_slug = :postTypeSlug";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([':postTypeSlug' => $postTypeSlug]);
            $count = $stmt->fetchColumn();
            return ($count > 0);
        } catch (PDOException $e) {
            die("Error checking post type existence in database: " . $e->getMessage());
        }
    }


    

    // Add more methods for post type management as needed...
}
// Assuming $dbh is your PDO database connection
$sq_PostTypes = new sq_PostTypes($dbh);

// Initialize core categories
$sq_PostTypes->sq_initCorePostTypes();