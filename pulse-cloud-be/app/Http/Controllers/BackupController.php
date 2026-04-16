<?php

namespace App\Http\Controllers;

use App\Jobs\BackupDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Generate and download database backup
     */
    public function downloadBackup(): JsonResponse
    {
        try {
            // Dispatch the backup job synchronously
            BackupDatabase::dispatch()->onConnection('sync');
            
            return $this->sendResponse('Database backup completed and uploaded to Google Drive successfully.');
            
        } catch (\Exception $e) {
            return $this->sendError(
                'Database backup failed',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get backup information without downloading
     */
    public function getBackupInfo(): JsonResponse
    {
        try {
            $database = config('database.connections.mysql.database');
            
            // Get database size
            $tables = DB::select('SHOW TABLE STATUS');
            $totalSize = array_sum(array_map(function($table) {
                return $table->Data_length + $table->Index_length;
            }, $tables));
            
            // Format size
            $sizeFormatted = $this->formatBytes($totalSize);
            
            // Get table count
            $tableCount = count($tables);
            
            return $this->sendResponse('Backup information retrieved successfully', [
                'database_name' => $database,
                'total_tables' => $tableCount,
                'estimated_size' => $sizeFormatted,
                'estimated_size_bytes' => $totalSize,
                'backup_timestamp' => Carbon::now()->toDateTimeString(),
                'tables' => array_map(function($table) {
                    return [
                        'name' => $table->Name,
                        'rows' => $table->Rows,
                        'size' => $this->formatBytes($table->Data_length + $table->Index_length)
                    ];
                }, $tables)
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError(
                'Failed to get backup information', 
                ['error' => $e->getMessage()], 
                500
            );
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2): string
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}