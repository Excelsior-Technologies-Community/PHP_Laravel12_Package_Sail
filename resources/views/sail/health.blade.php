<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Application Health Check</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .health-header {
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .healthy-header {
            background: #198754;
            color: white;
        }

        .unhealthy-header {
            background: #dc3545;
            color: white;
        }

        .health-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .status-icon {
            font-size: 35px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">

        <a href="{{ route('home') }}" class="navbar-brand">
            🐳 Laravel Sail
        </a>

        <div>

            <a href="{{ route('sail.dashboard') }}"
               class="btn btn-outline-light btn-sm me-1">
                Dashboard
            </a>

            <a href="{{ route('sail.health') }}"
               class="btn btn-outline-light btn-sm me-1">
                Health
            </a>

            <a href="{{ route('sail.system') }}"
               class="btn btn-outline-light btn-sm">
                System
            </a>

        </div>

    </div>
</nav>

<div class="container py-4">

    <div class="health-header
        {{ $overallHealthy ? 'healthy-header' : 'unhealthy-header' }}">

        @if($overallHealthy)

            <div class="status-icon">✅</div>

            <h1>Application Healthy</h1>

            <p class="mb-0">
                Laravel application and required services are working correctly.
            </p>

        @else

            <div class="status-icon">⚠️</div>

            <h1>Health Check Failed</h1>

            <p class="mb-0">
                One or more application services require attention.
            </p>

        @endif

    </div>

    <div class="row g-4">

        @foreach($checks as $service => $check)

            <div class="col-md-6">

                <div class="card health-card h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <h4 class="mb-0">
                                {{ $service }}
                            </h4>

                            @if($check['status'] === 'Healthy')

                                <span class="badge bg-success">
                                    Healthy
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Unhealthy
                                </span>

                            @endif

                        </div>

                        <hr>

                        <p class="text-muted mb-0">
                            {{ $check['message'] }}
                        </p>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

    <div class="mt-4">

        <a href="{{ route('sail.health') }}"
           class="btn btn-dark">

            🔄 Run Health Check Again

        </a>

    </div>

</div>

</body>
</html>