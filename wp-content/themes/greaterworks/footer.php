</div><!--end container>-->

<footer>
	<div id="contact-us">
		<div id="footer-button">
			<span>Contact Us</span>
		</div>
	</div>
	<div id="footer-bar">
		<div id="footer-form">
			<h3>Questions or comments? Drop us a line!</h3>
			 <?php echo do_shortcode( '[contact-form 1 "Contact Us"]' ); ?>
			 <p class="legal-print">&copy;<?php echo date("Y"); ?> - Greater Works Healing Ministries.  Site by <a href="http://www.sharpmachinemedia" rel="nofollow">Sharp Machine Media</a></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

<script src="<?php bloginfo('template_directory'); ?>/js/hashgrid.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/scripts.js" type="text/javascript"></script>
<?php if (is_front_page()): ?>
<!--[if lt IE 8]><script src="<?php bloginfo('template_directory'); ?>/js/ie6-alert.js" type="text/javascript" charset="utf-8"></script><![endif]-->
<?php endif; ?>
</body>
</html>
