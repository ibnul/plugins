<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function wsbd_youtube_channel_api_call() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_data';
    $date = date('Y-m-d H:i:s');    
    $select_query = "SELECT channelID from `$table_name`";
    $channelIDs = $wpdb->get_results($select_query, ARRAY_A);

    /* API Key */
    $options = get_option( 'wsbd_youtube_channel_data_settings' );
    $youtube_apikey = trim($options['apikey']);

    foreach($channelIDs as $channelID) {

        //$channel_icon = wsbd_youtube_channel_icon($channelID, $youtube_apikey);
        //$channelTitle = wsbd_youtube_channel_title($channelID, $youtube_apikey);
        //$videoCount = wsbd_youtube_channel_count($channelID, $youtube_apikey, 'videoCount');
        //$subscriberCount = wsbd_youtube_channel_count($channelID, $youtube_apikey, 'subscriberCount');
        //$viewCount = wsbd_youtube_channel_count($channelID, $youtube_apikey, 'viewCount');
        $video_subscribe_view = wsbd_youtube_channel_count($channelID, $youtube_apikey);

        
        $channel_icon = $video_subscribe_view['channel_icon'];
        $channelTitle = $video_subscribe_view['channelTitle'];
        $videoCount = $video_subscribe_view['videoCount'];
        $subscriberCount = $video_subscribe_view['subscriberCount'];
        $viewCount = $video_subscribe_view['viewCount'];

        $latestVideoID = wsbd_youtube_channel_latest_video($channelID, $youtube_apikey);
        //https://i.ytimg.com/vi/E4v2jeulyD8/default.jpg
        $popularVideoID = wsbd_youtube_channel_popular_video($channelID, $youtube_apikey);
        //https://i.ytimg.com/vi/Z1LctxzEREE/default.jpg

        if( $channel_icon != NULL && $channelTitle != NULL && $videoCount != 0){
            wsbd_update_channel_data($channelID, $channel_icon, $channelTitle, $videoCount, $latestVideoID, $popularVideoID, $subscriberCount, $viewCount );
        }

    }    
}


// podcast api call function 
function wsbd_podcast_channel_api_call() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'podcast_data';
    $date = date('Y-m-d H:i:s');    
    $select_query = "SELECT podcast_id from `$table_name` WHERE `updateTime` <= NOW() - INTERVAL 12 HOUR LIMIT 100";
    $podcast_ids = $wpdb->get_results($select_query, ARRAY_A);

    foreach($podcast_ids as $podcast_id) {
        wsbd_lookup_podcast_data($podcast_id);
    }

    //wsbd_ypcd_update_log( count($podcast_ids) ." ids of podcasters updated");
}

?>