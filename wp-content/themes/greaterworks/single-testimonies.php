<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="page" class="span-8">
	
			<div class="feature-box">
				<div class="box"><h4>Were you healed at a Great Works meeting or in our online healing rooms?  <a href="<?php bloginfo('url'); ?>/submit-a-testimony">Submit a testimony!</a></h4></div>
			</div>
			
			<?php
			/* Run the loop to output the post.
			 * If you want to oerload this in a child theme then include a file
			 * called loop-single.php and that will be used instead.
			 */
			get_template_part( 'loop', 'single' );
			?>
			
		</div><!-- #page -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
