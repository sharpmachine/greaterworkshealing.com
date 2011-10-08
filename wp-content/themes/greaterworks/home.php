<?php
/*
	Template Name: Home Page
*/
 get_header(); ?>

<div id="page" class="span-8">
	
			<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
	<?php else : ?>
		<div class="feature-box">
			<div class="box">
				<h2>Welcome to Greater Works Healing Ministries</h2>
				<p>Here we are all about seeing God touch humans lives and equipping everyone to GO and see heaven move through them. We are passionate about miracles, healing and revival and we want to see the fire of pentacost land on you to change the face of this earth!</p>
				<p>Mark and Cathy Kuntz (founders of Greater Works healing ministry) have a heart to see everyone infused with the healing power of God and equipped to spead it around the world bringing revival to this planet!</p>
			</div>
		</div>
<?php endif; ?>
		
		<div class="feature-box">
			<div class="box">
				<h3>Our Next Event:</h3>
			</div>
		</div>

		<div id="em-wrapper">
			<div><?php echo do_shortcode( '[events_list limit="1"]' ); ?></div>
		</div>
	<a href="<?php bloginfo('url'); ?>/events" style="margin-left: 10px;">See all upcoming events</a>
	
</div><!--end page-->
<?php get_sidebar(); ?> 
<?php get_footer(); ?>