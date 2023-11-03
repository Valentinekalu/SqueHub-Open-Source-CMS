<?php 
/**
 * Class sq_Tags
 *
 * This class handles Tags in SqueHub CMS.
 *
 * @package SqueHub
 * @subpackage sq_Tags
 */
class sq_Tags {
    /**
     * @var PDO $dbh Database connection object.
     */
    private $dbh;

    /**
     * Constructor initializes sq_Tags with a database connection.
     *
     * @param PDO $dbh Database connection object.
     */
    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

    /**
     * Initialize core tags.
     */
    public function sq_initCoreTags() {
        // Create the tags table if it doesn't exist
        $this->sq_createTagsTable();

        $coreTags = [
            ['important', 'Important', 'important', null], // Added null for parent tag
            ['featured', 'Featured', 'featured', null], // Added null for parent tag
            // Add more core tags as needed
        ];

        foreach ($coreTags as $tag) {
            $tagSlug = $tag[0];
            $tagName = $tag[1];
            $tagType = $tag[2];
            $parentTagID = $tag[3]; // Added parent tag ID

            // Check if the tag already exists in the database
            if (!$this->sq_tagExists($tagSlug)) {
                // Create the tag in the database
                $this->sq_insertTag($tagSlug, $tagName, $tagType, $parentTagID);
            }
        }
    }

    /**
     * Create the tags table if it doesn't exist.
     */
    private function sq_createTagsTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS " . SQ_PREFIX . "tags (
                tag_id INT AUTO_INCREMENT PRIMARY KEY,
                tag_name VARCHAR(255) NOT NULL,
                tag_slug VARCHAR(255) NOT NULL,
                tag_type VARCHAR(255) NOT NULL,
                parent_tag_id INT, -- Added parent tag ID field
                FOREIGN KEY (parent_tag_id) REFERENCES " . SQ_PREFIX . "tags(tag_id)
            )
        ";

        try {
            $this->dbh->exec($query);
        } catch (PDOException $e) {
            die("Error creating tags table: " . $e->getMessage());
        }
    }

    /**
     * Insert a tag into the database.
     *
     * @param string $tagSlug The slug of the tag.
     * @param string $tagName The name of the tag.
     * @param string $tagType The type of the tag (e.g., important, featured, etc.).
     * @param int|null $parentTagID The ID of the parent tag (null if none).
     */
    private function sq_insertTag($tagSlug, $tagName, $tagType, $parentTagID = null) {
        $query = "
            INSERT INTO " . SQ_PREFIX . "tags 
            (tag_slug, tag_name, tag_type, parent_tag_id) 
            VALUES 
            (:tagSlug, :tagName, :tagType, :parentTagID)
        ";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                ':tagSlug' => $tagSlug,
                ':tagName' => $tagName,
                ':tagType' => $tagType,
                ':parentTagID' => $parentTagID,
            ]);
        } catch (PDOException $e) {
            die("Error inserting tag into database: " . $e->getMessage());
        }
    }

    /**
     * Check if a tag already exists in the database.
     *
     * @param string $tagSlug The slug of the tag.
     * @return bool True if tag exists, false otherwise.
     */
    public function sq_tagExists($tagSlug) {
        $query = "SELECT COUNT(*) FROM " . SQ_PREFIX . "tags WHERE tag_slug = :tagSlug";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([':tagSlug' => $tagSlug]);
            $count = $stmt->fetchColumn();
            return ($count > 0);
        } catch (PDOException $e) {
            die("Error checking tag existence in database: " . $e->getMessage());
        }
    }

    // Add more methods for tag management as needed...
}

// Assuming $dbh is your PDO database connection
$coreTags = new sq_Tags($dbh);

// Initialize core tags
$coreTags->sq_initCoreTags();
