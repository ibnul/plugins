<?php

// create custom plugin settings menu
add_action('admin_menu', 'wsbd_youtube_channel_menu');  
add_action( 'admin_init', 'wsbd_youtube_channel_settings_init' );

function wsbd_youtube_channel_menu() {

	//create new top-level menu
    add_menu_page('Youtube Channel', 'Youtube Channel', 'administrator', 'youtube-channel-settings', 'youtube_channel_settings_func' , 'dashicons-video-alt3' );
    add_submenu_page('youtube-channel-settings', 'Youtube Settings', 'Youtube  Settings', 'administrator', 'youtube-channel-settings', 'youtube_channel_settings_func' );
    add_submenu_page('youtube-channel-settings', 'Add New Channel', 'Add New Channel', 'administrator', 'add-new-youtube-channel', 'add_new_channel_func' );
	add_submenu_page('youtube-channel-settings', 'Add New Podcast', 'Add New Podcast', 'administrator', 'add-new-podcast-channel', 'add_new_podcast_func' );
}

function wsbd_youtube_channel_settings_init() {
    register_setting( 'youtubechannel', 'wsbd_youtube_channel_data_settings' );
    add_settings_section(
        'wsbd_youtube_channel_data_ytchannel_section',
        __( 'YouTube API Key', 'wordpress' ), 
        'wsbd_youtube_channel_data_settings_section_callback', 
        'youtubechannel'
    );
    add_settings_field( 
        'apikey', 
        __( 'API Key', 'wordpress' ), 
        'wsbd_apikey_render', 
        'youtubechannel', 
        'wsbd_youtube_channel_data_ytchannel_section' 
    );

}

function wsbd_apikey_render(  ) { 
    $options = get_option( 'wsbd_youtube_channel_data_settings' );
    ?>
    <input type='text' name='wsbd_youtube_channel_data_settings[apikey]' value='<?php echo $options['apikey']; ?>'>
    <?php
}


function wsbd_youtube_channel_data_settings_section_callback(  ) { 
    _e( 'You can register an API Key <a href="http://console.developers.google.com/project" target="_blank">here</a>.', 'wordpress' );
}

function youtube_channel_settings_func(){
    
   ?>
    <form action='options.php' method='post'>
        <h1>YouTube Channel Settings</h1>
<?php //wsbd_podcast_api_call(); ?>
        <?php

        settings_fields( 'youtubechannel' );
        do_settings_sections( 'youtubechannel' );
        submit_button();

        ?>
    </form>
    <?php
}


function add_new_channel_func() {
?>
<div class="wrap">
<h1>Add Youtube Channel</h1>

<form method="post">
    <input type="hidden" name="action" value="add" />
    <table class="form-table">
        <tr valign="top">
        <th scope="row">New Channel ID</th>
        <td><input type="text" name="new_youtube_channel_ID" value="" placeholder="UCtu-auoQOw5x6Ji0RcH_W5A" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">New Channel Name</th>
        <td><input type="text" name="new_youtube_channel_name" value="" placeholder="wallstsurvivor" /></td>
        </tr>
        <tr><td class="submit"><input type="submit" value="Save" /></td></tr>
        
    </table>
    
</form>
</div>
<?php } 


if( isset($_POST['action']) ){
    if( $_POST['action'] == 'add' ) {
        // Read posted values
        $channel_ID = $_POST['new_youtube_channel_ID'];
        $channel_name = $_POST['new_youtube_channel_name'];

        /* API Key */
        $options = get_option( 'wsbd_youtube_channel_data_settings' );
        $youtube_apikey = trim($options['apikey']);
        // Save the posted value
        if( !empty($channel_ID) || !empty($channel_name) ){
            if($channel_ID == ""){
                $channel_ID = wsbd_getChannelID($channel_name, $youtube_apikey);
            }
        
            $result = wsbd_add_new_channel($channel_ID, $channel_name);
            // Put an options updated message on the screen
            if($result == true){
                add_action('admin_notices', 'wsbd_add_new_channel_success');            
            }
            else{ 
                add_action('admin_notices', 'wsbd_add_new_channel_failed');
            } 
        }
    }

}

function wsbd_add_new_channel_success(){    ?>
    <div class="notice notice-success is-dismissible"><p><strong><?php _e('New Channel Added.', 'wsbd-yt-channel' ); ?></strong></p></div>
<?php
}

function wsbd_add_new_channel_failed(){    ?>
    <div class="notice notice-error is-dismissible"><p><strong><?php _e('Error in New Channel Add. Please check fields.', 'wsbd-yt-channel' ); ?></strong></p></div> 
<?php
}
// database queries
function wsbd_add_new_channel($channel_ID,$channel_name) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_data';
    $date = date('Y-m-d H:i:s');
    $query = "INSERT INTO `$table_name` (channelID, username, updateTime) VALUES (%s, %s, %s)";

    $result = $wpdb->query( $wpdb->prepare( $query, $channel_ID, 
        $channel_name, $date));

    return $result;
} 



// Add new podcast channel function
function add_new_podcast_func() { ?>
<div class="wrap">
<h1>Add Youtube Podcast</h1>

<form method="post">
    <input type="hidden" name="action" value="addPodcast" />
    <table class="form-table">
        <tr valign="top">
        <th scope="row">New iTunes URL</th>
        <td colspan="2"><input type="text" name="new_itunes_url" value="" placeholder="https://itunes.apple.com/us/podcast/.." /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">New Stitcher URL</th>
        <td colspan="2"><input type="text" name="new_stitcher_url" value="" placeholder="https://www.stitcher.com/podcast/stacking-benjamins" /></td>
        </tr>
        <tr><td class="submit"><input type="submit" value="Save" /></td></tr>
        
    </table>
    
</form>
</div>
<?php 
}


if( isset($_POST['action']) ){
    if( $_POST['action'] == 'addPodcast' ) {
        // Read posted values
        $itunes_url = $_POST['new_itunes_url'];
        $stitcher_url = $_POST['new_stitcher_url'];

        // Save the posted value
        if( !empty($itunes_url) || !empty($stitcher_url) ){

            $podcast_ID = wsbd_getPodcastID($itunes_url);

            //var_dump($podcast_ID);
           
            $result = wsbd_add_new_podcast($podcast_ID, $stitcher_url);
            // Put an options updated message on the screen
            if($result == true){
                add_action('admin_notices', 'wsbd_add_new_channel_success');            
            }
            else{ 
                add_action('admin_notices', 'wsbd_add_new_channel_failed');
            }
        }
    }
}


function wsbd_add_new_podcast($podcast_ID, $stitcher_url){
    global $wpdb;
    $table_name = $wpdb->prefix . 'podcast_data';
    $date = date('Y-m-d H:i:s');
    $query = "INSERT INTO `$table_name` (podcast_id, stitcher_url, updateTime) VALUES (%s, %s, %s)";

    $result = $wpdb->query( $wpdb->prepare( $query, $podcast_ID, $stitcher_url, $date));

    return $result;
}
?>