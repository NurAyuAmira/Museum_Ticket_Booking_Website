<?php
session_start();
require_once 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database backup
$backup_file = 'backup_' . date('Ymd_His') . '.sql';
$tables = [];

// Get all table names from the database
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Iterate over each table and export its structure and data
$output = "";
foreach ($tables as $table) {
    // Get table structure
    $result = $conn->query("SHOW CREATE TABLE $table");
    $row = $result->fetch_row();
    $output .= "\n\n" . $row[1] . ";\n\n";

    // Get table data
    $result = $conn->query("SELECT * FROM $table");
    $column_count = $result->field_count;

    for ($i = 0; $i < $column_count; $i++) {
        while ($row = $result->fetch_row()) {
            $output .= "INSERT INTO $table VALUES(";
            for ($j = 0; $j < $column_count; $j++) {
                $row[$j] = $row[$j] ? addslashes($row[$j]) : 'NULL';
                $output .= '"' . $row[$j] . '"';
                if ($j < ($column_count - 1)) {
                    $output .= ', ';
                }
            }
            $output .= ");\n";
        }
    }
    $output .= "\n\n\n";
}

// Save the output to a file
$file_handle = fopen($backup_file, 'w+');
fwrite($file_handle, $output);
fclose($file_handle);

// Zip the entire project directory
$rootPath = realpath(__DIR__);
$zip_file = 'system_backup_' . date('Ymd_His') . '.zip';
$zip = new ZipArchive();
$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();

echo "Backup successful! The backup file is named: " . $zip_file;
?>
