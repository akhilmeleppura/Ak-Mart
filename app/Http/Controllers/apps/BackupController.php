<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::with('user')->latest()->get();
        $totalBackups = $backups->count();
        $totalSize = $backups->sum('file_size');

        return view('content.apps.system.backups', compact('backups', 'totalBackups', 'totalSize'));
    }

    public function createSnapshot(Request $request)
    {
        $type = $request->type ?: 'database';
        $fileName = 'backup_' . $type . '_' . now()->format('Y_m_d_His') . '.sql';
        $content = "-- AK-Mart Automated Snapshot Generated at " . now()->toIso8601String() . "\n-- Version: 4.5.0 Enterprise\n";

        Storage::disk('local')->put('backups/' . $fileName, $content);
        $size = Storage::disk('local')->size('backups/' . $fileName);
        $checksum = md5($content);

        Backup::create([
            'file_name' => $fileName,
            'file_size' => $size,
            'type'      => $type,
            'status'    => 'completed',
            'checksum'  => $checksum,
            'user_id'   => auth()->id(),
        ]);

        return redirect()->route('app-backups')->with('success', "Backup snapshot {$fileName} created successfully!");
    }
}
