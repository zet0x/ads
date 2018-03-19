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
					        <form id="pfuaprofileform" enctype="multipart/form-data" name="pfuaprofileform" method="POST" action="">
					            <div class="pfsearchformerrors">
					                <ul></ul><a class="button pfsearch-err-button">CLOSE</a></div>
					            <div class="">
					                <div class="">
					                    <div class="row">
					                        <div class="pfmu-itemlisting-container">
					                            <section>
					                                <div class="row">
					                                    <div class="col3 first"><label for="listing-filter-ltype" class="lbl-ui select">
										                              <select id="listing-filter-ltype" name="listing-filter-ltype">
										                                <option value="">Listing Types</option>
										                                <option value="14">For Rent</option><option value="15">For Sale</option>
										                              </select>
										                            </label></div>
					                                    <div class="col3"><label for="listing-filter-orderby" class="lbl-ui select">
										                              <select id="listing-filter-orderby" name="listing-filter-orderby"><option value="">Order By</option><option value="title">Title</option><option value="date">Date</option><option value="ID">ID</option>
										                              </select>
										                            </label></div>
					                                    <div class="col3"><label for="listing-filter-order" class="lbl-ui select">
										                              <select id="listing-filter-order" name="listing-filter-order"><option value="">Order</option><option value="ASC">ASC</option><option value="DESC">DESC</option>
										                              </select>
										                            </label></div>
					                                    <div class="col3 last"><button type="submit" value="" id="pf-ajax-itemrefine-button" class="button blue pfmyitempagebuttons" title="SEARCH"><i class="pfadmicon-glyph-627"></i></button><a class="button pfmyitempagebuttons" style="margin-left:4px;" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?page_id=4&amp;ua=favorites" title="RESET"><i class="pfadmicon-glyph-825"></i></a></div>
					                                </div>
					                            </section>
					                            <section>
					                                <div class="pfhtitle pf-row clearfix hidden-xs">
					                                    <div class="pfmu-itemlisting-htitle pfmu-itemlisting-htitlenc col-lg-1 col-md-1 col-sm-2 hidden-xs"></div>
					                                    <div class="pfmu-itemlisting-htitle pfexhtitle col-lg-4 col-md-4 col-sm-4 hidden-xs">Information</div>
					                                    <div class="pfmu-itemlisting-htitle pfexhtitle col-lg-2 col-md-2 col-sm-2 hidden-xs">Listing Type</div>
					                                    <div class="pfmu-itemlisting-htitle pfexhtitle col-lg-2 col-md-2 col-sm-2 hidden-xs">Location</div>
					                                    <div class="pfmu-itemlisting-htitle pfmu-itemlisting-htitlenc col-lg-3 col-md-3 col-sm-2"></div>
					                                </div>
					                                <div class="pfmu-itemlisting-inner pf-row clearfix">
					                                    <div class="pfmu-itemlisting-photo col-lg-1 col-md-1 col-sm-2 hidden-xs"><a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/11534685133_6567e97643_b.jpg" title="424 E Genesee St" rel="prettyPhoto"><img src="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/wp-content/uploads/2014/11/11534685133_6567e97643_b-60x60.jpg" alt=""></a></div>
					                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pfmu-itemlisting-title-wd">
					                                        <div class="pfmu-itemlisting-title"><a href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?properties=424-e-genesee-st">424 E Genesee St</a></div>
					                                        <div class="pfmu-itemlisting-info pffirst">
					                                            <div class="pf-fav-listing-item"><span class="pf-ftitle">Beds: </span><span class="pf-ftext">2</span></div>
					                                            <div class="pf-fav-listing-item"><span class="pf-ftitle">Baths: </span><span class="pf-ftext">1</span></div>
					                                            <div class="pf-fav-listing-item"><span class="pf-ftitle">Size: </span><span class="pf-ftext">180sqm</span></div>
					                                        </div>
					                                    </div>
					                                    <div class="pfmu-itemlisting-info pfflast col-lg-2 col-md-2 col-sm-2 hidden-xs">
					                                        <ul class="pfiteminfolist" style="padding-left:10px">
					                                            <li>For Sale</li>
					                                        </ul>
					                                    </div>
					                                    <div class="pfmu-itemlisting-info pfflast col-lg-3 col-md-3 col-sm-2 hidden-xs">
					                                        <ul class="pfiteminfolist" style="padding-left:10px">
					                                            <li>New York</li>
					                                        </ul>
					                                    </div>
					                                    <div class="pfmu-itemlisting-footer col-lg-2 col-md-2 col-sm-2 col-xs-12">
					                                        <ul class="pfmu-userbuttonlist">
					                                            <li class="pfmu-userbuttonlist-item"><a class="button pf-delete-item-button wpf-transition-all pf-favorites-link" data-pf-num="1109" data-pf-active="true" data-pf-item="false" title="Remove from Favorites"><i class="pfadmicon-glyph-644"></i></a></li>
					                                            <li class="pfmu-userbuttonlist-item"><a class="button pf-view-item-button wpf-transition-all" href="http://wp1.dvurechensky48.1wd26.spectrum.myjino.ru/?properties=424-e-genesee-st" title="View"><i class="pfadmicon-glyph-410"></i></a></li>
					                                        </ul>
					                                    </div>
					                                </div>
					                            </section>
					                            <div class="pfstatic_paginate"></div>
					                        </div>
					                    </div>
					                </div>
					            </div>
					            <div class="pfalign-right" style="background:transparent;background-color:transparent;display:none!important">
					                <section style="background:transparent;background-color:transparent;display:none!important">
					                    <input type="hidden" value="pf_refinefavlist" name="action">
					                    <input type="hidden" value="7ef09ed3d6" name="security">
					                </section>

					            </div>
					        </form>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</section>
<!--  END ACCOUNR  --> 

@endsection