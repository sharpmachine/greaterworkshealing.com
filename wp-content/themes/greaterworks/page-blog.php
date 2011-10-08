<?php 
/*
	Template Name: Blog
*/
get_header(); ?>

		<div id="page" class="span-8">
		<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
	<?php else : ?>
<?php endif; ?>

<?php wp_reset_query();?>
	<?php wp_reset_query();?>
	<?php query_posts("showposts=10"); ?>
	<?php
			/* Run the loop to output the posts.
			 * If you want to overload this in a child theme then include a file
			 * called loop-index.php and that will be used instead.
			 */
			 get_template_part( 'loop', 'index' );
			?>
		</div><!-- #page -->
		
<?php get_sidebar(); ?>
<?php get_footer(); ?>
