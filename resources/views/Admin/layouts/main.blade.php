<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Admin Panel">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dyscorse Admin : @stack('page_title')</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap">
    <link href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fontawsome/all.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('assets/vendor/fileinput/fileinput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dataTables.boostrap5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}"/>
    <!-- <link href="assets/vendor/smartWizard/smart_wizard_all.min.css" rel="stylesheet" /> -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" >
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
</head>
<body>
    <div class="wrapper">
        @include('Admin.components.header')
        @include('Admin.components.sidebar')
        <main id="main" class="main">
            @yield('contents')
        </main>
    </div>
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/js/validate.js') }}"></script>
    <script src="{{ asset('assets/vendor/fileinput/fileinput.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/smartWizard/jquery.smartWizard.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/charts-demo.js') }}"></script>
    <script src="{{ asset('assets/js/dataTable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
            $(".file_upload").fileinput({
                showUpload: false,
                layoutTemplates: {
                    main1: "{preview}\n" +
                    "<div class=\'input-group {class}\'>\n" +
                    "   {browse}\n" +
                    "   {upload}\n" +
                    "   {remove}\n" +
                    "   {caption}\n" +
                    "</div>"
                }
            });
        });
    </script>
</body>
</html>
