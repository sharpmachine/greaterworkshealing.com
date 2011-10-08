<?php get_header(); ?>

<?php // Let's get the data we need

	$vimeo_id = get_post_meta( $post->ID, 'vimeo_id', true );

?>

	<div id="page" class="span-8">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				
				<div class="entry-meta">
					<?php echo get_the_term_list( $post->ID, 'video_tags', 'Tagged: ', ', ', '' ); ?>
			</div>
			<div class="entry-content">
				<?php if ($vimeo_id) { // Check for a video ?>
					<iframe src="http://player.vimeo.com/video/<?php echo $vimeo_id; ?>?byline=0&title=0&portrait=0" width="636" height="358" frameborder="0" class="vimeo"></iframe>
				<?php } ?>

					<p><?php the_content(); ?></p>
					<a href="<?php bloginfo('url'); ?>/videos">Back to Videos</a>
			</div>
			
			<?php comments_template( '', true ); ?>
		<?php endwhile; ?>
		
		
		
		
		</div>
	</div><!--end page-->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
