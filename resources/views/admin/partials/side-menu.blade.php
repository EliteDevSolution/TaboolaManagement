<button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
    <i class="ion-close"></i>
</button>

<!-- LOGO -->
<div class="topbar-left">
    <div class="text-left ml-3 mt-1">
        <!--<a href="index.html" class="logo">Admiry</a>-->
        <a href="{{ route('dashboard') }}" class="logo"><img src="{{ asset('assets/img/logo-complete-1.png') }}" height="85" alt="logo"></a>
    </div>
</div>

<div class="sidebar-inner slimscrollleft">

{{--    <div class="user-details" style="margin-top: -20px;">--}}
{{--        <div class="text-center">--}}
{{--            <img src="--}}
{{--            @if( Auth::guard('admin')->user()->avatar == null)--}}
{{--            {{ asset('/assets/img/no-image.png') }}--}}
{{--            @else--}}
{{--            /storage/{{  Auth::guard('admin')->user()->avatar }}--}}
{{--            @endif--}}
{{--            " alt="" class="rounded-circle">--}}
{{--        </div>--}}
{{--        <div class="user-info">--}}
{{--            <h4 class="font-16">{{ Auth::guard('admin')->user()->name }}</h4>--}}
{{--            <span class="text-muted user-status"><i class="fa fa-dot-circle-o text-success"></i> Online</span>--}}
{{--        </div>--}}
{{--    </div>--}}

    <div id="sidebar-menu">
        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="waves-effect">
                    <i class="mdi mdi-view-dashboard"></i>
                    <span> {{ __('globals.common.dashboard') }}
                        {{-- <span class="badge badge-primary pull-right">8</span> --}}
                    </span>
                </a>
            </li>

            @if(Auth::guard('admin')->user()->is_super == true)
            <li class="has_sub">
                <a class="waves-effect"><i class="mdi mdi-account-location"></i> <span> {{ __('globals.common.accounts') }} </span> </a>
                <ul class="list-unstyled">
                    <li><a href="{{ route('admins.index') }}" class="waves-effect"><i class="ion-person-stalker"></i> <span> {{ __('globals.common.user_list') }} </span> </a></li>
                    <li><a href="{{ route('client_details.index') }}" class="waves-effect"><i class="ion-pull-request"></i> <span> {{ __('globals.common.request_list') }} </span> </a></li>
                </ul>
            </li>
            @endif



            @if(sizeof(session('permissions')) > 0 && session('permissions')['campaign_management_page'] == 1)
            <li>
                <a href="{{ route('campaigns.index') }}" class="waves-effect"><i class="ion-speakerphone"></i> <span> {{ __('globals.common.campaigns') }} </span> </a>
            </li>
            @endif

            @if(Auth::guard('admin')->user()->id === 1)
            <li>
                <a href="{{ route('ads.index') }}" class="waves-effect"><i class="fa fa-buysellads"></i> <span> {{ __('globals.common.advertising') }} </span> </a>
            </li>
            @else
                @if(sizeof(session('permissions')) > 0 && session('permissions')['ads_page'] == 1 && session()->get('cur_balance') >= 100)
                    <li>
                        <a href="{{ route('ads.index') }}" class="waves-effect"><i class="fa fa-buysellads"></i> <span> {{ __('globals.common.advertising') }} </span> </a>
                    </li>
                @endif
            @endif

            @if(sizeof(session('permissions')) > 0 && session('permissions')['report_page'] == 1)
            <li>
                <a href="{{ route('reports.index') }}" class="waves-effect"><i class="mdi mdi-message-outline"></i> <span> {{ __('globals.common.reports') }} </span> </a>
            </li>
            @endif

            @if(sizeof(session('permissions')) > 0 && session('permissions')['campaign_page'] == 1)
            <li>
                <a href="{{ route('sheet.index') }}" class="waves-effect"><i class="mdi mdi-file-document"></i> <span> {{ __('globals.common.sheet') }} </span> </a>
            </li>
            @endif

            @if(sizeof(session('permissions')) > 0 && session('permissions')['financial_setting'] == 1)
                <li class="has_sub">
                    <a class="waves-effect"><i class="ion-trophy"></i> <span> {{ __('globals.finance.finance') }} </span> </a>
                    <ul class="list-unstyled">
                        @if(Auth::guard('admin')->user()->id === 1)
                        <li><a href="{{ route('deposits.index') }}"><i class="ion-card"></i>{!! __('globals.finance.title') !!}</a></li>
                        @endif
                        @if(sizeof(session('permissions')) > 0 && session('permissions')['payment_history'] == 1)
                        <li><a href="{{ route('payments.index') }}"><i class="ion-podium"></i>{!! __('globals.payment_history.title') !!}</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(sizeof(session('permissions')) > 0 && session('permissions')['content_page'] == 1)
                <li>
                    <a href="{{ route('contents.index') }}" class="waves-effect"><i class="typcn typcn-image"></i> <span> {{ __('globals.common.content') }} </span> </a>
                </li>
            @endif

            <li>
                <a href="{{ route('title_analysis.index') }}" class="waves-effect"><i class="ion-ribbon-b"></i> <span> {{ __('globals.common.title_analyzer') }} </span> </a>
            </li>

            <li>
                <a href="https://suporte.smartpublishers.co" target="_blank" class="waves-effect"><i class="mdi mdi-comment-question-outline"></i> <span> {{ __('globals.common.contact_us') }} </span> </a>
            </li>
            @if(Auth::guard('admin')->user()->id !== 1)
            <li class="ml-3 mr-2">
                <label class="font-italic">Dados Smart Publishers para acrescentar saldo via TED</label>
                <div class="text-muted mb-1">Banco: 260 Nu Pagamentos S.A.</div>
                <div class="text-muted mb-1">Agência: 0001</div>
                <div class="text-muted mb-1">Conta Corrente: 16928336-3</div>
                <div class="text-muted mb-1">Nome: Smart Publishers</div>
                <div class="text-muted">CNPJ: 35.807.010/0001-18</div>
            </li>
            @endif

        </ul>
    </div>
    <div class="clearfix"></div>
</div> 