<nav class="navbar navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-btn">
            <button type="button" class="btn-toggle-offcanvas"><i class="lnr lnr-menu fa fa-bars"></i></button>
        </div>
        <div class="navbar-brand">
            @if (isset($logo))
                <a href="/"><img src="{{ $logo }}" class="img-responsive logo"></a>
            @endif
        </div>
        <div class="navbar-right">
            <form id="navbar-search" class="navbar-form search-form col-md-9">
                <div id="navbar-menu float-left">
                    <ul class="nav navbar-nav">
                        @if(\Auth::user()->user_access_id==1)
                            <li><a href="{{ route('users.index') }}" class="text-info icon-menu px-1">Users</a></li>
                        @endif
                        <li><a href="{{ route('plc.index') }}" class="text-info icon-menu px-1">PLC / Slave</a></li>
                    </ul>
                </div>
            </form>
            <div id="navbar-menu">
                <ul class="nav navbar-nav">
                    <li class="d-none d-sm-inline-block d-md-none d-lg-inline-block">
                        @if(\Auth::user()->name)
                            {{\Auth::user()->name}} {!!isset(\Auth::user()->access->name) ? '<br /><small>( '. \Auth::user()->access->name .' )</small>' : ''!!}
                        @endif
                    </li>
                    {{-- <li class="d-none d-sm-inline-block d-md-none d-lg-inline-block">
                        <a href="" class="icon-menu"><i class="fa fa-folder-open-o"></i></a>
                    </li>
                    <li class="d-none d-sm-inline-block d-md-none d-lg-inline-block">
                        <a href="" class="icon-menu"><i class="icon-calendar"></i></a>
                    </li>
                    <li class="d-none d-sm-inline-block">
                        <a href="" class="icon-menu"><i class="icon-bubbles"></i></a>
                    </li>
                    <li class="d-none d-sm-inline-block">
                        <a href="" class="icon-menu"><i class="icon-envelope"></i><span class="notification-dot"></span></a>
                    </li> --}}
                    {{-- <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle icon-menu" data-toggle="dropdown">
                            <i class="icon-bell"></i>
                            <span class="notification-dot"></span>
                        </a>
                        <ul class="dropdown-menu notifications">
                            <li class="header"><strong>You have 0 new Notifications</strong></li>
                            <li class="footer"><a href="javascript:void(0);" class="more">See all notifications</a></li>
                        </ul>
                    </li> --}}
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle icon-menu" data-toggle="dropdown"><i
                                class="icon-equalizer"></i></a>
                        <ul class="dropdown-menu user-menu menu-icon">
                            <li class="menu-heading">ACCOUNT SETTINGS</li>
                            <li><a href="{{ route('profile') }}"><i class="icon-note"></i> <span>My Profile</span></a>
                            </li>
                            <li><a href="{{ route('setting') }}"><i class="icon-equalizer"></i>
                                    <span>Setting</span></a>
                            </li>
                            <li><a href="{{ route('back-to-admin') }}" class="text-danger"><i
                                        class="fa fa-arrow-right"></i> <span>Back to Admin</span></a></li>
                        </ul>
                    </li>
                    <li><a href="" onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                            class="icon-menu"><i class="icon-login"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
