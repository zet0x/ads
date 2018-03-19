@extends('layouts.template')

@section('content')
<!--  LOGIN  -->
<div class="padding-100 ui-dialog-content ui-widget-content" id="pf-membersystem-dialog" style="display: block; width: auto; min-height: 0px; max-height: none; height: auto;">
        <script type="text/javascript">
        (function($) {"use strict";$.pfAjaxUserSystemVars = {};$.pfAjaxUserSystemVars.username_err = 'Please write username';$.pfAjaxUserSystemVars.username_err2 = 'Please enter at least 3 characters for Username.';$.pfAjaxUserSystemVars.password_err = 'Please write password';})(jQuery);
        </script>
        <div class="golden-forms wrapper mini">
            <div class="pftrwcontainer-overlay" id="pflgcontainer-overlay"></div>
            <form class="border-1px-ser" id="pf-ajax-login-form" name="pf-ajax-login-form" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="pfmodalclose">
                    
                </div>
                <div class="pfsearchformerrors">
                    <ul></ul><a class="button pfsearch-err-button"></a>
                </div>
                <div class="form-title">
                    <h2>{{ trans('auth.register') }}</h2>
                </div>
                <div class="form-enclose">
                    <div class="form-section">
                        <section>
                            <div class="pointfinder-login-scbuttons">
                                <span class="pflgtext">{{ trans('auth.register_width') }}</span><span class="pflgbuttons"></span>
                            </div>
                            <div class="tagline"></div>
                        </section>
                        <section>
                            <label class="lbl-text" for="usernames">{{ trans('auth.user') }}:</label><label class="lbl-ui append-icon"><input autofocus="" class="input" name="name" placeholder="{{ trans('auth.user_enter') }}" type="text" value="{{ old('name') }}" required><span><i class="pfadmicon-glyph-632"></i></span></label>
                        </section>
                        @if ($errors->has('name'))
                            <section>
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>   
                            </section>
                        @endif
                        <section>
                            <label class="lbl-text" for="usernames">{{ trans('auth.email') }}:</label><label class="lbl-ui append-icon"><input autofocus="" class="input" name="email" placeholder="{{ trans('auth.email_enter') }}" type="text" value="{{ old('email') }}" required><span><i class="pfadmicon-glyph-823"></i></span></label>
                        </section>
                        @if ($errors->has('email'))
                            <section>
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>    
                            </section>
                        @endif
                        <section>
                            <label class="lbl-text" for="pass">{{ trans('auth.pass') }}:</label><label class="lbl-ui append-icon"><input class="input" name="password" placeholder="{{ trans('auth.pass_enter') }}" type="password" required><span><i class="pfadmicon-glyph-465"></i></span></label>
                        </section>
                        @if ($errors->has('password'))
                            <section>
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>    
                            </section>
                        @endif
                        <section>
                            <label class="lbl-text" for="pass">{{ trans('auth.conf_pass') }}:</label><label class="lbl-ui append-icon"><input class="input" name="password_confirmation" placeholder="{{ trans('auth.pass_enter') }}" type="password" required><span><i class="pfadmicon-glyph-465"></i></span></label>
                        </section>

                        <script src="https://yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
                        <script src="https://yastatic.net/share2/share.js"></script>
                        <div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,moimir"></div>
                    </div>
                </div>
                <div class="form-buttons">
                    <section>
                        <input name="redirectpage" type="hidden" value="2"><button class="button blue" id="pf-ajax-login-button">{{ trans('auth.register') }}</button>
                    </section>
                </div>
            </form>
        </div>
    </div>
<!--  END LOGIN  -->
@endsection
