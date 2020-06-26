<button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
    <i class="ion-close"></i>
</button>

<!-- LOGO -->
<div class="topbar-left">
    <div class="text-center">
        <!--<a href="index.html" class="logo">Admiry</a>-->
        <a href="{{ route('dashboard') }}" class="logo"><img src="{{ asset('assets/img/logo-complete-1.png') }}" height="55" alt="logo"></a>
    </div>
</div>

<div class="sidebar-inner slimscrollleft">

    <div class="user-details" style="margin-top: -20px;">
        <div class="text-center">
            <img src="
            @if( Auth::guard('admin')->user()->avatar == null)
            {{ asset('/assets/img/no-image.png') }}
            @else
            /storage/{{  Auth::guard('admin')->user()->avatar }}
            @endif
            " alt="" class="rounded-circle">
        </div>
        <div class="user-info">
            <h4 class="font-16">{{ Auth::guard('admin')->user()->name }}</h4>
            <span class="text-muted user-status"><i class="fa fa-dot-circle-o text-success"></i> Online</span>
        </div>
    </div>

    <div id="sidebar-menu">
        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="waves-effect">
                    <i class="mdi mdi-view-dashboard"></i>
                    <span> Dashboard 
                        {{-- <span class="badge badge-primary pull-right">8</span> --}}
                    </span>
                </a>
            </li>

            @if(Auth::guard('admin')->user()->is_super == true)
                <li>
                    <a href="{{ route('admins.index') }}" class="waves-effect"><i class="mdi mdi-account-location"></i> <span> Accounts </span> </a>
                </li>
            @endif
           
            <li>
                <a href="{{ route('reports.index') }}" class="waves-effect"><i class="mdi mdi-message-outline"></i> <span> Reports </span> </a>
            </li>

            <li>
                <a href="{{ route('sheet.index') }}" class="waves-effect"><i class="mdi mdi-file-document"></i> <span> Sheet </span> </a>
            </li>

        </ul>
    </div>
    <div class="clearfix"></div>
</div> 