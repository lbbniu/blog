<!DOCTYPE HTML>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=10,IE=9,IE=8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<title><?php wp_title( '-', true, 'right' ); ?></title>

<script>
window._deel = {name: '<?php bloginfo('name');?>',url: '<?php bloginfo('template_url');?>', ajaxpager: 'on', commenton: 0, roll: [3,]}
</script>
<?php wp_head(); ?>
<!--[if lt IE 9]><script src="<?php bloginfo('template_url');?>/js/html5.js"></script><![endif]-->
</head>
<body class="home blog">
<header id="header" class="header">
<div class="container-inner">
 <div class="yusi-logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">
                        <h1>
                        	<img src='<?php bloginfo('template_url');?>/img/logo.jpg'/>
                          </h1>
                    </a>
    </div>    
</div>

	<?php 	
	$li = '<li style="float:right;"><div class="toggle-search"><i class="fa fa-search"></i></div><div class="search-expand" style="display: none;"><div class="search-expand-inner">';
	$li .='<form method="get" class="searchform themeform" onsubmit="location.href=\'http://blog.lbbniu.com/search/\' + encodeURIComponent(this.s.value).replace(/%20/g, \'+\'); return false;" action="/">';
	$li .=					'<div>'; 
	$li .=					'<input type="ext" class="search" name="s" onblur="if(this.value==\'\')this.value=\'search...\';" onfocus="if(this.value==\'search...\')this.value=\'\';" value="search...">';
	$li .=				'</div>';
	$li .=				'				</form>';
	$li .=				'			</div>';
	$li .=				'		</div>';
	$li .=				'	</li>';
	
	
	wp_nav_menu( array( 'theme_location' => 'primary', 'items_wrap'=>'<ul class="%2$s">%3$s'.$li.'</ul>','menu_class'=> 'nav','container'=> 'div','container_class' => 'navbar','container_id'=> 'nav-header',) ); 	
	?>
	<!--<li style="float:right;">
            <div class="toggle-search"><i class="fa fa-search"></i></div>
			<div class="search-expand" style="display: none;">
				<div class="search-expand-inner">
					<form method="get" class="searchform themeform" onsubmit="location.href='http://blog.lbbniu.com/search/' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;" action="/">
						<div> 
							<input type="ext" class="search" name="s" onblur="if(this.value=='')this.value='search...';" onfocus="if(this.value=='search...')this.value='';" value="search...">
						</div>
					</form>
				</div>
			</div>
		</li>	-->
</header>

<section class="container">
		<div class="speedbar">
		
			<div class="pull-right">
				<i class="fa fa-user"></i><?php wp_loginout();?>
			</div>
		
		<div class="toptip"><strong class="text-success"><i class="fa fa-volume-up"></i> </strong> 欢迎来到<?php bloginfo('name'); ?>，在这里，你会找到许多有趣的新闻 : )</div>
	</div>
