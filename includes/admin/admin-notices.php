<?php
/**
 * Admin Notices
 *
 * @package     QUADS
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Admin Messages
 *
 * @since 2.2.3
 * @global $mashsb_options Array of all the MASHSB Options
 * @return void
 */
function quads_admin_messages() {
    global $quads_options;

    if( !current_user_can( 'update_plugins' ) ){
        return;
    }
    
    quads_theme_notice();
    
    quads_update_notice();
    
    if (!quads_is_any_ad_activated() && quads_is_admin_page() ){
        echo '<div class="notice notice-warning">'.sprintf(__('<strong>No ads are activated!</strong> You need to assign at least 1 ad to an ad spot. Fix this in <a href="%s">General Settings</a>! Alternatively you need to use a shortcode in your posts or no ads are shown at all.', 'quick-adsense-reloaded'), admin_url().'admin.php?page=quads-settings#quads_settingsgeneral_header').'</div>';
    }
    
    if (quads_get_active_ads() === 0 && quads_is_admin_page() ){
        echo '<div class="notice notice-warning">'.sprintf(__('<strong>No ads defined!</strong> You need to create at least one ad code. Fix this in <a href="%s">ADSENSE CODE</a>.', 'quick-adsense-reloaded'), admin_url().'admin.php?page=quads-settings#quads_settingsadsense_header').'</div>';
    }
    
    if (!quads_is_post_type_activated() && quads_is_admin_page() ){
        echo '<div class="notice notice-warning">'.sprintf(__('<strong>No ads are shown - No post types selected</strong> You need to select at least 1 post type like <i>blog</i> or <i>page</i>. Fix this in <a href="%s">General Settings</a> or no ads are shown at all.', 'quick-adsense-reloaded'), admin_url().'admin.php?page=quads-settings#quads_settingsgeneral_header').'</div>';
    }
    
    if (isset($_GET['quads-action']) && $_GET['quads-action'] === 'validate' && quads_is_admin_page() && quads_is_any_ad_activated() && quads_is_post_type_activated() && quads_get_active_ads() > 0 ){
        echo '<div class="notice notice-success">' . sprintf(__('<strong>No errors detected in WP QUADS settings.</strong> If ads are still not shown read the <a href="%s" target="_blank">troubleshooting guide</a>'), 'http://wpquads.com/docs/adsense-ads-are-not-showing/?utm_source=plugin&utm_campaign=wpquads-settings&utm_medium=website&utm_term=toplink') . '</div>';
    }

    //quads_plugin_deactivated_notice();
    
    $install_date = get_option( 'quads_install_date' );
    $display_date = date( 'Y-m-d h:i:s' );
    $datetime1 = new DateTime( $install_date );
    $datetime2 = new DateTime( $display_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );
    
    
    if( $diff_intrval >= 7 && get_option( 'quads_rating_div' ) == "no" || false === get_option( 'quads_rating_div' ) || quads_rate_again() ) {
        echo '<div class="quads_fivestar updated " style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);background-color:white;">
    	<p>Awesome, you\'ve been using <strong>WP QUADS</strong> for more than 1 week. <br> May i ask you to give it a <strong>5-star rating</strong> on Wordpress? </br>
        This will help to spread its popularity and to make this plugin a better one.
        <br><br>Your help is much appreciated. Thank you very much,<br> ~René Hermenau
        <ul>
            <li><a href="https://wordpress.org/support/plugin/quick-adsense-reloaded/reviews/?filter=5#new-post" class="thankyou" target="_new" title="Ok, you deserved it" style="font-weight:bold;">Ok, you deserved it</a></li>
            <li><a href="javascript:void(0);" class="quadsHideRating" title="I already did" style="font-weight:bold;">I already did</a></li>
            <li><a href="javascript:void(0);" class="quadsHideRating" title="No, not good enough" style="font-weight:bold;">No, not good enough</a></li>
            <br>
            <li><a href="javascript:void(0);" class="quadsHideRatingWeek" title="No, not good enough" style="font-weight:bold;">I want to rate it later. Ask me again in a week!</a></li>
            <li class="spinner" style="float:none;display:list-item;margin:0px;"></li>        
</ul>

    </div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.quadsHideRating\').click(function(){
    jQuery(".spinner").addClass("is-active");
        var data={\'action\':\'quads_hide_rating\'}
             jQuery.ajax({
        
        url: "' . admin_url( 'admin-ajax.php' ) . '",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
               jQuery(".spinner").removeClass("is-active");
               jQuery(\'.quads_fivestar\').slideUp(\'fast\');
			   
            }
        }
         });
        })
    
        jQuery(\'.quadsHideRatingWeek\').click(function(){
        jQuery(".spinner").addClass("is-active");
        var data={\'action\':\'quads_hide_rating_week\'}
             jQuery.ajax({
        
        url: "' . admin_url( 'admin-ajax.php' ) . '",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
               jQuery(".spinner").removeClass("is-active");
               jQuery(\'.quads_fivestar\').slideUp(\'fast\');
			   
            }
        }
         });
        })
    
    });
    </script>
    ';
    }
}

