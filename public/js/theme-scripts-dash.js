(function($) {
	"use strict";

	/***************************************************************************************************************
	*
	*
	* USER DASHBOARD ACTIONS
	*
	*
	***************************************************************************************************************/
	
	/* Post Tag System STARTED */
		$('.pf-item-posttag a').live('click touchstart',function(){
			var selectedtag = $(this);
			var selectedtagicon = $(this).children('i');

			$.ajax({
		    	beforeSend:function(){
		    		selectedtagicon.switchClass('pfadmicon-glyph-644','pfadmicon-glyph-647');
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'pfget_posttag',
					id: $(this).data('pid'),
					pid: $(this).data('pid2'),
					lang: theme_scriptspfm.pfcurlang,
					security: theme_scriptspfm.pfget_posttag
				}
			}).success(function(obj) {
				if (obj == 1) {
					selectedtag.closest('.pf-item-posttag').remove();
				}
				
			}).complete(function(){

				selectedtagicon.switchClass('pfadmicon-glyph-647','pfadmicon-glyph-644');

			});
		});
	/* Post Tag System END */


	/* Map function STARTED */
		$.pf_submit_page_map = function(){

			var mapcontainer = $('#pfupload_map');
			var pf_lat = mapcontainer.data('pf-lat');
			var pf_lng = mapcontainer.data('pf-lng');
			var pf_type = mapcontainer.data('pf-type');
			var pf_zoom = mapcontainer.data('pf-zoom');
			var pf_istatus = mapcontainer.data('pf-istatus');
			

			if (!pf_istatus) {
				mapcontainer.gmap3({
				  map:{
					  options:{
						center:[pf_lat,pf_lng],
						zoom: pf_zoom, 
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						mapTypeControl: true,
						mapTypeControlOptions: {
				          style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
				          position: google.maps.ControlPosition.RIGHT_TOP
				        },
						zoomControl: true,
						zoomControlOptions: {
				          position: google.maps.ControlPosition.LEFT_BOTTOM
				        },
						panControl: false,
						scaleControl: false,
						navigationControl: false,
						draggable:true,
						scrollwheel: false,
						streetViewControl: false,
					  },
					  callback:function(map){
					  	
					  	$.pf_submit_page_map_fallback();
						console.log('Callback');
					  }
				  },
				  marker:{
				    values:[{
				    	latLng:[pf_lat,pf_lng],
				    }],
				    options:{
				      draggable: true
				    },
				    events:{
				    	dragend: function(marker){
				    		$('#pfupload_lat_coordinate').val(marker.getPosition().lat());
				    		$('#pfupload_lng_coordinate').val(marker.getPosition().lng());
				    	},
				    },
				  }

				});
				$('#pfupload_map').data("pf-istatus","true");
			}

			var pf_map = $('#pfupload_map').gmap3("get");

			if (pf_type == 'ROADMAP') {
				pf_map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
			} else if (pf_type == 'TERRAIN'){
				pf_map.setMapTypeId(google.maps.MapTypeId.TERRAIN);
			} else if (pf_type == 'SATELLITE'){
				pf_map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
			} else if (pf_type == 'HYBRID'){
				pf_map.setMapTypeId(google.maps.MapTypeId.HYBRID);
			}
		}

		$.pf_submit_page_map_fallback = function(){
			if ($(".pf-search-locatemebut").length && $("#pf_search_geolocateme").data("istatus") == false) {			
				$(".pf-search-locatemebut").svgInject();
				$("#pf_search_geolocateme").data("istatus","true");
			}

			var pf_map = $('#pfupload_map').gmap3("get");
			setTimeout(function(){
				console.log('AutoComplete');
				var pf_input = document.getElementById('pfupload_address');
				$("#pfupload_address").bind("keypress", function(e) {
				  if (e.keyCode == 13) {               
				    e.preventDefault();
				    return false;
				  }
				});
					
				var autocomplete = new google.maps.places.Autocomplete(pf_input);
				autocomplete.bindTo("bounds", pf_map);
					
				google.maps.event.addListener(autocomplete, "place_changed", function() {
					console.log('AutoCompleteAction');
				    var place = autocomplete.getPlace();
				    if (!place.geometry) {
				      return;
				    }
				    
				    if (place.geometry.viewport) {
				      pf_map.fitBounds(place.geometry.viewport);
				    } else {
				      pf_map.setCenter(place.geometry.location);
				      pf_map.setZoom(17);
				    }
			    	var marker = $('#pfupload_map').gmap3({get:"marker"});
			    	marker.setPosition(place.geometry.location);
					$("#pfupload_lat_coordinate").val(marker.getPosition().lat());
				    $("#pfupload_lng_coordinate").val(marker.getPosition().lng());
				});
			},1000);

		}

		$("#pf_search_geolocateme").on("click",function(){
			$(".pf-search-locatemebut").hide("fast"); 
			$(".pf-search-locatemebutloading").show("fast");
			$('#pfupload_map').gmap3({
				getgeoloc:{
					callback : function(latLng){
					  if (latLng){
						var geocoder = new google.maps.Geocoder();
						geocoder.geocode({"latLng": latLng}, function(results, status) {
						    if (status == google.maps.GeocoderStatus.OK) {
						      if (results[0]) {
						      	var map = $('#pfupload_map').gmap3("get");
						        map.setCenter(latLng);
						        map.setZoom(17);
						    	var marker =  $('#pfupload_map').gmap3({get:"marker"});
						    	marker.setPosition(latLng);

						        $("#pfupload_address").val(results[0].formatted_address);
						        $("#pfupload_lat_coordinate").val(latLng.lat());
		    					$("#pfupload_lng_coordinate").val(latLng.lng());
						      } 
						    }
						});

					  }
					  $(".pf-search-locatemebut").show("fast");
					  $(".pf-search-locatemebutloading").hide("fast");
					}
				  },
			});
			return false;
		});
	/* Map Function END */


	/* MEMBERSHIP PACKAGES STARTED */
		$('.pf-membership-splan-button a').on('click', function() {
			var packageid = $(this).data('id');
			var ptype = $(this).data('ptype');
			$.pfmembershipgetp(packageid,ptype);
		});

		$.pfmembershipgetp = function(packageid,ptype){

			$('.pfsubmit-inner-membership').hide( "fade");
			$('.pfsubmit-inner-membership-payment').show("fade");
			$('input[name="selectedpackageid"]').val(packageid)

			$.ajax({
				beforeSend:function(){
					$("#pf-ajax-s-button").attr("disabled", true);
					$('.pfm-payment-plans').pfLoadingOverlay({action:'show',message: theme_scriptspfm.buttonwait});
				},
	            type: 'POST',
	            dataType: 'html',
	            url: theme_scriptspf.ajaxurl,
	            data: { 
	                'action': 'pfget_membershippaymentsystem',
	                'ptype':ptype,
	                'pid': packageid,
	                'security': theme_scriptspfm.pfget_membershipsystem,
	                'lang': theme_scriptspfm.pfcurlang
	            },
	            success:function(data){
	            	$('.pfm-payment-plans').html(data);
	            },
	            error: function (request, status, error) {
	            	console.log(error);
	            },
	            complete: function(){
	            	$("#pf-ajax-purchasepack-button").attr("disabled", false);
	            	$("#pf-ajax-uploaditem-button").val(theme_scriptspfm.buttonwaitex2);
	            	$('.pfm-payment-plans').pfLoadingOverlay({action:'hide'});
	            },
	        });
			return false;
		};

		$('.pfsubmit-title-membershippack').on('click', function() {
			$('.pfsubmit-inner-membership').show("fade");
			$('.pfsubmit-inner-membership-payment').hide("fade");
		});

		$('.pfsubmit-title-membershippack-payment').on('click', function() {
			$('.pfsubmit-inner-membership').hide("fade");
			$('.pfsubmit-inner-membership-payment').show("fade");
		});

		// AJAX MEMBERSHIP PAYMENT PROCESS
		$("#pf-ajax-purchasepack-button").on("click touchstart",function(){

			var form = $("#pfuaprofileform");
			form.validate();
			
			if(!form.valid()){
				$.pfscrolltotop();
			}else{
				$("#pf-ajax-purchasepack-button").val(theme_scriptspfm.buttonwait);
				$.pfOpenMembershipModal('open','purchasepackage',form.serialize());
				return false;
			};
		});

		// AJAX MEMBERSHIP CANCEL RECURRING
		$('.pf-dash-cancelrecurring').live('click', function() {
			$.pfOpenMembershipModal('open','cancelrecurring','');
			return false;
		});
		
		$.pfOpenMembershipModal = function(status,modalname,formdata) {

			$.pfdialogstatus = '';
			
		    if(status == 'open'){
		    	
		    	if ($.pfdialogstatus == 'true') {$( "#pf-membersystem-dialog" ).dialog( "close" );}

		    	if (modalname == 'purchasepackage' || modalname == 'cancelrecurring') {
		    		$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect2});
		    	};
		    	
	    		var minwidthofdialog = 380;

	    		if(!$.pf_mobile_check()){ minwidthofdialog = 320;};
			
	    		$.ajax({
		            type: 'POST',
		            dataType: 'json',
		            url: theme_scriptspf.ajaxurl,
		            data: { 
		                'action': 'pfget_membershipsystem',
		                'formtype': modalname,
		                'dt': formdata,
		                'security': theme_scriptspfm.pfget_membershipsystem
		            },
		            success:function(data){
		            	
		            	var obj = [];
						$.each(data, function(index, element) {
							obj[index] = element;
						});

						

						if(obj.process == true){
							if (obj.processname == 'paypal'|| obj.processname == 'paypal2' ) {
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect});
								window.location = obj.returnurl;
							}else if (obj.processname == 'pags') {
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.generalredirect});
								window.location = obj.returnurl;
							}else if (obj.processname == 'ideal') {
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.generalredirect});
								window.location = obj.returnurl;
							}else if (obj.processname == 'payu') {
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.payumail);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);

								var payuForm = document.forms.payuForm;
							    payuForm.submit();

							}else if (obj.processname == 'robo') {
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.mes);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);

								var roboForm = document.forms.roboForm;
							    roboForm.submit();

							}else if(obj.processname == 'iyzico'){
								
								if (obj.iyzico_status == 'success') {
									$('.pointfinder-dialog').remove();
									$('.pf-membersystem-overlay').remove();
									$("#iyzipay-checkout-form").html(obj.iyzico_content);
								}else{
									setTimeout(function(){window.location = obj.returnurl;},1000);
								}

								$('body.pfdashboardpagenewedit').on('click','.iyzi-closeButton',function(){
									window.location = obj.returnurl;
								});
								$('body.pfdashboardpage').on('click','.iyzi-closeButton',function(){
									window.location = obj.returnurl;
								});
								$('.pf-overlay-close').click(function(){
									window.location = obj.returnurl;
								});

							}else if(obj.processname == 'stripe'){
								var handler = StripeCheckout.configure({
									key: obj.key,
									token: function(token) {
										$.pfOpenMembershipModal('open','stripepay',token);
									}
								});

								
								handler.open({
								  name: obj.name,
								  description: obj.description,
								  amount: obj.amount,
								  email: obj.email,
								  currency: obj.currency,
								  allowRememberMe: false,
								  opened:function(){
								  	$.pfOpenMembershipModal('close');
								  }
								});
								

								$(window).live('popstate', function() {
									handler.close();
								});
							}else if(obj.processname == 'stripepay'){
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.mes);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);

								window.location = theme_scriptspfm.dashurl;

							}else if(obj.processname == 'free' || obj.processname == 'trial'){

								if (obj.process == true) {
									$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect4});
									window.location = theme_scriptspfm.dashurl;
								}else{
									$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect});
									$("#pf-membersystem-dialog").html(obj.mes);

									var pfreviewoverlay = $("#pfmdcontainer-overlay");
									pfreviewoverlay.show("slide",{direction : "up"},100);
								};
							}else if(obj.processname == 'bank'){

								if (obj.process == true) {
									$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect4});
									window.location = obj.returnurl;
								}else{
									$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect});
									$("#pf-membersystem-dialog").html(obj.mes);

									var pfreviewoverlay = $("#pfmdcontainer-overlay");
									pfreviewoverlay.show("slide",{direction : "up"},100);
								};
							}else if(obj.processname == 'cancelrecurring'){
								if (obj.process == true) {
									$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect4});
									setTimeout(function(){
										window.location = theme_scriptspfm.dashurl;
									},2000);
								}else{
									$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect});
									$("#pf-membersystem-dialog").html(obj.mes);

									var pfreviewoverlay = $("#pfmdcontainer-overlay");
									pfreviewoverlay.show("slide",{direction : "up"},100);
								};
							};

						}else{

							$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
							$("#pf-membersystem-dialog").html(obj.mes);

							var pfreviewoverlay = $("#pfmdcontainer-overlay");
							pfreviewoverlay.show("slide",{direction : "up"},100);

							if(modalname == 'payu'){setTimeout(function(){window.location = obj.returnurl;},1000);}
						}

						$('.pf-overlay-close').click(function(){
							$.pfOpenMembershipModal('close');
						});


		            },
		            error: function (request, status, error) {
		            	
	                	$("#pf-membersystem-dialog").html('Error:'+request.responseText);
		            	
		            },
		            complete: function(){
	            		$("#pf-membersystem-dialog").dialog({position:{my: "center", at: "center",collision:"fit"}});
		            	$('.pointfinder-dialog').center(true);
		            },
		        });
			
	        	if(modalname != ''){
			    $("#pf-membersystem-dialog").dialog({
			    	closeOnEscape: false,
			        resizable: false,
			        modal: true,
			        minWidth: minwidthofdialog,
			        show: { effect: "fade", duration: 100 },
			        dialogClass: 'pointfinder-dialog',
			        open: function() {
				        $('.ui-widget-overlay').addClass('pf-membersystem-overlay');
				        $('.ui-widget-overlay').click(function(e) {
						    e.preventDefault();
						    return false;
						});
				    },
				    close: function() {
				        $('.ui-widget-overlay').removeClass('pf-membersystem-overlay');
				    },
				    position:{my: "center", at: "center",collision:"fit"}
			    });
			    $.pfdialogstatus = 'true';
				}

			}else{
				$( "#pf-membersystem-dialog" ).dialog( "close" );
				$.pfdialogstatus = '';
			}
		};
	/* MEMBERSHIP PACKAGES END */


	/* AJAX PAYMENT MODAL STARTED */
		$('.pfbuttonpaymentb').on('click',function(){
			var selectedval = $(this).parent().prev().find('select option:selected').val();
			var itemnum = $(this).data('pfitemnum');

			if(selectedval == 'creditcard'){

				$.pfOpenPaymentModal('open','creditcardstripe',itemnum,'');

			}else if(selectedval == 'paypal'){

				$.pfOpenPaymentModal('open','paypalrequest',itemnum,'');

			}else if(selectedval == 'pags'){

				$.pfOpenPaymentModal('open','pags',itemnum,'');

			}else if(selectedval == 'ideal'){

				$.pfOpenPaymentModal('open','ideal',itemnum,'');


			}else if(selectedval == 'payu'){

				$.pfOpenPaymentModal('open','payu',itemnum,'');

			}else if(selectedval == 'robo'){

				$.pfOpenPaymentModal('open','robo',itemnum,'');

			}else if(selectedval == 'iyzico'){

				$.pfOpenPaymentModal('open','iyzico',itemnum,'');

			}else{

				window.location = selectedval;

			};
				

			return false;
		});

		$.pfOpenPaymentModal = function(status,modalname,itemid,token,otype) {


			$.pfdialogstatus = '';
			
		    if(status == 'open'){
		    	if ($.pfdialogstatus == 'true') {$( "#pf-membersystem-dialog" ).dialog( "close" );}

		    	if (modalname == 'creditcardstripe') {
		    		$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect2});
		    	}else if(modalname == 'paypalrequest'){
		    		$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect});
		    	}else if(modalname == 'pags' || modalname == 'payu' || modalname == 'robo'){
		    		$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.generalredirect});
		    	}else if(modalname == 'stripepayment' || modalname == 'iyzico'){
		    		$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect3});
		    	};
		    	
	    		var minwidthofdialog = 380;

	    		if(!$.pf_mobile_check()){ minwidthofdialog = 320;};
			
	    		$.ajax({
		            type: 'POST',
		            dataType: 'json',
		            url: theme_scriptspf.ajaxurl,
		            data: { 
		                'action': 'pfget_paymentsystem',
		                'formtype': modalname,
		                'itemid': itemid,
		                'otype':otype,
		                'token': token,
		                'security': theme_scriptspfm.pfget_paymentsystem
		            },
		            success:function(data){
		            	
		            	var obj = [];
						$.each(data, function(index, element) {
							obj[index] = element;
						});

						

						if(obj.process == true){
							if (modalname == 'paypalrequest' || modalname == 'pags' || modalname == 'ideal') {
								window.location = obj.returnurl;
							}else if(modalname == 'payu'){

								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.payumail);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);

								var payuForm = document.forms.payuForm;
							    payuForm.submit();
							}else if(modalname == 'iyzico'){
								if (obj.iyzico_status == 'success') {
									$('.pointfinder-dialog').remove();
									$('.pf-membersystem-overlay').remove();
									$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
									$("#iyzipay-checkout-form").html(obj.iyzico_content);
								}else{
									setTimeout(function(){window.location = obj.returnurl;},1000);
								}

								$('body.pfdashboardpagenewedit').on('click','.iyzi-closeButton',function(){
									window.location = obj.returnurl;
								});
								$('body.pfdashboardpage').on('click','.iyzi-closeButton',function(){
									window.location = obj.returnurl;
								});
								$('.pf-overlay-close').click(function(){
									window.location = obj.returnurl;
								});

								$('body').on('click', '.iyzi-closeButton', function(event) {
									window.location = obj.returnurl;
								});
								
							}else if(modalname == 'robo'){

								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.mes);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);

								var roboForm = document.forms.roboForm;
							    roboForm.submit();


							}else if(modalname == 'creditcardstripe'){
								var handler = StripeCheckout.configure({
									key: obj.key,
									token: function(token) {
										$.pfOpenPaymentModal('open','stripepayment',itemid,token,obj.otype);
									}
								});

								
								handler.open({
								  name: obj.name,
								  description: obj.description,
								  amount: obj.amount,
								  email: obj.email,
								  currency: obj.currency,
								  allowRememberMe: false,
								  opened:function(){
								  	$.pfOpenPaymentModal('close');
								  },
								  closed:function(){
								  	if ($('#pfupload_type').val() == 1) {
								  		setTimeout(function(){window.location = theme_scriptspfm.dashurl2;},2000);
								  	};
								  }
								});
								

								$(window).live('popstate', function() {
									handler.close();
								});
							}else if(modalname == 'stripepayment'){
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.mes);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);
								
								setTimeout(function(){window.location = obj.returnurl;},2000);
							};

						}else{

							$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
							$("#pf-membersystem-dialog").html(obj.mes);

							var pfreviewoverlay = $("#pfmdcontainer-overlay");
							pfreviewoverlay.show("slide",{direction : "up"},100);

							if(modalname == 'payu'){setTimeout(function(){window.location = obj.returnurl;},1000);}
						}

						$('.pf-overlay-close').click(function(){
							$.pfOpenPaymentModal('close');
						});


		            },
		            error: function (request, status, error) {
		            	
	                	$("#pf-membersystem-dialog").html('Error:'+request.responseText);
		            	
		            },
		            complete: function(){
		            	if (modalname != 'iyzico') {
		            		$("#pf-membersystem-dialog").dialog({position:{my: "center", at: "center",collision:"fit"}});
		            		$('.pointfinder-dialog').center(true);
		            	}
	            		
		            },
		        });
			
	        	if(modalname != '' && modalname != 'iyzico'){
				    $("#pf-membersystem-dialog").dialog({
				    	closeOnEscape: false,
				        resizable: false,
				        modal: true,
				        minWidth: minwidthofdialog,
				        show: { effect: "fade", duration: 100 },
				        dialogClass: 'pointfinder-dialog',
				        open: function() {
					        $('.ui-widget-overlay').addClass('pf-membersystem-overlay');
					        $('.ui-widget-overlay').click(function(e) {
							    e.preventDefault();
							    return false;
							});
					    },
					    close: function() {
					        $('.ui-widget-overlay').removeClass('pf-membersystem-overlay');
					    },
					    position:{my: "center", at: "center",collision:"fit"}
				    });
				    $.pfdialogstatus = 'true';
				}

			}else{
				$( "#pf-membersystem-dialog" ).dialog( "close" );
				$.pfdialogstatus = '';
			}
		};
	/* AJAX PAYMENT MODAL END */

	/* LISTING PACK PAYMENTS STARTED */
		$('.pfpackselector').change(function(){
			$.pf_get_priceoutput(1);
		});

		$('#featureditembox').on('change',function(){
			$.pf_get_priceoutput();
		});

		$.pf_get_priceoutput = function(pcs){
			if ($('#pfupload_type').val() == 1) {
				
				var listing_category = $('input.pflistingtypeselector:checked').val();
				var listing_pack = $('input.pfpackselector:checked').val();
				var listing_featured = ($('#featureditembox').attr('checked'))? 1:0;

				var status_c = $('#pfupload_c').val();
				var status_f = $('#pfupload_f').val();
				var status_p = $('#pfupload_p').val();
				var status_o = $('#pfupload_o').val();
				var status_px = $('#pfupload_px').val();

				if (status_c == 1) {listing_category = '';};
				if (status_f == 1) {listing_featured = '';};
				if (listing_pack == status_p) {listing_pack = '';};
				
				$.ajax({
			    	beforeSend:function(){	
			    		if (pcs == 1) {$('.pflistingtype-selector-main-top').pfLoadingOverlay({action:'show'})};
			    		$('.pfsubmit-inner-payment .pfsubmit-inner-sub').pfLoadingOverlay({action:'show'});
			    		$("#pf-ajax-uploaditem-button").val(theme_scriptspfm.buttonwait);
						$("#pf-ajax-uploaditem-button").attr("disabled", true);
			    	},
					url: theme_scriptspf.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'pfget_listingpaymentsystem',
						c:listing_category,
						p:listing_pack,
						f:listing_featured,
						o:status_o,
						px:status_px,
						lang: theme_scriptspfm.pfcurlang,
						security: theme_scriptspfm.pfget_lprice
					},
				}).success(function(obj) {
					
					if (obj) {
						if (obj.totalpr != 0) {
							$('.pfsubmit-inner-totalcost-output').html(obj.html);
							$('.pfsubmit-inner-payment').show();
						}else{
							$('.pfsubmit-inner-totalcost-output').html(obj.html);
							$('.pfsubmit-inner-payment').hide();
						}
						
					};
					
				}).complete(function(){
					$("#pf-ajax-uploaditem-button").attr("disabled", false);
					$("#pf-ajax-uploaditem-button").val(theme_scriptspfm.buttonwaitex2);
					$('.pfsubmit-inner-payment .pfsubmit-inner-sub').pfLoadingOverlay({action:'hide'});
					if (pcs == 1) {$('.pflistingtype-selector-main-top').pfLoadingOverlay({action:'hide'})};
				});
			};
		}
	/* LISTING PACK PAYMENTS END */


	/* PROFILE UPDATE FUNCTION STARTED */
		$('#pf-ajax-profileupdate-button').on('click touchstart',function(){

			var form = $('#pfuaprofileform');
			var pfsearchformerrors = form.find(".pfsearchformerrors");
			if ($.isEmptyObject($.pfAjaxUserSystemVars4)) {

				$.pfAjaxUserSystemVars4 = {};
				$.pfAjaxUserSystemVars4.email_err = 'Please write an email';
				$.pfAjaxUserSystemVars4.email_err2 = 'Your email address must be in the format of name@domain.com';
				$.pfAjaxUserSystemVars4.nickname_err = 'Please write nickname';
				$.pfAjaxUserSystemVars4.nickname_err2 = 'Please enter at least 3 characters for nickname.';
				$.pfAjaxUserSystemVars4.passwd_err = $.validator.format("Enter at least {0} characters");
				$.pfAjaxUserSystemVars4.passwd_err2 = "Enter the same password as above";
			}

			form.validate({
				  debug:false,
				  onfocus: false,
				  onfocusout: false,
				  onkeyup: false,
				  rules:{
				    nickname:{
				      required: true,
				      minlength: 3
				    },
				    password: {
						minlength: 7
					},
					password2: {
						minlength: 7,
						equalTo: "#password"
					},
				  	email:{
				  		required:true,
				  		email:true
				  	}
				  },
				  messages:{
				  	nickname:{
					  	required:$.pfAjaxUserSystemVars4.nickname_err,
					  	minlength:$.pfAjaxUserSystemVars4.nickname_err2
				  	},
				  	password: {
						rangelength: $.pfAjaxUserSystemVars4.passwd_err
					},
					password2: {
						minlength: $.pfAjaxUserSystemVars4.passwd_err,
						equalTo: $.pfAjaxUserSystemVars4.passwd_err2
					},
				  	email: {
					    required: $.pfAjaxUserSystemVars4.email_err,
					    email: $.pfAjaxUserSystemVars4.email_err2
				    }
				  },
				  validClass: "pfvalid",
				  errorClass: "pfnotvalid pfadmicon-glyph-858",
				  errorElement: "li",
				  errorContainer: pfsearchformerrors,
				  errorLabelContainer: $("ul", pfsearchformerrors),
				  invalidHandler: function(event, validator) {
					var errors = validator.numberOfInvalids();
					if (errors) {
						$.pfscrolltotop();
						pfsearchformerrors.show("slide",{direction : "up"},100);
						form.find(".pfsearch-err-button").click(function(){
							pfsearchformerrors.hide("slide",{direction : "up"},100);
							return false;
						});
					}else{
						pfsearchformerrors.hide("fade",300);
					}
				  }
			});
			
			
			if(!form.valid()){	
				$.pfscrolltotop();		
				return false;
			};
		});
	/* PROFILE UPDATE FUNCTION END */


	/* MOBILEDROPDOWNS  FUNCTION STARTED  */
		$(function(){
			if (theme_scriptspf.mobiledropdowns == 1 && !$.pf_tablet_check()) {
				$("#pfupload_itemtypes").select2("destroy");
				$("#pfupload_sublistingtypes").select2("destroy");
				$("#pfupload_subsublistingtypes").select2("destroy");
				$("#pflocationselector").select2("destroy");
				$("#pfupload_locations").select2("destroy");
			};
		});
	/* MOBILEDROPDOWNS  FUNCTION END  */



	/* FEATURES  FUNCTION STARTED  */
		$.pf_get_modules_now = function(itemid){
			var postid = $('#pfupload_listingpid').val();

			$.ajax({
		    	beforeSend:function(){
		    		$('.pfsubmit-inner-features').pfLoadingOverlay({action:'show'});
		    		$('.pfsubmit-inner-customfields').pfLoadingOverlay({action:'show'});
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'pfget_featuresystem',
					id: itemid,
					postid:postid,
					lang: theme_scriptspfm.pfcurlang,
					security: theme_scriptspfm.pfget_featuresystem
				},
			}).done(function(obj) {


				if (obj.features == null || obj.features == '' || obj.features == 'undefined') {
					$('.pfsubmit-inner-features').hide();
					$('.pfsubmit-inner-features-title').hide();
					$('#pfupload_itemtypes').rules('remove');
				} else {
					$('.pfsubmit-inner-features').html(obj.features);
					$('.pfsubmit-inner-features').show();
					$('.pfsubmit-inner-features-title').show();

					if ($('input[name="pointfinderfeaturecount"]').val() == 0) {
						$('.pfsubmit-inner-features').hide();
						$('.pfsubmit-inner-features-title').hide();
					}else{
						$('.pfsubmit-inner-features').show();
						$('.pfsubmit-inner-features-title').show();
					}

					$('.pfitemdetailcheckall').on('click',function(event) {
						$.each($('[name="pffeature[]"]'), function(index, val) {
							 $(this).attr('checked', true);
						});
					});

					$('.pfitemdetailuncheckall').on('click',function(event) {
						$.each($('[name="pffeature[]"]'), function(index, val) {
							 $(this).attr('checked', false);
						});
					});
				}



				if (obj.itemtypes == null || obj.itemtypes == '' || obj.itemtypes == 'undefined') {
					$('.pfsubmit-inner-sub-itype').hide();
				}else{
					$('.pfsubmit-inner.pfsubmit-inner-sub-itype').html(obj.itemtypes);
					$('.pfsubmit-inner-sub-itype').show();
				}



				if (obj.conditions == null || obj.conditions == '' || obj.conditions == 'undefined') {
					$('.pfsubmit-inner-sub-conditions').hide();
				}else{
					$('.pfsubmit-inner.pfsubmit-inner-sub-conditions').html(obj.conditions);
					$('.pfsubmit-inner-sub-conditions').show();
				}

				if (obj.customfields == null || obj.customfields == '' || obj.customfields == 'undefined') {
					$('.pfsubmit-inner-customfields').hide();
					$('.pfsubmit-inner-customfields-title').hide();
				}else{
					$('.pfsubmit-inner-customfields').html(obj.customfields);
					$('.pfsubmit-inner-customfields').show();
					$('.pfsubmit-inner-customfields-title').show();
				}

				if (obj.eventdetails == null || obj.eventdetails == '' || obj.eventdetails == 'undefined') {
					$('.eventdetails-output-container').hide();
				}else{
					$('.eventdetails-output-container').html(obj.eventdetails);
					$('.eventdetails-output-container').show();
				}


				if (obj.customtabs == null || obj.customtabs == '' || obj.customtabs == 'undefined') {
					$('.customtab-output-container').hide();
				}else{
					$('.customtab-output-container').html(obj.customtabs);
					$('.customtab-output-container').show();

					
					setTimeout(function(){
						if($('textarea[name="webbupointfinder_item_custombox1"]').hasClass('textareaadv')){
							if ( typeof( tinyMCE) != "undefined" ) {
								tinyMCE.execCommand( 'mceRemoveEditor', true, 'webbupointfinder_item_custombox1' );
								tinyMCE.execCommand( 'mceAddEditor', true, 'webbupointfinder_item_custombox1' );
							}
							$('textarea[name="webbupointfinder_item_custombox1"]').hide();

						}

						if($('textarea[name="webbupointfinder_item_custombox2"]').hasClass('textareaadv')){
							if ( typeof( tinyMCE) != "undefined" ) {
								tinyMCE.execCommand( 'mceRemoveEditor', true, 'webbupointfinder_item_custombox2' );
								tinyMCE.execCommand( 'mceAddEditor', true, 'webbupointfinder_item_custombox2' );
							}
							$('textarea[name="webbupointfinder_item_custombox2"]').hide();
						}

						if($('textarea[name="webbupointfinder_item_custombox3"]').hasClass('textareaadv')){
							if ( typeof( tinyMCE) != "undefined" ) {
								tinyMCE.execCommand( 'mceRemoveEditor', true, 'webbupointfinder_item_custombox3' );
								tinyMCE.execCommand( 'mceAddEditor', true, 'webbupointfinder_item_custombox3' );
							}
							$('textarea[name="webbupointfinder_item_custombox3"]').hide();
						}

						if($('textarea[name="webbupointfinder_item_custombox4"]').hasClass('textareaadv')){
							if ( typeof( tinyMCE) != "undefined" ) {
								tinyMCE.execCommand( 'mceRemoveEditor', true, 'webbupointfinder_item_custombox4' );
								tinyMCE.execCommand( 'mceAddEditor', true, 'webbupointfinder_item_custombox4' );
							}
							$('textarea[name="webbupointfinder_item_custombox4"]').hide();
						}

						if($('textarea[name="webbupointfinder_item_custombox5"]').hasClass('textareaadv')){
							if ( typeof( tinyMCE) != "undefined" ) {
								tinyMCE.execCommand( 'mceRemoveEditor', true, 'webbupointfinder_item_custombox5' );
								tinyMCE.execCommand( 'mceAddEditor', true, 'webbupointfinder_item_custombox5' );
							}
							$('textarea[name="webbupointfinder_item_custombox5"]').hide();
						}

						if($('textarea[name="webbupointfinder_item_custombox6"]').hasClass('textareaadv')){
							if ( typeof( tinyMCE) != "undefined" ) {
								tinyMCE.execCommand( 'mceRemoveEditor', true, 'webbupointfinder_item_custombox6' );
								tinyMCE.execCommand( 'mceAddEditor', true, 'webbupointfinder_item_custombox6' );
							}
							$('textarea[name="webbupointfinder_item_custombox6"]').hide();
						}
					},0);
				}



				if (obj.video == null || obj.video == '' || obj.video == 'undefined') {
					$('.pfvideotab-output-container').hide();
				}else{
					$('.pfvideotab-output-container').html(obj.video);
					$('.pfvideotab-output-container').show();
				}



				if (obj.ohours == null || obj.ohours == '' || obj.ohours == 'undefined') {
					$('.openinghourstab-output-container').hide();
				}else{
					$('.openinghourstab-output-container').html(obj.ohours);
					$('.openinghourstab-output-container').show();
				}


				$('.pfsubmit-inner-customfields').pfLoadingOverlay({action:'hide'});
				$('.pfsubmit-inner-features').pfLoadingOverlay({action:'hide'});

			}).complete(function(obj) {
				$('.pf-excludecategory-container').show();
			}).error(function(jqXHR,textStatus,errorThrown){
				console.log(errorThrown);
			});
		}
	/* FEATURES FUNCTION END  */


	/* ITEM ADD/UPDATE FUNCTION STARTED  */
		$("#pf-ajax-uploaditem-button").on("click touchstart",function(){

			var form = $("#pfuaprofileform");
			
			/*if($('#item_desc').hasClass('textarea')){
				tinyMCE.triggerSave();
			}*/
			if($('textarea[name="item_desc"]').hasClass('textarea')){
				if ( typeof( tinyMCE) != "undefined" ) {
					$('textarea[name="item_desc"]').html( tinyMCE.get('item_desc').getContent() );
				}
			}

			if($('textarea[name="webbupointfinder_item_custombox1"]').hasClass('textareaadv')){
				if ( typeof( tinyMCE) != "undefined" ) {
					$('textarea[name="webbupointfinder_item_custombox1"]').html( tinyMCE.get('webbupointfinder_item_custombox1').getContent() );
				}
			}

			if($('textarea[name="webbupointfinder_item_custombox2"]').hasClass('textareaadv')){
				if ( typeof( tinyMCE) != "undefined" ) {
					$('textarea[name="webbupointfinder_item_custombox2"]').html( tinyMCE.get('webbupointfinder_item_custombox2').getContent() );
				}
			}

			if($('textarea[name="webbupointfinder_item_custombox3"]').hasClass('textareaadv')){
				if ( typeof( tinyMCE) != "undefined" ) {
					$('textarea[name="webbupointfinder_item_custombox3"]').html( tinyMCE.get('webbupointfinder_item_custombox3').getContent() );
				}
			}

			if($('textarea[name="webbupointfinder_item_custombox4"]').hasClass('textareaadv')){
				if ( typeof( tinyMCE) != "undefined" ) {
					$('textarea[name="webbupointfinder_item_custombox4"]').html( tinyMCE.get('webbupointfinder_item_custombox4').getContent() );
				}
			}

			if($('textarea[name="webbupointfinder_item_custombox5"]').hasClass('textareaadv')){
				if ( typeof( tinyMCE) != "undefined" ) {
					$('textarea[name="webbupointfinder_item_custombox5"]').html( tinyMCE.get('webbupointfinder_item_custombox5').getContent() );
				}
			}

			if($('textarea[name="webbupointfinder_item_custombox6"]').hasClass('textareaadv')){
				if ( typeof( tinyMCE) != "undefined" ) {
					$('textarea[name="webbupointfinder_item_custombox6"]').html( tinyMCE.get('webbupointfinder_item_custombox6').getContent() );
				}
			}

			form.validate();
			
			if(!form.valid()){
				/*Extra classes for image and listing type*/
					if ($('#pfupload_listingtypes').hasClass('pfnotvalid')) {
						$('.pfsubmit-inner-listingtype').addClass('pfnotvalid');
					}else{
						$('.pfsubmit-inner-listingtype').removeClass('pfnotvalid');
					};
					if ($('#pfupload_locations').hasClass('pfnotvalid')) {
						$('.pfsubmit-location-errc').addClass('pfnotvalid');
					}else{
						$('.pfsubmit-location-errc').removeClass('pfnotvalid');
					};
					if ($('.pfuploadimagesrc').hasClass('pfnotvalid')) {
						$('.pfitemimgcontainer').addClass('pfnotvalid');
					}else{
						$('.pfitemimgcontainer').removeClass('pfnotvalid');
					};
					if ($('#pfuploadfilesrc').hasClass('pfnotvalid')) {
						$('.pfitemfilecontainer').addClass('pfnotvalid');
					}else{
						$('.pfitemfilecontainer').removeClass('pfnotvalid');
					};

					if ($('#pfupload_sublistingtypes').hasClass('pfnotvalid')) {
						$('#s2id_pfupload_sublistingtypes input').addClass('pfnotvalid');
					}else{
						$('#s2id_pfupload_sublistingtypes input').removeClass('pfnotvalid');
					};

					if ($('#pfupload_subsublistingtypes').hasClass('pfnotvalid')) {
						$('#s2id_pfupload_subsublistingtypes input').addClass('pfnotvalid');
					}else{
						$('#s2id_pfupload_subsublistingtypes input').removeClass('pfnotvalid');
					};

					if ($('#pfupload_address').hasClass('pfnotvalid') || $('#pfupload_lat_coordinate').hasClass('pfnotvalid') || $('#pfupload_lng_coordinate').hasClass('pfnotvalid')) {
						$('.pfsubmit-address-errc').addClass('pfnotvalid');
						$('#pfupload_address').removeClass('pfnotvalid');
						$('#pfupload_lat_coordinate').removeClass('pfnotvalid');
						$('#pfupload_lng_coordinate').removeClass('pfnotvalid');
					}else{
						$('.pfsubmit-address-errc').removeClass('pfnotvalid');
					};

					
					if ($('#item_desc').hasClass('pfnotvalid')) {
						if ( typeof( tinyMCE) != "undefined" ) {
							tinymce.activeEditor.contentDocument.body.style.backgroundColor = '#F0D7D7';
						}
					}else{
						if ( typeof( tinyMCE) != "undefined" ) {
							if($('#item_desc').hasClass('textarea')){
								tinymce.activeEditor.contentDocument.body.style.backgroundColor = '#ffffff'
							}
						}
					};


				$.pfscrolltotop();
				return false;
			}else{
				$("#pf-ajax-uploaditem-button").val(theme_scriptspfm.buttonwait);
				$("#pf-ajax-uploaditem-button").attr("disabled", true);
				//form.submit();
				if ($("#pf-ajax-uploaditem-button").data('edit') > 0) {
					$.pfOpenItemUpEditModal('open','edit',form.serialize());
				}else{
					$.pfOpenItemUpEditModal('open','upload',form.serialize());
				};
				
				return false;
			};
		});

		/*Delete Item*/
		$(".pf-itemdelete-link").on("click touchstart",function(){
			if (confirm(theme_scriptspfm.delmsg)) {
				$.pfOpenItemUpEditModal('open','delete',$(this).data('pid'));
			};	
		});


		$.pfOpenItemUpEditModal = function(status,modalname,formdata) {
			$.pfdialogstatus = '';
			
		    if(status == 'open'){
		    	
		    	if ($.pfdialogstatus == 'true') {$( "#pf-membersystem-dialog" ).dialog( "close" );}

		    	$('#pf-membersystem-dialog').pfLoadingOverlay({action:'show',message: theme_scriptspfm.paypalredirect2});
		    	
	    		var minwidthofdialog = 380;

	    		if(!$.pf_mobile_check()){ minwidthofdialog = 320;};
			
	    		$.ajax({
		            type: 'POST',
		            dataType: 'json',
		            url: theme_scriptspf.ajaxurl,
		            data: { 
		                'action': 'pfget_itemsystem',
		                'formtype': modalname,
		                'dt': formdata,
		                'lang': theme_scriptspfm.pfcurlang,
		                'security': theme_scriptspfm.pfget_itemsystem
		            },
		            success:function(data){
		            	
		            	var obj = [];
						$.each(data, function(index, element) {
							obj[index] = element;
						});

						if(obj.process == true){

							if(obj.processname == 'upload' || obj.processname == 'edit'){
								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.mes);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);
								
								if (obj.returnval.ppps != '') {

									if (obj.processname == 'edit' && obj.returnval.pppso == 1) {
										var otype = 1;
									}else{
										var otype = 0;
									}

									if (obj.returnval.ppps == 'paypal') {
										$.pfOpenPaymentModal('open','paypalrequest',obj.returnval.post_id,'',otype);
									}else if(obj.returnval.ppps == 'pags'){
										$.pfOpenPaymentModal('open','pags',obj.returnval.post_id,'',otype);
									}else if(obj.returnval.ppps == 'iyzico'){
										$.pfOpenPaymentModal('open','iyzico',obj.returnval.post_id,'',otype);
									}else if(obj.returnval.ppps == 'ideal'){
										$.pfOpenPaymentModal('open','ideal',obj.returnval.post_id,obj.returnval.issuer,otype);
									}else if(obj.returnval.ppps == 'payu'){
										$.pfOpenPaymentModal('open','payu',obj.returnval.post_id,'',otype);
									}else if(obj.returnval.ppps == 'robo'){
										$.pfOpenPaymentModal('open','robo',obj.returnval.post_id,'',otype);
									}else if(obj.returnval.ppps == 'stripe'){
										$.pfOpenPaymentModal('open','creditcardstripe',obj.returnval.post_id,'',otype);
									}else if(obj.returnval.ppps == 'bank'){
											setTimeout(function(){window.location = obj.returnval.pppsru;},2000);
									}else if(obj.returnval.ppps == 'free'){
										if (obj.processname == 'edit') {
											setTimeout(function(){window.location = obj.returnurl;},3500);
										}else{
											setTimeout(function(){window.location = obj.returnurl;},2000);
										};
									};
								}else{
									if (obj.processname == 'edit') {
										setTimeout(function(){window.location = obj.returnurl;},3500);
									}else{
										setTimeout(function(){window.location = obj.returnurl;},2000);
									};
								};
							}else if(obj.processname == 'delete'){

								$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
								$("#pf-membersystem-dialog").html(obj.mes);

								var pfreviewoverlay = $("#pfmdcontainer-overlay");
								pfreviewoverlay.show("slide",{direction : "up"},100);

								setTimeout(function(){window.location = obj.returnurl;},2000);
							};

						}else{

							$('#pf-membersystem-dialog').pfLoadingOverlay({action:'hide'});
							$("#pf-membersystem-dialog").html(obj.mes);

							var pfreviewoverlay = $("#pfmdcontainer-overlay");
							pfreviewoverlay.show("slide",{direction : "up"},100);

							$("#pf-ajax-uploaditem-button").val(theme_scriptspfm.buttonwaitex);
							$("#pf-ajax-uploaditem-button").attr("disabled", false);
						}

						$('.pf-overlay-close').click(function(){
							$.pfOpenMembershipModal('close');
						});


		            },
		            error: function (request, status, error) {
		            	
	                	$("#pf-membersystem-dialog").html('Error:'+request.responseText);
		            	
		            },
		            complete: function(){
	            		$("#pf-membersystem-dialog").dialog({position:{my: "center", at: "center",collision:"fit"}});
		            	$('.pointfinder-dialog').center(true);
		            },
		        });
			
	        	if(modalname != ''){
			    $("#pf-membersystem-dialog").dialog({
			    	closeOnEscape: false,
			        resizable: false,
			        modal: true,
			        minWidth: minwidthofdialog,
			        show: { effect: "fade", duration: 100 },
			        dialogClass: 'pointfinder-dialog',
			        open: function() {
				        $('.ui-widget-overlay').addClass('pf-membersystem-overlay');
				        $('.ui-widget-overlay').click(function(e) {
						    e.preventDefault();
						    return false;
						});
				    },
				    close: function() {
				        $('.ui-widget-overlay').removeClass('pf-membersystem-overlay');
				    },
				    position:{my: "center", at: "center",collision:"fit"}
			    });
			    $.pfdialogstatus = 'true';
				}

			}else{
				$( "#pf-membersystem-dialog" ).dialog( "close" );
				$.pfdialogstatus = '';
			}
		};
	/* ITEM ADD/UPDATE FUNCTION END  */

	/* IMAGE AND FILE UPLOAD STARTED */

		/* Delete Photo */
		$(".pf-delete-standartimg").live("click", function(){
			
			var deleting_item = $(this).data("pfimgno");
			var post_id = $(this).data("pfpid");
			
			if ($(this).data("pffeatured") == 'yes') {
				return alert(theme_scriptspfm.dashtext1);
			}else{
				if(confirm(theme_scriptspfm.dashtext2)){
					/*Send ajax*/
					$.ajax({
						beforeSend:function(){
				    		$('.pfuploadedimages').pfLoadingOverlay({action:'show'});
				    	},
						url: theme_scriptspf.ajaxurl,
						type: 'POST',
						dataType: 'html',
						data: {
							action: 'pfget_imagesystem',
							iid: deleting_item,
							id: post_id,
							process: 'd',
							security: theme_scriptspfm.pfget_imagesystem
						},
					})
					.done(function(obj) {
						$.pfitemdetail_listimages(post_id);
						
						$.drzoneuploadlimit = $.drzoneuploadlimit +1;
						$(".pfuploaddrzonenum").text($.drzoneuploadlimit);

						var myDropzone = Dropzone.forElement("div#pfdropzoneupload");
						myDropzone.options.maxFiles = $.drzoneuploadlimit;

					})
					
				}
			}
		    
		    
		    return false;
		});

		
		/* Change Cover Photo */
		$(".pf-change-standartimg").live("click", function(){
			
			var changing_item = $(this).data("pfimgno");
			var post_id = $(this).data("pfpid");

			/*Send ajax*/
		    $.ajax({
		    	beforeSend:function(){
		    		$('.pfuploadedimages').pfLoadingOverlay({action:'show'});
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'pfget_imagesystem',
					iid: changing_item,
					id: post_id,
					process: 'c',
					security: theme_scriptspfm.pfget_imagesystem
				},
			})
			.done(function(obj) {
				$.pfitemdetail_listimages(post_id);
			})
		    
		    return false;
		});

		$.pfitemdetail_listimages = function(id){
			$.ajax({
				beforeSend:function(){
		    		$('.pfuploadedimages').pfLoadingOverlay({action:'show'});
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'pfget_imagesystem',
					id: id,
					process: 'l',
					security: theme_scriptspfm.pfget_imagesystem
				},
			})
			.done(function(obj) {
				$('.pfuploadedimages').html(obj);
			})
		};

		/* OLD Upload system */

		/* Delete Photo OLD */
		$(".pf-delete-standartimg-old").live("click", function(){
			
			var deleting_item = $(this).data("pfimgno");
			var post_id = $(this).data("pfpid");
			
			if ($(this).data("pffeatured") == 'yes') {
				return alert("This is your cover photo and can not remove. Please change your cover photo first.");
			}else{
				if(confirm("Are you sure want to delete this item? (This action can not be rollback.")){
					/*Send ajax*/
					$.ajax({
						beforeSend:function(){
				    		$('.pfuploadedimages').pfLoadingOverlay({action:'show'});
				    	},
						url: theme_scriptspf.ajaxurl,
						type: 'POST',
						dataType: 'html',
						data: {
							action: 'pfget_imagesystem',
							iid: deleting_item,
							id: post_id,
							oldup:1,
							process: 'd',
							security: theme_scriptspfm.pfget_imagesystem
						},
					})
					.done(function(obj) {
						$.pfitemdetail_listimages_old(post_id);

						$.pfuploadimagelimit = $.pfuploadimagelimit +1;
						$('.pfmaxtext').text($.pfuploadimagelimit);

						if ($.pfuploadimagelimit > 0) {
							$('.pfuploadfeaturedimgupl-container').css('display','inline-block');
						}

					})
					
				}
			}
		    
		    
		    return false;
		});

		/*List images - OLD*/
		$.pfitemdetail_listimages_old = function(id){
			$.ajax({
				beforeSend:function(){
		    		$('.pfuploadedimages').pfLoadingOverlay({action:'show'});
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'pfget_imagesystem',
					id: id,
					oldup:1,
					process: 'l',
					security: theme_scriptspfm.pfget_imagesystem
				},
			})
			.done(function(obj) {
				$('.pfuploadedimages').html(obj);
			})
		};


		/* Change Cover Photo - OLD */
		$(".pf-change-standartimg-old").live("click", function(){
			
			var changing_item = $(this).data("pfimgno");
			var post_id = $(this).data("pfpid");

			/*Send ajax*/
		    $.ajax({
		    	beforeSend:function(){
		    		$('.pfuploadedimages').pfLoadingOverlay({action:'show'});
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'pfget_imagesystem',
					iid: changing_item,
					id: post_id,
					process: 'c',
					oldup:1,
					security: theme_scriptspfm.pfget_imagesystem
				},
			})
			.done(function(obj) {
				$.pfitemdetail_listimages_old(post_id);
			})
		    
		    return false;
		});


		/* FILE Upload system */

		/* Delete File */
		$(".pf-delete-standartfile").live("click", function(){
			
			var deleting_item = $(this).data("pffileno");
			var post_id = $(this).data("pfpid");
			
			if ($(this).data("pffeatured") == 'yes') {
				return alert("This is your cover photo and can not remove. Please change your cover photo first.");
			}else{
				if(confirm("Are you sure want to delete this item? (This action can not be rollback.")){
					/*Send ajax*/
					$.ajax({
						beforeSend:function(){
				    		$('.pfuploadedfiles').pfLoadingOverlay({action:'show'});
				    	},
						url: theme_scriptspf.ajaxurl,
						type: 'POST',
						dataType: 'html',
						data: {
							action: 'pfget_filesystem',
							iid: deleting_item,
							id: post_id,
							process: 'd',
							security: theme_scriptspfm.pfget_filesystem
						},
					})
					.done(function(obj) {
						$.pfitemdetail_listfiles(post_id);

						$.pfuploadfilelimit = $.pfuploadfilelimit +1;
						$('.pfmaxtext2').text($.pfuploadfilelimit);

						if ($.pfuploadfilelimit > 0) {
							$('.pfuploadfeaturedfileupl-container').css('display','inline-block');
						}

					})
					
				}
			}
		    
		    
		    return false;
		});

		/*List images */
		$.pfitemdetail_listfiles = function(id){
			$.ajax({
				beforeSend:function(){
		    		$('.pfuploadedfiles').pfLoadingOverlay({action:'show'});
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'pfget_filesystem',
					id: id,

					process: 'l',
					security: theme_scriptspfm.pfget_filesystem
				},
			})
			.done(function(obj) {
				$('.pfuploadedfiles').html(obj);
			})
		};


		/* Change Cover Photo  */
		$(".pf-change-standartfile").live("click", function(){
			
			var changing_item = $(this).data("pffileno");
			var post_id = $(this).data("pfpid");

			/*Send ajax*/
		    $.ajax({
		    	beforeSend:function(){
		    		$('.pfuploadedfiles').pfLoadingOverlay({action:'show'});
		    	},
				url: theme_scriptspf.ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'pfget_filesystem',
					iid: changing_item,
					id: post_id,
					process: 'c',
					security: theme_scriptspfm.pfget_filesystem
				},
			})
			.done(function(obj) {
				$.pfitemdetail_listimages_old(post_id);
			})
		    
		    return false;
		});
	/* IMAGE AND FILE UPLOAD END */


	/* ON-OFF SYSTEM STARTED */
		$("body").on('click touchstart',".pfstatusbuttonaction",function(event) {

			var pid = $(this).data('pfid');
			var atext = $(this).data('pf-active');
			var dtext = $(this).data('pf-deactive');
			var thisitem = $(this);

			$.ajax({
			beforeSend:function(){
				$('.pfmu-itemlisting-inner'+pid).pfLoadingOverlay({action:'show'});
			},
	        type: 'POST',
	        dataType: 'json',
	        url: theme_scriptspf.ajaxurl,
	        data: { 
	            'action': 'pfget_onoffsystem',
	            'itemid': pid,
	            'lang': theme_scriptspfm.pfcurlang,
	            'security': theme_scriptspfm.pfget_onoffsystem
	        }
	    	})
			.done(function(obj) {

				if (obj == 1) {
					thisitem.switchClass('pfstatusbuttonactive','pfstatusbuttondeactive');
					thisitem.attr('title', dtext);
					$('.pfmu-itemlisting-inner-overlay'+pid).css('display','block');
				}else{
					thisitem.switchClass('pfstatusbuttondeactive','pfstatusbuttonactive');
					thisitem.attr('title', atext);
					$('.pfmu-itemlisting-inner-overlay'+pid).css('display','none');
				};

				

				$('.pfmu-itemlisting-inner'+pid).pfLoadingOverlay({action:'hide'});
			})
		});
	/* ON-OFF SYSTEM STARTED */


	$(function(){
		$('#mceu_12').live('click', function(event) {
		  wpLink.open();
		  return false;
		});
	});

})(jQuery);