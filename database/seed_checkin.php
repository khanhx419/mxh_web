<?php
define('BASE_PATH', 'c:/xampp/htdocs/mxh_web');
require BASE_PATH . '/app/Helpers/helpers.php';
require BASE_PATH . '/config/database.php';
$db = getDatabaseConnection();
$db->exec("INSERT IGNORE INTO settings (name, value) VALUES ('checkin_spins_per_day','1'), ('checkin_bonus_day7','3'), ('checkin_green_points','5')");
echo "Settings seeded OK\n";
