<?php get_header(); ?>

		<div id="page" class="span-8">

			<?php
			/* Run the loop to output the page.
			 * If you want to overload this in a child theme then include a file
			 * called loop-page.php and that will be used instead.
			 */
			get_template_part( 'loop', 'page' );
			?>
		</div><!-- #page -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
