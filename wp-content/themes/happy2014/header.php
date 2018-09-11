<!doctype html>
<html lang="en">
<head>
	<!--[if lt IE 9]>   
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!--[if IE 8]>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/sbie.css" type="text/css" />
	<![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
	<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>?time=20140117"/>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/adipoli.css" />
	

	<?php wp_head(); ?>
	<?php do_action("wp_footer"); ?>
</head>

	<body>
	<!-- 头部开始 -->
	<div class="header">
		<!-- top begin -->
		<div class="top">
		<div class="container">
			<div class="logo ">
				<a href="<?php bloginfo('siteurl');?>"><img  src="<?php bloginfo('template_directory'); ?>/images/logo2.png" alt="安全者" /> </a>
			</div>
			<div class="searchbox">
				<form class="navbar-form navbar-left" action="<?php echo home_url( '/' ); ?>" method="post"  role="search">
					<div class="input-group">
						<input type="text" name="s" class="form-control" x-webkit-speech placeholder="Search">
						<span class="input-group-btn">
			              <button class="btn btn-primary" type="button">Search</button>
			            </span>
					</div>
				</form>
			</div>
		</div>
		</div>
		<!-- top end -->
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
					
			    <!-- search box begin -->
				  <div class="search visible-xs navbar-right">
				    <form  method="post"   action="<?php echo home_url( '/' ); ?>"  class="navbar-form navbar-left" role="search"> 
					<div class="form-group">
						<input type="text" name="s" class="form-control" placeholder="Search"  x-webkit-speech="">
					</div>
				    </form>
				  </div>
			    <!-- search box end -->
			    </nav>
			</div>
		</header>
		<!-- 头部导航结束 -->
		
	</div>
	<!-- 头部结束 -->

	
