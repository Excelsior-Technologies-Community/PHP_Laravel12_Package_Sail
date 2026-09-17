<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sail Environment Dashboard</title>

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

        .hero {
            background: #212529;
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .info-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .info-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 18px;
            font-weight: 600;
            margin-top: 5px;
        }

        .sail-badge {
            font-size: 14px;
            padding: 8px 12px;
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
            <a href="{{ route('sail.dashboard') }}" class="btn btn-outline-light btn-sm me-1">
                Dashboard
            </a>

            <a href="{{ route('sail.health') }}" class="btn btn-outline-light btn-sm me-1">
                Health
            </a>

            <a href="{{ route('sail.system') }}" class="btn btn-outline-light btn-sm">
                System
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <div class="hero">
        <h1>🐳 Sail Environment Dashboard</h1>

        <p class="mb-3">
            Laravel development environment information powered by
            Docker and Laravel Sail.
        </p>

        @if($environment['Docker Container'] === true)
            <span class="badge bg-success sail-badge">
                Docker Container Detected
            </span>
        @else
            <span class="badge bg-warning text-dark sail-badge">
                Docker Container Not Detected
            </span>
        @endif

        @if($environment['Sail Environment'] === true)
            <span class="badge bg-primary sail-badge">
                Laravel Sail Detected
            </span>
        @endif
    </div>

    <div class="row g-4">

        @foreach($environment as $label => $value)

            @if($label !== 'Docker Container' && $label !== 'Sail Environment')

                <div class="col-md-6 col-lg-4">

                    <div class="card info-card h-100">

                        <div class="card-body">

                            <div class="info-label">
                                {{ $label }}
                            </div>

                            <div class="info-value">

                                @if(is_bool($value))
                                    {{ $value ? 'Yes' : 'No' }}
                                @else
                                    {{ $value }}
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @endif

        @endforeach

    </div>

</div>

</body>
</html>