<div id="left-sidebar" class="sidebar">
    <div class="sidebar-scroll">
        <div class="user-account">
            @if (\Auth::user()->profile_photo_path != '')
                <img src="{{ \Auth::user()->profile_photo_path }}" class="rounded-circle user-photo"
                    alt="User Profile Picture">
            @endif
            <div class="dropdown">
                <span>Welcome,</span>
                <a href="javascript:void(0);" class="dropdown-toggle user-name"
                    data-toggle="dropdown"><strong>{{ isset(\Auth::user()->name) ? \Auth::user()->name : '' }}</strong></a>
                <ul class="dropdown-menu dropdown-menu-right account">
                    <li><a href="{{ route('profile') }}"><i class="icon-user"></i>My Profile</a></li>
                    <li><a href="{{ route('setting') }}"><i class="icon-settings"></i>Settings</a></li>
                    <li class="divider"></li>
                    <li><a href="#" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i class="icon-power"></i>Logout</a></li>
                </ul>
            </div>
            <hr>
        </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#menu"><i
                        class="fa fa-database"></i> Data</a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content p-l-0 p-r-0">
            <div class="tab-pane active" id="menu">
                <nav id="left-sidebar-nav" class="sidebar-nav">
                    <ul id="main-menu" class="metismenu">

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
