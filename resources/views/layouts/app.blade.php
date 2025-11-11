<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    {{-- @vite('resources/css/app.css') --}}
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">

    @yield('link')
    @yield('style')

    <title>SugarPros - @yield('title')</title>
    <link rel="shortcut icon" href="{{ $brandicon }}" type="image/x-icon">
</head>

<body>


    @include('layouts.header')

    @yield('content')

    @include('layouts.footer')


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/js/index.js') }}"></script>


    <script>
        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
        @endif
    </script>


    <script>
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 150) {
                $(".headerpage").addClass('bg-white').addClass('fixed').removeClass('relative').removeClass('bg-[#133A59]/10').removeClass('lg:bg-[unset]');
            } else {
                $(".headerpage").removeClass('bg-white').removeClass('fixed').addClass('relative').addClass('bg-[#133A59]/10').addClass('lg:bg-[unset]');
            }
        });
    </script>

    @yield('script')
</body>

</html>
