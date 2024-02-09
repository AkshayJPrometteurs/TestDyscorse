<header id="header" class="header fixed-top d-flex align-items-center bg-white py-3 px-6">
    <div class="d-flex align-items-center justify-content-between">
        <a href="dashboard.php" class="logo d-flex align-items-center text-decoration-none">
            {{-- <img class="img-fluid" src="{{ asset('assets/images/logo.svg') }}" alt="Filing Buddy Logo" width="auto"> --}}
            <h4 class="mb-0">DYSCORSE</h4>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center mb-0">
            <li class="nav-item">
                <div class="p-0 fw-normal d-flex align-items-center">
                    <div class="me-3 fw-bold text-uppercase">{{ Auth::guard('webadmin')->user()->name }}</div>
                    <div class="me-2">
                        <img class="img-fluid rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"src="{{ asset('assets/images/admin.avif') }}"alt="admin">
                    </div>
                    <button onclick="logout();" class="btn btn-danger" style="padding: 0.25rem 0.7rem;font-size:1rem;"><i class="fas fa-power-off"></i></button>
                </div>
            </li>
        </ul>
    </nav>
</header>
<script>
    function logout(){
        Swal.fire({
        title: 'Are you sure logout now?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Sure',
        cancelButtonText: 'No, Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:'POST',
                    url:"{{ route('logout') }}",
                    data:{"_token": "{{ csrf_token() }}"},
                    success:function(data) {
                        window.location.href = "{{ route('login_page') }}";
                    }
                });
            }
        })
    }
</script>
