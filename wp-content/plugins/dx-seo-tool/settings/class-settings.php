<?php

class DX_Seo_Settings {
	
	/**
	 * Properties
	 */
	protected $slug = 'dx_seo';		// Settings menu page slug
	protected $hook_suffix;
	protected $prefix = 'dx_seo';
	protected $options_name = array(	// Set all options name
		'web589seo_switch_meta' => array( 'seo meta功能', '开启则可自定义首页、文章页、页面、分类页、标签页、自定义post type以及自定义taxonomy的title、keywords、description。' ),
		'web589seo_switch_search_keywords' => array( '关键词建议功能', '开启则在title处显示来自百度的相关搜索词。需先开启meta功能。' ),
		'web589seo_image_attr' => array( '图片自动属性功能', '开启则可自动添加图片的title、alt属性' ),
		'dxseo_nofollow' => array( '自动nofollow功能', '开启则可自动给文章外链添加nofollow属性'),
		'dxseo_relative' => array( '相关文章功能', '开启则在文章底部显示相关文章，提高文章的相关度，加强内链。'),
		'dxseo_sitemap' => array( '站点地图功能', '开启则生成sitemap格式的xml文件，全面兼容百度、360、google等主流搜索引擎。'),
		'dxseo_anchor_text' => array( '自动锚文本功能', '开启则给文章内容自动添加链接锚文本，加强网站内链的建设。'),
	);
	
