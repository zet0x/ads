<?php
/**********************************************************************************************************************************
*
* Ajax Payment System
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/


add_action( 'PF_AJAX_HANDLER_pfget_membershippaymentsystem', 'pf_ajax_membershippaymentsystem' );
add_action( 'PF_AJAX_HANDLER_nopriv_pfget_membershippaymentsystem', 'pf_ajax_membershippaymentsystem' );

function pf_ajax_membershippaymentsystem(){
  
	//Security
  check_ajax_referer( 'pfget_membershipsystem', 'security');
  
	header('Content-Type: text/html; charset=UTF-8;');

	//Get form type
  if(isset($_POST['pid']) && $_POST['pid']!=''){
    $pid = esc_attr($_POST['pid']);
  }

  if(isset($_POST['ptype']) && $_POST['ptype']!=''){
    $ptype = esc_attr($_POST['ptype']);
  }

  if(isset($_POST['lang']) && $_POST['lang']!=''){
    $lang_c = sanitize_text_field($_POST['lang']);
  }

  if(function_exists('icl_t')) {
    if (!empty($lang_c)) {
      do_action( 'wpml_switch_language', $lang_c );
    }
  }

  $output = '';

  if (!empty($pid) && !empty($ptype)) {
    $bank_status = PFSAIssetControl('setup20_paypalsettings_bankdeposit_status','','0');
    $paypal_status = PFSAIssetControl('setup20_paypalsettings_paypal_status','','1');
    $stripe_status = PFSAIssetControl('setup20_stripesettings_status','','0');
    $pags_status = PFPGIssetControl('pags_status','','0');
    $payu_status = PFPGIssetControl('payu_status','','0');
    $ideal_status = PFPGIssetControl('ideal_status','','0');
    $robo_status = PFPGIssetControl('robo_status','','0');
    $iyzico_status = PFPGIssetControl('iyzico_status','','0');

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $active_order_ex = get_user_meta($user_id, 'membership_user_activeorder_ex',true );
    if ($active_order_ex != false && !empty($active_order_ex)) {
      $bank_current = get_post_meta( $active_order_ex, 'pointfinder_order_bankcheck', 1);
    } else {
      $bank_current = false;
    }
    
    

    $packageinfo = pointfinder_membership_package_details_get($pid);

    /*Package payment total*/
      $output .= '<div class="pf-membership-price-header">'.esc_html__('Selected Package','pointfindert2d').'</div>';
      $output .= '
      <div class="pf-membership-package-box">
        <div class="pf-membership-package-title">' . get_the_title($pid) . '</div>
        <div class="pf-membership-package-info">
        <ul>
          <li><span class="pf-membership-package-info-title">'.esc_html__('Number of listings included in the package:','pointfindert2d').' </span> '.$packageinfo['packageinfo_itemnumber_output_text'].'</li>
          <li><span class="pf-membership-package-info-title">'.esc_html__('Number of featured listings included in the package:','pointfindert2d').' </span> '.$packageinfo['webbupointfinder_mp_fitemnumber'].'</li>
          <li><span class="pf-membership-package-info-title">'.esc_html__('Number of images (per listing) included in the package:','pointfindert2d').' </span> '.$packageinfo['webbupointfinder_mp_images'].'</li>
          <li><span class="pf-membership-package-info-title">'.esc_html__('Listings can be submitted within:','pointfindert2d').' </span> '.$packageinfo['webbupointfinder_mp_billing_period'].' '.$packageinfo['webbupointfinder_mp_billing_time_unit_text'].'</li>
          ';
          if ($packageinfo['webbupointfinder_mp_trial'] == 1 && $packageinfo['packageinfo_priceoutput'] != 0) {
            $output .= '<li><span class="pf-membership-package-info-title">'.esc_html__('Trial Period:','pointfindert2d').' </span> '.$packageinfo['webbupointfinder_mp_trial_period'].' '.$packageinfo['webbupointfinder_mp_billing_time_unit_text'].' <br/><small>'.esc_html__('Note: Your listing will expire end of trial period.','pointfindert2d').'</small></li>';
          }
          if (!empty($packageinfo['webbupointfinder_mp_description'])) {
            $output.= '<li><span class="pf-membership-package-info-title">'.esc_html__('Description:','pointfindert2d').' </span> '.$packageinfo['webbupointfinder_mp_description'].'</li>';
          }

          $setup4_pricevat = PFSAIssetControl('setup4_pricevat','','0');
          if ($setup4_pricevat == 1) {
            $setup4_pv_pr = PFSAIssetControl('setup4_pv_pr','','0');
            $output .= '<li style="margin-top:10px;font-weight:600;"><span class="pf-membership-package-info-title">'.esc_html__("Sub Total Before VAT :",'pointfindert2d').' </span> '.$packageinfo['packageinfo_priceoutput_bfvat'].'</li>';
            $output .= '<li style="font-weight:600;"><span class="pf-membership-package-info-title">'.sprintf(esc_html__("VAT (%s) :",'pointfindert2d'),$setup4_pv_pr.'%').' </span> '.$packageinfo['packageinfo_priceoutput_vat'].'</li>';
          }

          $output .= '<li style="margin-top:10px;font-weight:600;"><span class="pf-membership-package-info-title">'.esc_html__('Total:','pointfindert2d').' </span>'.$packageinfo['packageinfo_priceoutput_text'].'</li>';

          
          $output .= '
        </ul>
        </div>
      </div>';

   

    /*Payment Options*/
    if ($packageinfo['packageinfo_priceoutput'] != 0) {
      $output .= '<div class="pf-membership-price-header">'.esc_html__('Payment Options','pointfindert2d').'</div>';
      if ($packageinfo['webbupointfinder_mp_trial'] == 1  && ($ptype != 'renewplan' && $ptype != 'upgradeplan')) {        
        $output .= '
          <div class="pf-membership-upload-option">
            <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-trial" value="trial">
            <label for="pfm-payment-trial">'.esc_html__('Trial Period (Free)','pointfindert2d').'</label>
            <div class="pfm-active">
            <p>'.sprintf(__('You can use this package trial for %d %s','pointfindert2d'),$packageinfo['webbupointfinder_mp_trial_period'], $packageinfo['webbupointfinder_mp_billing_time_unit_text']).'</p>
            </div>
          </div>';
      }

      if ($bank_status == 1) {
       
        if ($bank_current != false && !empty($bank_current)) {
          $output .= '
        <div class="pf-membership-upload-option">
          <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-bank" value="bank" disabled="disabled">
          <label for="pfm-payment-bank">'.esc_html__('Bank Transfer','pointfindert2d').' <font style="font-weight:normal;"> '.esc_html__('(Disabled - Please complete or cancel existing transfer.)','pointfindert2d').'</font></label>
          <div class="pfm-active">
          <p>'.__("Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won't be approved until the funds have cleared in our account.",'pointfindert2d').'</p>
          </div>
        </div>';
        } else {
          $output .= '
          <div class="pf-membership-upload-option">
            <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-bank" value="bank">
            <label for="pfm-payment-bank">'.esc_html__('Bank Transfer','pointfindert2d').'</label>
            <div class="pfm-active">
            <p>'.__("Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won't be approved until the funds have cleared in our account.",'pointfindert2d').'</p>
            </div>
          </div>';
        }
      }

      if ($paypal_status == 1) {  
        $output .= '
        <div class="pf-membership-upload-option">
          <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-paypal" value="paypal">
          <label for="pfm-payment-paypal">'.esc_html__('Paypal','pointfindert2d').'</label>
          <div class="pfm-active">
          <p>'.__("Pay via PayPal; you can pay with your credit card if you don't have a PayPal account.",'pointfindert2d').'</p>
          </div>
        </div>';
        $setup31_userpayments_recurringoption = PFSAIssetControl('setup31_userpayments_recurringoption','','1');
        if($setup31_userpayments_recurringoption == 1){
          $output .= '
          <div class="pf-membership-upload-option">
            <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-paypal2" value="paypal2">
            <label for="pfm-payment-paypal2">'.esc_html__('Paypal Recurring Payment','pointfindert2d').'</label>
            <div class="pfm-active">
            <p>'.__("Pay via PayPal Recurring Payment; you can create automated payments for this order.",'pointfindert2d').'</p>
            </div>
          </div>';
        }
      }

      if ($stripe_status == 1) {
        $output .= '
        <div class="pf-membership-upload-option">
          <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-stripe" value="stripe">
          <label for="pfm-payment-stripe">'.esc_html__('Credit Card (Stripe)','pointfindert2d').'</label>
          <div class="pfm-active">
          <p>'.__("Pay via Credit Card; you can pay with your credit card. (This service is using Stripe Payment Gateway)",'pointfindert2d').'</p>
          </div>
        </div>';
      }

      if ($pags_status == 1) {
        $output .= '
        <div class="pf-membership-upload-option">
          <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-pags" value="pags">
          <label for="pfm-payment-pags">'.esc_html__('PagSeguro Payment System','pointfindert2d').'</label>
          <div class="pfm-active">
          <p>'.__("Pay via PagSeguro; you can pay with your PagSeguro account.",'pointfindert2d').'</p>
          </div>
        </div>';
      }

      if ($payu_status == 1) {
        $output .= '
        <div class="pf-membership-upload-option">
          <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-payu" value="payu">
          <label for="pfm-payment-payu">'.esc_html__('PayU Money Payment System','pointfindert2d').'</label>
          <div class="pfm-active">
          <p>'.__("Pay via Payu Money; you can pay with your Payu Money account.",'pointfindert2d').'</p>
          </div>
        </div>';
      }

      if ($ideal_status == 1) {

        require_once( get_template_directory(). '/admin/core/Mollie/API/Autoloader.php' );
        $ideal_id = PFPGIssetControl('ideal_id','','');
        $mollie = new Mollie_API_Client;
        $mollie->setApiKey($ideal_id);

        $output .= '
        <div class="pf-membership-upload-option">
          <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-ideal" value="ideal">
          <label for="pfm-payment-ideal">'.esc_html__('iDeal Payment System','pointfindert2d').'</label>
          <div class="pfm-active">
          <p>'.__("Pay via iDeal; you can pay with your iDeal account.",'pointfindert2d').'</p>
          ';
          $issuers = $mollie->issuers->all();
          $output .= esc_html__("Select your bank:","pointfindert2d");
          $output .= '<select name="issuer" style="margin-top:5px;margin-left: 5px;">';

          foreach ($issuers as $issuer)
          {
            if ($issuer->method == Mollie_API_Object_Method::IDEAL)
            {
              $output .= '<option value=' . htmlspecialchars($issuer->id) . '>' . htmlspecialchars($issuer->name) . '</option>';
            }
          }

          $output .= '<option value="">or select later</option>';
          $output .= '</select>';
          $output .= '
          </div>
        </div>';
      }

      if ($robo_status == 1) {
        $output .= '
        <div class="pf-membership-upload-option">
          <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-robo" value="robo">
          <label for="pfm-payment-robo">'.esc_html__('Robokassa Payment System','pointfindert2d').'</label>
          <div class="pfm-active">
          <p>'.__("Pay via Robokassa; you can pay with your Robokassa account.",'pointfindert2d').'</p>
          </div>
        </div>';
      }

      if ($iyzico_status == 1) {
          $output .= '
          <div class="pf-membership-upload-option">
            <input name="pf_membership_payment_selection" type="radio" id="pfm-payment-iyzico" value="iyzico">
            <label for="pfm-payment-iyzico">'.esc_html__('Iyzico Payment System','pointfindert2d').'</label>
            <div class="pfm-active">
              <p>'.esc_html__("Pay via iyzico; you can pay with your iyzico account.",'pointfindert2d').'</p>
              ';
              $usermetaarr = get_user_meta($user_id);

              $output .= '<div class="iyzico-fields golden-forms">';
            
              if(empty($usermetaarr['first_name'][0])){
                $output .= '<section>
                  <label for="pfusr_firstname" class="lbl-text">'.esc_html__('First Name','pointfindert2d').'<span style="color:red!important">*</span></label>
                  <label class="lbl-ui">
                    <input type="text" name="pfusr_firstname" id="pfusr_firstname" class="input">
                  </label>                            
                </section>';
              }
              if(empty($usermetaarr['last_name'][0])){
                $output .= '<section>
                  <label for="pfusr_lastname" class="lbl-text">'.esc_html__('Last Name','pointfindert2d').'<span style="color:red!important">*</span></label>
                  <label class="lbl-ui">
                    <input type="text" name="pfusr_lastname" id="pfusr_lastname" class="input">
                  </label>                            
                </section>';
              }
              if(empty($usermetaarr['user_mobile'][0])){
                $output .= '<section>
                  <label for="pfusr_mobile" class="lbl-text">'.esc_html__('GSM Number','pointfindert2d').'</label>
                  <label class="lbl-ui">
                    <input type="text" name="pfusr_mobile" id="pfusr_mobile" class="input">
                  </label>                            
                </section>';
              }
              if(empty($usermetaarr['user_vatnumber'][0])){
                $output .= '<section>
                  <label for="pfusr_vatnumber" class="lbl-text">'.esc_html__('VAT Number','pointfindert2d').'<span style="color:red!important">*</span></label>
                  <label class="lbl-ui">
                    <input type="text" name="pfusr_vatnumber" id="pfusr_vatnumber" class="input">
                  </label>                            
                </section>';
              }
              if(empty($usermetaarr['user_country'][0])){
                $output .= '<section>
                  <label for="pfusr_country" class="lbl-text">'.esc_html__('Country','pointfindert2d').'<span style="color:red!important">*</span></label>
                  <label class="lbl-ui">
                    <input type="text" name="pfusr_country" id="pfusr_country" class="input">
                  </label>                            
                </section>';
              }
              if(empty($usermetaarr['user_city'][0])){
                $output .= '<section>
                  <label for="pfusr_city" class="lbl-text">'.esc_html__('City','pointfindert2d').'<span style="color:red!important">*</span></label>
                  <label class="lbl-ui">
                    <input type="text" name="pfusr_city" id="pfusr_city" class="input">
                  </label>                            
                </section>';
              }
              
              if(empty($usermetaarr['user_address'][0])){
                $output .= '<section>
                  <label for="pfusr_address" class="lbl-text">'.esc_html__('Address','pointfindert2d').'<span style="color:red!important">*</span></label>
                  <label class="lbl-ui">
                    <input type="text" name="pfusr_address" id="pfusr_address" class="input">
                  </label>                            
                </section>';
              }

              if (
                empty($usermetaarr['user_address'][0]) 
                || empty($usermetaarr['user_city'][0])
                || empty($usermetaarr['user_country'][0])
                || empty($usermetaarr['user_vatnumber'][0])
                || empty($usermetaarr['user_mobile'][0])
                || empty($usermetaarr['first_name'][0])
                || empty($usermetaarr['last_name'][0])
                ) {
                $output .= '<small>'.esc_html__('Please fill above informations before use iyzico payment system.','pointfindert2d').'</small>';
              }
              

              $output .= '</div>';
            $output .= '</div>';

          $output .= '</div>';
      }

      if (
        $stripe_status != 1 
        && $paypal_status != 1 
        && $bank_status != 1 
        && $ideal_status != 1 
        && $payu_status != 1 
        && $pags_status != 1
        && $robo_status != 1
        && $iyzico_status != 1
        ) {
        $output .= '<div class="pf-membership-upload-option">'.esc_html__('Please enable a payment system by using Options Panel','pointfindert2d').'</div>';
      }else{
        $output .= '
        <script>
        (function($) {
        "use strict";
          $(function(){
            var membership_radio = $(".pf-membership-upload-option input[type=\'radio\']");
            membership_radio.on("change", function () {
            membership_radio.parents().removeClass("active");
            $(this).parent().addClass("active");
            if ($(this).val() == "iyzico") {
              ';
                 if(empty($usermetaarr['first_name'][0])){
                 $output .= '$("#pfusr_firstname").rules( "add", {
                  required: true,
                  messages: {
                    required: "'.esc_html__('Please add your Name (Iyzico requirement)','pointfindert2d').'"
                  }
                  });';
                 }
                 if(empty($usermetaarr['last_name'][0])){
                 $output .= '$("#pfusr_lastname").rules( "add", {required: true,messages: {required: "'.esc_html__('Please add your Last Name (Iyzico requirement)','pointfindert2d').'"}});';
                 }
                 if(empty($usermetaarr['user_vatnumber'][0])){
                 $output .= '$("#pfusr_vatnumber").rules( "add", {required: true,messages: {required: "'.esc_html__('Please add your VAT Number (Iyzico requirement)','pointfindert2d').'"}});';
                 }
                 if(empty($usermetaarr['user_country'][0])){
                 $output .= '$("#pfusr_country").rules( "add", {required: true,messages: {required: "'.esc_html__('Please add your Country (Iyzico requirement)','pointfindert2d').'"}});';
                 }
                 if(empty($usermetaarr['user_city'][0])){
                 $output .= '$("#pfusr_city").rules( "add", {required: true,messages: {required: "'.esc_html__('Please add your City (Iyzico requirement)','pointfindert2d').'"}});';
                 }
                 if(empty($usermetaarr['user_address'][0])){
                 $output .= '$("#pfusr_address").rules( "add", {required: true,messages: {required: "'.esc_html__('Please add your Address (Iyzico requirement)','pointfindert2d').'"}});';
                 }
              $output .= '
              }else{
               
                ';
                 if(empty($usermetaarr['first_name'][0])){
                 $output .= '$("#pfusr_firstname").rules( "remove" );';
                 }
                 if(empty($usermetaarr['last_name'][0])){
                 $output .= '$("#pfusr_lastname").rules( "remove" );';
                 }
                 if(empty($usermetaarr['user_vatnumber'][0])){
                 $output .= '$("#pfusr_vatnumber").rules( "remove" );';
                 }
                 if(empty($usermetaarr['user_country'][0])){
                 $output .= '$("#pfusr_country").rules( "remove" );';
                 }
                 if(empty($usermetaarr['user_city'][0])){
                 $output .= '$("#pfusr_city").rules( "remove" );';
                 }
                 if(empty($usermetaarr['user_address'][0])){
                 $output .= '$("#pfusr_address").rules( "remove" );';
                 }
              $output .= '

              }
            });
          });
        })(jQuery);
        </script>
        ';
      }
    }else{
      $output .= '<input name="pf_membership_payment_selection" type="hidden" id="pfm-payment-free" value="free">';
    }

  }
  echo $output;

die();
}

?>