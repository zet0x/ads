

<ul class="pf-nav-dropdown pfnavmenu pf-topnavmenu">
    @if(count($arResult) > 0)
        @foreach($arResult as $value)
           <li id="nav-menu-item-1249" class="main-menu-item  menu-item-even menu-item-depth-0 menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-7 current_page_item menu-item-has-children">
                <a 
                    href="{{ $value['parent']->slug }}" 
                    class="menu-link main-menu-link"
                    title="{{ $value['parent']->title }}"
                    target="{{ $value['parent']->target }}"
                    rel="{{ $value['parent']->robot }}">
                        {{ $value['parent']->label }}
                    @if(count($value['child'])>0)
                    <span class="pfadmicon-glyph-860"></span>
                    @endif
                </a>
                @if(count($value['child'])>0)
                <ul class="sub-menu menu-odd pfnavsub-menu   menu-depth-1">
                    @foreach($value['child'] as $child)
                    <li id="nav-menu-item-3344" class="sub-menu-item  menu-item-odd menu-item-depth-1 menu-item menu-item-type-post_type menu-item-object-page">
                        <a 
                            href="{{ $child->slug }}" 
                            class="menu-link sub-menu-link"
                            title="{{ $child->title }}"
                            target="{{ $child->target }}"
                            rel="{{ $child->robot }}"
                            >{{ $child->label }}</a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li> 
        @endforeach
    @endif

<li class="main-menu-item menu-item-even menu-item-depth-0 menu-item menu-item-type-post_type menu-item-object-page current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor menu-item-has-children" id="pfpostitemlink">
    <a class="menu-link main-menu-link" href="{{ route('account.add_ads') }}"><span class="pfadmicon-glyph-478"></span>{{ trans('widget/headermenu.post_an_item') }}</a>
</li>

</ul>