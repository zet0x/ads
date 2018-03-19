<?php
/**********************************************************************************************************************************
*
* Filters
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/

/* Custom Comments Callback */
function pointfindert2dcomments($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);
	
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
	<?php echo '<'; echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ){ ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php }; ?>

	<div class="comment-author-image">
	   <?php if ($args['avatar_size'] != 0){echo get_avatar( $comment,128 );} ?>
	</div>
    
    <div class="comments-detail-container">
       
        <div class="comment-author-vcard">
            <?php printf(esc_html__('%s says:', 'pointfindert2d'), get_comment_author_link()) ?>
        </div>
    
        <?php if ($comment->comment_approved == '0') { ?>
        	<em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'pointfindert2d') ?></em>
        	<br />
        <?php }; ?>

    	<div class="comment-meta commentmetadata">
            <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
    		<?php
    			printf( esc_html__('%1$s at %2$s', 'pointfindert2d'), get_comment_date(),  get_comment_time()) ?></a>
                <?php edit_comment_link(esc_html__('Edit', 'pointfindert2d'),'  ','' );
    		?>
    	</div>
        
        <div class="comment-textarea">
	    <?php comment_text() ?>
        </div>

	    <div class="reply"> <i class="pfadmicon-glyph-362"></i>
	       <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	    </div>
    </div>

	<?php if ( 'div' != $args['style'] ){ ?>
	</div>
	<?php }; ?>
<?php }


function pointfinder_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}


function pf_remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

function pf_add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    if(is_tax() || is_tag() || is_category() || is_search()){
        $general_ct_page_layout = PFSAIssetControl('general_ct_page_layout','','1');
        if( $general_ct_page_layout == 3 ) {
            $classes[] = 'pfhalfpagemapview';
        }
        
    }

    return $classes;
}


function pointfinder_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

add_filter( 'the_content_more_link', 'pointfinder_modify_read_more_link' );
function pointfinder_modify_read_more_link() {
return '...';
}




function pointfinderwp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;

    $output = do_shortcode(get_the_content('' . esc_html__('Read more', 'pointfindert2d') . ''));
    $output = apply_filters('convert_chars', $output);
    if (strpos($output, '<!--more-->')){$output = apply_filters('the_content_more_link', $output);}
    $output = apply_filters('the_content', $output);
    echo $output;
	
}



function pointfinderwp_excerpt_single($length_callback = '', $more_callback = '')
{   
    
    global $post;
    global $more;
    $more = 0;

    remove_shortcode('gallery');
    $output = do_shortcode(get_the_content('' . esc_html__('Read more', 'pointfindert2d') . ''));
    $output = preg_replace('/\[gallery(.*?)\]/', '', $output);
    $output = apply_filters('convert_chars', $output);
    $output = apply_filters('the_content', $output);
    
    echo $output;
    
}

function pointfinderh_blank_view_article($more)
{
    global $post;
    if($post->post_type == 'post'){
        $output = '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . esc_html__('View Article', 'pointfindert2d') . '</a>';
    	return $output;
    }
}
add_filter('excerpt_more', 'pointfinderh_blank_view_article');


function pointfinderh_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

function pointfinder_remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

function pointfindert2dgravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

function pf_enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}



/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function pointfinder_wp_title( $title, $sep ) {
    if ( is_feed() ) {
        return $title;
    }
    
    global $page, $paged;

    $title .= get_bloginfo( 'name', 'display' );

    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
        $title .= " $sep $site_description";
    }

    if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
        $title .= " $sep " . sprintf( esc_html__( 'Page %s', 'pointfindert2d' ), max( $paged, $page ) );
    }

    return $title;
}
add_filter( 'wp_title', 'pointfinder_wp_title', 10, 2 );


/*
* Redirects after login
* Added with v1.6
*/
$as_redirect_logins = PFASSIssetControl('as_redirect_logins','','0');
if ($as_redirect_logins) {

    function pointfinder_possibly_redirect(){
      global $pagenow;
      if( 'wp-login.php' == $pagenow ) {

        $setup4_membersettings_dashboard = PFSAIssetControl('setup4_membersettings_dashboard','',site_url());
        $setup4_membersettings_dashboard_link = get_permalink($setup4_membersettings_dashboard);
        
        $pfmenu_perout = PFPermalinkCheck();

        $special_linkurl = $setup4_membersettings_dashboard_link.$pfmenu_perout.'ua=myitems';

        if (isset($_GET['action']) == 'rp') {
            
        } else {
            if ( isset( $_POST['wp-submit'] ) ||   
              ( isset($_GET['action']) && $_GET['action']=='logout') || 
              ( isset($_GET['checkemail']) && $_GET['checkemail']=='confirm') || 
              ( isset($_GET['checkemail']) && $_GET['checkemail']=='registered') ) return;
            else wp_redirect( $special_linkurl ); 
            exit();
        }
        
        
      }
    }
    add_action('init','pointfinder_possibly_redirect');
}

/**
 * Manage WooCommerce styles and scripts.
 * Added with v1.6
 */
