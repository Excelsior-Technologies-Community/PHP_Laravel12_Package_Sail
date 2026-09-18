<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SailDashboardController extends Controller
{
    /**
     * Display the Sail environment dashboard.
     */
public function dashboard(): View
{
    $isSail = env('LARAVEL_SAIL') == 1;

    $isDocker = file_exists('/.dockerenv');

    return view('sail.dashboard', [
        'isSail' => $isSail,
        'isDocker' => $isDocker,
    ]);
}

    /**
     * Application health check.
     */
    public function health(): View
    {
        $checks = [];

        $checks['Application'] = [
            'status' => app()->isDownForMaintenance()
                ? 'Unhealthy'
                : 'Healthy',
            'message' => app()->isDownForMaintenance()
                ? 'Application is currently in maintenance mode.'
                : 'Laravel application is running normally.',
        ];

        try {
            DB::connection()->getPdo();

            $checks['Database'] = [
                'status' => 'Healthy',
                'message' => 'Database connection is working successfully.',
            ];
        } catch (\Throwable $exception) {
            $checks['Database'] = [
                'status' => 'Unhealthy',
                'message' => 'Database connection failed.',
            ];
        }

        try {
            $key = 'sail_health_check';

            Cache::put($key, 'ok', now()->addMinutes(1));

            $cacheWorking = Cache::get($key) === 'ok';

            $checks['Cache'] = [
                'status' => $cacheWorking ? 'Healthy' : 'Unhealthy',
                'message' => $cacheWorking
                    ? 'Cache read/write operations are working.'
                    : 'Cache read/write test failed.',
            ];

            Cache::forget($key);
        } catch (\Throwable $exception) {
            $checks['Cache'] = [
                'status' => 'Unhealthy',
                'message' => 'Cache service is not responding correctly.',
            ];
        }

        try {
            $storagePath = storage_path('app');

            $writable = File::isWritable($storagePath);

            $checks['Storage'] = [
                'status' => $writable ? 'Healthy' : 'Unhealthy',
                'message' => $writable
                    ? 'Laravel storage directory is writable.'
                    : 'Laravel storage directory is not writable.',
            ];
        } catch (\Throwable $exception) {
            $checks['Storage'] = [
                'status' => 'Unhealthy',
                'message' => 'Storage check failed.',
            ];
        }

        $overallHealthy = collect($checks)
            ->every(fn ($check) => $check['status'] === 'Healthy');

        return view('sail.health', compact(
            'checks',
            'overallHealthy'
        ));
    }

    /**
     * Display detailed system information.
     */
    public function system(): View
    {
        $system = [
            'Operating System' => PHP_OS_FAMILY,
            'OS Version' => php_uname('s'),
            'PHP Version' => PHP_VERSION,
            'PHP SAPI' => PHP_SAPI,
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'Server Name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
            'Server Port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
            'Laravel Version' => app()->version(),
            'Application Environment' => app()->environment(),
            'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'Database Driver' => config('database.default'),
            'Cache Driver' => config('cache.default'),
            'Session Driver' => config('session.driver'),
            'Queue Connection' => config('queue.default'),
            'Timezone' => config('app.timezone'),
            'Locale' => config('app.locale'),
            'Server Time' => now()->format('d M Y, h:i:s A'),
            'Docker Container' => $this->isDockerEnvironment()
                ? 'Yes'
                : 'No',
            'Sail Environment' => $this->isSailEnvironment()
                ? 'Yes'
                : 'No',
        ];

        return view('sail.system', compact('system'));
    }

    /**
     * 4. Database statistics.
     */
    public function database(): View
    {
        $tables = [];
        $databaseName = config('database.connections.' . config('database.default') . '.database');

        try {
            $tableRows = DB::select('
                SELECT
                    TABLE_NAME,
                    TABLE_ROWS,
                    DATA_LENGTH,
                    INDEX_LENGTH
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = ?
                ORDER BY TABLE_NAME
            ', [$databaseName]);

            foreach ($tableRows as $table) {
                $size = ($table->DATA_LENGTH ?? 0)
                    + ($table->INDEX_LENGTH ?? 0);

                $tables[] = [
                    'name' => $table->TABLE_NAME,
                    'rows' => (int) ($table->TABLE_ROWS ?? 0),
                    'size' => $this->formatBytes($size),
                ];
            }

            $databaseStatus = 'Connected';
        } catch (\Throwable $exception) {
            $databaseStatus = 'Connection failed';
        }

        return view('sail.tool', [
            'page' => 'Database Statistics',
            'icon' => '🗄️',
            'description' => 'Database tables, row counts and storage usage.',
            'type' => 'database',
            'tables' => $tables,
            'databaseName' => $databaseName,
            'databaseStatus' => $databaseStatus,
        ]);
    }

    /**
     * 5. Cache tester.
     */
    public function cache(): View
    {
        $result = null;

        if (request()->isMethod('post')) {
            try {
                $key = 'sail_cache_test_' . uniqid();
                $value = 'Laravel Sail Cache Test';

                Cache::put($key, $value, now()->addMinutes(5));

                $readValue = Cache::get($key);

                Cache::forget($key);

                $result = [
                    'status' => $readValue === $value
                        ? 'Success'
                        : 'Failed',
                    'message' => $readValue === $value
                        ? 'Cache write, read and delete operations completed successfully.'
                        : 'Cache read value did not match the written value.',
                ];
            } catch (\Throwable $exception) {
                $result = [
                    'status' => 'Failed',
                    'message' => 'Cache test failed.',
                ];
            }
        }

        return view('sail.tool', [
            'page' => 'Cache Tester',
            'icon' => '⚡',
            'description' => 'Test Laravel cache write, read and delete operations.',
            'type' => 'cache',
            'result' => $result,
            'driver' => config('cache.default'),
        ]);
    }

    /**
     * 6. Storage diagnostics.
     */
    public function storage(): View
    {
        $storagePath = storage_path();
        $appPath = storage_path('app');
        $logsPath = storage_path('logs');

        $diskTotal = @disk_total_space(storage_path());
        $diskFree = @disk_free_space(storage_path());

        $storage = [
            'Storage Path' => $storagePath,
            'Application Storage' => $appPath,
            'Logs Path' => $logsPath,
            'Storage Writable' => File::isWritable($storagePath)
                ? 'Yes'
                : 'No',
            'App Writable' => File::isWritable($appPath)
                ? 'Yes'
                : 'No',
            'Total Disk Space' => $diskTotal
                ? $this->formatBytes($diskTotal)
                : 'Unknown',
            'Free Disk Space' => $diskFree
                ? $this->formatBytes($diskFree)
                : 'Unknown',
            'Used Disk Space' => ($diskTotal && $diskFree)
                ? $this->formatBytes($diskTotal - $diskFree)
                : 'Unknown',
        ];

        return view('sail.tool', [
            'page' => 'Storage Diagnostics',
            'icon' => '💾',
            'description' => 'Inspect Laravel storage and disk information.',
            'type' => 'storage',
            'storage' => $storage,
        ]);
    }

    /**
     * 7. PHP extensions.
     */
    public function extensions(): View
    {
        $extensions = get_loaded_extensions();

        sort($extensions);

        return view('sail.tool', [
            'page' => 'PHP Extensions',
            'icon' => '🧩',
            'description' => 'List all PHP extensions installed in the Sail container.',
            'type' => 'extensions',
            'extensions' => $extensions,
            'count' => count($extensions),
        ]);
    }

    /**
     * 8. Route inspector.
     */
    public function routes(): View
    {
        $routes = [];

        foreach (Route::getRoutes() as $route) {
            $methods = array_diff(
                $route->methods(),
                ['HEAD']
            );

            $routes[] = [
                'methods' => implode(', ', $methods),
                'uri' => $route->uri(),
                'name' => $route->getName() ?: '-',
                'action' => $route->getActionName(),
            ];
        }

        usort($routes, function ($a, $b) {
            return strcmp($a['uri'], $b['uri']);
        });

        return view('sail.tool', [
            'page' => 'Route Inspector',
            'icon' => '🛣️',
            'description' => 'Inspect registered Laravel application routes.',
            'type' => 'routes',
            'routes' => $routes,
            'count' => count($routes),
        ]);
    }

    /**
     * 9. Laravel log viewer.
     */
    public function logs(): View
    {
        $logPath = storage_path('logs/laravel.log');
        $content = '';

        if (File::exists($logPath)) {
            $content = File::get($logPath);

            if (strlen($content) > 30000) {
                $content = substr(
                    $content,
                    -30000
                );
            }
        }

        return view('sail.tool', [
            'page' => 'Laravel Log Viewer',
            'icon' => '📜',
            'description' => 'View the latest Laravel application log entries.',
            'type' => 'logs',
            'logPath' => $logPath,
            'logExists' => File::exists($logPath),
            'logSize' => File::exists($logPath)
                ? $this->formatBytes(File::size($logPath))
                : '0 B',
            'content' => $content,
        ]);
    }

    /**
     * 10. Environment diagnostics.
     */
    public function environment(): View
    {
        $environment = [
            'APP_ENV' => [
                'value' => config('app.env'),
                'status' => !empty(config('app.env')),
            ],

            'APP_DEBUG' => [
                'value' => config('app.debug')
                    ? 'true'
                    : 'false',
                'status' => true,
            ],

            'APP_URL' => [
                'value' => config('app.url'),
                'status' => !empty(config('app.url')),
            ],

            'APP_KEY' => [
                'value' => config('app.key')
                    ? 'Configured'
                    : 'Missing',
                'status' => !empty(config('app.key')),
            ],

            'DB_CONNECTION' => [
                'value' => config('database.default'),
                'status' => !empty(config('database.default')),
            ],

            'CACHE_STORE' => [
                'value' => config('cache.default'),
                'status' => !empty(config('cache.default')),
            ],

            'SESSION_DRIVER' => [
                'value' => config('session.driver'),
                'status' => !empty(config('session.driver')),
            ],

            'QUEUE_CONNECTION' => [
                'value' => config('queue.default'),
                'status' => !empty(config('queue.default')),
            ],

            'MAIL_MAILER' => [
                'value' => config('mail.default'),
                'status' => !empty(config('mail.default')),
            ],

            'TIMEZONE' => [
                'value' => config('app.timezone'),
                'status' => !empty(config('app.timezone')),
            ],
        ];

        return view('sail.tool', [
            'page' => 'Environment Diagnostics',
            'icon' => '🔧',
            'description' => 'Check important Laravel environment configuration without exposing secrets.',
            'type' => 'environment',
            'environment' => $environment,
        ]);
    }

    /**
     * 11. Safe configuration information.
     */
    public function configuration(): View
    {
        $configuration = [
            'Application' => [
                'Name' => config('app.name'),
                'Environment' => config('app.env'),
                'Debug' => config('app.debug')
                    ? 'Enabled'
                    : 'Disabled',
                'URL' => config('app.url'),
                'Timezone' => config('app.timezone'),
                'Locale' => config('app.locale'),
            ],

            'Database' => [
                'Driver' => config('database.default'),
            ],

            'Cache' => [
                'Driver' => config('cache.default'),
            ],

            'Session' => [
                'Driver' => config('session.driver'),
            ],

            'Queue' => [
                'Connection' => config('queue.default'),
            ],

            'Mail' => [
                'Mailer' => config('mail.default'),
            ],
        ];

        return view('sail.tool', [
            'page' => 'Application Configuration',
            'icon' => '⚙️',
            'description' => 'View safe Laravel application configuration values.',
            'type' => 'configuration',
            'configuration' => $configuration,
        ]);
    }

    /**
     * 12. Server metrics.
     */
    public function metrics(): View
    {
        $memoryLimit = ini_get('memory_limit');

        $metrics = [
            'PHP Memory Limit' => $memoryLimit ?: 'Unknown',
            'Current Memory Usage' => $this->formatBytes(
                memory_get_usage(true)
            ),
            'Peak Memory Usage' => $this->formatBytes(
                memory_get_peak_usage(true)
            ),
            'PHP Maximum Execution Time' => ini_get('max_execution_time') . ' seconds',
            'PHP Upload Max Size' => ini_get('upload_max_filesize'),
            'PHP Post Max Size' => ini_get('post_max_size'),
            'PHP Max Input Vars' => ini_get('max_input_vars'),
            'Server Time' => now()->format('d M Y, h:i:s A'),
            'Unix Timestamp' => now()->timestamp,
            'Docker Container' => $this->isDockerEnvironment()
                ? 'Yes'
                : 'No',
            'Sail Environment' => $this->isSailEnvironment()
                ? 'Yes'
                : 'No',
        ];

        return view('sail.tool', [
            'page' => 'Server Metrics',
            'icon' => '📈',
            'description' => 'Inspect PHP runtime and server metrics.',
            'type' => 'metrics',
            'metrics' => $metrics,
        ]);
    }

    /**
     * Format bytes.
     */
    private function formatBytes($bytes): string
    {
        if (!$bytes || $bytes <= 0) {
            return '0 B';
        }

        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];

        $power = floor(
            log($bytes, 1024)
        );

        $power = min(
            $power,
            count($units) - 1
        );

        return number_format(
            $bytes / pow(1024, $power),
            2
        ) . ' ' . $units[$power];
    }

    /**
     * Determine whether the application is running inside Docker.
     */
    private function isDockerEnvironment(): bool
    {
        return file_exists('/.dockerenv');
    }

    /**
     * Determine whether Laravel Sail is being used.
     */
    private function isSailEnvironment(): bool
    {
        return env('LARAVEL_SAIL', false) === true
            || env('LARAVEL_SAIL', false) === 'true'
            || env('LARAVEL_SAIL', false) === '1';
    }
}

