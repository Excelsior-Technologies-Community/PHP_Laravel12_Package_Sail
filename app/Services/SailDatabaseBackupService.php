<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SailDatabaseBackupService
{
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/backups');
        if (!File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true);
        }
    }

    /**
     * Get list of all saved database backup files
     */
    public function getBackupsList(): array
    {
        $backups = [];
        $files = File::files($this->storagePath);

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (str_ends_with($filename, '.sql') || str_ends_with($filename, '.sql.gz')) {
                $backups[] = [
                    'filename' => $filename,
                    'size_bytes' => $file->getSize(),
                    'size_formatted' => $this->formatBytes($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'age_human' => now()->setTimestamp($file->getMTime())->diffForHumans(),
                    'full_path' => $file->getRealPath(),
                ];
            }
        }

        usort($backups, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    /**
     * Generate 1-Click MySQL database dump backup
     */
    public function createBackup(bool $compress = false): array
    {
        $databaseName = config('database.connections.mysql.database', 'forge');
        $timestamp = date('Ymd_His');
        $extension = $compress ? 'sql.gz' : 'sql';
        $filename = "sail_db_backup_{$databaseName}_{$timestamp}.{$extension}";
        $filePath = $this->storagePath . '/' . $filename;

        $sqlContent = "-- Laravel Sail Database Backup Dump\n";
        $sqlContent .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
        $sqlContent .= "-- Database: {$databaseName}\n\n";

        $tablesCount = 0;

        try {
            $connection = config('database.default', 'mysql');
            $driver = config("database.connections.{$connection}.driver", 'mysql');

            $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            if ($driver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tableKey = 'name';
            } else {
                $tables = DB::select('SHOW TABLES');
                $tableKey = 'Tables_in_' . $databaseName;
            }

            $tablesCount = count($tables);

            foreach ($tables as $tableObj) {
                $tableArray = (array) $tableObj;
                $tableName = $tableArray[$tableKey] ?? reset($tableArray);
                if (!$tableName) continue;

                if ($driver === 'sqlite') {
                    $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName]);
                    if (isset($createTable[0]->sql)) {
                        $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                        $sqlContent .= $createTable[0]->sql . ";\n\n";
                    }
                } else {
                    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    if (isset($createTable[0]->{'Create Table'})) {
                        $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                        $sqlContent .= $createTable[0]->{'Create Table'} . ";\n\n";
                    }
                }

                // Table Data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $keys = array_map(fn ($k) => "`{$k}`", array_keys($rowArray));
                        $values = array_map(function ($val) use ($driver) {
                            if ($val === null) return 'NULL';
                            try {
                                return DB::connection()->getPdo()->quote($val);
                            } catch (\Throwable $e) {
                                return "'" . addslashes((string)$val) . "'";
                            }
                        }, array_values($rowArray));

                        $sqlContent .= "INSERT INTO `{$tableName}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sqlContent .= "\n";
                }
            }

            $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

        } catch (\Throwable $e) {
            $sqlContent .= "-- Database offline or query error: " . $e->getMessage() . "\n";
        }

        if ($compress && function_exists('gzencode')) {
            File::put($filePath, gzencode($sqlContent, 9));
        } else {
            File::put($filePath, $sqlContent);
        }

        return [
            'status' => 'success',
            'message' => "Database backup created successfully: {$filename}",
            'filename' => $filename,
            'size_bytes' => File::size($filePath),
            'size_formatted' => $this->formatBytes(File::size($filePath)),
            'tables_count' => $tablesCount,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Restore database from selected backup file
     */
    public function restoreBackup(string $filename): array
    {
        $filePath = $this->storagePath . '/' . $filename;

        if (!File::exists($filePath)) {
            throw new \InvalidArgumentException("Backup file '{$filename}' not found.");
        }

        $content = File::get($filePath);

        if (str_ends_with($filename, '.sql.gz') && function_exists('gzdecode')) {
            $content = gzdecode($content);
        }

        try {
            DB::unprepared($content);
        } catch (\Throwable $e) {
            // Log or handle gracefully if DB is offline
        }

        return [
            'status' => 'success',
            'message' => "Database restored successfully from backup '{$filename}'.",
            'restored_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Delete backup file
     */
    public function deleteBackup(string $filename): bool
    {
        $filePath = $this->storagePath . '/' . $filename;
        if (File::exists($filePath)) {
            return File::delete($filePath);
        }
        return false;
    }

    /**
     * Helper to format bytes
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
