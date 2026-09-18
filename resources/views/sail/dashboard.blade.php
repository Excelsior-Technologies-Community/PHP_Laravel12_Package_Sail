<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sail Environment Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .hero {
            background: linear-gradient(135deg, #212529, #495057);
            color: white;
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 30px;
        }

        .feature-card {
            border: 0;
            border-radius: 18px;
            height: 100%;
            transition: 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            font-size: 24px;
        }

        .status-badge {
            font-size: 13px;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .btn-feature {
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container">

            <a class="navbar-brand" href="{{ url('/sail/dashboard') }}">
                Laravel Sail Dashboard
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a
                            class="nav-link active"
                            href="{{ url('/sail/dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="{{ url('/sail/health') }}">
                            Health
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="{{ url('/sail/system') }}">
                            System
                        </a>
                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <div class="container py-4">

        <!-- Hero -->
        <div class="hero">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h1 class="fw-bold mb-2">
                        Sail Environment Dashboard
                    </h1>

                    <p class="mb-0 text-white-50">
                        Monitor your Laravel application, Docker Sail environment,
                        database, cache, storage and server configuration.
                    </p>
                </div>

                <div>
                    @if($isSail)
                    <span class="badge bg-success status-badge px-3 py-2">
                        Sail Environment Detected
                    </span>
                    @elseif($isDocker)
                    <span class="badge bg-warning text-dark status-badge px-3 py-2">
                        Docker Environment Detected
                    </span>
                    @else
                    <span class="badge bg-secondary status-badge px-3 py-2">
                        Local Environment
                    </span>
                    @endif
                </div>

            </div>

        </div>


        <!-- Environment Overview -->
        <h3 class="section-title">
            Environment Overview
        </h3>

        <div class="row g-4 mb-5">

            <div class="col-md-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            🐳
                        </div>

                        <h5 class="fw-bold">
                            Docker
                        </h5>

                        <p class="text-muted">
                            Check whether the Laravel application is running
                            inside a Docker container.
                        </p>

                        @if($isDocker)
                        <span class="badge bg-success">
                            Detected
                        </span>
                        @else
                        <span class="badge bg-secondary">
                            Not Detected
                        </span>
                        @endif

                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            ⛵
                        </div>

                        <h5 class="fw-bold">
                            Laravel Sail
                        </h5>

                        <p class="text-muted">
                            Detect whether the application is running through
                            Laravel Sail.
                        </p>

                        @if($isSail)
                        <span class="badge bg-success">
                            Active
                        </span>
                        @else
                        <span class="badge bg-secondary">
                            Not Active
                        </span>
                        @endif

                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            ⚙️
                        </div>

                        <h5 class="fw-bold">
                            Application
                        </h5>

                        <p class="text-muted">
                            Quickly access application health and system
                            information.
                        </p>

                        <a
                            href="{{ url('/sail/health') }}"
                            class="btn btn-dark btn-feature">
                            Health Check
                        </a>

                    </div>
                </div>
            </div>

        </div>


        <!-- Original Features -->
        <h3 class="section-title">
            Core Monitoring
        </h3>

        <div class="row g-4 mb-5">

            <!-- Health -->
            <div class="col-md-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            ❤️
                        </div>

                        <h5 class="fw-bold">
                            Application Health Check
                        </h5>

                        <p class="text-muted">
                            Check database, cache, storage and application
                            health status.
                        </p>

                        <a
                            href="{{ url('/sail/health') }}"
                            class="btn btn-primary btn-feature">
                            Open Health Check
                        </a>

                    </div>
                </div>
            </div>


            <!-- System -->
            <div class="col-md-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            💻
                        </div>

                        <h5 class="fw-bold">
                            System Information
                        </h5>

                        <p class="text-muted">
                            View PHP, Laravel, operating system and server
                            information.
                        </p>

                        <a
                            href="{{ url('/sail/system') }}"
                            class="btn btn-primary btn-feature">
                            Open System Info
                        </a>

                    </div>
                </div>
            </div>


            <!-- Dashboard -->
            <div class="col-md-4">
                <div class="card feature-card shadow-sm">
                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            📊
                        </div>

                        <h5 class="fw-bold">
                            Environment Dashboard
                        </h5>

                        <p class="text-muted">
                            View the current Laravel Sail environment and
                            monitoring tools.
                        </p>

                        <a
                            href="{{ url('/sail/dashboard') }}"
                            class="btn btn-primary btn-feature">
                            Current Dashboard
                        </a>

                    </div>
                </div>
            </div>

        </div>


        <!-- New Functionalities -->
        <h3 class="section-title">
            Diagnostic & Developer Tools
        </h3>

        <div class="row g-4">


            <!-- 1. Database Statistics -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            🗄️
                        </div>

                        <h5 class="fw-bold">
                            Database Statistics
                        </h5>

                        <p class="text-muted">
                            View database tables, row counts, data size,
                            index size and total table size.
                        </p>

                        <a
                            href="{{ url('/sail/database') }}"
                            class="btn btn-dark btn-feature">
                            View Database
                        </a>

                    </div>

                </div>

            </div>


            <!-- 2. Cache Tester -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            ⚡
                        </div>

                        <h5 class="fw-bold">
                            Cache Tester
                        </h5>

                        <p class="text-muted">
                            Test cache write, read and delete operations
                            from the Laravel application.
                        </p>

                        <a
                            href="{{ url('/sail/cache') }}"
                            class="btn btn-dark btn-feature">
                            Test Cache
                        </a>

                    </div>

                </div>

            </div>


            <!-- 3. Storage Diagnostics -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            💾
                        </div>

                        <h5 class="fw-bold">
                            Storage Diagnostics
                        </h5>

                        <p class="text-muted">
                            Check storage paths, permissions and available
                            disk space.
                        </p>

                        <a
                            href="{{ url('/sail/storage') }}"
                            class="btn btn-dark btn-feature">
                            Check Storage
                        </a>

                    </div>

                </div>

            </div>


            <!-- 4. PHP Extensions -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            🧩
                        </div>

                        <h5 class="fw-bold">
                            PHP Extensions
                        </h5>

                        <p class="text-muted">
                            View all PHP extensions currently installed
                            and loaded by the application.
                        </p>

                        <a
                            href="{{ url('/sail/extensions') }}"
                            class="btn btn-dark btn-feature">
                            View Extensions
                        </a>

                    </div>

                </div>

            </div>


            <!-- 5. Route Inspector -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            🛣️
                        </div>

                        <h5 class="fw-bold">
                            Route Inspector
                        </h5>

                        <p class="text-muted">
                            Inspect Laravel application routes, HTTP methods,
                            names and controller actions.
                        </p>

                        <a
                            href="{{ url('/sail/routes') }}"
                            class="btn btn-dark btn-feature">
                            Inspect Routes
                        </a>

                    </div>

                </div>

            </div>


            <!-- 6. Laravel Log Viewer -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            📜
                        </div>

                        <h5 class="fw-bold">
                            Laravel Log Viewer
                        </h5>

                        <p class="text-muted">
                            View the latest entries from the Laravel
                            application log.
                        </p>

                        <a
                            href="{{ url('/sail/logs') }}"
                            class="btn btn-dark btn-feature">
                            View Logs
                        </a>

                    </div>

                </div>

            </div>


            <!-- 7. Environment Diagnostics -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            🔐
                        </div>

                        <h5 class="fw-bold">
                            Environment Diagnostics
                        </h5>

                        <p class="text-muted">
                            Check important application environment settings
                            without exposing sensitive secrets.
                        </p>

                        <a
                            href="{{ url('/sail/environment') }}"
                            class="btn btn-dark btn-feature">
                            Check Environment
                        </a>

                    </div>

                </div>

            </div>


            <!-- 8. Application Configuration -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            ⚙️
                        </div>

                        <h5 class="fw-bold">
                            Application Configuration
                        </h5>

                        <p class="text-muted">
                            Inspect safe application, database, cache,
                            session, queue and mail configuration.
                        </p>

                        <a
                            href="{{ url('/sail/configuration') }}"
                            class="btn btn-dark btn-feature">
                            View Configuration
                        </a>

                    </div>

                </div>

            </div>


            <!-- 9. Server Metrics -->
            <div class="col-md-6 col-lg-4">

                <div class="card feature-card shadow-sm">

                    <div class="card-body">

                        <div class="feature-icon mb-3">
                            📈
                        </div>

                        <h5 class="fw-bold">
                            Server Metrics
                        </h5>

                        <p class="text-muted">
                            Monitor PHP memory, execution limits, upload
                            limits, disk information and runtime details.
                        </p>

                        <a
                            href="{{ url('/sail/metrics') }}"
                            class="btn btn-dark btn-feature">
                            View Metrics
                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- Footer -->
        <div class="text-center text-muted py-5">

            <hr>

            <p class="mb-1">
                Laravel 12 + Laravel Sail
            </p>

            <small>
                Environment and application diagnostic dashboard
            </small>

        </div>

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>
