<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    private function checkAdmin(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized access to system backups.');
        }
        $roles = method_exists($user, 'roles') ? $user->roles->pluck('name')->map('strtolower')->toArray() : [];
        $role = strtolower($user->role ?? '');
        $userType = strtolower($user->user_type ?? '');
        $isSuper = !empty($user->is_supreme_admin) || !empty($user->is_super_admin);

        $hasAccess = $isSuper
            || in_array('admin', $roles) || in_array('super_admin', $roles)
            || in_array($role, ['admin', 'super_admin', 'super admin'])
            || in_array($userType, ['admin', 'super_admin', 'super admin']);

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to system backups.');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $backups = Backup::with('user')->latest()->get();
        $totalBackups = $backups->count();
        $totalSize = $backups->sum('file_size');

        return view('content.apps.system.backups', compact('backups', 'totalBackups', 'totalSize'));
    }

    /**
     * Create a real database dump or snapshot with SHA-256 verification
     */
    public function createSnapshot(Request $request)
    {
        $this->checkAdmin();
        $type = $request->type ?: 'database';
        $timestamp = now()->format('Y_m_d_His');
        $fileName = 'backup_' . $type . '_' . $timestamp . '.sql';

        $connection = config('database.default');
        $sqlDump = "-- ==========================================================\n";
        $sqlDump .= "-- AK-Mart Production Database Snapshot\n";
        $sqlDump .= "-- Generated: " . now()->toIso8601String() . "\n";
        $sqlDump .= "-- Connection: " . $connection . "\n";
        $sqlDump .= "-- ==========================================================\n\n";
        $sqlDump .= "PRAGMA foreign_keys = OFF;\n\n";

        try {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        } catch (\Throwable $e) {
            // Fallback for native SQLite table listing
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';");
            $tables = array_map(fn($t) => $t->name ?? reset($t), $tables);
        }

        foreach ($tables as $table) {
            if (in_array($table, ['sqlite_sequence', 'sqlite_stat1'])) {
                continue;
            }

            $sqlDump .= "-- Table structure for: `{$table}`\n";
            $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";

            try {
                $createRow = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (!empty($createRow) && isset($createRow[0]->sql)) {
                    $sqlDump .= $createRow[0]->sql . ";\n\n";
                }
            } catch (\Throwable $e) {
                // Ignore if schema retrieval varies by driver
            }

            // Dump data in chunks
            $sqlDump .= "-- Dumping data for: `{$table}`\n";
            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $columns = array_keys($rowArray);
                $escapedValues = array_map(function ($val) {
                    if (is_null($val)) return 'NULL';
                    if (is_numeric($val)) return $val;
                    return "'" . str_replace("'", "''", (string)$val) . "'";
                }, array_values($rowArray));

                $sqlDump .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
            }
            $sqlDump .= "\n";
        }

        $sqlDump .= "PRAGMA foreign_keys = ON;\n";

        // Save real SQL dump file to local disk
        Storage::disk('local')->put('backups/' . $fileName, $sqlDump);
        $size = Storage::disk('local')->size('backups/' . $fileName);
        $checksum = hash('sha256', $sqlDump);

        $backup = Backup::create([
            'file_name' => $fileName,
            'file_size' => $size,
            'type'      => $type,
            'status'    => 'completed',
            'checksum'  => $checksum,
            'user_id'   => auth()->id(),
        ]);

        return redirect()->route('app-backups')->with('success', "Production database snapshot #{$backup->id} ({$fileName}) created successfully! (Size: " . round($size / 1024, 2) . " KB)");
    }

    /**
     * Download backup snapshot file
     */
    public function download($id)
    {
        $this->checkAdmin();
        $backup = Backup::findOrFail($id);
        $path = 'backups/' . $backup->file_name;

        if (!Storage::disk('local')->exists($path)) {
            return redirect()->route('app-backups')->with('error', "Backup file {$backup->file_name} not found on server.");
        }

        return Storage::disk('local')->download($path, $backup->file_name);
    }

    /**
     * Delete backup snapshot from disk and database
     */
    public function destroy($id)
    {
        $this->checkAdmin();
        $backup = Backup::findOrFail($id);
        $path = 'backups/' . $backup->file_name;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        $backup->delete();

        return redirect()->route('app-backups')->with('success', "Backup {$backup->file_name} deleted successfully.");
    }
}
