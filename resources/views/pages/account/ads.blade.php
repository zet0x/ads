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
					        <form id="pfuaprofileform" enctype="multipart/form-data" name="pfuaprofileform" method="GET" action=""><input type="hidden" value="myitems" name="ua">
					            <div class="pfsearchformerrors">
					                <ul></ul><a class="button pfsearch-err-button">CLOSE</a></div>
					            <div class="">
					                <div class="">
					                    <div class="row">
					                        <div class="pfmu-itemlisting-container pfmu-itemlisting-container-new">
					                            <section>
					                                <div class="row">
					                                    <div class="col1-5 first"><label for="listing-filter-status" class="lbl-ui select">
										                              <select id="listing-filter-status" name="listing-filter-status"><option value="">Status</option><option value="publish">Published</option><option value="pendingapproval">Pending Approval</option><option value="pendingpayment">Pending Payment</option><option value="rejected">Rejected</option>
										                              </select>
										                            </label></div>
					                                    <div class="col1-5 first"><label for="listing-filter-ltype" class="lbl-ui select">
										                              <select id="listing-filter-ltype" name="listing-filter-ltype">
										                                <option value="">Listing Types</option>
										                                <option value="14">For Rent</option><option value="15">For Sale</option>
										                              </select>
										                            </label></div>
					                                    <div class="col1-5"><label for="listing-filter-orderby" class="lbl-ui select">
										                              <select id="listing-filter-orderby" name="listing-filter-orderby"><option value="">Order By</option><option value="title">Title</option><option value="date">Date</option><option value="ID">ID</option>
										                              </select>
										                            </label></div>
					                                    <div class="col1-5"><label for="listing-filter-order" class="lbl-ui select">
										                              <select id="listing-filter-order" name="listing-filter-order"><option value="">Order</option><option value="ASC">ASC</option><option value="DESC">DESC</option>
										                              </select>
										                            </label></div>
					                                    <div class="col1-5 last"><button type="submit" value="" id="pf-ajax-itemrefine-button" class="button blue pfmyitempagebuttons" title="SEARCH"><i class="pfadmicon-glyph-627"></i></button><a class="button pfmyitempagebuttons" style="margin-left:4px;" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?page_id=4&amp;ua=myitems" title="RESET"><i class="pfadmicon-glyph-825"></i></a><a class="button pfmyitempagebuttons" style="margin-left:4px;" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?page_id=4&amp;ua=newitem" title="ADD NEW"><i class="pfadmicon-glyph-722"></i></a></div>
					                                </div>
					                            </section>
					                            
					                            <section>
					                            	@if(count($arResult) > 0)
					                            		<div class="pfhtitle pf-row clearfix hidden-xs">
						                                    <div class="pfmu-itemlisting-htitle pfmu-itemlisting-htitlenc col-lg-1 col-md-1 col-sm-2 hidden-xs"></div>
						                                    <div class="pfmu-itemlisting-htitle pfexhtitle col-lg-4 col-md-4 col-sm-4 hidden-xs">Information</div>
						                                    <div class="pfmu-itemlisting-htitle pfexhtitle col-lg-2 col-md-2 col-sm-2 hidden-xs">Listing Type</div>
						                                    <div class="pfmu-itemlisting-htitle pfexhtitle col-lg-2 col-md-2 col-sm-2 hidden-xs">Posted on</div>
						                                    <div class="pfmu-itemlisting-htitle col-lg-3 col-md-3 col-sm-2"></div>
						                                </div>
					                            		@foreach($arResult as $value)
							                            	<!--  START AD  -->
							                                <div class="pfmu-itemlisting-inner pfmu-itemlisting-inner1111 pf-row clearfix">
							                                    <div class="pfmu-itemlisting-inner-overlay pfmu-itemlisting-inner-overlay1111"></div>
							                                    <div class="pfmu-itemlisting-photo col-lg-1 col-md-1 col-sm-2 hidden-xs"><a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/3640110857_db80356eba_b.jpg" title="424/3 E Genesee St" rel="prettyPhoto"><img src="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/3640110857_db80356eba_b-60x60.jpg" alt=""></a></div>
							                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pfmu-itemlisting-title-wd">
							                                        <div class="pfmu-itemlisting-title"><a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?properties=4243-e-genesee-st">{{ $value->title }}</a></div>
							                                        <div class="pfmu-itemlisting-info pfmu-itemlisting-info-1111 pffirst" data-deactivatedt="Deactivated by user">
							                                            <ul class="pfiteminfolist">
							                                                <li><span class="pfiteminfolist-infotext lblcompleted"></span></li>
							                                            </ul>
							                                        </div>
							                                    </div>
							                                    <div class="pfmu-itemlisting-info pfflast col-lg-2 col-md-2 col-sm-2 hidden-xs">
							                                        <ul class="pfiteminfolist" style="padding-left:10px">
							                                            <li><strong><ul class="pointfinderpflistterms"><li><a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?pointfinderltypes=for-sale" rel="tag">For Sale</a></li></ul></strong></li>
							                                        </ul>
							                                    </div>
							                                    <div class="pfmu-itemlisting-info pfflast col-lg-2 col-md-2 col-sm-2 hidden-xs">
							                                        <ul class="pfiteminfolist" style="padding-left:10px">
							                                            <? 
							                                            $date = explode(' ', $value->updated_at)[0]; 
							                                            $date = explode('-',$date);
							                                            ?>
							                                            <li>{{ $date[2] }}.{{ $date[1] }}.{{ $date[0] }}</li>
							                                        </ul>
							                                    </div>
							                                    <div class="pfmu-itemlisting-footer col-lg-3 col-md-3 col-sm-2 col-xs-12">
							                                        <ul class="pfmu-userbuttonlist">
							                                            <li class="pfmu-userbuttonlist-item"><a class="button pf-delete-item-button wpf-transition-all pf-itemdelete-link" data-pid="1111" id="pf-delete-item-1111" title="Delete"><i class="pfadmicon-glyph-644"></i></a></li>
							                                            <li class="pfmu-userbuttonlist-item"><a class="button pf-view-item-button wpf-transition-all" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?properties=4243-e-genesee-st" title="View"><i class="pfadmicon-glyph-410"></i></a></li>
							                                            <li class="pfmu-userbuttonlist-item"><a class="button pf-edit-item-button wpf-transition-all" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?page_id=4&amp;ua=edititem&amp;i=1111" title="Edit"><i class="pfadmicon-glyph-685"></i></a></li>
							                                        </ul>
							                                    </div>
							                                </div>
							                                <div class="pf-listing-item-inner-addinfo">
							                                    <ul>
							                                        <li><span class="pfiteminfolist-title pfstatus-title pfreviews" title="Views"><i class="pfadmicon-glyph-729"></i></span><span class="pfiteminfolist-infotext pfreviews">1493</span></li>
							                                        <li><span data-pfid="1111" class="pfiteminfolist-title pfstatus-title pfstatusbuttonactive pfstatusbuttonaction" title="Your listing is active" data-pf-deactive="Your listing is deactive" data-pf-active="Your listing is active"><i class="pfadmicon-glyph-348"></i></span></li>
							                                    </ul>
							                                </div>
							                                <!-- END AD -->	
					                            		@endforeach
					                            	@else
					                            	<section><div class="notification warning" id="pfuaprofileform-notify-warning"><p>No record found!</p></div></section>
					                            	@endif
					                                

					                                
					                            </section>
					                            <!-- PGAINATION
					                            <div class="pfstatic_paginate">
					                                <ul class="page-numbers">
					                                    <li><span aria-current="page" class="page-numbers current">1</span></li>
					                                    <li><a class="page-numbers" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?page_id=4&amp;ua=myitems#038;ua=myitems&amp;paged=2">2</a></li>
					                                    <li><a class="page-numbers" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?page_id=4&amp;ua=myitems#038;ua=myitems&amp;paged=3">3</a></li>
					                                    <li><a class="next page-numbers" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?page_id=4&amp;ua=myitems#038;ua=myitems&amp;paged=2">Далее →</a></li>
					                                </ul>
					                            </div>
					                            -->
					                        </div>
					                    </div>
					                </div>
					            </div>
					            <div class="pfalign-right" style="background:transparent;background-color:transparent;display:none!important">
					                <section style="background:transparent;background-color:transparent;display:none!important">
					                    <input type="hidden" value="pf_refineitemlist" name="action">
					                    <input type="hidden" value="b151449c67" name="security">
					                </section>

					            </div>
					        </form>
					    </div>
					    <script type="text/javascript">
					        (function($) {
					            "use strict";

					        })(jQuery);
					    </script>
					</div>					
				</div>
			</div>
		</div>
	</section>
<!--  END ACCOUNR  --> 

@endsection