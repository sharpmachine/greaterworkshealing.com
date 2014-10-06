<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="keywords" content="mark kuntz, cathy kuntz, healing, miracles, signs, wonders, healing rooms, divine healing, prayer, gospel, deliverance, restoration, ministry, jesus, trinity, holy spirit" /> 
<meta name="description" content="Greater Works is passionate about revival, miracles and healings.  The core of the gospel is revival and restoration of the entire man, spirit, soul and body." /> 
<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_directory'); ?>/favicon.ico">
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<!--[if lte IE 8]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link href='http://fonts.googleapis.com/css?family=Homemade+Apple' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Arvo:regular,italic,bold,bolditalic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/screen.css" media="screen, projection">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/print.css" media="print">
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/style.css" media="screen, projection">
<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/ie.css" media="screen, projection"><![endif]-->
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
	wp_head();
?>
</head>

<body <?php body_class(); ?>>
	<div id="header-bar">
		<header>
			<div id="logo"><a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/logo.png" width="187" height="47" alt="Logo"></a></div>
			<nav>
				<?php wp_nav_menu( array( 'sort_column' => 'menu_order', 'menu' => 'Primary Navigation', 'container_class' => 'menu-header' ) ); ?>
			</nav>
		</header>
	</div>
	
	<?php if (is_front_page()): ?>
		<div id="page-title-banner-home">
			<div id="heading">
				<h1>Heaven Is Kicking the Door Down</h1>
				<h2>It's time to step up and let the miraculous power of the God of the universe flow through you</h2>
			</div>
		</div>
		<?php else : ?>
			<div id="page-title-banner">
				<div id="heading">
					
					<h1><?php if (is_404()) {

	_e('Bummer');

} elseif (is_category()) {

	_e('Category');

} elseif (is_tag()) {

	_e('Tag');

} elseif (is_search()) {

	_e('Search Results for:','ndesignthemes'); echo ' ' . $s;

} elseif ( is_day() || is_month() || is_year() ) {

	_e('Archives:','ndesignthemes'); wp_title('');
	
} elseif (is_single()) {

	echo get_post_type();
	
} elseif (is_page('Events')) {

	_e('Events');
	
} elseif (is_page('Store')) {

	_e('Store');

} else {

	echo wp_title('');

}

?></h1>
					
				</div>
			</div>
			<?php endif;?>

<div class="container">