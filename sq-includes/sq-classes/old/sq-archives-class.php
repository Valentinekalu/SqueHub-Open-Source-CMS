<?php
/**
 * Class sq_Archives
 *
 * This class handles Archives in SqueHub CMS.
 *
 * @package SqueHub
 * @subpackage sq_Archives
 */
class sq_Archives {
    /**
     * @var PDO $dbh Database connection object.
     */
    private $dbh;

    /**
     * Constructor initializes sq_Archives with a database connection.
     *
     * @param PDO $dbh Database connection object.
     */
    public function __construct($dbh) {
        $this->dbh = $dbh;
    }

/**
 * Initialize core archives (year, month, day).
 */
public function sq_initCoreArchives() {
    // Create the archives table if it doesn't exist
    $this->sq_createArchivesTable();

    $coreArchives = [
        ['post', 'Post Archive', 'post', null, 1, null],
        ['category', 'Category Archive', 'category', null, 1, null],
        ['tag', 'Tag Archive', 'tag', null, 1, null],
        ['products', 'Products Archive', 'products', null, 1, null]
    ];

    foreach ($coreArchives as $archive) {
        $archiveSlug = $archive[0];
        $archiveName = $archive[1];
        $coreType = $archive[2];
        $archiveDesc = $archive[3];
        $isArchiveCore = $archive[4];
        $archiveParentID = $archive[5];

        // Check if the core archive already exists in the database
        if (!$this->sq_coreArchiveExists($coreType)) {
            // Create the archive in the database
            $this->sq_insertArchive($archiveSlug, $archiveName, $coreType, $archiveDesc, $isArchiveCore, $archiveParentID);
        }
    }

        // Delete post types with core types that don't exist in corePostTypes
        $existingCoreTypes = array_column($coreArchives, 2);
        $this->sq_deleteArchivesWithoutCoreType($existingCoreTypes);
}

/**
 * Create the archives table if it doesn't exist.
 */
private function sq_createArchivesTable() {
    $query = "
        CREATE TABLE IF NOT EXISTS " . SQ_PREFIX . "archives (
            archive_id INT AUTO_INCREMENT PRIMARY KEY,
            archive_slug VARCHAR(255) NOT NULL,
            archive_name VARCHAR(255) NOT NULL,
            archive_desc TEXT,
            core_type VARCHAR(255),
            is_archive_core INT DEFAULT 0,
            archive_parent_id INT,
            FOREIGN KEY (archive_parent_id) REFERENCES " . SQ_PREFIX . "archives(archive_id)
        )
    ";

    try {
        $this->dbh->exec($query);
    } catch (PDOException $e) {
        die("Error creating archives table: " . $e->getMessage());
    }
}

/**
 * Delete archives from the database that have is_archive_core set to 1 and their core_type is not in the list of existing core archives.
 *
 * @param array $existingCoreArchives Array of core archives that should exist.
 */
private function sq_deleteArchivesWithoutCoreType($existingCoreArchives) {
    $query = "DELETE FROM " . SQ_PREFIX . "archives WHERE is_archive_core = 1 AND core_type NOT IN (" . implode(',', array_fill(0, count($existingCoreArchives), '?')) . ")";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($existingCoreArchives);
    } catch (PDOException $e) {
        die("Error deleting archives from database: " . $e->getMessage());
    }
}

/**
 * Delete an archive from the database based on its core type.
 *
 * @param string $coreType The core type of the archive to delete.
 */
public function sq_deleteArchiveByCoreType($coreType) {
    $query = "DELETE FROM " . SQ_PREFIX . "archives WHERE core_type = :coreType";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([':coreType' => $coreType]);
    } catch (PDOException $e) {
        die("Error deleting archive from database: " . $e->getMessage());
    }
}

/**
 * Check if a core archive already exists in the database.
 *
 * @param string $coreType The core type to check.
 * @return bool True if core archive exists, false otherwise.
 */
public function sq_coreArchiveExists($coreType) {
    $query = "SELECT COUNT(*) FROM " . SQ_PREFIX . "archives WHERE core_type = :coreType";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([':coreType' => $coreType]);
        $count = $stmt->fetchColumn();
        return ($count > 0);
    } catch (PDOException $e) {
        die("Error checking core archive existence in database: " . $e->getMessage());
    }
}

/**
 * Insert an archive into the database.
 *
 * @param string $archiveSlug The slug of the archive.
 * @param string $archiveName The name of the archive.
 * @param string $coreType The core type of the archive (e.g., year, month, etc.).
 * @param string $archiveDesc The description of the archive.
 * @param int $isArchiveCore Whether the archive is a core archive (default is 0).
 * @param int $archiveParentID The parent archive ID (default is null).
 */
private function sq_insertArchive($archiveSlug, $archiveName, $coreType, $archiveDesc = null, $isArchiveCore = 0, $archiveParentID = null) {
    $query = "
        INSERT INTO " . SQ_PREFIX . "archives 
        (archive_slug, archive_name, core_type, archive_desc, is_archive_core, archive_parent_id) 
        VALUES 
        (:archiveSlug, :archiveName, :coreType, :archiveDesc, :isArchiveCore, :archiveParentID)
    ";

    try {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute([
            ':archiveSlug' => $archiveSlug,
            ':archiveName' => $archiveName,
            ':coreType' => $coreType,
            ':archiveDesc' => $archiveDesc,
            ':isArchiveCore' => $isArchiveCore,
            ':archiveParentID' => $archiveParentID
        ]);
    } catch (PDOException $e) {
        die("Error inserting archive into database: " . $e->getMessage());
    }
}

/**
 * Retrieve a list of archives from the database.
 *
 * @return array List of archives.
 */
public function sq_getArchives() {
    $query = "SELECT * FROM " . SQ_PREFIX . "archives";

    try {
        $stmt = $this->dbh->query($query);
        $archives = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $archives;
    } catch (PDOException $e) {
        die("Error retrieving archives from database: " . $e->getMessage());
    }
}



    // Add more methods for post type management as needed...
}

// Assuming $dbh is your PDO database connection
$corePostType = new sq_Archives($dbh);

// Initialize core post types
$corePostType->sq_initCoreArchives();




