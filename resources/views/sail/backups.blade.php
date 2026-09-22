<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sail Volume Database Dump & Backup Exporter Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans" x-data="backupStudio()">
    
    <!-- Top Nav Header -->
    <header class="bg-slate-900/80 backdrop-blur border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-xl shadow-lg shadow-indigo-500/20">
                    📦
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-300 bg-clip-text text-transparent">
                        Sail Volume Database Dump & Backup Exporter
                    </h1>
                    <p class="text-xs text-slate-400">1-Click MySQL Backup Creation & Restoration Tools</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="/sail/container-health" class="px-3.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-cyan-400 border border-slate-700 transition">
                    🐳 Container Health
                </a>
                <a href="/sail/dashboard" class="px-3.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition">
                    📊 Sail Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Status Flash Message -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-sm font-medium flex items-center justify-between">
                <span>🟢 {{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-sm font-medium flex items-center justify-between">
                <span>🔴 {{ session('error') }}</span>
            </div>
        @endif

        <!-- Top Action Card -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 shadow-xl mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-100 mb-1">Generate 1-Click MySQL Backup Dump</h2>
                <p class="text-xs text-slate-400">Exports all database tables, schemas, and records cleanly into a downloadable dump file.</p>
            </div>

            <form method="POST" action="/sail/backups/create" class="flex items-center space-x-3">
                @csrf
                <label class="flex items-center space-x-2 text-xs text-slate-300 cursor-pointer">
                    <input type="checkbox" name="compress" value="1" class="rounded bg-slate-800 border-slate-700 text-indigo-500">
                    <span>Compress (.sql.gz)</span>
                </label>
                
                <button type="submit" class="py-2.5 px-5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-xs font-bold text-white shadow-lg shadow-indigo-500/20 transition flex items-center space-x-2">
                    <span>✨ Create MySQL Backup Now</span>
                </button>
            </form>
        </div>

        <!-- Backups List Table -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <span>🗄️ Saved Database Backup Files</span>
                </h2>
                <span class="text-xs font-mono text-slate-400">Total Backups: <strong class="text-indigo-400">{{ count($backups) }}</strong></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Filename</th>
                            <th class="py-3 px-4">Size</th>
                            <th class="py-3 px-4">Created Date</th>
                            <th class="py-3 px-4">Age</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($backups as $b)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 font-bold text-indigo-300">{{ $b['filename'] }}</td>
                                <td class="py-3 px-4 text-emerald-400">{{ $b['size_formatted'] }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ $b['created_at'] }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ $b['age_human'] }}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <a href="/sail/backups/download/{{ $b['filename'] }}" 
                                       class="inline-block py-1.5 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-[11px] transition">
                                        📥 Download
                                    </a>

                                    <form method="POST" action="/sail/backups/restore" class="inline-block" onsubmit="return confirm('Restore database from {{ $b['filename'] }}? Current data will be overwritten.');">
                                        @csrf
                                        <input type="hidden" name="filename" value="{{ $b['filename'] }}">
                                        <button type="submit" class="py-1.5 px-3 rounded-lg bg-emerald-600/80 hover:bg-emerald-600 text-white text-[11px] font-semibold transition">
                                            🔄 Restore DB
                                        </button>
                                    </form>

                                    <form method="POST" action="/sail/backups/delete/{{ $b['filename'] }}" class="inline-block" onsubmit="return confirm('Delete backup {{ $b['filename'] }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="py-1.5 px-3 rounded-lg bg-rose-950/80 hover:bg-rose-900 border border-rose-800 text-rose-300 text-[11px] transition">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 text-sm font-sans">
                                    No database backup files found. Click "✨ Create MySQL Backup Now" to generate one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        function backupStudio() {
            return {};
        }
    </script>
</body>
</html>
