<?php get_header(); ?>

		<div id="page" class="single-attachment span-8">

			<?php
			/* Run the loop to output the attachment.
			 * If you want to overload this in a child theme then include a file
			 * called loop-attachment.php and that will be used instead.
			 */
			get_template_part( 'loop', 'attachment' );
			?>

		</div><!-- #page -->

<?php get_footer(); ?>
