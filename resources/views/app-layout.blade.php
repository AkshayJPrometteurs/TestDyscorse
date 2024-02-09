<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dyscorse</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap">
    <link href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet" >
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
</head>
<body>
    <header>
        <nav class="navbar bg-primary p-2 text-white">
            <div class="container-fluid justify-content-center">
                <div class="navbar-brand fw-bold text-uppercase" style="font-size:1.5rem;letter-spacing: 0.05em;">Dyscorse</div>
            </div>
        </nav>
    </header>
    <main class="container p-5 my-3" style="text-align: justify;">@yield('content')</main>
    <footer class="p-4 text-center fw-bold text-white bg-black">&copy;{{ date('Y') }} {{ config('app.name') }}. All rights reserved.</footer>
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
