<?php
/**
 * Class sq_CorePostType
 *
 * This class handles custom post types in SqueHub CMS.
 *
 * @package SqueHub
 * @subpackage CorePostType
 */
class sq_CorePostType {
    /**
     * @var PDO $dbh Database connection object.
     */
    private $dbh;

    /**
     * Constructor initializes CorePostType with a database connection.
     *
     * @param PDO $dbh Database connection object.
     */
    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

/**
 * Initialize core post types (post, page, category, tag, author).
 */
public function sq_initCorePostTypes() {
    // Create the post types table if it doesn't exist
    $this->sq_createPostTypesTable();

    $corePostTypes = [
        ['testimonials', 'Testimonials', 'testimonials', null, 1, null],
        ['projects', 'Projects', 'projects', null, 1, null],
        ['case-study', 'Case Study', 'case-study', null, 1, null],
        ['services', 'Services', 'services', null, 1, null]
    ];

    foreach ($corePostTypes as $postType) {
        $postTypeSlug = $postType[0];
        $postTypeName = $postType[1];
        $coreType = $postType[2];
        $postTypeDesc = $postType[3];
        $isPostTypeCore = $postType[4];
        $postTypeParentID = $postType[5];

        // Check if the core type already exists in the database
        if (!$this->sq_coreTypeExists($coreType)) {
            // Create the post type in the database
            $this->sq_insertPostType($postTypeSlug, $postTypeName, $coreType, $postTypeDesc, $isPostTypeCore, $postTypeParentID);
        }
    }

    // Delete post types with core types that don't exist in corePostTypes
    $existingCoreTypes = array_column($corePostTypes, 2);
    $this->sq_deletePostTypesWithoutCoreType($existingCoreTypes);
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
                core_type VARCHAR(255),
                is_posttype_core INT DEFAULT 0,
                posttype_parent_id INT,
                FOREIGN KEY (posttype_parent_id) REFERENCES " . SQ_PREFIX . "post_types(post_type_id)
            )
        ";

        try {
            $this->dbh->exec($query);
        } catch (PDOException $e) {
            die("Error creating post types table: " . $e->getMessage());
        }
    }



    
/**
 * Delete post types from the database that have is_posttype_core set to 1 and their core_type is not in the list of existing core types.
 *
 * @param array $existingCoreTypes Array of core types that should exist.
 */
private function sq_deletePostTypesWithoutCoreType($existingCoreTypes) {
    $query = "DELETE FROM " . SQ_PREFIX . "post_types WHERE is_posttype_core = 1 AND core_type NOT IN (" . implode(',', array_fill(0, count($existingCoreTypes), '?')) . ")";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($existingCoreTypes);
    } catch (PDOException $e) {
        die("Error deleting post types from database: " . $e->getMessage());
    }
}


    /**
 * Delete a post type from the database based on its core type.
 *
 * @param string $coreType The core type of the post type to delete.
 */
public function sq_deletePostTypeByCoreType($coreType) {
    $query = "DELETE FROM " . SQ_PREFIX . "post_types WHERE core_type = :coreType";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([':coreType' => $coreType]);
    } catch (PDOException $e) {
        die("Error deleting post type from database: " . $e->getMessage());
    }
}


    /**
 * Check if a core type already exists in the database.
 *
 * @param string $coreType The core type to check.
 * @return bool True if core type exists, false otherwise.
 */
public function sq_coreTypeExists($coreType) {
    $query = "SELECT COUNT(*) FROM " . SQ_PREFIX . "post_types WHERE core_type = :coreType";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([':coreType' => $coreType]);
        $count = $stmt->fetchColumn();
        return ($count > 0);
    } catch (PDOException $e) {
        die("Error checking core type existence in database: " . $e->getMessage());
    }
}

    

/**
 * Insert a post type into the database.
 *
 * @param string $postTypeSlug The slug of the post type.
 * @param string $postTypeName The name of the post type.
 * @param string $coreType The core type of the post type (e.g., post, page, etc.).
 * @param string $postTypeDesc The description of the post type.
 * @param int $isPostTypeCore Whether the post type is a core type (default is 0).
 * @param int $postTypeParentID The parent post type ID (default is null).
 */
private function sq_insertPostType($postTypeSlug, $postTypeName, $coreType, $postTypeDesc = null, $isPostTypeCore = 0, $postTypeParentID = null) {
    $query = "
        INSERT INTO " . SQ_PREFIX . "post_types 
        (post_type_slug, post_type_name, core_type, post_type_desc, is_posttype_core, posttype_parent_id) 
        VALUES 
        (:postTypeSlug, :postTypeName, :coreType, :postTypeDesc, :isPostTypeCore, :postTypeParentID)
    ";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([
            ':postTypeSlug' => $postTypeSlug,
            ':postTypeName' => $postTypeName,
            ':coreType' => $coreType,
            ':postTypeDesc' => $postTypeDesc,
            ':isPostTypeCore' => $isPostTypeCore,
            ':postTypeParentID' => $postTypeParentID
        ]);
    } catch (PDOException $e) {
        die("Error inserting post type into database: " . $e->getMessage());
    }
}



    /**
     * Retrieve a list of post types from the database.
     *
     * @return array List of post types.
     */
    public function sq_getPostTypes() {
        $query = "SELECT * FROM " . SQ_PREFIX . "post_types";

        try {
            $stmt = $this->dbh->query($query);
            $postTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $postTypes;
        } catch (PDOException $e) {
            die("Error retrieving post types from database: " . $e->getMessage());
        }
    }

    /**
 * Retrieve the slug of an archive page for a specific post type.
 *
 * @param string $postType The post type (e.g., post, page, custom post type, etc.).
 * @return string|null The archive slug or null if not found.
 */
public function sq_get_archive_slug($postType) {
    $query = "SELECT post_type_slug FROM " . SQ_PREFIX . "post_types WHERE post_type_name = :postType";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([':postType' => $postType]);

        $slug = $stmt->fetchColumn();
        return $slug ? $slug : null;
    } catch (PDOException $e) {
        die("Error retrieving archive slug: " . $e->getMessage());
    }
}


    // Add more methods for post type management as needed...
}

// Assuming $dbh is your PDO database connection
$corePostType = new sq_CorePostType($dbh);

// Initialize core post types
$corePostType->sq_initCorePostTypes();


$archiveSlug = $corePostType->sq_get_archive_slug('post');


