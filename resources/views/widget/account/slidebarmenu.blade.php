<li>
	<a href="{{ route('account.edit') }}"><i class="pfadmicon-glyph-406"></i> {{ trans('widget/slidebarmenu.profile') }}</a>
</li>
<li>
	<a href="{{ route('account.add_ads') }}"><i class="pfadmicon-glyph-475"></i> {{ trans('widget/slidebarmenu.add') }}</a>
</li>
<li>
	<a href="{{ route('account.ads') }}"><i class="pfadmicon-glyph-460"></i> {{ trans('widget/slidebarmenu.ads') }}<span class="pfbadge">{{ $arResult['ads'] }}</span></a>
</li>
<li>
	<a href="{{ route('account.favorite') }}"><i class="pfadmicon-glyph-375"></i> {{ trans('widget/slidebarmenu.favorite') }}<span class="pfbadge">{{ $arResult['favorites'] }}</span></a>
</li>
@if(Auth::user()->permissions != null)
<li>
	<a href="{{ route('dashboard.index') }}"><i class="pfadmicon-glyph-583"></i> {{ trans('widget/slidebarmenu.admin') }}</a>
</li>
@endif
<li>
	<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="pfadmicon-glyph-476"></i> {{ trans('widget/slidebarmenu.logout') }}</a>
	 <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>