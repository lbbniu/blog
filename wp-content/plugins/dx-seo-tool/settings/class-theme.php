<?php

class Daxiawp_Plugin_Theme {
	
	/**
	 * Hooks
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ), 999 );
	}
	
	/**
	 * Add menu page
	 */
	function menu_page() {
		add_submenu_page( 'dx_seo', 'daxiawp主题商城', 'daxiawp主题商城', 'manage_options', 'daxiawp_theme', array( $this, 'menu_content' ) );
	}
	
	/**
	 * Show menu content
	 */
	function menu_content() {
?>

<style type="text/css">
	ul.theme{width:980px;}
	ul.theme li{width:300px; float:left; border:1px solid #ccc; padding:5px; margin-right:10px; margin-bottom:15px;}
</style>

<script type="text/javascript" src="http://cbjs.baidu.com/js/m.js"></script>
<?php 
	$codes=array(
		'454435',
		'454451',
		'454453',
		'454454',
		'454455',
		'454457',
		'454459',
		'454461',
		'454462'
	);
?>
<div class="wrap">

	<div id="icon-options-general" class="icon32"><br></div><h2>daxiawp主题</h2>
	
	<p>以下列出大侠wp最新制作的9个主题预览图，某些浏览器的插件可能会阻止图片的显示。浏览所有主题请访问<a href="http://www.daxiawp.com/wordpress-theme/all/" target="_blank">daxiawp</a>。</p>
	
	<ul class="theme">
		<?php for( $n=0; $n<9; $n++ ): ?>
		<li><script type="text/javascript">BAIDU_CLB_fillSlot("<?php echo $codes[$n]; ?>");</script></li>
		<?php endfor;?>
	</ul>
	
	<div style="clear:both;"></div>

</div>

<?php		
	}
}

new Daxiawp_Plugin_Theme;