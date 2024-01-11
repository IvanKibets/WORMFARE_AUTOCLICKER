<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@php($company = get_setting('company'))
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ get_setting('favicon') }}" type="image/x-icon"> <!-- Favicon-->
    <title>@yield('title') - {{ $company }}</title>
    <meta name="description" content="@yield('meta_description', config('app.name'))">
    <meta name="author" content="@yield('meta_author', config('app.name'))">
    @yield('meta')

    {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}
    @stack('before-styles')

    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/jvectormap/jquery-jvectormap-2.0.3.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/morrisjs/morris.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/toastr.min.css') }}"/>

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/color_skins.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v={{ date('YmdHis') }}">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/datatables/datatables.min.css') }}" />
    <script src="{{ asset('assets/vendor/datatables/datatables.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendor/datatables/FixedColumns-4.1.0/js/dataTables.fixedColumns.min.js') }}" defer></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}

    @stack('after-styles')
    @if (trim($__env->yieldContent('page-styles')))
        @yield('page-styles')
    @endif
    @livewireStyles
</head>

<body class="theme-blue   layout-fullwidth">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            @php($logo = get_setting('logo'))
            @if ($logo)
                <div class="m-t-30">
                    <img src="{{ $logo }}" height="48" alt="{{ $company }}">
                </div>
            @endif
            <p>Please wait...</p>
        </div>
    </div>

    <div id="wrapper">
        @include('layouts.navbar',['logo'=>$logo])
        @include('layouts.sidebar')
        <div id="main-content">
            <div class="container-fluid">
                <div class="block-header">
                    @if (session()->has('message-success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <i class="fa fa-check-circle"></i> {!! session('message-success') !!}
                        </div>
                    @endif
                    @if (session()->has('message-error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <i class="fa fa-times-circle"></i> {!! session('message-error') !!}
                        </div>
                    @endif

                    <div class="alert alert-danger alert-dismissible" role="alert" style="display:none">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <i class="fa fa-times-circle"></i> <span class="message"></span>
                    </div>
                    <div class="alert alert-success alert-dismissible" role="alert" style="display:none">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <i class="fa fa-check-circle"></i> <span class="message"></span>
                    </div>
                    <div class="col-lg-5 col-md-8 col-sm-12">
                        @if (trim($__env->yieldContent('title')))
                            <h2><a href="javascript:void(0);" onclick="history.back()" class="btn btn-xs "><i class="fa fa-arrow-left"></i></a> @yield('title')</h2>
                        @endif
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/"><i class="icon-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">@yield('sub-title')</li>
                        </ul>
                    </div>

                </div>
                @yield('content')
                {{ $slot }}
            </div>
        </div>
    </div>
    <!-- Scripts -->
    @stack('before-scripts')
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/morrisscripts.bundle.js') }}"></script><!-- Morris Plugin Js -->
    <script src="{{ asset('assets/bundles/jvectormap.bundle.js') }}"></script> <!-- JVectorMap Plugin Js -->
    <script src="{{ asset('assets/bundles/knob.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/toastr.js') }}"></script>
    <script src="{{ asset('assets/bundles/mainscripts.bundle.js') }}"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    @livewireScripts
        <script>
            var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
                cluster: 'ap1'
            });

            var channel_general = pusher.subscribe('general');
            channel_general.bind('notification', function(data) {
                show_toast(data.message,'top-right');

            });
        </script>
    @stack('after-scripts')
    @if (trim($__env->yieldContent('page-script')))
        <script>
            @yield('page-script')
        </script>
    @endif
    <script>
        Livewire.on('modal', (act) => {
            if(act=='hide')
                $('.modal').modal('hide');
            else
                $(act).modal('show');
        });
        Livewire.on('message-success', (msg) => {
            $('.alert-success').show();
            $('.alert-success .message').html(msg);
            $('html, body').animate({
                scrollTop: $("#wrapper").offset().top
            }, 0);
        });
        Livewire.on('message-error', (msg) => {
            $('.alert-error').show();
            $('.alert-error .message').html(msg);
            $('html, body').animate({
                scrollTop: $("#wrapper").offset().top
            }, 0);
        });

        function show_toast(message,positon)
        {
            toastr.options.closeButton = true;
            toastr['info'](message, '', {
                positionClass: 'toast-'+positon
            });
        }
    </script>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
</body>

</html>