add_action( 'admin_notices', 'quads_admin_messages' );


/* Hide the rating div
 * 
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.9
 * 
 * @return json string
 * 
 */

function quads_hide_rating_div() {
    update_option( 'quads_rating_div', 'yes' );
    delete_option( 'quads_date_next_notice' );
    echo json_encode( array("success") );
    exit;
}
add_action( 'wp_ajax_quads_hide_rating', 'quads_hide_rating_div' );

/**
 * Write the timestamp when rating notice will be opened again
 */
function quads_hide_rating_notice_week() {
    $nextweek = time() + (7 * 24 * 60 * 60);
    $human_date = date( 'Y-m-d h:i:s', $nextweek );
    update_option( 'quads_date_next_notice', $human_date  );
    update_option( 'quads_rating_div', 'yes'  );
    echo json_encode( array("success") );
    exit;
}
add_action( 'wp_ajax_quads_hide_rating_week', 'quads_hide_rating_notice_week' );

/**
 * Check if admin notice will open again after one week of closing
 * @return boolean
 */
function quads_rate_again(){
        
    $rate_again_date = get_option( 'quads_date_next_notice' );

    if (false === $rate_again_date){
        return false;
    }

    $current_date = date( 'Y-m-d h:i:s' );
    $datetime1 = new DateTime( $rate_again_date );
    $datetime2 = new DateTime( $current_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );

    if ($diff_intrval >= 0){
        return true;
    }
}


/**
 * Show a message when pro or free plugin gets disabled
 * 
 * @return void
 * @not used
 */
function quads_plugin_deactivated_notice() {
    if( false !== ( $deactivated_notice_id = get_transient( 'quads_deactivated_notice_id' ) ) ) {
        if( '1' === $deactivated_notice_id ) {
            $message = __( "WP QUADS and WP QUADS Pro cannot be activated both. We've automatically deactivated WP QUADS.", 'wpstg' );
        } else {
            $message = __( "WP QUADS and WP QUADS Pro cannot be activated both. We've automatically deactivated WP QUADS Pro.", 'wpstg' );
        }
        ?>
        <div class="updated notice is-dismissible" style="border-left: 4px solid #ffba00;">
            <p><?php echo esc_html( $message ); ?></p>
        </div> <?php
        delete_transient( 'quads_deactivated_notice_id' );
    }
}

/**
 * This notice is shown for user of the bimber and bunchy theme
 * 
 * Not used at the moment
 */
function quads_theme_notice(){
    
    $show_notice = get_option('quads_show_theme_notice');
    
        if( false !== $show_notice && 'no' !== $show_notice && quads_is_commercial_theme() )  {
            $message = __( '<strong>Extend the <strong>' . quads_is_commercial_theme(). '</strong> theme with <strong>WP QUADS PRO!</strong><br>Save time and earn more - Bring your AdSense earnings to next level. <a href="http://wpquads.com?utm_campaign=adminnotice&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice" target="_blank"> Purchase Now</a> or <a href="http://wpquads.com?utm_campaign=free_plugin&utm_source=admin_notice&utm_medium=admin&utm_content=bimber_upgrade_notice" target="_blank">Get Details</a></strong> <p> <a href="'.admin_url().'admin.php?page=quads-settings&quads-action=close_upgrade_notice" class="button">Close Notice</a>', 'quick-adsense-reloaded' );
        ?>
        <div class="updated notice" style="border-left: 4px solid #ffba00;">
            <p><?php echo $message; ?></p>
        </div> <?php
        //update_option ('quads_show_theme_notice', 'no');
    }
}

