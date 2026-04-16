<?php

namespace App\Jobs;

use App\Services\GoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupDatabase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleDriveService $drive): void
    {
        try {
            // Get database configuration
            $database = config('database.connections.mysql.database');

            if (empty($database)) {
                Log::error('Database configuration is invalid - Database name is not configured');
                return;
            }

            // Generate backup filename with timestamp
            $timestamp = Carbon::now()->format('Y-m-d');
            $filename = "medrec-{$timestamp}.sql";
            
            // Create backups directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $filePath = $backupPath . DIRECTORY_SEPARATOR . $filename;

            // Generate SQL backup using Laravel's database functionality
            $sqlContent = $this->generateSqlBackup();
            
            // Write SQL content to file
            file_put_contents($filePath, $sqlContent);

            // Check if file was created and has content
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                Log::error('Backup file was not created or is empty');
                return;
            }

            // Clean filename for headers
            $safeFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

            // Upload file to Google Drive
            try {
                $drive->upload($filePath, $safeFilename);
                Log::info("Backup uploaded to Google Drive successfully: {$safeFilename}");
                
                // Delete local file after successful upload
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to upload backup to Google Drive: ' . $e->getMessage());
                Log::info("Backup saved locally at: {$filePath}");
            }

        } catch (\Exception $e) {
            Log::error('Database backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate SQL backup using Laravel's database functionality
     */
    private function generateSqlBackup(): string
    {
        $database = config('database.connections.mysql.database');
        $sql = '';
        
        // Add header comment
        $sql .= "-- Database Backup\n";
        $sql .= "-- Generated on: " . Carbon::now()->toDateTimeString() . "\n";
        $sql .= "-- Database: {$database}\n";
        $sql .= "-- Laravel Database Backup\n\n";
        
        // Disable foreign key checks
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $database;
        
        foreach ($tables as $table) {
            $tableName = $table->{$tableKey};
            
            // Get table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "-- Table structure for table `{$tableName}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $data = DB::table($tableName)->get();
            
            if ($data->count() > 0) {
                $sql .= "-- Dumping data for table `{$tableName}`\n";
                
                // Build INSERT statements in chunks
                $chunks = $data->chunk(100);
                foreach ($chunks as $chunk) {
                    $values = [];
                    foreach ($chunk as $row) {
                        $rowData = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $rowData[] = 'NULL';
                            } else {
                                $rowData[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $values[] = '(' . implode(',', $rowData) . ')';
                    }
                    
                    if (!empty($values)) {
                        $columns = '`' . implode('`,`', array_keys((array)$chunk->first())) . '`';
                        $sql .= "INSERT INTO `{$tableName}` ({$columns}) VALUES\n";
                        $sql .= implode(",\n", $values) . ";\n";
                    }
                }
                $sql .= "\n";
            }
        }
        
        // Re-enable foreign key checks
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        return $sql;
    }
}
