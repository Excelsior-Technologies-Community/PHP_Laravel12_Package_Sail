<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>System Information</title>

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

        .system-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .system-label {
            color: #6c757d;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .system-value {
            font-size: 17px;
            font-weight: 600;
            margin-top: 5px;
            word-break: break-word;
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

    <div class="hero">

        <h1>📊 System Information</h1>

        <p class="mb-0">
            Runtime and Laravel environment information from the Sail container.
        </p>

    </div>

    <div class="row g-4">

        @foreach($system as $label => $value)

            <div class="col-md-6 col-lg-4">

                <div class="card system-card h-100">

                    <div class="card-body">

                        <div class="system-label">
                            {{ $label }}
                        </div>

                        <div class="system-value">
                            {{ $value }}
                        </div>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>

</body>
</html>