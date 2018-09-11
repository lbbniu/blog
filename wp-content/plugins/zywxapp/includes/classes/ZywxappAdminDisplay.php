<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappAdminDisplay
{
    /**
    * 根据配置信息显示管理菜单
    */
    public function setup()
    {
    	//插件 设定是否完成 与 平台交互 确定
        $iconPath = ZYWXAPP_IMAGES_URL."/Icon.png";
		// 插件安装类
        $installer = new ZywxappInstaller();
		//检测当前登入用户是否有管理员权限 
        if ( current_user_can('administrator') ) {
        	// admin_notices 管理菜单显示在页面上时执行此动作函数（左侧菜单加载完后） 绑定钩子开始执行钩子方法
            add_action('admin_notices', array('ZywxappAdminDisplay', 'configNotice'));//在初次启用插件时 绑定 是否 在后台首页内容区头部 显示插件简介
            //add_action('admin_notices', array('ZywxappAdminDisplay', 'versionCheck'));//向平台发送请求检测 当前插件版本
            add_action('admin_notices', array('ZywxappAdminDisplay', 'upgradeCheck'));// 检测插件是否需要升级
          	// 插件功能定制页面 当插件安装完成后 添加插件顶级菜单绑定功能 
            if ($installer->needUpgrade()){
            	add_menu_page('AppCan-WP手机客户端版本升级', 'AppCan-WP', 'administrator', 'zywxapp', array('ZywxappUpgradeDisplay', 'display'), $iconPath);
            } else if (ZywxappConfig::getInstance()->email_verified === FALSE || ! ZywxappConfig::getInstance()->app_email ){ 
                add_menu_page('AppCan-WP手机客户端平台注册或登录', 'AppCan-WP', 'administrator', 'zywxapp', array('ZywxappPlatformRegisterDisplay', 'display'), $iconPath);
            } else if (ZywxappConfig::getInstance()->settings_done === FALSE){ //添加后台插件设定菜单 （模版定制 功能定制等）
            	add_menu_page('AppCan-WP手机客户端创建应用', 'AppCan-WP', 'administrator', 'zywxapp', array('ZywxappGeneratorDisplay', 'display'), $iconPath);
            }  else {
            	if (ZywxappConfig::getInstance()->configured === FALSE) {
            		add_menu_page('AppCan-WP手机客户端定制应用', 'AppCan-WP', 'administrator', 'zywxapp', array('ZywxappGeneratorDisplay', 'display'), $iconPath);
            	} else {
            		add_menu_page('AppCan-WP手机客户端定制应用', 'AppCan-WP', 'administrator', 'zywxapp', array('ZywxappAdminDisplay', 'settingsDisplay'), $iconPath);
            	}
            	add_submenu_page('zywxapp', __('AppCan-WP手机客户端管理应用'), __('管理应用'), 'administrator', 'zywxapp_dashboard_display', array('ZywxappAdminDisplay', 'dashboardDisplay'));
            	add_submenu_page('zywxapp', __('AppCan-WP手机客户端宣传图设置'), __('宣传图设置'), 'administrator', 'zywxapp_advertisement_display', array('ZywxappGeneratorDisplay', 'advertisementDispaly'));
            	add_submenu_page('zywxapp', __('AppCan-WP手机客户端升级设置'), __('客户端升级设置'), 'administrator', 'zywxapp_client_display', array('ZywxappClientDisplay', 'dispaly'));
				add_submenu_page('zywxapp', __('AppCan-WP手机客户端推广设置'), __('客户端推广设置'), 'administrator', 'zywxapp_promotion_display', array('ZywxappClientDisplay', 'promotionDisplay'));
            	global $submenu;
				$submenu['zywxapp'][0][0] = __('定制应用','zywxapp');
            }
        }
    }
    
    /**
     * 去平台获取后台管理信息
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午02:58:48
     * @author author
     * 
     */
    public static function dashboardDisplay()
    {
        self::includeGeneralDisplay('dashboard');
    }
    
	public static function settingsDisplay()
	{
        self::includeGeneralDisplay('settings');
    }
    
    /**
     * 向平台发送请求信息在后台添加界面
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午03:00:40
     * @author author
     * 
     */
    protected static function includeGeneralDisplay($display_action)
    {
    	$jqueryJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/jquery.tools.min.js';
    	wp_register_script('zywxapp_jquery_tools_script', $jqueryJsFile, array('jquery'));
    	wp_enqueue_script('zywxapp_jquery_tools_script');
    	if (isset($_GET['version']) && !empty($_GET['version'])) {
			if ($_GET['version'] > ZywxappConfig::getInstance()->version) {
				ZywxappConfig::getInstance()->version = $_GET['version'];
				ZywxappConfig::getInstance()->configured = FALSE;
				zywxapp_redirect(admin_url('admin.php?page='.$_GET['page'])); 
			}
		}
		?>
		<div id="zywxapp_js_enabled" class="wrap">
			<?php
				$cms = new ZywxappCms();
		    	$checker = new ZywxappCompatibilitiesChecker();
		    	if (! ZywxappConfig::getInstance()->app_key) {
		        	$registerApp = $cms->registerApp();
		        	if ( ZywxappError::isError($registerApp) ){
			            $errorsHtml = $checker->getErrorButtonHtml($registerApp->getHTML());
		        	}
		    	}
		    	
				if ('dashboard' == $display_action) {
					$iframeSrc = ZywxappConfig::getInstance()->getCdnServer() . '/plugin/plugin_app_detail.action?app_key='.ZywxappConfig::getInstance()->app_key;
					$title = "AppCan-WP手机客户端管理应用";
				} else if ('settings' == $display_action) {
					if (! ZywxappConfig::getInstance()->settings_done) {
						$createApp = $cms->createApp(); 
		    			if ( ZywxappError::isError($createApp) ){
			            	$errorsHtml = $checker->getErrorButtonHtml($createApp->getHTML());
		        		}
		    		}
					$iframeSrc = ZywxappConfig::getInstance()->getCdnServer() . '/plugin/create_app_plugin.action?app_key='.ZywxappConfig::getInstance()->app_key
					.'&pluginName='.ZywxappConfig::getInstance()->plugin_name.'&pluginVersion='.ZYWXAPP_P_VERSION;
					$title = "AppCan-WP手机客户端定制应用";
				}
				ZywxappLog::getInstance()->write('DEBUG', 'iframe src :'.print_r($iframeSrc, TRUE), 'ZywxappAdminDisplay.includeGeneralDisplay');
			echo "{$errorsHtml}";
			?>
			<div id="zywxapp_icon" class="icon32"></div>
			<h2><?php echo $title;?></h2>
			<div class="clear"></div>
            <div id="zywxapp_clent" class="zywxapp_content">
            	<iframe id="zywxapp_frame" scrolling="no" src="" frameborder="0" ></iframe>
            </div>
			<script type="text/javascript">
				var iframe_src = "<?php echo $iframeSrc; ?>";
				document.getElementById("zywxapp_frame").src = iframe_src;
				var can_run = <?php echo (empty($errorsHtml)) ? 'true' :'false'; ?>;
	            var got_critical_errors = <?php echo ($checker->foundCriticalIssues()) ? 'true' :'false'; ?>;
	            jQuery(document).ready(function(){
	            	if (! can_run ){
	            		var $box = jQuery('#zywxapp_compatibilities_errors');
	                    var overlayParams = {
	                        top: 100,
	                        left: (screen.width / 2) - ($box.outerWidth() / 2),
	                        onClose: function(){
	                            jQuery("#zywxapp_error_mask").hide();
	                        },
	                        onBeforeLoad: function(){
	                            var $toCover = jQuery('#wpbody');
	                            var $mask = jQuery('#zywxapp_error_mask');
	                            if ( $mask.length == 0 ){
	                                $mask = jQuery('<div></div>').attr("id", "zywxapp_error_mask");
	                                jQuery("body").append($mask);
	                            }
	                            $mask.css({
	                                position:'absolute',
	                                top: $toCover.offset().top,
	                                left: $toCover.offset().left,
	                                width: $toCover.outerWidth(),
	                                height: $toCover.outerHeight(),
	                                display: 'block',
	                                opacity: 0.9,
	                                backgroundColor: '#444444'
	                            });
	                            $mask = $toCover = null;
	                        },
	                        closeOnClick: false,
	                        closeOnEsc: false,
	                        load: true
	                  };
	                    $box.overlay(overlayParams);
	                }
	            });
			</script>
		</div>
		<?php
    }

    /**
     * 去平台检测版本
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 上午10:39:10
     * @author author
     * 
     */
    public function versionCheck()
    {
        $needCheck = TRUE;
        $needShow = TRUE;

        // 检查 只有当我们在过去12小时内没有检查
        if ( isset(ZywxappConfig::getInstance()->last_version_checked_at) ){
            //我们已经检查过的版本,但它是在过去的12个小时
            if ((time() - ZywxappConfig::getInstance()->last_version_checked_at) <= 60*60*12){
                $needCheck = FALSE;
            }
        }
        if ( $needCheck ){
            //得到平台当前版本号
            if ( empty(ZywxappConfig::getInstance()->zywxapp_avail_version) ){
                ZywxappConfig::getInstance()->zywxapp_avail_version = ZYWXAPP_P_VERSION;
            }
            $r = new ZywxappHTTPRequest();
            //向平台请求版本信息 和 cookie信息
            $response = $r->api(array('plugin_name' => ZywxappConfig::getInstance()->plugin_name), '/index.php?m=curl&a=getNewestVersion', 'POST');
            if ( !is_wp_error($response) ) {
                $vResponse = json_decode($response['body'], TRUE);
                // 更新数据库版本信息
                if ( !empty($vResponse) && $vResponse['status']){
                    ZywxappConfig::getInstance()->zywxapp_avail_version = $vResponse['version'];
                    ZywxappConfig::getInstance()->last_version_checked_at = time();
                }
            }
        }
		// 如果当前插件信息和数据库（平台）版本不一样 提示升级版本
        if ( ZywxappConfig::getInstance()->zywxapp_avail_version != ZYWXAPP_P_VERSION ){
            if ( isset(ZywxappConfig::getInstance()->show_need_upgrade_msg) && ZywxappConfig::getInstance()->show_need_upgrade_msg === FALSE ) {
                if ( ZywxappConfig::getInstance()->last_version_shown === ZywxappConfig::getInstance()->zywxapp_avail_version ){
                    $needShow = FALSE;
                }
            }
			// 升级 提醒 在后台内容区 头部显示
            if ( $needShow ){
                ?>
                <div id="zywxapp_upgrade_needed_message" class="updated fade">
                    <p style="line-height: 150%">
                        AppCan-WP手机客户端有一个重要的更新版本
                        <br />
                        确保尽快<a href="plugins.php">更新</a>，享受安全，bug修复和新功能包含在此更新版本中。
                    </p>
                    <p>
                        <input id="zywxappHideUpgrade" type="button" class="button" value="隐藏这个消息" />
                    </p>
                    <script type="text/javascript">
                    jQuery(document).ready(function(){
                        jQuery("#zywxappHideUpgrade").click(function(){
                            var params = {
                                action: 'zywxapp_hide_upgrade_msg'
                            };
                            jQuery.post(ajaxurl, params, function(data){
                                jQuery("#zywxapp_upgrade_needed_message").remove();
                            });
                        });
                    });
                    </script>
                </div>
            <?php
            }
        }
    }

    /**
     * 数据库和数据库配置信息升级检测
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午02:22:37
     * @author author
     * 
     */
    public function upgradeCheck()
    {
    	// 插件安装类
        $installer = new ZywxappInstaller();
		//在后台内容区 头部显示 升级提示
        if ( $installer->needUpgrade() && $_GET['page'] != 'zywxapp' ){
            ?>
            <div id="zywxapp_internal_upgrade_needed_message" class="updated fade">
                <p style="line-height: 150%">
                	AppCan-WP手机客户端需要多走一步完成升级过程,请点击<a href="admin.php?page=zywxapp">这里</a>升级你的数据库。
                    <br />
                    确保尽快更新，你可以享受安全，bug修复和新功能，在此更新包含。
                </p>
            </div>
        <?php
        }
    }

    /**
     * 第一次安装在后台管理界面给出简介信息和邮箱验证信息
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-3 下午07:06:50
     * @author author
     * 
     */
    public function configNotice()
    {
    	//判断是否启在初次启用插件时  在后台首页内容区头部 显示插件简介
        if (!isset(ZywxappConfig::getInstance()->zywxapp_showed_config_once) || ZywxappConfig::getInstance()->zywxapp_showed_config_once !== TRUE){
            ?>
                <div id="message" class="updated fade" style="font-size: 1.1em">
                    <p>
                        <span style="font-weight: bolder;">AppCan-WP手机客户端</span> AppCan-WP手机客户端让你的WordPress博客拥有一个iPhone/Android客户端
                    </p>
                    <p>首先,我们需要你使用我们友好的向导来完成简单步骤的配置。</p>
                    <p>现在你可以点击 <a href="admin.php?page=zywxapp">这里</a>, 或任何时候你都可以通过主菜单中管理控制面板在来点击。
                    </p>
                </div>
            <?php
            ZywxappConfig::getInstance()->zywxapp_showed_config_once = TRUE;
        }
    }

    /**
     * 隐藏升级提升
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 上午10:04:17
     * @author author
     * 
     */
    public function hideUpgradeMsg()
    {
        $status = TRUE;
        ZywxappConfig::getInstance()->show_need_upgrade_msg = FALSE;
        ZywxappConfig::getInstance()->last_version_shown = ZywxappConfig::getInstance()->zywxapp_avail_version;
        $header = array(
            'action' => 'hideUpgradeMsg',
            'status' => $status,
            'code' => ($status) ? 200 : 400,
            'message' => '',
        );
        echo json_encode(array('header' => $header));
        //echo json_encode($header);
        exit;
    }
}
