<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            {{-- <a href="{{ route('home') }}">{{ $setting->site_name }}</a> --}}
        </div>

        <ul class="sidebar-menu">
            {{-- ********************************Dashboard**************************************** --}}
            <li class="menu-header" style="color: black">Eamils</li>
            <li class="dropdown {{ setActive(['admin.dashboard*']) }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link "><i
                        class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>


            {{-- *********************************Setting******************************************* --}}
            <li class="menu-header" style="color: black"> </i> Settings</li>
            {{-- settings---------------------------------------------------------------- --}}
            <li class="{{ setActive(['admin.settings.*']) }}"><a href="{{ route('admin.settings.index') }}"
                    class="nav-link "><i class="fas fa-cog"></i><span>General Settings</span></a></li>
            <li class="{{ setActive(['admin.category.*']) }}"><a href="{{ route('admin.category.index') }}"
                    class="nav-link "><i class="fas fa-cog"></i><span>Categories</span></a></li>
            <li class="{{ setActive(['admin.sub-category.*']) }}"><a href="{{ route('admin.sub-category.index') }}"
                    class="nav-link "><i class="fas fa-cog"></i><span>SubCategories</span></a></li>
        </ul>

        <br>
        <br>


    </aside>
</div>