function pf_grd_woocommerce_script_cleaner() {
    if (function_exists('is_woocommerce')) {
       if ( !is_woocommerce() && !is_page('store') && !is_shop() && !is_product_category() && !is_product() && !is_cart() && !is_checkout() && !is_product_tag() && !is_product_taxonomy() && !is_view_order_page() ) {
            wp_dequeue_style( 'select2' );
            wp_dequeue_script( 'select2' );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'pf_grd_woocommerce_script_cleaner', 99 );


/**
 * Tags Cloud Filter
 * Added with v1.6.5
 */
add_filter( 'widget_tag_cloud_args', 'pointfinder_tag_cloud_limit' );
function pointfinder_tag_cloud_limit($args){ 
    if ( isset($args['taxonomy']) && $args['taxonomy'] == 'post_tag' ){
        $as_tags_cloud = PFASSIssetControl('as_tags_cloud','',45);
        $args['number'] = $as_tags_cloud;
    }
    return $args;
}


/**
 * Title Filter
 * Added with v1.7.2
 */
function pointfinder_title_filter( $where, $wp_query )
{
    

    global $wpdb;
    

    if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
        $system_search_setup = PFSAIssetControl('system_search_setup','','3');

        $search_term_original = $wpdb->esc_like( $search_term );

        if($search_term != ''){

            switch ($system_search_setup) {
                case '1':
                    /*Or operator*/
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' OR ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' OR ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }
                        }else{
                            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                        }
                    }else{
                        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    }
                    break;
                
                case '2':
                    /* and operator*/
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }
                        }else{
                            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                        }
                    }else{
                        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    }
                    break;

                case '3':
                    /* exact word */
                    $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    break;

                case '4':
                    /*Mixed words*/
                    $setup3_pointposttype_pt1 = PFSAIssetControl('setup3_pointposttype_pt1','','pfitemfinder');
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\' AND '.$wpdb->posts.'.post_type = "'.$setup3_pointposttype_pt1.'")';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\' AND '.$wpdb->posts.'.post_type = "'.$setup3_pointposttype_pt1.'")';
                                    }else{
                                        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }
                            $where .= ' OR (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\' AND '.$wpdb->posts.'.post_type = "'.$setup3_pointposttype_pt1.'")';

                        }else{
                            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                        }
                    }else{
                        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    }
                    break;
            }
            
            
            
        }
    }
    return $where;
}

add_filter( 'posts_where', 'pointfinder_title_filter', 10, 2 );



/**
 * Description Filter
 * Added with v1.7.2
 */
function pointfinder_description_filter( $where, $wp_query )
{
    global $wpdb;
    if ( $search_term = $wp_query->get( 'search_prod_desc' ) ) {

        $system_search_setup = PFSAIssetControl('system_search_setup','','3');

        if($search_term != ''){

            $search_term_original = $wpdb->esc_like( $search_term );
            switch ($system_search_setup) {
                case '1':
                    /*Or operator*/
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' OR ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' OR ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }
                        }else{
                            $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                        }
                    }else{
                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    }
                    break;
                
                case '2':
                    /* and operator*/
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }
                        }else{
                            $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                        }
                    }else{
                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    }
                    break;

                case '3':
                    /* exact word */
                    $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    break;

                case '4':
                    /*Mixed words*/
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }
                            $where .= ' OR ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';

                        }else{
                            $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                        }
                    }else{
                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\'';
                    }
                    break;
            }

            
        }
    }
    return $where;
}

add_filter( 'posts_where', 'pointfinder_description_filter', 10, 3 );


/**
 * Title Filter
 * Added with v1.7.3.3
 */
function pointfinder_title_desc_filter( $where, $wp_query )
{
    

    global $wpdb;
    

    if ( $search_term = $wp_query->get( 'search_prod_desc_title' ) ) {
        $system_search_setup = PFSAIssetControl('system_search_setup','','3');

        $search_term_original = $wpdb->esc_like( $search_term );
        $where2 = '';

        if($search_term != ''){

            switch ($system_search_setup) {
                case '4':
                case '1':
                    /*Or operator*/
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }

                                    if ($i == $search_term_count) {
                                        $where2 .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where2 .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' OR ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' OR ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }

                                    if ($i == $search_term_count) {
                                        $where2 .= ' OR ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where2 .= ' OR ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }

                            $where .= $where2;

                        }else{
                            $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\' OR post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\')';
                        }
                    }else{
                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\' OR post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\')';
                    }
                    break;
                
                case '2':
                    /* and operator*/
                    $search_term = explode(' ', $search_term_original);
                    if (is_array($search_term)) {
                        if (count($search_term) > 1) {
                            $i = 1;
                            $search_term_count = count($search_term);
                            foreach ($search_term as $single_search_term) {
                                if ($i == 1) {
                                    if ($i == $search_term_count) {
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND (' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }

                                    if ($i == $search_term_count) {
                                        $where2 .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where2 .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                    
                                }else{
                                    if ($i == $search_term_count) {
                                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where .= ' AND ' . $wpdb->posts . '.post_content LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }

                                    if ($i == $search_term_count) {
                                        $where2 .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\')';
                                    }else{
                                        $where2 .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $single_search_term ) . '%\'';
                                    }
                                }
                                $i++;
                            }

                            $where .= $where2;

                        }else{
                            $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\' AND post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\')';
                        }
                    }else{
                        $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\' AND post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\')';
                    }

                case '3':
                    /* exact word */
                    $where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\' OR post_content LIKE \'%' . sanitize_text_field(  $search_term_original ) . '%\')';
                    break;  
            }
        }
    }
    return $where;
}

add_filter( 'posts_where', 'pointfinder_title_desc_filter', 10, 2 );



add_filter( 'body_class', 'pointfinder_halfpage_map_body_class' );
function pointfinder_halfpage_map_body_class( $classes ) {
    $general_ct_page_layout = PFSAIssetControl('general_ct_page_layout','','1');
    $pffullwlayoutheader = PFSAIssetControl('pffullwlayoutheader','','0');
    if ($general_ct_page_layout == 3) {
        if (is_archive() || is_category() || is_tag() || is_search()) {
            $classes[] = 'pfdisableshrink';
        }
    }
    if ($pffullwlayoutheader == 1 && (!is_archive() && !is_category() && !is_tag() && !is_search())) {
        $classes[] = 'pffullwidthheader';
    }
    return $classes;
}

?>