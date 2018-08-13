<?php
/*
Plugin Name: WSBD YouTube Podcast API
Version: 3.0
Plugin URI: https://www.upwork.com/o/profiles/users/_~01c0d51a3194de2650/
Description: Render YouTube video count, channel subscribers, or total video views into WP with shortcodes.
Author: Ibnul Hasan
Author URI: https://www.upwork.com/o/profiles/users/_~01c0d51a3194de2650/
*/

include_once(dirname(__FILE__) . '/inc/wsbd-functions.php');
include_once(dirname(__FILE__) . '/inc/wsbd-add-new-channel.php');
include_once(dirname(__FILE__) . '/inc/wsbd-api-call.php');
include_once(dirname(__FILE__) . '/inc/wsbd-itunes-api.php');

register_activation_hook(__FILE__, 'wsbd_yt_channel_activation');

function wsbd_yt_channel_activation() {
    if (! wp_next_scheduled ( 'wsbd_yt_api_call_event' )) {
        wp_schedule_event(time(), 'hourly', 'wsbd_yt_api_call_event');
    }
}

add_action('wsbd_yt_api_call_event', 'wsbd_call_yt_api_save');

function wsbd_call_yt_api_save() {
    // do something every day
    wsbd_youtube_api_call(); 
    wsbd_podcast_api_call();   
}

register_deactivation_hook(__FILE__, 'wsbd_yt_channel_deactivation');

function wsbd_yt_channel_deactivation() {
    wp_clear_scheduled_hook('wsbd_yt_api_call_event');
}


/*
	Admin Options
*/

/********************* add Settings link ************************/
function wsbd_ytchannel_plugin_settngs_links( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/admin.php?page=youtube-channel-settings' ) ) . '">' . __( 'Settings', 'ytchannel' ) . '</a>'
	), $links );
	return $links;
}
    add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wsbd_ytchannel_plugin_settngs_links' );

/********************* /end add Settings link ************************/
?>