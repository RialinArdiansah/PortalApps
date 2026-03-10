<?php
/**
 * Database setup script — creates database, runs schema, and seeds data.
 * Run from project root:  php database/setup.php
 */

echo "=== Portal Sertifikasi — Database Setup ===\n\n";

// Load config
require_once __DIR__ . '/../config.php';

try {
    // Connect without database first to create it
    $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // 1. Create database
    echo "1. Creating database '" . DB_NAME . "'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "   ✓ Database created.\n\n";

    // Use database
    $pdo->exec("USE `" . DB_NAME . "`");

    // 2. Run schema
    echo "2. Running schema.sql...\n";
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schema);
    echo "   ✓ Schema imported.\n\n";

    // 3. Import seed data (if seed.sql exists)
    $seedFile = __DIR__ . '/seed.sql';
    $fresh = in_array('--fresh', $argv ?? []);

    if (file_exists($seedFile)) {
        if ($fresh) {
            echo "3. Fresh mode: truncating all tables...\n";
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $pdo->exec("TRUNCATE TABLE `$table`");
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            echo "   ✓ All tables truncated.\n\n";
            echo "4. Importing seed data from seed.sql...\n";
        } else {
            echo "3. Importing seed data from seed.sql...\n";
            echo "   (Use --fresh to truncate tables first)\n";
        }

        $seedSql = file_get_contents($seedFile);

        // Split by semicolons and execute each statement
        $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $seedSql)));
        $insertCount = 0;
        foreach ($statements as $stmt) {
            if (stripos($stmt, 'INSERT') === 0 || stripos($stmt, 'SET') === 0) {
                $pdo->exec($stmt);
                if (stripos($stmt, 'INSERT') === 0) $insertCount++;
            }
        }
        echo "   ✓ $insertCount rows inserted.\n\n";
    } else {
        echo "3. No seed.sql found — skipping seed data.\n";
        echo "   (Database will be empty. Add data manually.)\n\n";
    }

    echo "=== Setup complete! ===\n";
    echo "You can now access the application.\n";
    echo "Demo accounts (if seeded):\n";
    echo "  Super Admin: superadmin / superadmin123\n";
    echo "  Admin:       admin / admin123\n";
    echo "  Manager:     manager / manager123\n";
    echo "  Karyawan:    karyawan / karyawan123\n";
    echo "  Marketing:   marketing / marketing123\n";
    echo "  Mitra:       mitra / mitra123\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
