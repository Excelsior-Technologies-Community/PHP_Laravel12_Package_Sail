<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SailBackupAndHealthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure backups directory exists
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
    }

    public function test_container_health_page_is_accessible()
    {
        $response = $this->get('/sail/container-health');

        $response->assertStatus(200);
        $response->assertSee('Sail Container Health');
    }

    public function test_container_telemetry_json_endpoint()
    {
        $response = $this->get('/sail/container-health/json');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'containers',
                'telemetry' => [
                    'db_ping_ms',
                    'db_size_mb',
                    'memory_peak_mb',
                ],
                'environment' => [
                    'php_version',
                    'laravel_version',
                ],
            ],
        ]);
    }

    public function test_backups_page_is_accessible()
    {
        $response = $this->get('/sail/backups');

        $response->assertStatus(200);
        $response->assertSee('Sail Volume Database Dump');
    }

    public function test_create_database_backup()
    {
        $response = $this->from('/sail/backups')->post('/sail/backups/create', [
            'compress' => 0
        ]);

        $response->assertRedirect('/sail/backups');
        $response->assertSessionHas('success');

        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        $this->assertNotEmpty($files);
    }

    public function test_restore_database_backup()
    {
        // First create a backup to restore
        $this->from('/sail/backups')->post('/sail/backups/create', ['compress' => 0]);
        
        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        $this->assertNotEmpty($files);
        $latestFile = end($files);
        $filename = $latestFile->getFilename();

        $response = $this->from('/sail/backups')->post('/sail/backups/restore', [
            'filename' => $filename
        ]);

        $response->assertRedirect('/sail/backups');
        $response->assertSessionHas('success');
    }
}
