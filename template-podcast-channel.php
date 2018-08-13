<?php
/**
 * Template Name: Podcasts Channel
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
		$class = ' full-width';
	}
}

if( post_password_required() ) {
	$class .= ' container no-vc-active';
}

?>

<style type="text/css">
	.table-heading th {
	    text-align: left;
	    padding: 10px;
	}
	.table-heading th.column-1, .table-heading th.column-4 {
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
	.podcasters td.column-1, .podcasters td.column-4 {
	    width: 10%;
	    text-align: center;
	}
	.podcasters td { 
		padding: 10px;
		width: 35%;
		text-align: left;
		position: relative;
		line-height: 20px;
	}
	.podcasters td.column-1 img {
	    border-radius: 10%;
	}
	.podcasters td.column-2 {
	    text-align: left;
	}
	.column-3 a {
	    display: block;
	}
	.column-3 .release-date {
		color: #999;
	}


	@media only screen and (max-width: 776px) {

	    .podcasters-page .container {
		    max-width: 100%;
		}
	}
</style>

	<div id="primary" class="content-area podcasters-page">
		
		<main id="main" class="site-main<?php echo esc_attr( $class ); ?>">

				<?php 
					if ( $composer_page_layout == 'right-sidebar' || $composer_page_layout == 'left-sidebar' || $composer_page_layout == 'right-nav' || $composer_page_layout == 'left-nav' ) {
						echo '<div class="row padding-top">';

						echo '<div class="col-md-9 col2-layout">';
					}
				?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php the_content(); ?>

<div class="podcasters">	
	<table>
		<tbody>
			<tr class="table-heading">
				<th class="column-1">Podcaster</th>
				<th class="column-2">Name</th>				
				<th class="column-3">Recent Episode</th>
				<th class="column-4">Links</th>
			</tr>
			
<?php		
	global $wpdb;
	$table_name = $wpdb->prefix . 'podcast_data';    
	$select_query = "SELECT * from `$table_name` ORDER BY UNIX_TIMESTAMP(`recent_release_time`) DESC";

	$podcast_data = $wpdb->get_results($select_query, ARRAY_A);

	$row_count = 1;

	foreach ($podcast_data as $single_podcast) {

		$icon_url = $single_podcast['podcasters_logo'];
		$podcaster_title = $single_podcast['podcasters_name'];
		$recent_url = $single_podcast['recent_url'];
		$recent_title = $single_podcast['recent_title'];
		$itunes_url = $single_podcast['itunes_url'];
		$stitcher_url = $single_podcast['stitcher_url'];
		$recent_release_time = $single_podcast['recent_release_time'];
		
	    $recent_release_time = strtotime($recent_release_time);
	    $recent_release_date = date('m/d/Y', $recent_release_time);

		if ( empty($recent_url) ) {
			$recent_url = 'javascript:void(0)';
		}


		if( $icon_url != NULL && $podcaster_title != NULL && $itunes_url != NULL) {
			if($row_count % 2 == 0) $row_class = "even_row";
			else $row_class = "odd_row";
			?>

			<tr class="row-<?php echo $row_count; ?> <?php echo $row_class; ?>">
				<td class="column-1"><img width="100px" src="<?php echo $icon_url; ?>"></td>
				<td class="column-2"><?php echo $podcaster_title; ?></td>
				<td class="column-3">
					<a target="_blank" href="<?php echo $recent_url; ?>"><?php echo $recent_title; ?></a>
					<span class="release-date">Released on: <?php echo $recent_release_date; ?></span>
				</td>
				<td class="column-4">
					<?php if ( !empty($itunes_url) ) { ?>					
						<a href="<?php echo $itunes_url; ?>" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/itunes-logo-50px.png" width="47px" alt="itune-logo"></a>
					<?php } ?>
					<?php if ( !empty($stitcher_url) ) { ?>
						<a href="<?php echo $stitcher_url; ?>" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/stitcher-logo-40px.png" alt="stitcher-logo"></a>
					<?php } ?>
				</td>

			</tr>			
	<?php 
		}
		$row_count++;
	}
	?>
				
			</tbody>
		</table>
		
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
