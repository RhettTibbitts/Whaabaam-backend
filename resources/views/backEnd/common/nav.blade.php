<?php $url = Request::url(); ?> 
<?php echo "url";
	if(str_contains($url, ['admin/users','admin/user/add','admin/user/edit'])) { 
		$a = 'active'; 
	} else{ 
		$a = 'no'; 
	} //die;*/ 
?>

<div class="site-sidebar">
	<div class="custom-scroll custom-scroll-light">
		<ul class="sidebar-menu">
			<li class="menu-title">Main </li>
			<li class="<?php if(str_contains($url, ['admin/dashboard'])) { echo 'active'; } ?>" >
				<a href="{{ url('admin/dashboard') }}" class="waves-effect  waves-light ">
					<span class="s-icon"><i class="fa fa-dashboard"></i></span>
					<span class="s-text">Dashboard</span>
				</a>
			</li>
			
			<li class="menu-title">Members </li>
			<li class="with-sub  <?php if(str_contains($url, ['admin/users','admin/user/add','admin/user/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-user"></i></span>
					<span class="s-text">Users</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/users','admin/user/add','admin/user/edit/'] ) ) { ?> d-blk <?php } ?>">
					<li class="<?php if(str_contains($url, ['admin/users','admin/user/edit/'] ) ) { ?> active1 <?php } ?> "><a href="{{url('admin/users')}}">List Users</a></li>
					<li><a href="{{url('admin/user/add')}}">Add New User</a></li>
				</ul>
			</li>

			<li class="menu-title">System </li>

			<li class="with-sub  <?php if(str_contains($url, ['admin/militaries','admin/military/add','admin/military/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-shield"></i></span>
					<span class="s-text">Militaries</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/militaries','admin/military/add','admin/military/edit/'] ) ) { ?> d-blk <?php } ?>">
					<li class="<?php if(str_contains($url, ['admin/militaries','admin/military/edit/'] ) ) { ?> active1 <?php } ?> "><a href="{{url('admin/militaries')}}">Military's List</a></li>
					<li><a href="{{url('admin/military/add')}}">Add New Military</a></li>
				</ul>
			</li>	

			<li class="with-sub  <?php if(str_contains($url, ['admin/politicals','admin/political/add','admin/political/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-flag-o"></i></span>
					<span class="s-text">Politicals</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/politicals','admin/political/add','admin/political/edit/'] ) ) { ?> d-blk <?php } ?>">
					<li class="<?php if(str_contains($url, ['admin/politicals','admin/political/edit/'] ) ) { ?> active1 <?php } ?> "><a href="{{url('admin/politicals')}}">Political's List</a></li>
					<li ><a href="{{url('admin/political/add')}}">Add New Political</a></li>
				</ul>
			</li>

			<li class="with-sub  <?php if(str_contains($url, ['admin/relationships','admin/relationship/add','admin/relationship/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-heart-o"></i></span>
					<span class="s-text">Relationships</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/relationships','admin/relationship/add','admin/relationship/edit/'] ) ) { ?> d-blk <?php } ?>">
					<li class="<?php if(str_contains($url, ['admin/relationships','admin/relationship/edit/'] ) ) { ?> active1 <?php } ?> "><a href="{{url('admin/relationships')}}">Relationship's List</a></li>
					<li><a href="{{url('admin/relationship/add')}}">Add New Relationship</a></li>
				</ul>
			</li>
			<li class="with-sub  <?php if(str_contains($url, ['admin/religions','admin/religion/add','admin/religion/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-tags"></i></span>
					<span class="s-text">Religions</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/religions','admin/religion/add','admin/religion/edit/'] ) ) { ?> d-blk <?php } ?>" >
					<li class="<?php if(str_contains($url, ['admin/religions','admin/religion/edit/'] ) ) { ?> active1 <?php } ?> " ><a href="{{url('admin/religions')}}">Religion's List</a></li>
					<li><a href="{{url('admin/religion/add')}}">Add New Religion</a></li>
				</ul>
			</li>
			<li class="with-sub  <?php if(str_contains($url, ['admin/states','admin/state/add','admin/state/edit/','admin/cities','admin/city/add','admin/city/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-globe"></i></span>
					<span class="s-text">States</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/states','admin/state/add','admin/state/edit/','admin/cities','admin/city/add','admin/city/edit/'] ) ) { ?> d-blk <?php } ?>" >
					<li class="<?php if(str_contains($url, ['admin/states','admin/state/edit/','admin/cities','admin/city/add','admin/city/edit/'] ) ) { ?> active1 <?php } ?>"><a href="{{url('admin/states')}}">State's List</a></li>
					<li><a href="{{url('admin/state/add')}}">Add New State</a></li>
				</ul>
			</li>
			<li class="with-sub  <?php if(str_contains($url, ['admin/emails','admin/email/add','admin/email/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-envelope-o"></i></span>
					<span class="s-text">Email Templates</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/emails','admin/email/add','admin/email/edit/'] ) ) { ?> d-blk <?php } ?>">
					<li class="<?php if(str_contains($url, ['admin/emails','admin/email/edit/'] ) ) { ?> active1 <?php } ?>"><a href="{{url('admin/emails')}}">Email Template's List</a></li>
				</ul>
			</li>
			<li class="with-sub <?php if(str_contains($url, ['admin/pages','admin/page/edit/'] ) ) { ?> active1 <?php } ?>" >
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="fa fa-file-text-o"></i></span>
					<span class="s-text">Cms Pages</span>
				</a>
				<ul class="<?php if(str_contains($url, ['admin/pages','admin/page/edit/'] ) ) { ?> d-blk <?php } ?>">
					<li><a href="{{url('admin/pages')}}">Cms Page's List</a></li>
				</ul>
			</li>
			<!-- <li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-car"></i></span>
					<span class="s-text">@lang('main.provider')s</span>
				</a>
				<ul>
					<li><a href="{{url('admin.provider.index')}}">List @lang('main.provider')s</a></li>
					<li><a href="{{url('admin.provider.create')}}">Add New @lang('main.provider')</a></li>
				</ul>
			</li> -->
			
	
			<li class="menu-title">Account</li>
			<li>
				<a href="{{url('admin/profile')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-user"></i></span>
					<span class="s-text">Account Settings</span>
				</a>
			</li>
			<li>
				<a href="{{url('admin/change-password')}}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
					<span class="s-text">Change Password</span>
				</a>
			</li>
			<li class="compact-hide">
				<a href="{{ url('admin/logout') }}">
					<span class="s-icon"><i class="ti-power-off"></i></span>
					<span class="s-text">Logout</span>
                </a>
            </li>
			
		</ul>
	</div>
</div>