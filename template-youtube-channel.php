<?php
/**
 * Template Name: Youtubers Channel
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Composer
 */

get_header(); 

$composer_page_layout = composer_get_meta_value( get_the_ID(), '_amz_layout', 'default' );

//Sidebar
$composer_selected_sidebar = composer_get_meta_value( get_the_ID(), '_amz_sidebar', '0' );

$class = composer_check_vc_active();

if( empty( $class ) ) {
	if ( $composer_page_layout == 'right-sidebar' || $composer_page_layout == 'left-sidebar' || $composer_page_layout == 'right-nav' || $composer_page_layout == 'left-nav' ) {
		$class = ' container';
	}
}

if( post_password_required() ) {
	$class .= ' container no-vc-active';
}
?>

<div id="primary" class="content-area youtube-page">		
	<main id="main" class="site-main<?php echo esc_attr( $class ); ?>">

		<?php 
		if ( $composer_page_layout == 'right-sidebar' || $composer_page_layout == 'left-sidebar' || $composer_page_layout == 'right-nav' || $composer_page_layout == 'left-nav' ) {
			echo '<div class="row padding-top">';

			echo '<div class="col-md-9 col2-layout">';
		}
		?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php the_content(); ?>

			<?php
			global $wpdb;
			$table_name = $wpdb->prefix . 'youtube_data';    
			$select_query = "SELECT * from `$table_name` ORDER BY subscribers DESC";

			$channel_data = $wpdb->get_results($select_query, ARRAY_A);
			//var_dump($channel_data);
			?>

			<style type="text/css">
				.table-heading th {
				    text-align: center;
				}
				.table-heading {
				    background: #39608F;
				    color: #fff;
				    height: 50px;
				    width: 100%;
				}
				.even_row {
				    background: #f0f0f0;
				}
				.youtube-channel-list td { 
					padding: 10px;
					width: 10%;
					text-align: center;
					position: relative;
				}
				.youtube-channel-list td.column-1 img {
				    border-radius: 50%;
				}
				.table-heading .column-2 {
				    text-align: left;
				    padding: 10px;
				}
				.youtube-channel-list td.column-2 {
				    text-align: left;
				}

				@media only screen and (max-width: 776px) {
				    .column-1, .column-3, .column-4, .column-5, .column-6  {
				        display: none;
				    }
				    .youtube-page .container {
					    max-width: 100%;
					}
				}
			</style>
			<script src="https://apis.google.com/js/platform.js"></script>

			<table class="youtube-channel-list">
				<tbody>
					<tr class="table-heading">
						<th class="column-1">Channel Image</th>
						<th class="column-2">Youtuber</th>
						<th class="column-3">Total Videos</th>
						<th class="column-4">Latest Video</th>
						<th class="column-5">Popular Video</th>
						<th class="column-6">Subscribers</th>
						<th class="column-7">Total Views</th>
						<th class="column-8">Subscribe</th>
					</tr>
					<?php
					$row_count = 1;

					foreach ($channel_data as $single_channel) {
						$channel_ID = $single_channel['channelID'];
						$channel_icon = $single_channel['channelimage'];
						$channel_title = $single_channel['youtuber'];
						$total_videos = $single_channel['totalvideos'];
						$latest_video_ID = $single_channel['latestvideo'];
						$popular_video_ID = $single_channel['popularvideo'];
						$subscribers = $single_channel['subscribers'];
						$total_views = $single_channel['totalviews'];
						//var_dump($channel_icon);

						if( $channel_icon != NULL && $channel_title != NULL && $total_videos != 0) {

							if($row_count % 2 == 0) $row_class = "even_row";
							else $row_class = "odd_row";
							?>

							<tr class="row-<?php echo $row_count; ?> <?php echo $row_class; ?>">
								<td class="column-1">
									<a href="https://www.youtube.com/channel/<?php echo $channel_ID; ?>" target="_blank">
										<img src="<?php echo $channel_icon; ?>" alt="channel icon">
									</a>
								</td>
								<td class="column-2">
									<?php echo $channel_title; ?>								
								</td>
								<td class="column-3">
									<?php echo $total_videos; ?>								
								</td>
								<td class="column-4">
									<a href="https://www.youtube.com/watch?v=<?php echo $latest_video_ID; ?>" class="fancybox-youtube" title="Click to watch video">
										<img src="https://i.ytimg.com/vi/<?php echo $latest_video_ID; ?>/default.jpg" alt="Latest video">
										<div class="play-icon"></div>
									</a>
								</td>
								<td class="column-5">
									<a href="https://www.youtube.com/watch?v=<?php echo $popular_video_ID; ?>" class="fancybox-youtube" title="Click to watch video">
										<img src="https://i.ytimg.com/vi/<?php echo $popular_video_ID; ?>/default.jpg" alt="Popular video">
										<div class="play-icon"></div>
									</a>
								</td>
								<td class="column-6">
									<?php echo $subscribers; ?>								
								</td>
								<td class="column-7">
									<?php echo $total_views; ?>								
								</td>
								<td class="column-8">
									<div class="g-ytsubscribe" data-channelid="<?php echo $channel_ID; ?>" data-layout="default" data-count="default"></div>
								</td>
							<?php
							echo "</tr>";
							$row_count++;
						} // End of if statement
					} // End of foreach
					?>	
				</tbody>
			</table>

			<div class="vc_row wpb_row vc_row-fluid container" style="padding: 20px 0">
				<div class="wpb_column vc_column_container vc_col-sm-12">
					<div class="vc_column-inner ">
						<div class="wpb_wrapper">
							<div class="wpb_text_column wpb_content_element ">
								<div class="wpb_wrapper">
									<h2>Add Your Channel</h2>
									<p><img class="alignright size-full wp-image-9448" src="https://finconexpo.com/wp-content/uploads/2017/08/FinCon-Youtuber-Badge-e1504274501737.png" alt="FinCon Youtuber Badge" width="200" height="201" />Above is the first-ever mega list of the best personal finance and investing Youtubers and channels active today, ranked by subscribers.</p>
									<p>Are you a Youtuber? Please <a href="mailto:admin@finconexpo.com">email</a> us to have your channel considered for inclusion.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if ( comments_open() ) : ?>
				<div class="container page-comments">
					<?php
						//Show/Hide comment section
	                    comments_template();
					?>
				</div>
			<?php endif; ?>

		<?php endwhile; // End of the loop. ?>
		
		<?php 
		if ( $composer_page_layout == 'right-sidebar' || $composer_page_layout == 'left-sidebar' || $composer_page_layout == 'right-nav' || $composer_page_layout == 'left-nav' ) {

			echo '</div>'; //col-md-9

			//If the sidebar position is right or left sidebar, it ll apply
			if( 'full-width' != $composer_page_layout || 'default' != $composer_page_layout ){

				if ( $composer_page_layout == 'right-sidebar' || $composer_page_layout == 'left-sidebar' ) {

					composer_sidebar( $composer_selected_sidebar , 'primary-sidebar' );

				//If the Side Menu Position is right or left, it ll apply
				} elseif( $composer_page_layout == 'right-nav' || $composer_page_layout == 'left-nav' ) {
					echo '<div id="aside" class="sidebar col-md-3">';
						if($composer_page_layout == 'left-nav' ){
							composer_side_nav( $composer_page_layout, 'left');
						} elseif($composer_page_layout == 'right-nav' ) {
							composer_side_nav($composer_page_layout, 'right');									
						}
					echo '</div>';
				}
			}

			echo '</div>'; //row
		}
		?>

		</main><!-- #main -->
	</div><!-- #primary -->
	
<?php get_footer(); ?>
