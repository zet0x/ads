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
						<div class="golden-forms">
							<form id="pfuaprofileform" method="post">
								@csrf
								<div class="pfsearchformerrors">
									<ul></ul><a class="button pfsearch-err-button">CLOSE</a>
								</div>
								<div class="">
									<div class="">
										<div class="row">
											<div class="col12 first">
												<section>
													<label class="lbl-text" for="name"><strong>{{ trans('account.name') }}</strong>:</label> <label class="lbl-ui"><input class="input form-control" name="name" type="text" value="{{ Auth::user()->name }}"></label>
												</section>
												<section>
													<label class="lbl-text" for="email"><strong>{{ trans('account.email') }}</strong>:</label> <label class="lbl-ui"><input class="input form-control" name="email" type="email" value="{{ Auth::user()->email }}" disabled></label>
												</section>
												<!-- ПОКА НЕ НАДО
												<section>
													<label class="lbl-text" for="phone"><strong>{{ trans('account.phone') }}</strong>:</label> <label class="lbl-ui"><input class="input" name="nickname" type="text" value="{{ Auth::user()->phone }}"></label>
												</section>
												<section>
													<label class="lbl-text" for="phone"><strong>{{ trans('account.city') }}</strong>:</label> <label class="lbl-ui"><input class="input" name="nickname" type="text" value="{{ Auth::user()->city }}"></label>
												</section>
												
												<section>
													<label class="lbl-text" for="descr">Biographical Info:</label> <label class="lbl-ui">
													<textarea class="textarea mini no-resize" name="descr"></textarea></label>
												</section>
												<section>
													<label class="lbl-text" for="userphoto">User Photo (Recommend:200px W/H) (.jpg, .png, .gif):</label>
													<div class="col-lg-3">
														<div class="pfuserphoto-container"><img src="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/themes/pointfinder/images/noimg.png"></div>
													</div>
													<div class="col-lg-9">
														<label class="lbl-ui file-input" for="userphoto"><input name="userphoto" type="file"></label>
														<div class="clearfix" style="margin-bottom:10px">
															<label class="lbl-ui file-input" for="userphoto"></label>
														</div><label class="lbl-ui file-input" for="userphoto"><span class="goption"><label class="options"><input name="deletephoto" type="checkbox" value="1"> <span class="checkbox"></span></label> <label for="check1">Remove Photo</label></span></label>
													</div>
													<div class="clearfix"></div>
												</section>
												<section>
													<label class="lbl-text" for="password">New Password:</label> <label class="lbl-ui"><input class="input" id="password" name="password" type="password"></label>
												</section>
												<section>
													<label class="lbl-text" for="password2">Repeat New Password:</label> <label class="lbl-ui"><input class="input" name="password2" type="password"></label>
												</section>
												<section>
													<small><strong>Hint:</strong> The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).</small>
												</section>
												<section>
													<label class="lbl-text" for="address">Address:</label> <label class="lbl-ui">
													<textarea class="textarea mini no-resize" name="address"></textarea></label>
												</section>
												-->
											</div>
											<!-- ПОКА НЕ НАДО
											<div class="col6 last">
												<section>
													<label class="lbl-text" for="firstname">First name:</label> <label class="lbl-ui"><input class="input" name="firstname" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="lastname">Last Name:</label> <label class="lbl-ui"><input class="input" name="lastname" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="webaddr">Website:</label> <label class="lbl-ui"><input class="input" name="webaddr" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="phone">Telephone:</label> <label class="lbl-ui"><input class="input" name="phone" placeholder="" type="tel" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="mobile">Mobile:</label> <label class="lbl-ui"><input class="input" name="mobile" placeholder="" type="tel" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="twitter">Twitter:</label> <label class="lbl-ui"><input class="input" name="twitter" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="facebook">Facebook:</label> <label class="lbl-ui"><input class="input" name="facebook" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="googleplus">Google+:</label> <label class="lbl-ui"><input class="input" name="googleplus" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="linkedin">LinkedIn:</label> <label class="lbl-ui"><input class="input" name="linkedin" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="vatnumber">VAT Number:</label> <label class="lbl-ui"><input class="input" name="vatnumber" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="country">Country:</label> <label class="lbl-ui"><input class="input" name="country" type="text" value=""></label>
												</section>
												<section>
													<label class="lbl-text" for="city">City:</label> <label class="lbl-ui"><input class="input" name="city" type="text" value=""></label>
												</section>
											</div>
											-->
										</div>
									</div>
								</div>
								<div class="pfalign-right">
									<section>
										<input class="button blue pfmyitempagebuttonsex" data-edit="" id="pf-ajax-profileupdate-button" type="submit" value="UPDATE INFO">
									</section>
								</div>
							</form>
						</div>
						<script type="text/javascript">
						                          (function($) {
						                              "use strict";
						                              
						                              $.pfAjaxUserSystemVars4 = {};
						                              $.pfAjaxUserSystemVars4.email_err = 'Please write an email';
						                              $.pfAjaxUserSystemVars4.email_err2 = 'Your email address must be in the format of name@domain.com';
						                              $.pfAjaxUserSystemVars4.nickname_err = 'Please write nickname';
						                              $.pfAjaxUserSystemVars4.nickname_err2 = 'Please enter at least 3 characters for nickname.';
						                              $.pfAjaxUserSystemVars4.passwd_err = 'Enter at least 7 characters';
						                              $.pfAjaxUserSystemVars4.passwd_err2 = 'Enter the same password as above';
						                          
						                          })(jQuery);
						</script>
					</div>
				</div>
			</div>
		</div>
	</section>
<!--  END ACCOUNR  --> 

@endsection