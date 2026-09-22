<?php

namespace App\Http\Controllers;

use App\Services\SailDatabaseBackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SailBackupController extends Controller
{
    protected SailDatabaseBackupService $backupService;

    public function __construct(SailDatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display database backups management interface
     */
    public function index(): View
    {
        $backups = $this->backupService->getBackupsList();
        return view('sail.backups', compact('backups'));
    }

    /**
     * Generate 1-Click database dump backup
     */
    public function create(Request $request)
    {
        $compress = (bool) $request->input('compress', false);
        $result = $this->backupService->createBackup($compress);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Restore database from selected backup file
     */
    public function restore(Request $request)
    {
        $filename = $request->input('filename');
        $result = $this->backupService->restoreBackup($filename);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Download backup file
     */
    public function download(string $filename)
    {
        $backups = $this->backupService->getBackupsList();
        $target = collect($backups)->firstWhere('filename', $filename);

        if (!$target) {
            abort(404, "Backup file not found.");
        }

        return response()->download($target['full_path']);
    }

    /**
     * Delete backup file
     */
    public function delete(string $filename, Request $request)
    {
        $deleted = $this->backupService->deleteBackup($filename);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $deleted ? 'success' : 'error',
                'message' => $deleted ? "Backup file deleted successfully." : "Failed to delete backup file.",
            ]);
        }

        return redirect()->back()->with('success', 'Backup deleted successfully.');
    }
}
