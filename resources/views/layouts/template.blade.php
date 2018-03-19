<!DOCTYPE html>
<html>
<head>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
<style type="text/css">
    .gm-style .gm-style-cc span,.gm-style .gm-style-cc a,.gm-style .gm-style-mtc div{font-size:10px}
</style>


<meta charset="UTF-8">
				
<meta name="description" content="Ещё один сайт на WordPress">
<!--[if lt IE 9]>
<script src="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/themes/pointfinder/js/html5shiv.js"></script>
<![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"><link rel="shortcut icon" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/favicon.png" type="image/x-icon"><link rel="icon" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/favicon.png" type="image/x-icon">
<title>Мой сайт на WordPress — Ещё один сайт на WordPress</title>
<link rel="stylesheet" id="rs-plugin-settings-css" href="revslider/css/settings.css" media="all">
<style id="rs-plugin-settings-inline-css" type="text/css">
#rs-demo-id {}
</style>


<link rel="stylesheet" id="pfsearch-select2-css-css" href="{{ asset('css/select2.css') }}" media="all">


<link rel="stylesheet" id="bootstrap-css" href="{{ asset('css/bootstrap.min.css') }}" media="all">


<link rel="stylesheet" id="fontellopf-css" href="{{ asset('css/fontello.min.css')}}" media="all">


<link rel="stylesheet" id="flaticons-css" href="{{ asset('css/flaticon.css')}}" media="all">


<link rel="stylesheet" id="theme-prettyphotocss-css" href="{{ asset('css/prettyPhoto.css')}}" media="all">


<link rel="stylesheet" id="theme-style-css" href="{{ asset('style.css')}}" media="all">


<link rel="stylesheet" id="theme-owlcarousel-css" href="{{ asset('css/owl.carousel.min.css') }}" media="all">


<link rel="stylesheet" id="pfcss-animations-css" href="{{ asset('css/animate.min.css') }}" media="all">


<link rel="stylesheet" id="pfsearch-goldenforms-css-css" href="{{ asset('css/golden-forms.min.css') }}" media="all">


<link rel="stylesheet" id="pf-frontend-compiler-css" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/pfstyles/pf-style-frontend.css?ver=1520854142" media="all">
<link rel="stylesheet" id="pf-opensn-css" href="https://fonts.googleapis.com/css?family=Open+Sans%3A400%2C600%2C700&amp;ver=4.9.4" media="all">


<link rel="stylesheet" id="pf-main-compiler-local-css" href="{{ asset('admin/options/pfstyles/pf-style-main.css') }}" media="all">


<link rel="stylesheet" id="pf-customp-compiler-local-css" href="{{ asset('admin/options/pfstyles/pf-style-custompoints.css') }}" media="all">


<link rel="stylesheet" id="pf-pbstyles-compiler-local-css" href="{{ asset('admin/options/pfstyles/pf-style-pbstyles.css') }}" media="all">


<link rel="stylesheet" id="js_composer_front-css" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/plugins/js_composer/assets/css/js_composer.min.css?ver=5.4.4" media="all">


<link rel="stylesheet" id="bsf-Defaults-css" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/smile_fonts/Defaults/Defaults.css?ver=4.9.4" media="all">
<link rel="stylesheet" id="ultimate-style-css" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/plugins/Ultimate_VC_Addons/assets/min-css/style.min.css?ver=3.16.20" media="all">


<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,700%7CRoboto:400%7CRoboto+Condensed:700,400&amp;subset=latin">

<script type='text/javascript' src="{{ asset('js/jquery/jquery.js') }}"></script>











</head>

<!--  HEADER  -->
<header class="wpf-header hidden-print" id="pfheadernav">
    <div class="pftopline wpf-transition-all">
        <div class="pf-container">
            <div class="pf-row">
                <div class="col-lg-12 col-md-12">
                    <div class="wpf-toplinewrapper">
                        <div class="pf-toplinks-left clearfix">
                            
                        </div>
                        <div class="pf-toplinks-right clearfix">
                            <nav class="pf-topprimary-nav pf-nav-dropdown clearfix hidden-sm hidden-xs" id="pf-topprimary-nav">
                                <ul class="pf-nav-dropdown pfnavmenu pf-topnavmenu">
                                    <li class="pf-my-account pfloggedin"></li>
                                    @guest
                                    <li class="pf-login-register" id="pf-login-trigger-button">
                                        <a href="/login"><i class="pfadmicon-glyph-584"></i> {{ trans('layouts.login') }}</a>
                                    </li>
                                    <li class="pf-login-register" id="pf-register-trigger-button">
                                        <a href="/register"><i class="pfadmicon-glyph-365"></i> {{ trans('layouts.register') }}</a>
                                    </li>
                                    @else
                                    <li class="pf-login-register" id="pf-login-trigger-button">
                                        <a href="/">{{ trans('layouts.hello') }} {{ Auth::user()->name }}</a>
                                    </li>
                                    <li class="pf-login-register" id="pf-register-trigger-button">
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="pfadmicon-glyph-584"></i> {{ trans('layouts.logout') }}</a>
                                    </li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                    @endguest
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wpf-navwrapper">
        <a id="pf-topprimary-nav-button" title="User Menu"><i class="pfadmicon-glyph-632"></i></a> <a id="pf-topprimary-nav-button2" title="User Menu"><i class="pfadmicon-glyph-787"></i></a> <a id="pf-primary-search-button" style="display: none;" title="Search"><i class="pfadmicon-glyph-627"></i></a>
        <div class="pf-container pf-megamenu-container">
            <div class="pf-row">
                <div class="col-lg-3 col-md-3">
                    <a class="pf-logo-container" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru"></a>
                </div>
                <div class="col-lg-9 col-md-9" id="pfmenucol1">
                    <div class="pf-menu-container">
                        <nav class="pf-nav-dropdown clearfix" id="pf-primary-nav">
                            @widget('HeaderMenuWidget')
                        </nav>
                        <nav class="pf-topprimary-nav pf-nav-dropdown clearfix" id="pf-topprimary-navmobi" style="display: none;">
                            <ul class="pf-nav-dropdown pfnavmenu pf-topnavmenu pf-nav-dropdownmobi">
                                <li class="pf-login-register" id="pf-login-trigger-button-mobi">
                                    <a href="#"><i class="pfadmicon-glyph-584"></i> Login</a>
                                </li>
                                <li class="pf-login-register" id="pf-register-trigger-button-mobi">
                                    <a href="#"><i class="pfadmicon-glyph-365"></i> Register</a>
                                </li>
                                <li class="pf-login-register" id="pf-lp-trigger-button-mobi">
                                    <a href="#"><i class="pfadmicon-glyph-889"></i>Forgot Password</a>
                                </li>
                            </ul>
                        </nav>
                        <nav class="pf-topprimary-nav pf-nav-dropdown clearfix" id="pf-topprimary-navmobi2" style="display: none;">
                            <ul class="pf-nav-dropdown pfnavmenu pf-topnavmenu pf-nav-dropdownmobi"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!--  END HEADER   -->

@yield('content')

<!--  FOOTER   -->
<a title="Back to Top" class="pf-up-but" style="display: block;"><i class="pfadmicon-glyph-859"></i></a>
<div class="wpf-footer-row-move">
        <div class="vc_row wpb_row vc_row-fluid vc_custom_1451242464933 vc_row-has-fill pointfinderexfooterclassx">
            <div class="pf-container pointfinderexfooterclass" id="pf-footer-row" style="color: rgb(255, 255, 255);">
                <div class="pf-row">
                    <div class="wpb_column col-lg-3 col-md-3">
                        <div class="vc_column-inner">
                            <div class="wpb_wrapper">
                                <div class="wpb_widgetised_column wpb_content_element">
                                    <div class="wpb_wrapper">
                                        <div class="pfwidgettitle">
                                            <div class="widgetheader">
                                                Sample Text
                                            </div>
                                        </div>
                                        <div class="pfwidgetinner">
                                            <div class="widget_text" id="text-3">
                                                <div class="textwidget">
                                                    Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorpe.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wpb_column col-lg-3 col-md-3">
                        <div class="vc_column-inner">
                            <div class="wpb_wrapper">
                                <div class="wpb_widgetised_column wpb_content_element">
                                    <div class="wpb_wrapper">
                                        <div class="pfwidgettitle">
                                            <div class="widgetheader">
                                                Featured Item
                                            </div>
                                        </div>
                                        <div class="pfwidgetinner">
                                            <div class="widget_pfitem_recent_entries" id="pf_featured_items_w-5">
                                                <ul class="pf-widget-itemlist">
                                                    <li class="clearfix">
                                                        <a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?properties=150-kingsbury-rd" style="color: rgb(255, 255, 255);" title=""><img alt="" src="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/13651509023_76383875d8_b-140x140.jpg">
                                                        <div class="pf-recent-items-title">
                                                            150 Kingsbury Rd
                                                        </div>
                                                        <div class="pf-recent-items-address">
                                                            150 Kingsbury Road, NY 10804, USA
                                                        </div></a>
                                                    </li>
                                                    <li class="clearfix">
                                                        <a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?properties=235-chapman-rd" style="color: rgb(255, 255, 255);" title=""><img alt="" src="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/3640924252_21e2e7d3ff_b-140x140.jpg">
                                                        <div class="pf-recent-items-title">
                                                            235 Chapman Rd
                                                        </div>
                                                        <div class="pf-recent-items-address">
                                                            235 Chapman Drive, Madera, CA, USA
                                                        </div></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wpb_column col-lg-3 col-md-3">
                        <div class="vc_column-inner">
                            <div class="wpb_wrapper">
                                <div class="wpb_widgetised_column wpb_content_element">
                                    <div class="wpb_wrapper">
                                        <div class="pfwidgettitle">
                                            <div class="widgetheader">
                                                Latest Tweet
                                            </div>
                                        </div>
                                        <div class="pfwidgetinner">
                                            <div class="widget_pfitem_recent_entries" id="pf_twitter_w-3">
                                                
                                                <div id="jstwitter">
                                                    <div class="tweet">
                                                        <i class="icon-twitter" style="font-size:14px;"></i> Please control secret keys!
                                                        <div class="time"></div>
                                                        <div class="dividertwitter"></div>
                                                    </div>
                                                </div><!-- Twitter End -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wpb_column col-lg-3 col-md-3">
                        <div class="vc_column-inner">
                            <div class="wpb_wrapper">
                                <div class="wpb_widgetised_column wpb_content_element">
                                    <div class="wpb_wrapper">
                                        <div class="pfwidgettitle">
                                            <div class="widgetheader">
                                                Recent Posts
                                            </div>
                                        </div>
                                        <div class="pfwidgetinner">
                                            <div class="widget_recent_entries" id="recent-posts-4">
                                                <ul>
                                                    <li>
                                                        <a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?p=1" style="color: rgb(255, 255, 255);">Привет, мир!</a> <span class="post-date">12.03.2018</span>
                                                    </li>
                                                    <li>
                                                        <a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?p=703" style="color: rgb(255, 255, 255);">Gallery Sample</a> <span class="post-date">06.12.2014</span>
                                                    </li>
                                                    <li>
                                                        <a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?p=701" style="color: rgb(255, 255, 255);">Quote Post Sample</a> <span class="post-date">06.12.2014</span>
                                                    </li>
                                                    <li>
                                                        <a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?p=699" style="color: rgb(255, 255, 255);">Link Post Sample</a> <span class="post-date">06.12.2014</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<footer class="wpf-footer">
    <div class="pf-container">
        <div class="pf-row clearfix">
            <div class="wpf-footer-text col-lg-12">
                © Copyright - <a href="http://pointfindertheme.com/demo">Pointfinder Theme Demo</a> - <a href="http://themeforest.net/user/Webbu/portfolio">PointFinder Theme by Webbu</a>
            </div>
            <ul class="pf-footer-menu pfrightside"></ul>
        </div>
    </div>
</footer>


<!--  END FOOTER  -->

<!--  BOTTOM SCRIPTS   -->


<script type='text/javascript' src="{{ asset('js/owl.carousel.min.js') }}"></script>


</html>