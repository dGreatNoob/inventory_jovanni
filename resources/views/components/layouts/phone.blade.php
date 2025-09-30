<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#0134d4" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <title>{{ $title ?? 'Mobile UI' }}</title>

    @vite(['resources/css/style.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        #scanner-container {
            position: relative;
            width: 80%;
            max-width: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #preview {
            width: 100%;
            border-radius: 11px;
            height: auto;
        }

        .corner {
            position: absolute;
            width: 25px;
            height: 25px;
            border: 5px solid red;
            opacity: 1;
            animation: blink 1s infinite;
        }

        #corner-top-left {
            top: 25%;
            left: 25%;
            border-right: none;
            border-bottom: none;
        }

        #corner-top-right {
            top: 25%;
            right: 25%;
            border-left: none;
            border-bottom: none;
        }

        #corner-bottom-left {
            bottom: 25%;
            left: 25%;
            border-right: none;
            border-top: none;
        }

        #corner-bottom-right {
            bottom: 25%;
            right: 25%;
            border-left: none;
            border-top: none;
        }

        #center-line {
            position: absolute;
            top: 50%;
            left: 25%;
            width: 50%;
            height: 2px;
            background-color: red;
            opacity: 1;
            animation: blink 1s infinite;
            transform: translateY(-50%);
        }

        #qr-result {
            margin-top: 20px;
            font-size: 1.2em;
            color: #333;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }
    </style>
    <style>
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            display: inline-block;
            margin-bottom: 10px;
            vertical-align: middle;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 120px;
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_length select {
            width: auto;
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_paginate {
            text-align: center;
        }

        table.dataTable {
            font-size: 14px;
        }

        table.dataTable {
            width: 100% !important;
        }
    </style>

</head>

<body>
    <audio id="scanSound" src="{{ asset('scan.mp3') }}"></audio>
    <div id="preloader">
        <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>





    <main class="page-content-wrapper py-3">
        <div class="container">

            {{-- Card container only, no scanner --}}
            <div style="display: flex; justify-content: center; margin-top: 40px;">
                <div class="card" style="max-width: 600px; width: 90%;">
                    {{ $slot }}
                </div>
            </div>
        </div>
        </div>
    </main>

    <div class="footer-nav-area fixed bottom-0 left-0 right-0 bg-white shadow z-50">
        <div class="container px-0">
            <div class="footer-nav relative">
                <ul class="flex justify-between items-center w-full text-center text-xs h-14">
                    <li class="flex-1">
                        <a href="index.php"
                            class="flex flex-col items-center justify-center text-gray-600 hover:text-blue-600">
                            <i class="fas fa-warehouse text-base mt-1.5 leading-none"></i>
                            <span class="text-[11px] mt-0.5">Home</span>
                        </a>
                    </li>
                    <li class="flex-1 active">
                        <a href="{{ route('bodegero.stockin') }}"
                            class="flex flex-col items-center justify-center text-blue-600">
                            <i class="fas fa-qrcode text-base mt-1.5 leading-none"></i>
                            <span class="text-[11px] mt-0.5">Stock In</span>
                        </a>
                    </li>
                    <li class="flex-1">
                        <a href="stockout.php"
                            class="flex flex-col items-center justify-center text-gray-600 hover:text-blue-600">
                            <i class="fas fa-qrcode text-base mt-1.5 leading-none"></i>
                            <span class="text-[11px] mt-0.5">Stock Out</span>
                        </a>
                    </li>
                    <li class="flex-1">
                        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#affanOffcanvas"
                            aria-controls="affanOffcanvas"
                            class="flex flex-col items-center justify-center text-gray-600 hover:text-blue-600">
                            <i class="fas fa-bars text-base mt-1.5 leading-none"></i>
                            <span class="text-[11px] mt-0.5">Menu</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>



    @livewireScripts
    <script>
        Livewire.on('statusUpdated', ({
            message,
            error = false
        }) => {
            Swal.fire({
                title: error ? 'Error' : 'Success',
                text: message,
                icon: error ? 'error' : 'success',
                confirmButtonText: 'OK'
            });
        });
    </script>


    <script>
        window.addEventListener("load", () => {
            const preloader = document.getElementById("preloader");
            if (preloader) preloader.style.display = "none";
        });
    </script>
    @stack('scripts')
</body>

</html>
