<?php

function wsbd_youtube_api_call() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_data';
    $date = date('Y-m-d H:i:s');    
    $select_query = "SELECT channelID from `$table_name`";
    $channelIDs = $wpdb->get_results($select_query, ARRAY_A);

    /* API Key */
    $options = get_option( 'wsbd_youtube_channel_data_settings' );
    $youtube_apikey = trim($options['apikey']);

    foreach($channelIDs as $channelID) {

        $channel_icon = wsbd_youtube_channel_icon($channelID, $youtube_apikey);
        $channelTitle = wsbd_youtube_channel_title($channelID, $youtube_apikey);
        $videoCount = wsbd_youtube_channel_count($channelID, $youtube_apikey, 'videoCount');
        $subscriberCount = wsbd_youtube_channel_count($channelID, $youtube_apikey, 'subscriberCount');
        $viewCount = wsbd_youtube_channel_count($channelID, $youtube_apikey, 'viewCount');
        $latestVideoID = wsbd_youtube_channel_latest_video($channelID, $youtube_apikey);
        //https://i.ytimg.com/vi/E4v2jeulyD8/default.jpg

        $popularVideoID = wsbd_youtube_channel_popular_video($channelID, $youtube_apikey);
        //https://i.ytimg.com/vi/Z1LctxzEREE/default.jpg


        wsbd_update_channel_data($channelID, $channel_icon, $channelTitle, $videoCount, $latestVideoID, $popularVideoID, $subscriberCount, $viewCount );

    }      
}


// podcast api call function 
function wsbd_podcast_api_call() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'podcast_data';
    $date = date('Y-m-d H:i:s');    
    $select_query = "SELECT podcast_id from `$table_name`";
    $podcast_ids = $wpdb->get_results($select_query, ARRAY_A);

    foreach($podcast_ids as $podcast_id) {

        wsbd_lookup_podcast_data($podcast_id);

    }      
}

?>