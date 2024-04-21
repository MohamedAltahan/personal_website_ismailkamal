<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            {{-- <a href="{{ route('home') }}">{{ $setting->site_name }}</a> --}}
        </div>

        <ul class="sidebar-menu">
            {{-- ********************************Dashboard**************************************** --}}
            <li class="menu-header" style="color: black">Dashboard</li>
            <li class="dropdown {{ setActive(['admin.dashboard*']) }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link "><i
                        class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>
            {{-- *********************************Setting******************************************* --}}
            <li class="menu-header" style="color: black"> </i> Settings</li>
            {{-- settings---------------------------------------------------------------- --}}
            <li class="{{ setActive(['admin.settings.*']) }}"><a href="{{ route('admin.settings.index') }}"
                    class="nav-link "><i class="fas fa-cog"></i><span>General Settings</span></a></li>

            {{-- Frontend setting--------------------------------------------------------- --}}
            <li class="dropdown {{ setActive([]) }}">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-wrench"></i>
                    <span>Frontend setting</span></a>

                <ul class="dropdown-menu">
                    <li class="dropdown {{ setActive([]) }}"><a class="nav-link" href="">Home page setting</a>
                    </li>
                </ul>

            </li>
        </ul>

        <br>
        <br>


    </aside>
</div>