/**
 * This notice is shown after updating to 1.3.9
 * 
 */
function quads_update_notice() {

    $show_notice = get_option( 'quads_show_update_notice' );

    // do not do anything
    if( false !== $show_notice ) {
        return false;
    }

    if( (version_compare( QUADS_VERSION, '1.3.9', '>=' ) ) && quads_is_advanced() && (version_compare( QUADS_PRO_VERSION, '1.3.0', '<' ) ) ) {
        $message = sprintf( __( '<strong>WP QUADS ' . QUADS_VERSION . ': <strong> Update WP QUADS PRO to get custom post type support from <a href="%s">General Settings</a>.', 'quick-adsense-reloaded' ), admin_url() . 'admin.php?page=quads-settings' );
        $message .= '<br><br><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Close Notice</a>';
?>
                        <div class="updated notice" style="border-left: 4px solid #ffba00;">
                            <p><?php echo $message; ?></p>
                        </div> <?php
        //update_option ('quads_show_update_notice', 'no');
    } else
        if( !quads_is_advanced() ) {
        $message = sprintf( __( '<strong>WP QUADS ' . QUADS_VERSION . ': <strong> Install <a href="%1s" target="_blank">WP QUADS PRO</a> to get custom post type support in <a href="%2s">General Settings</a>.', 'quick-adsense-reloaded' ), 'http://wpquads.com?utm_campaign=admin_notice&utm_source=admin_notice&utm_medium=admin&utm_content=custom_post_type', admin_url() . 'admin.php?page=quads-settings' );
        $message .= '<br><br><a href="' . admin_url() . 'admin.php?page=quads-settings&quads-action=hide_update_notice" class="button-primary thankyou" target="_self" title="Close Notice" style="font-weight:bold;">Close Notice</a>';
?>
                        <div class="updated notice" style="border-left: 4px solid #ffba00;">
                            <p><?php echo $message; ?></p>
                        </div>
        <?php
    }
}
/**
 * Hide Notice and update db option quads_hide_notice
 */
function quads_hide_notice(){
    update_option ('quads_show_update_notice', 'no');
}
add_action('quads_hide_update_notice', 'quads_hide_notice', 10);

/**
 * Check if any ad is activated and assigned in general settings
 * 
 * @global array $quads_options
 * @return boolean
 */
function quads_is_any_ad_activated() {
    global $quads_options;

    // Check if custom positions location_settings is empty or does not exists
    $check = array();
    if( isset( $quads_options['location_settings'] ) ) {
        foreach ( $quads_options['location_settings'] as $location_array ) {
            if( isset( $location_array['status'] ) ) {
                $check[] = $location_array['status'];
            }
        }
    }
    //wp_die(print_r($check));

    if( count( $check ) === 0 &&
            !isset( $quads_options['pos1']['BegnAds'] ) &&
            !isset( $quads_options['pos2']['MiddAds'] ) &&
            !isset( $quads_options['pos3']['EndiAds'] ) &&
            !isset( $quads_options['pos4']['MoreAds'] ) &&
            !isset( $quads_options['pos5']['LapaAds'] ) &&
            !isset( $quads_options['pos6']['Par1Ads'] ) &&
            !isset( $quads_options['pos7']['Par2Ads'] ) &&
            !isset( $quads_options['pos8']['Par3Ads'] ) &&
            !isset( $quads_options['pos9']['Img1Ads'] ) ) {
        return false;
    }
    return true;
}

/**
 * Check if any post type is enabled
 * 
 * @global array $quads_options
 * @return boolean
 */
function quads_is_post_type_activated(){
        global $quads_options;

        if (empty($quads_options['post_types'])){
            return false;
        }
        return true;
}

/**
 * Check if ad codes are populated
 * 
 * @global array $quads_options
 * @return booleantrue if ads are empty
 */
function quads_ads_empty() {
    global $quads_options;

    $check = array();

    for ( $i = 1; $i <= 10; $i++ ) {
        if( !empty( $quads_options['ad' . $i]['code'] ) ) {
            $check[] = 'true';
        }
    }
    if( count( $check ) === 0 ) {
        return true;
    }
    return false;
}
