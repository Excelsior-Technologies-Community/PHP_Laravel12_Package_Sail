<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SailDashboardController extends Controller
{
    /**
     * Display the Sail environment dashboard.
     */
    public function dashboard(): View
    {
        $environment = [
            'Laravel Version' => app()->version(),
            'PHP Version' => PHP_VERSION,
            'Environment' => app()->environment(),
            'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'Database Driver' => config('database.default'),
            'Application URL' => config('app.url'),
            'Timezone' => config('app.timezone'),
            'Locale' => config('app.locale'),
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'Docker Container' => $this->isDockerEnvironment(),
            'Sail Environment' => $this->isSailEnvironment(),
        ];

        return view('sail.dashboard', compact('environment'));
    }

    /**
     * Display application and service health status.
     */
    public function health(): View
    {
        $checks = [];

        // Application check
        $checks['Application'] = [
            'status' => app()->isDownForMaintenance() ? 'Unhealthy' : 'Healthy',
            'message' => app()->isDownForMaintenance()
                ? 'Application is currently in maintenance mode.'
                : 'Laravel application is running normally.',
        ];

        // Database check
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

        // Cache check
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

        // Storage check
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

        // Overall status
        $overallHealthy = collect($checks)
            ->every(fn ($check) => $check['status'] === 'Healthy');

        return view('sail.health', compact('checks', 'overallHealthy'));
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
            'Docker Container' => $this->isDockerEnvironment() ? 'Yes' : 'No',
            'Sail Environment' => $this->isSailEnvironment() ? 'Yes' : 'No',
        ];

        return view('sail.system', compact('system'));
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