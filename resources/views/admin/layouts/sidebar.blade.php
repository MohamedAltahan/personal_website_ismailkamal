<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            {{-- <a href="{{ route('home') }}">{{ $setting->site_name }}</a> --}}
        </div>

        <ul class="sidebar-menu">
            {{-- ___________________________Dashboard__________________________________________ --}}
            <li class="menu-header" style="color: black">Eamils</li>
            <li class="dropdown {{ setActive(['admin.get-emails.*']) }}">
                <a href="{{ route('admin.get-emails.index') }}" class="nav-link "><i
                        class="fas fa-envelope"></i><span>Email(inbox)</span></a>
            </li>


            {{-- ___________________________setting__________________________________________________ --}}
            <li class="menu-header" style="color: black"> </i> Settings</li>
            {{-- settings____________________________________________________________________________ --}}
            <li class="{{ setActive(['admin.settings.*']) }}"><a href="{{ route('admin.settings.index') }}"
                    class="nav-link "><i class="fas fa-wrench"></i><span>General Settings</span></a></li>

            <li class="{{ setActive(['admin.category.*']) }}"><a href="{{ route('admin.category.index') }}"
                    class="nav-link "><i class="fas fa-cog"></i><span>Categories</span></a></li>

            {{-- <li class="{{ setActive(['admin.sub-category.*']) }}"><a href="{{ route('admin.sub-category.index') }}"
                    class="nav-link "><i class="fas fa-cog"></i><span>SubCategories</span></a></li> --}}

            <li class="{{ setActive(['admin.show-designs.*', 'admin.design.*']) }}"><a
                    href="{{ route('admin.show-designs.index') }}" class="nav-link "><i
                        class="fas fa-pen-fancy"></i><span>Designs</span></a></li>

            <li class="{{ setActive(['admin.socials.*']) }}"><a href="{{ route('admin.socials.index') }}"
                    class="nav-link "><i class="fab fa-facebook-square"></i><span>Social buttons</span></a></li>
        </ul>

        <br>
        <br>


    </aside>
</div>
