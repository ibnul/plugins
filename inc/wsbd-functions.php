<?php 

/********************************** Start channel ID *****************/
function wsbd_getChannelID( $userName, $apikey ){
	$channelurl = "https://www.googleapis.com/youtube/v3/channels?key=". $apikey ."&forUsername=". $userName ."&part=id";

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $channelurl );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $channelID = json_decode( curl_exec( $ch ) );

    //var_dump($channelID->items[0]->id);
    $channelID = $channelID->items[0]->id;

    return $channelID;
}

/********************************** End channel ID *****************/


// get itunes id from url
function wsbd_getPodcastID($itunes_url){
	preg_match('/id(\d+)/', $itunes_url, $podcast_id);

	return $podcast_id[1];
}

/********************************** Start Channel icon *****************/

function wsbd_youtube_channel_icon($channelID, $apikey){

	$url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id=" . $channelID['channelID'] ."&key=". $apikey ."&fields=items%2Fsnippet%2Fthumbnails%2Fdefault";

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$channelOBJ = json_decode( curl_exec( $ch ) );

	$thumbnail_url = $channelOBJ->items[0]->snippet->thumbnails->default->url;

	return $thumbnail_url;
}
/********************************** End Channel icon *****************/


/********************************** Start channel title *****************/

function wsbd_youtube_channel_title($channelID, $apikey){

	$url = "https://www.googleapis.com/youtube/v3/search?channelId=" . $channelID['channelID'] . "&key=" . $apikey ."&part=snippet";

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$channelOBJ = json_decode( curl_exec( $ch ) );

	$channelTitle = $channelOBJ->items[0]->snippet->channelTitle;

	return $channelTitle;

}
/********************************** End channel title *****************/


/********************************** Start channel Count *****************/
function wsbd_youtube_channel_count($channelID, $apikey, $type){

	$json_string = @file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channelID['channelID'] . '&key=' . $apikey );
   if ($json_string !== false) $json = json_decode($json_string, true);
  
   if ( (is_array($json)) && (count($json) != 0) ) {

        $temp = array();
        $temp['viewCount'] = $json['items']['0']['statistics']['viewCount'];
        $temp['subscriberCount'] = $json['items']['0']['statistics']['subscriberCount'];
        $temp['videoCount'] = $json['items']['0']['statistics']['videoCount'];

        return $temp["$type"];

 	}else {
    	return 'Unavailable';
  	}
}
/********************************** End channel count *****************/

/********************************** Start Latest Video ****************/
function wsbd_youtube_channel_latest_video($channelID, $apikey) {

	$url = "https://www.googleapis.com/youtube/v3/search?channelId=" . $channelID['channelID'] . "&key=" . $apikey ."&part=snippet,id&order=date&maxResults=2";

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$channelOBJ = json_decode( curl_exec( $ch ) );

	$latestVideoID = $channelOBJ->items[0]->id->videoId; 
	
	return $latestVideoID;
	//return '<a href="https://www.youtube.com/watch?v='. $videoId .'" class="fancybox-youtube" title="Click to watch video"><img src="'. $thumbnail_url .'" alt="Latest video" /><div class="play-icon"></div></a>';
}
/********************************** End Latest Video *****************/


/********************************** Start Popular Video *****************/
function wsbd_youtube_channel_popular_video($channelID, $apikey) {
	$url = "https://www.googleapis.com/youtube/v3/search?channelId=" . $channelID['channelID'] . "&key=" . $apikey ."&part=snippet,id&maxResults=2&sort=-views";

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$channelOBJ = json_decode( curl_exec( $ch ) );

	//$popularVideo['videoId'] = $channelOBJ->items[0]->id->videoId; 
	//$popularVideo['thumbnail_url'] = $channelOBJ->items[0]->snippet->thumbnails->default->url;
	$popularVideoID = $channelOBJ->items[0]->id->videoId;
	return $popularVideoID;
}
/********************************** End Popular Video *****************/

// function to update youtube data 
// dbTable name : wp_youtube_data
function wsbd_update_channel_data($channelID, $channel_icon, $channelTitle, $videoCount, $latestVideoID, $popularVideoID, $subscriberCount, $viewCount ){
	global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_data';
    $date = date('Y-m-d H:i:s');

	if( !empty($channelID) ){
            
            $update_query = $wpdb->query( $wpdb->prepare( 
                "UPDATE $table_name 
                SET channelimage = %s,
                youtuber = %s,
                totalvideos = %d,
                latestvideo = %s,
                popularvideo = %s,
                subscribers = %d,
                totalviews = %d,
                updateTime = %s
                WHERE channelID = %s                
                ", $channel_icon, $channelTitle, $videoCount, $latestVideoID, $popularVideoID, $subscriberCount, $viewCount, $date, $channelID['channelID']                
            ) ); 
        }
} 


// function lookup podcast data
function wsbd_lookup_podcast_data($podcast_id){
	$podcast = iTunes::lookup($podcast_id['podcast_id'], 'id', array(
		'entity' => 'podcast'
	))->results;

//echo '<pre>' . var_export($result_array, true) . '</pre>';

	foreach($podcast as $item){
		$url = $item->feedUrl;
		$icon_url = $item->artworkUrl100;
		$trackViewUrl = $item->trackViewUrl;
		$podcasters_name = $item->collectionName;
		$recent_release_time = $item->releaseDate;
	}

	if($url != "") {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($curl);

		$xml = simplexml_load_string($data);

		foreach ($xml->children()->children()->image->url as $podcasters_logo) {
			$podcasters_logo = $podcasters_logo;
		}
		foreach ($xml->children()->children()->item->title as $recent_title) {
			$recent_title = $recent_title;
		}
		foreach ($xml->children()->children()->item->link as $recent_url) {
			$recent_url = $recent_url;
		}
	}

	wsbd_update_podcast_data($podcast_id['podcast_id'], $trackViewUrl, $icon_url, $podcasters_name, $recent_title[0], $recent_url[0], $recent_release_time );

}

// update podcast channel data function
function wsbd_update_podcast_data($podcast_id, $trackViewUrl, $icon_url, $podcasters_name, $recent_title, $recent_url, 
	$recent_release_time ){
	global $wpdb;
    $table_name = $wpdb->prefix . 'podcast_data';
    $date = date('Y-m-d H:i:s');

	if( !empty($podcast_id) ){
            
            $update_query = $wpdb->query( $wpdb->prepare( 
                "UPDATE $table_name 
                SET itunes_url = %s,
                podcasters_logo = %s,
                podcasters_name = %s,
                recent_title = %s,
                recent_url = %s,
                recent_release_time = %s,
                updateTime = %s
                WHERE podcast_id = %d                
                ", $trackViewUrl, $icon_url, $podcasters_name, $recent_title, $recent_url, $recent_release_time, $date, $podcast_id
            ) ); 
        }
} 
?>