<div class="topbar">

    <nav class="navbar-custom">

        <ul class="list-inline float-right mb-0">
            {{--  <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-effect notif-link" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <i class="ion-ios7-bell noti-icon"></i>
                </a>
                <div class="notif-link-temp" style="display: none;">
                    <span class="badge badge-danger noti-icon-badge"></span>
                </div>
                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5>Notification</h5>
                    </div>

                    <div class="noti-message-container">
                    </div>

                    <div class="noti-message-container-temp" style="display:none;">
                        <a href="url" class="dropdown-item notify-item">
                            <div class="notify-icon bg-primary"></div>
                            <p class="notify-details"><b class="notify-title">Title</b><small class="text-muted">Message</small></p>
                        </a>
                    </div>

                    <!-- All-->
                    <a href="#" class="dropdown-item notify-item">
                        View All
                    </a>

                </div>
            </li>  --}}
            <li class="list-inline-item">
                <span>{{ __('globals.common.balance') }}: </span>
                <span id="span_balance" class="@if(session()->get('cur_balance') < 100) text-danger @else text-success @endif"
                      style="cursor: pointer;"
                      data-placement="bottom"
                      data-trigger="hover"
                      data-content="{{ __('globals.msg.balance_limit') }}">R$ {{ number_format(session()->get('cur_balance'), 2, '.', ',') }}</span>

            </li>

            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <img src="
                    @if( Auth::guard('admin')->user()->avatar == null)
                    {{ asset('/assets/img/no-image.png') }}
                    @else
                    /storage/{{  Auth::guard('admin')->user()->avatar }}
                    @endif
                    " alt="user" class="rounded-circle">
                    <span>{{ Auth::guard('admin')->user()->name ?? '' }}<i class="mdi mdi-chevron-down"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <a class="dropdown-item" href="{{ route('admin.profile', Auth::guard('admin')->user()->id) }}"><i class="fa fa-user-circle-o m-r-5 text-muted"></i> My Account</a>
{{--                    @if(\DLW\Models\ClientDetail::where(['email' => Auth::guard('admin')->user()->email])->first())--}}
{{--                    <a class="dropdown-item" href="{{ route('profile.client_details.show', urlencode(Auth::guard('admin')->user()->email)) }}"><i class="mdi mdi-settings m-r-5 text-muted"></i> Request Details</a>--}}
{{--                    @endif--}}
                    <a class="dropdown-item" href="#" onclick="to_logout()"><i class="mdi mdi-logout m-r-5 text-muted"></i> Logout</a>
                      @push('scripts')
                        <script>
                            function to_logout(){
                                $('#logout-form').submit();
                            }
                        </script>
                      @endpush
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">{{ csrf_field() }}</form>
                </div>
            </li>

        </ul>

        <ul class="list-inline menu-left mb-0">
            <li class="list-inline-item">
                <button type="button" class="button-menu-mobile open-left waves-effect">
                    <i class="ion-navicon"></i>
                </button>
            </li>
            <li class="hide-phone list-inline-item app-search">
                <h3 class="page-title">{{ $title }}</h3>
            </li>
        </ul>

        <div class="clearfix"></div>

    </nav>
</div>