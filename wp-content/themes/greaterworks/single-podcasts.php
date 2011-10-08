<?php get_header(); ?>

	<div id="page" class="span-8">
	
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-meta">
					<?php twentyten_posted_on(); ?>
					
				</div>
				<div class="entry-content">
					<?php the_content(); ?>
					<p><?php echo get_the_term_list( $post->ID, 'podcast_tags', 'Tagged ', ', ', '' ); ?></p>
					<a href="<?php bloginfo('url'); ?>/podcasts">Back to Podcasts</a>
				</div>
				<?php comments_template( '', true ); ?>
		<?php endwhile; ?>
		
	</div><!--end page-->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