	/**
	 * Hooks
	 */
	function __construct() {
		add_filter( 'plugin_action_links_' . DXSEO_FILE , array( $this, 'plugin_settings_link' ), 10, 4 );
		add_filter('plugin_row_meta', array( $this, 'plugin_des_link' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'update_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_head', array( $this, 'admin_print_scripts' ) );		
	}
	
	/**
	 * Plugin settings link
	 */
	function plugin_settings_link( $actions, $plugin_file, $plugin_data, $context ) {
		$actions['settings'] = '<a href="' . add_query_arg( 'page', $this->slug, admin_url( 'admin.php' ) ) . '">设置</a>';
		return $actions;
	}
	
	/**
	 * Plugin description bottom link
	 */
	function plugin_des_link( $links, $file ) {
		if( DXSEO_FILE == $file ) {
			$links['donate'] = '<a href="https://me.alipay.com/daxiawp" target="_blank">捐赠帮助发展插件</a>';
		}
		return $links;
	}
	
	/**
	 * Add admin menu assign to page
	 */
	function add_admin_menu() {
		$this->hook_suffix = add_menu_page( 'DX-Seo', 'DX-Seo', 'manage_options', $this->slug, array( $this, 'menu_page' ), plugins_url( 'icon.png', __FILE__ ) );
		// Theme store
		if( ! class_exists( 'Daxiawp_Plugin_Theme' ) )
			require_once( 'class-theme.php' );	
		// Help
		require_once( 'class-help.php' );
		add_action( "load-$this->hook_suffix", array( 'DX_Seo_Help', 'init' ) );
	}
	
	/**
	 * Content on menu page
	 */
	function menu_page() {
	?>
		<div class="wrap dxseo-wrap " id="settings-container">
			<?php screen_icon( 'options-general' ); ?>
			<h2>全局选项</h2>
			<form method="post" action="options.php" id="settings-form">
			<?php
				settings_fields( 'dx_seo_settings' );
				do_settings_sections( $this->prefix . '_meta_slug' );
				submit_button();
			?>
			</form>
			<?php $this->sidebar(); ?>
		</div>
		<br style="clear:both;">
	<?php
	}
	
	/**
	 * Register settings
	 */
	function register_settings() {
		add_settings_section( $this->prefix . '_meta', '', array( $this, 'section' ), $this->prefix . '_meta_slug' );
		foreach( $this->options_name as $key => $val ) {
			register_setting( 'dx_seo_settings', $key );
			add_settings_field( $key, $val[0], array( $this, 'fields' ), $this->prefix . '_meta_slug', $this->prefix . '_meta', array( 'label_for' => $key, 'des' => $val[1] ) );
		}
	}
	
	/**
	 * Settings section content
	 */
	function section() {
		echo '';
	}
	
	/**
	 * Add settings fields
	 */
	function fields( $args ) {
		$name = $args['label_for'];
		$setting = get_option( $name );
    	echo '<input type="checkbox" name="' . $name . '" id="' . $name . '" class="ibutton" value="on" ' . checked( 'on', $setting, false ) .' />';
		echo '<p class="description">' . $args['des'] . '</p>';
	}
	
	/**
	 * Show notices when update
	 */	
	function update_notices() {
		global $hook_suffix;
		if( $hook_suffix == $this->hook_suffix )
			settings_errors();
	}
	
	/**
	 * Admin enqueue scripts
	 */
	function admin_enqueue_scripts( $hook ) {
		if( $hook != $this->hook_suffix )
			return;
		
		// Main
		wp_enqueue_style( $this->prefix . '-style', plugins_url( 'scripts/style.css', __FILE__ ) );
		wp_enqueue_script( $this->prefix . '-js', plugins_url( 'scripts/script.js', __FILE__ ) );
		// Ibutton
		wp_enqueue_style( $this->prefix . '-ibutton-style', plugins_url( 'scripts/ibutton/jquery.ibutton.min.css', __FILE__ ) );
		wp_enqueue_script( $this->prefix . '-ibutton-js', plugins_url( 'scripts/ibutton/jquery.ibutton.min.js', __FILE__ ) );
		// Bd guanjia
		wp_enqueue_script( $this->prefix . '-baidu-guanjia-js', 'http://cbjs.baidu.com/js/m.js' );
	}
	
	/**
	 * Adimn print scripts
	 */
	function admin_print_scripts() {
		global $hook_suffix;
		if( $this->hook_suffix == $hook_suffix ):
	?>
			<style type="text/css">
				#settings-container{ margin-right: 340px; }
				#settings-form { width: 100%; float: left; }
				#postbox-container { float: right; margin-right: -320px; width: 300px; }
				.postbox h3 { font-size: 15px; font-weight: normal; padding: 7px 10px; margin: 0; line-height: 1; cursor: default !important; }	
				.no-border{ border: none; }
				#side-recommend .inside { margin: 0; padding: 0; }
			</style>
	<?php
		endif;
	}	
	
	/**
	 * Sidebar
	 */
	function sidebar() {
		if( get_bloginfo( 'language' ) == 'zh_CN' )
			return;
	?>
		<div id="postbox-container" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables">
				<div id="side-help" class="postbox">
					<h3 class="hndle"><span>帮助</span></h3>
					<div class="inside">
						<div class="misc-pub-section"><b>捐赠：</b><a href="https://me.alipay.com/daxiawp" target="_blank"><img style="vertical-align:middle;" src="<?php echo plugins_url( 'donate.png', __FILE__ ); ?>"/></a></div>
						<div class="misc-pub-section">插件介绍以及教程请<a href="http://www.daxiawp.com/dx-seo-plugin.html" target="_blank">点击浏览</a></div>
						<div class="misc-pub-section">您如果有什么问题或者建议请<a href="http://www.daxiawp.com/dx-seo-plugin.html#respond" target="_blank">点击给我留言</a></div>
						<div class="misc-pub-section no-border">wordpress定制、仿站、插件开发请联系：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=1683134075&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:1683134075:51&t=<?php echo time(); ?>" style="vertical-align: middle; " alt="点击这里给我发消息" title="点击这里给我发消息"/>1683134075</a></div>
					</div>
				</div>
				<div id="side-recommend" class="postbox">
					<h3 class="hndle"><span>推荐主题</span></h3>
					<div class="inside">
						<script type="text/javascript">BAIDU_CLB_fillSlot("782579");</script>
					</div>
				</div>				
			</div>
		</div>
	<?php
	}
	
}

new DX_Seo_Settings;