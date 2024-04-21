<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>

    </form>
    <ul class="navbar-nav navbar-right">

        <li class="dropdown"><a href="#" data-toggle="dropdown"
                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="logo" style="width: 40px;height: 40px;object-fit: cover;"
                    src="{{ asset('uploads/' . Auth::guard('web')->user()->image) }}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">{{ Auth::guard('web')->user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">

                <a href="{{ route('admin.profile') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>

                <a href="{{ route('admin.settings.index') }}" class="dropdown-item has-icon">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <div class="dropdown-divider"></div>

                <form method="post" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item has-icon text-danger">
                        <i class="fas fa-sign-out-alt"> {{ ' Logout' }} </i>
                    </button>
                </form>


            </div>
        </li>
    </ul>
</nav>
