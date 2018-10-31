<section id="top-navs" >
    <nav class="nav-left two-fifth">
        <ul class="inlineBlockLi">
            <li><a href="javascript:void(0)">Sydney<i class="drop-icon icons"></i></a>
                <ul class="dropMenu">
                    <li><a href="#">Sydney</a></li>
                    <li><a href="#">Sydney</a></li>
                    <li><a href="#">Sydney</a></li>
                    <li><a href="#">Sydney</a></li>
                </ul>                          
            </li>
            <li><a href="javascript:void(0)">Browse Chefs</a></li>
            <li><a href="javascript:void(0)">How it works</a></li>
            <li><a href="javascript:void(0)">Help</a></li>
        </ul>
    </nav>

    <div class="nav-logo one-fifth">
        <a href="{{ url('/') }}"><img src="{{ asset('public/images/system/logo.png') }}" class="chefservice"></a>
    </div>

    <div class="nav-right two-fifth">
        <ul class="inlineBlockLi inlineBlock">
            @if(Auth::check())
                
                <li><a href="javascript:void(0)" >{{ ucfirst(Auth::user()->first_name).' '.Auth::user()->last_name }}</a></li>
                @if(Auth::user()->user_type == '1')
                <li><a href="{{ url('/dish/add') }}" >Add Dish</a></li>                            
                @endif

            @else
                <li><a href="javascript:void(0)" class="login-btn">Login</a></li>
                <li><a href="javascript:void(0)" id="i-am-chef-btn">i'm chef</a></li>
            @endif
        </ul>
        @if(Auth::check())
            <a href="{{ url('/logout') }}" class="brown-button inlineBlock">Logout</a>
        @else
            <a href="javascript:void(0)" class="brown-button inlineBlock signup-btn">Sign Up Now!</a>
        @endif
    </div>
    <div class="clear"></div>
</section><!--#top-navs-->