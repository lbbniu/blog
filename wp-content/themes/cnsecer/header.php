<!DOCTYPE html>
<html>
	<head>
		<!--[if lt IE 9]>   
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<!--[if IE 8]>
			<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/sbie.css" type="text/css" />
		<![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta  charset="<?php bloginfo('charset'); ?>" />
		<!--判断页面开始-->
		<?php if ( is_search() ) { ?><title>搜索结果 - <?php bloginfo('name'); ?></title><?php } ?>
		<?php if ( is_page() ) { ?><title><?php echo trim(wp_title('',0)); ?> - <?php bloginfo('name'); ?></title><?php } ?>
		<?php if ( is_category() ) { ?><title><?php single_cat_title(); ?> - <?php bloginfo('name'); ?></title><?php } ?>
	
		
		<?php if ( is_single() ) { ?>
		<title><?php echo trim(wp_title('',0)); ?> - <?php $keywords = ""; $tags = wp_get_post_tags($post->ID);  foreach ($tags as $tag ) {   $keywords = $keywords . $tag->name . ", "; } echo $keywords; ?></title>
		<meta name="description" content="<?php if (is_single()){ echo mb_strimwidth(strip_tags(apply_filters('the_content', $post->post_content)), 0, 180,"");} ?>"/>
		<meta name="keywords" content="<?php $keywords = ""; $tags = wp_get_post_tags($post->ID);  foreach ($tags as $tag ) {   $keywords = $keywords . $tag->name . ", "; } echo $keywords; ?>" />
		<?php } ?>
		<?php if ( is_home() ) { ?>
		<title><?php echo stripslashes(get_option('cnsecer_title')); ?></title>
		<meta name="description" content="<?php echo stripslashes(get_option('cnsecer_description')); ?>" />
		<meta name="keywords" content="<?php echo stripslashes(get_option('cnsecer_keywords')); ?>" />
		<?php } ?>
		<!--判断页面结束-->
		<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/images/favicon.ico" type="image/x-icon" />	
		<!--css file-->
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap/css/bootstrap.min.css"  media="screen" />
		<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>?time=20131012"/>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/adipoli.css" />
		<!--js file-->
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.1.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/css/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/index.js" type="text/javascript"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/respond.min.js" type="text/javascript"></script>
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.adipoli.min.js" type="text/javascript"></script>
		<?php wp_head(); ?>
	</head>
	
	<body>
	<!-- 头部导航开始 -->
	<header class="navbar navbar-inverse navbar-fixed-top bs-docs-nav" role="banner navigation" >
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="<?php bloginfo('siteurl');?>"><img  src="<?php bloginfo('template_directory'); ?>/images/logo.png" alt="主页" /> </a>
			</div>
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
				
				<?php bootstrap_nav(); ?>
				<div class="searchfrom hidden-xs hidden-sm">
					<form class="navbar-form navbar-left" action="<?php echo home_url( '/' ); ?>" method="get" target="_blank" role="search">
						<div class="form-group">
							<input type="text" name="s" class="form-control" x-webkit-speech placeholder="Search">
						</div>
					</form>
				</div>
			</nav>	
		</div>
	</header>
	<!-- 头部导航结束 -->