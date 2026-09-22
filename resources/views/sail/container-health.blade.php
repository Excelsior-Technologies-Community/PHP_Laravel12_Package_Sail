<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Docker Container Health & Telemetry Dashboard - Sail Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans" x-data="containerHealthApp()">
    
    <!-- Top Nav Header -->
    <header class="bg-slate-900/80 backdrop-blur border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center font-bold text-xl shadow-lg shadow-cyan-500/20">
                    🐳
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-300 bg-clip-text text-transparent">
                        Sail Container Health & Telemetry Dashboard
                    </h1>
                    <p class="text-xs text-slate-400">Docker Sail Containerized Environment Inspector</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/sail/dashboard" class="px-3.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition">
                    📊 Sail Dashboard
                </a>
                <a href="/sail/backups" class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold text-white shadow transition">
                    📦 Volume Backups
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Top Metrics Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">MySQL Ping Latency</span>
                <div class="text-2xl font-extrabold text-emerald-400 mt-2">
                    <span x-text="telemetry.db_ping_ms || 0"></span> <span class="text-xs font-normal text-slate-400">ms</span>
                </div>
                <span class="text-[10px] text-slate-500">PDO Database Connection</span>
            </div>

            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Database Size</span>
                <div class="text-2xl font-extrabold text-cyan-400 mt-2">
                    <span x-text="telemetry.db_size_mb || 0"></span> <span class="text-xs font-normal text-slate-400">MB</span>
                </div>
                <span class="text-[10px] text-slate-500">Total MySQL Volume Data</span>
            </div>

            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Peak Memory</span>
                <div class="text-2xl font-extrabold text-amber-400 mt-2">
                    <span x-text="telemetry.memory_peak_mb || 0"></span> <span class="text-xs font-normal text-slate-400">MB</span>
                </div>
                <span class="text-[10px] text-slate-500">PHP Container Memory</span>
            </div>

            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sail Mode</span>
                <div class="text-lg font-bold mt-2" :class="environment.is_sail ? 'text-emerald-400' : 'text-slate-300'">
                    <span x-text="environment.is_sail ? '🟢 ACTIVE (Sail 8.5)' : '⚙️ Standalone PHP'"></span>
                </div>
                <span class="text-[10px] text-slate-500">Docker Runtime</span>
            </div>
        </div>

        <!-- Main Grid: Containers Status & Environment Config -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Containers Health Cards (2 Cols) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                        <span>🐳 Docker Container Stack Status</span>
                    </h2>
                    <button @click="fetchTelemetry()" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-400 border border-slate-700 transition">
                        🔄 Refresh Metrics
                    </button>
                </div>

                <template x-for="c in containers" :key="c.name">
                    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-mono font-bold text-lg text-cyan-300">
                                <span x-text="c.name.substring(0,2).toUpperCase()"></span>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-bold text-slate-100 text-base" x-text="c.name"></h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-mono font-semibold"
                                          :class="c.status === 'RUNNING' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400'"
                                          x-text="c.status">
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="c.service"></p>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs font-semibold font-mono" x-text="c.health"></div>
                            <div class="text-[11px] text-slate-500 mt-1">Bound Port: <span class="text-slate-300 font-mono" x-text="c.port"></span></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Sail Environment Config Parameters -->
            <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 shadow-xl h-fit">
                <h2 class="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2">
                    <span>⚙️ Environment Telemetry</span>
                </h2>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-2 border-b border-slate-800">
                        <span class="text-slate-400">PHP Version</span>
                        <span class="font-mono text-slate-200" x-text="environment.php_version"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-800">
                        <span class="text-slate-400">Laravel Version</span>
                        <span class="font-mono text-cyan-300" x-text="environment.laravel_version"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-800">
                        <span class="text-slate-400">WWWGroup / User</span>
                        <span class="font-mono text-slate-200" x-text="(environment.wwwgroup || '1000') + ' / ' + (environment.wwwuser || '1000')"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-800">
                        <span class="text-slate-400">Xdebug Mode</span>
                        <span class="font-mono text-amber-300" x-text="environment.xdebug_mode || 'off'"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-800">
                        <span class="text-slate-400">Database Tables</span>
                        <span class="font-mono text-emerald-400" x-text="telemetry.tables_count || 0"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-800">
                        <span class="text-slate-400">Last Telemetry Sync</span>
                        <span class="font-mono text-slate-400" x-text="telemetry.timestamp || ''"></span>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <script>
        function containerHealthApp() {
            return {
                environment: @json($metrics['environment'] ?? []),
                telemetry: @json($metrics['telemetry'] ?? []),
                containers: @json($metrics['containers'] ?? []),

                async fetchTelemetry() {
                    try {
                        const res = await fetch('/sail/container-health/json');
                        const json = await res.json();
                        if (json.status === 'success') {
                            this.environment = json.data.environment;
                            this.telemetry = json.data.telemetry;
                            this.containers = json.data.containers;
                        }
                    } catch (e) {
                        console.error("Telemetry fetch error:", e);
                    }
                }
            };
        }
    </script>
</body>
</html>
