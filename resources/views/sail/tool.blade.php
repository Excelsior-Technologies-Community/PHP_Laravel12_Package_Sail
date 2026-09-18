<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>{{ $page }} - Laravel Sail</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .hero {
            background: #212529;
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            min-height: 140px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: 700;
        }

        .label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6c757d;
        }

        .value {
            font-size: 17px;
            font-weight: 600;
            word-break: break-word;
        }

        .extension-badge {
            margin: 4px;
            font-size: 14px;
        }

        .log-box {
            background: #111827;
            color: #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            min-height: 400px;
            max-height: 600px;
            overflow: auto;
            white-space: pre-wrap;
            font-family: Consolas, monospace;
            font-size: 13px;
        }

        .route-table {
            font-size: 14px;
        }

        .method-badge {
            min-width: 65px;
        }

        .status-ok {
            color: #198754;
            font-weight: 700;
        }

        .status-failed {
            color: #dc3545;
            font-weight: 700;
        }

        .table-card {
            overflow-x: auto;
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-dark bg-dark">

        <div class="container">

            <a
                href="{{ route('home') }}"
                class="navbar-brand">
                🐳 Laravel Sail
            </a>

            <div class="d-flex flex-wrap gap-1">

                <a
                    href="{{ route('sail.dashboard') }}"
                    class="btn btn-outline-light btn-sm">
                    Dashboard
                </a>

                <a
                    href="{{ route('sail.health') }}"
                    class="btn btn-outline-light btn-sm">
                    Health
                </a>

                <a
                    href="{{ route('sail.system') }}"
                    class="btn btn-outline-light btn-sm">
                    System
                </a>

            </div>

        </div>

    </nav>


    <div class="container py-4">

        <div class="hero">

            <h1>
                {{ $icon }} {{ $page }}
            </h1>

            <p class="mb-0">
                {{ $description }}
            </p>

        </div>


        {{-- DATABASE --}}

        @if($type === 'database')

        <div class="row g-4 mb-4">

            <div class="col-md-4">

                <div class="card stat-card">

                    <div class="card-body">

                        <div class="label">
                            Database
                        </div>

                        <div class="value mt-2">
                            {{ $databaseName }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card stat-card">

                    <div class="card-body">

                        <div class="label">
                            Status
                        </div>

                        <div class="value mt-2">

                            @if($databaseStatus === 'Connected')

                            <span class="text-success">
                                ✓ Connected
                            </span>

                            @else

                            <span class="text-danger">
                                ✕ Connection Failed
                            </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card stat-card">

                    <div class="card-body">

                        <div class="label">
                            Tables
                        </div>

                        <div class="stat-number">
                            {{ count($tables) }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="card table-card">

            <div class="card-body">

                <h4 class="mb-3">
                    Database Tables
                </h4>

                <table class="table table-hover">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>Table</th>
                            <th>Rows</th>
                            <th>Size</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($tables as $index => $table)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <strong>
                                    {{ $table['name'] }}
                                </strong>
                            </td>

                            <td>
                                {{ number_format($table['rows']) }}
                            </td>

                            <td>
                                {{ $table['size'] }}
                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="4"
                                class="text-center text-muted">
                                No database tables found.
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @endif


        {{-- CACHE --}}

        @if($type === 'cache')

        <div class="row justify-content-center">

            <div class="col-lg-7">

                <div class="card">

                    <div class="card-body text-center">

                        <h3>
                            Cache Driver
                        </h3>

                        <div class="display-6 mb-4">
                            {{ $driver }}
                        </div>

                        <form
                            method="POST"
                            action="{{ route('sail.cache') }}">

                            @csrf

                            <button
                                type="submit"
                                class="btn btn-dark btn-lg">
                                ⚡ Run Cache Test
                            </button>

                        </form>


                        @if($result)

                        <div class="alert mt-4
                                {{ $result['status'] === 'Success'
                                    ? 'alert-success'
                                    : 'alert-danger' }}">

                            <h5>
                                {{ $result['status'] }}
                            </h5>

                            <p class="mb-0">
                                {{ $result['message'] }}
                            </p>

                        </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        @endif


        {{-- STORAGE --}}

        @if($type === 'storage')

        <div class="row g-4">

            @foreach($storage as $label => $value)

            <div class="col-md-6 col-lg-4">

                <div class="card h-100">

                    <div class="card-body">

                        <div class="label">
                            {{ $label }}
                        </div>

                        <div class="value mt-2">
                            {{ $value }}
                        </div>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

        @endif


        {{-- EXTENSIONS --}}

        @if($type === 'extensions')

        <div class="card mb-4">

            <div class="card-body">

                <h4>
                    Installed Extensions
                </h4>

                <p class="text-muted">
                    Total installed extensions:
                    <strong>{{ $count }}</strong>
                </p>

                <hr>

                @foreach($extensions as $extension)

                <span class="badge bg-dark extension-badge">
                    {{ $extension }}
                </span>

                @endforeach

            </div>

        </div>

        @endif


        {{-- ROUTES --}}

        @if($type === 'routes')

        <div class="card table-card">

            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">

                    <h4>
                        Registered Routes
                    </h4>

                    <span class="badge bg-dark align-self-center">
                        {{ $count }} Routes
                    </span>

                </div>

                <table class="table table-hover route-table">

                    <thead>

                        <tr>

                            <th>Method</th>
                            <th>URI</th>
                            <th>Name</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($routes as $route)

                        <tr>

                            <td>

                                @foreach(explode(', ', $route['methods']) as $method)

                                <span class="badge bg-primary method-badge">
                                    {{ $method }}
                                </span>

                                @endforeach

                            </td>

                            <td>
                                <code>
                                    /{{ $route['uri'] }}
                                </code>
                            </td>

                            <td>
                                {{ $route['name'] }}
                            </td>

                            <td>
                                <small>
                                    {{ $route['action'] }}
                                </small>
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        @endif


        {{-- LOGS --}}

        @if($type === 'logs')

        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h4>
                            Laravel Log
                        </h4>

                        <small class="text-muted">
                            {{ $logPath }}
                        </small>

                    </div>

                    <span class="badge bg-secondary">
                        {{ $logSize }}
                    </span>

                </div>

                @if($logExists)

                <div class="log-box">
                    {{ $content }}
                </div>

                @else

                <div class="alert alert-info">
                    Laravel log file does not exist yet.
                </div>

                @endif

            </div>

        </div>

        @endif


        {{-- ENVIRONMENT --}}

        @if($type === 'environment')

        <div class="row g-4">

            @foreach($environment as $key => $item)

            <div class="col-md-6 col-lg-4">

                <div class="card h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <strong>
                                {{ $key }}
                            </strong>

                            @if($item['status'])

                            <span class="text-success">
                                ✓
                            </span>

                            @else

                            <span class="text-danger">
                                ✕
                            </span>

                            @endif

                        </div>

                        <hr>

                        <div class="value">

                            {{ $item['value'] }}

                        </div>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

        @endif


        {{-- CONFIGURATION --}}

        @if($type === 'configuration')

        @foreach($configuration as $section => $items)

        <div class="card mb-4">

            <div class="card-body">

                <h4 class="mb-3">
                    {{ $section }}
                </h4>

                <div class="row g-3">

                    @foreach($items as $label => $value)

                    <div class="col-md-6 col-lg-4">

                        <div class="border rounded p-3 h-100">

                            <div class="label">
                                {{ $label }}
                            </div>

                            <div class="value mt-2">
                                {{ $value }}
                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

            </div>

        </div>

        @endforeach

        @endif


        {{-- METRICS --}}

        @if($type === 'metrics')

        <div class="row g-4">

            @foreach($metrics as $label => $value)

            <div class="col-md-6 col-lg-4">

                <div class="card h-100">

                    <div class="card-body">

                        <div class="label">
                            {{ $label }}
                        </div>

                        <div class="value mt-2">
                            {{ $value }}
                        </div>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

        @endif


        <div class="mt-4">

            <a
                href="{{ route('sail.dashboard') }}"
                class="btn btn-dark">
                ← Back to Dashboard
            </a>

        </div>

    </div>

</body>

</html>
