@extends('layouts.template')

@section('content')

<!--  ACCOUNT  -->
	<section role="main">
		<div class="pf-container clearfix">
			<div class="pf-row clearfix">
				<div class="pf-uadashboard-container clearfix">
					<div class="col-lg-3 col-md-3">
						
						<div class="pfuaformsidebar">
							<ul class="pf-sidebar-menu">
								<li class="pf-dash-userprof"><img class="pf-dash-userphoto" src="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/themes/pointfinder/images/empty_avatar.jpg"><span class="pf-dash-usernamef">{{ Auth::user()->name }}</span></li>
								<!--  SLIDE BAR MENU  -->
								@widget('SlideBarMenuWidget')
								<!--  END SLIDE BAR MENU  -->
							</ul>
						</div>
						<div class="sidebar-widget"></div>
					</div>
					<div class="col-lg-9 col-md-9">
						ЗДЕСЬ ДОБОВЛЯТЬ ОБЪЯВЛЕНИЯ!!
					</div>
				</div>
			</div>
		</div>
	</section>
<!--  END ACCOUNR  --> 

@endsection