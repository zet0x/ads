<?php
/**********************************************************************************************************************************
*
* Point Finder
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/

function pointfinder_registrationpg_content(){

  $token = pointfinder_apim()->get_option( 'token' );
  $items = pointfinder_apim()->get_option( 'items', array() );
?>
<style>.about-wrap .notice{display:block!important}</style>
	<div class="wrap about-wrap">

      <h1><?php echo esc_html__('Welcome to PointFinder','pointfindert2d');?></h1>

      <div class="about-text"><?php echo sprintf(esc_html__('PointFinder is now installed and ready to use! Please %sregister%s your purchase to get use theme functions and quick setup.','pointfindert2d'),'<a href="'.admin_url('admin.php?page=pointfinder_registration').'">','</a>');?></div>

      <h2 class="nav-tab-wrapper">
      	<a href="<?php echo admin_url('admin.php?page=pointfinder_tools');?>" class="nav-tab nav-tab">
           <?php echo esc_html__('Instruction','pointfindert2d');?></a>
       
        <a href="<?php echo admin_url('admin.php?page=pointfinder_demo_installer');?>" class="nav-tab nav-tab">
           <?php echo esc_html__('Quick Setup','pointfindert2d');?></a>

        <a href="<?php echo admin_url('admin.php?page=pointfinder_registration');?>" class="nav-tab nav-tab-active"><?php echo esc_html__('Registration','pointfindert2d');?></a>

      </h2>
      
      <div class="pointfinder-main-window">
        
        <br/>
        <div style="border-left: 4px solid #00a0d2;padding: 30px;background: #fff;margin: 30px 0 0 0;">
              <?php if ( pointfinder_apim()->is_registered() ) : ?>
                <p class="about-description" style="color:#1da02f"><span class="dashicons dashicons-yes" style="font-size: 32px;margin-right: 8px;"></span> <?php esc_attr_e( 'Congratulations! Your product is registered now.', 'pointfindert2d' ); ?></p>
              <?php else : ?>
                <p class="about-description"><?php esc_attr_e( 'Please enter your Envato token to complete registration.', 'pointfindert2d' ); ?></p>
              <?php endif; ?>
                <div>
                <form id="pointfinder_product_registration" method="post" action="options.php">
                  
                  <?php settings_fields( pointfinder_apim()->get_slug() ); ?>
                  <?php PointFinder_APIM_Admin::do_settings_sections( pointfinder_apim()->get_slug(), 2 ); ?>
                  <input type="submit" name="submit" id="submit" class="button button-primary button-large pointfinder-register" value="Submit" style="width: 120px;height: 42px!important;font-size: 16px!important;margin-top: 10px!important;">
                </form>

                
                 <?php if ( ( '' !== $token || ! empty( $items ) ) ) { ?>
                  <a href="<?php echo esc_url( add_query_arg( array( 'authorization' => 'check' ), pointfinder_apim()->get_page_url() ) ); ?>" class="button button-secondary auth-check-button" style="margin: 10px 0px 0px 0px;"><?php esc_html_e( 'Test API Connection', 'pointfindert2d' ); ?></a>
                <?php } ?>
                
                
            
              <?php if ( !pointfinder_apim()->is_registered() ) : ?>
              <div style="font-size:17px;line-height:27px;margin-top:1em;padding-top:1em">
                <hr>

                <h3><?php esc_html__( 'Instructions For Generating A Token', 'pointfindert2d' ); ?></h3>
                <ol>
                  
                  <li>Click on this <a href="https://build.envato.com/create-token/?purchase:download=t&amp;purchase:verify=t&amp;purchase:list=t" target="_blank">Generate A Personal Token</a> link. <strong>IMPORTANT:</strong> You must be logged into the same Themeforest account that purchased PointFinder. If you are logged in already, look in the top menu bar to ensure it is the right account. If you are not logged in, you will be directed to login then directed back to the Create A Token Page.</li>
                  
                  <li>Enter a name for your token, then check the boxes for <strong>View Your Envato Account Username, List Purchases You've Made</strong>,<strong>Download Your Purchased Items</strong> and <strong>Verify Purchases You've Made</strong> from the permissions needed section. Check the box to agree to the terms and conditions, then click the <strong>Create Token button</strong></li>
                  
                  <li>A new page will load with a token number in a box. Copy the token number then come back to this registration page and paste it into the field below and click the <strong>Submit</strong> button.</li>
                  
                  <li>You will see a green check mark for success, or a failure message if something went wrong. If it failed, please make sure you followed the steps above correctly.</li>
                              </ol>

              </div>
              <?php endif; ?>

                  </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="clear"></div>
      </div>

    </div>
    <?php
}
?>
