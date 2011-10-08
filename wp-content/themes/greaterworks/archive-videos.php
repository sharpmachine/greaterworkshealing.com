<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="page" class="span-8">
<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		
<?php $vimeo_id = get_post_meta( $post->ID, 'vimeo_id', true );?>
			<div class="vimeo-thumb">
				<a href="<?php the_permalink(); ?>"><img src="<?php $imgid = $vimeo_id;
			 					$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$imgid.php"));
			 					echo $hash[0]['thumbnail_medium']; ?>" class="vimeo"></a>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		</div>
	<?php endwhile; ?>
	<?php else : ?>
		<p>Sorry, no videos yet</p>
<?php endif; ?>

		</div><!-- #page -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
