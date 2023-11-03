<?php 
/**
 * Class sq_Categories
 *
 * This class handles Categories in SqueHub CMS.
 *
 * @package SqueHub
 * @subpackage sq_Categories
 */
class sq_Categories {
    /**
     * @var PDO $dbh Database connection object.
     */
    private $dbh;

    /**
     * Constructor initializes sq_Categories with a database connection.
     *
     * @param PDO $dbh Database connection object.
     */
    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

    /**
     * Initialize core categories.
     */
    public function sq_initCoreCategories() {
        // Create the categories table if it doesn't exist
        $this->sq_createCategoriesTable();
    
        $coreCategories = [
            ['uncategorized', 'Uncategorized', 'uncategorized', null, null, 1], // Set the value to 1 for "Uncategorized"
            ['featured', 'Featured', 'featured', null, null],
            // Add more core categories as needed
        ];
    
        foreach ($coreCategories as $category) {
            $categorySlug = $category[0];
            $categoryName = $category[1];
            $categoryDesc = $category[2];
            $parentCategoryID = $category[3];
            $isdefaultCategory = $category[4];
    
            // Check if the category already exists in the database
            if (!$this->sq_categoryExists($categorySlug)) {
                // Create the category in the database
                $this->sq_insertCategory($categorySlug, $categoryName, $categoryDesc, $parentCategoryID, $isdefaultCategory);
            }
        }
    }
    

    /**
     * Create the categories table if it doesn't exist.
     */
    private function sq_createCategoriesTable() {
        $query = "
                CREATE TABLE IF NOT EXISTS " . SQ_PREFIX . "categories (
                    category_id INT AUTO_INCREMENT PRIMARY KEY,
                    category_name VARCHAR(255) NOT NULL,
                    category_slug VARCHAR(255) NOT NULL,
                    category_desc TEXT,
                    parent_category_id INT,
                    is_default_category INT DEFAULT 0,  -- Added is_default_category field
                    FOREIGN KEY (parent_category_id) REFERENCES " . SQ_PREFIX . "categories(category_id)
                )
        ";

        try {
            $this->dbh->exec($query);
        } catch (PDOException $e) {
            die("Error creating categories table: " . $e->getMessage());
        }
    }

    /**
     * Insert a category into the database.
     *
     * @param string $categorySlug The slug of the category.
     * @param string $categoryName The name of the category.
     * @param string $categoryType The type of the category (e.g., uncategorized, featured, etc.).
     * @param string $categoryDesc The description of the category.
     * @param int|null $parentCategoryID The ID of the parent category (null if none).
     * @param int $isdefaultCategory
     */
    private function sq_insertCategory($categorySlug, $categoryName, $isdefaultCategory,  $categoryDesc = null, $parentCategoryID = null) {
        $isDefaultCategory = ($categoryName === 'Uncategorized') ? 1 : 0; // Set is_default_category based on category name
    
        $query = "
            INSERT INTO " . SQ_PREFIX . "categories 
            (category_slug, category_name, category_desc, parent_category_id, is_default_category) 
            VALUES 
            (:categorySlug, :categoryName, :categoryDesc, :parentCategoryID, :isDefaultCategory)
        ";
    
        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([
                ':categorySlug' => $categorySlug,
                ':categoryName' => $categoryName,
                ':categoryDesc' => $categoryDesc,
                ':parentCategoryID' => $parentCategoryID,
                ':isDefaultCategory' => $isDefaultCategory,
            ]);
        } catch (PDOException $e) {
            die("Error inserting category into database: " . $e->getMessage());
        }
    }
    


    /**
     * Check if a category already exists in the database.
     *
     * @param string $categorySlug The slug of the category.
     * @return bool True if category exists, false otherwise.
     */
    public function sq_categoryExists($categorySlug) {
        $query = "SELECT COUNT(*) FROM " . SQ_PREFIX . "categories WHERE category_slug = :categorySlug";

        try {
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([':categorySlug' => $categorySlug]);
            $count = $stmt->fetchColumn();
            return ($count > 0);
        } catch (PDOException $e) {
            die("Error checking category existence in database: " . $e->getMessage());
        }
    }

    // Add more methods for category management as needed...
}

// Assuming $dbh is your PDO database connection
$coreCategories = new sq_Categories($dbh);

// Initialize core categories
$coreCategories->sq_initCoreCategories();