<?php
class ZywxappPlatformRegisterDisplay
{
    /**
     * 去平台注册
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-5-8 下午02:27:26
     * @author 
     * 
     */
 	public function display()
    {
		// 检测是否已经安装过 
		if ( !ZywxappConfig::getInstance()->isInstalled() ){
			$installer = new ZywxappInstaller();
			//安装插件
			$installer->install();
			// 如果程序运行到这 我们已经可以显示安装成功信息
			ZywxappConfig::getInstance()->zywxapp_showed_config_once = TRUE;
		}
		
		$jqueryJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/jquery.tools.min.js';
    	wp_register_script('zywxapp_jquery_tools_script', $jqueryJsFile, array('jquery'));
    	wp_enqueue_script('zywxapp_jquery_tools_script');
    	
    	$baseJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/admin_base.js';
    	wp_register_script('zywxapp_base_script', $baseJsFile);
    	wp_enqueue_script('zywxapp_base_script');
    	
    	$page = $_REQUEST['page'];
    	if (in_array($_GET['type'], array('register', 'login'))) {
    		$type = trim($_GET['type']);
    	} else {
    		$type = 'register' ;
    	}
    	$url = admin_url('admin.php?page='.$page);
    	$login_url = admin_url('admin.php?page='.$page.'&type=login');
    	$checker = new ZywxappCompatibilitiesChecker();
    	$cms = new ZywxappCms();
    	if ('register' == $type) {
	    	$submit = $_POST['zywxapp_register_submit'];
			if (! $submit) {
				// 检测网站服务器 插件安装需要的php扩展功能 是否完善 在后台显示出信息
	        	$errorsHtml = $checker->fullTestAsHtml();
			} else {
	    		if (! current_user_can('administrator')) {
	    			wp_die(__('您没有足够的操作权限。','zywxapp'));
	    		}
	    		if (empty($_POST['nickname']) || empty($_POST['password']) || empty($_POST['email'])) {
	    			$error = __('<strong>错误</strong>：请填写必填项','zywxapp');
	    		} elseif ($_POST['password'] != $_POST['password1']) {
	    			$error = __('<strong>错误</strong>：两次密码不一致','zywxapp');
	    		} else {
		        	$registerUser = $cms->registerUser();
			    	if ( ZywxappError::isError($registerUser) ){
			            $errorsHtml = $checker->getErrorButtonHtml($registerUser->getHTML());
			        } else {
			        	$registerApp = $cms->registerApp();
				        if ( ZywxappError::isError($registerApp) ){
			            	$errorsHtml = $checker->getErrorButtonHtml($registerApp->getHTML());
			        	} else {
			        		zywxapp_redirect($url); 
			        	}
			        }
	    		}
	    	}
    	} else {
    		$callback_uri = get_bloginfo('url').'/?zywxapp/system/success';
    		$iframeSrc = 'http://'. ZywxappConfig::getInstance()->api_server.'/index.php?m=curl&a=toLogin&callback_uri='.$callback_uri;
    	}
		echo "{$errorsHtml}"; ?>
        <div id="zywxapp_activation_container" class="no_js">
            <div id="zywxapp_js_disabled">
                <div id="js_error" class="zywxapp_errors_container s_container">
                    <div class="errors">
                        <div class="zywxapp_error"><?php echo __('看来您的浏览器阻止使用JavaScript。请更改您的浏览器设置并再次尝试', 'zywxapp');?></div>
                   </div>
                </div>
            </div>
            <div id="zywxapp_js_enabled">
            	<div id="zywxapp_icon" class="icon32"><br /></div>
				<h2>AppCan-WP手机客户端平台注册或登录</h2>
				<div class="clear"></div>
				<?php if ( isset( $error ) && !empty($error)) : ?>
					<div class="error"><p><?php echo $error; unset( $error );?></p></div>
				<?php endif;?>
				<div id="message" class="hidden error">
					<p><strong></strong></p>
				</div>
				<div class="zywxapp_content">
					<ul class="zywxapp_nav">
						<li><a class="zhuce <?php if ($type =='register'){ echo "stop";}?>" href="<?php echo $url;?>" id="zywxapp_register">注册</a></li>
						<li><a class="<?php if ($type =='login'){ echo "stop";}?>" href="<?php echo $login_url;?>" id="zywxapp_login">AppCan帐号登陆</a></li>
					</ul>
					<div class="zywxapp_platform_content">
					<?php if ($type =='register') {
						?>
						<div id="zywxapp_register_box">
							<form name="zywxapp_register_form" id="zywxapp_register_form" action="" method="post">
								<table class="form-table">
									<tbody>
										<tr>
											<th><label for="nickname">昵称 <span class="description">(必填)</span></label></th>
											<td><input type="text" class="regular-text" value="" id="nickname" name="nickname"></td>
										</tr>
										
										<tr>
											<th><label for="password">密码 <span class="description">(必填)</span></label></th>
											<td><input type="password" class="regular-text" value="" id="password" name="password"></td>
										</tr>
										<tr>
											<th><label for="password1">确认密码 <span class="description">(必填)</span></label></th>
											<td><input type="password" class="regular-text" value="" id="password1" name="password1"></td>
										</tr>
										<tr>
											<th><label for="email">邮箱 <span class="description">(必填)</span></label></th>
											<td><input type="text" class="regular-text" value="" id="email" name="email"></td>
										</tr>
									</tbody>
								</table>
								<div class="zywxapp_line">
									备注：<br />
									1.请填写您常用邮箱，此邮箱为您管理插件找回密码必填信息<br />
									2.确认邮箱页面中，请勿刷新该页，如刷新将导致无法认证成功<br />
									3.密码用于登录AppCan开发平台，为保证您的账户安全，建议您至官网修改密码。点击进入<a href="http://www.appcan.cn" target="_blank">官网</a>
								</div>
								<p class="submit">
									<input type="submit" value="提交" class="button-primary zywxapp_submit" id="zywxapp_register_submit" name="zywxapp_register_submit">
								</p>
							</form>
						</div>
						<?php 
					} else {
						?>
						<div id="zywxapp_login_box">
							<iframe src="<?php echo $iframeSrc; ?>"  id="zywxapp_login_frame" style="margin-top:50px;overflow:hidden;width:100%;height:480px;border:0px none;" scrolling="no" frameborder="0" ></iframe>
						</div>
						<?php
						ZywxappLog::getInstance()->write('DEBUG', 'login iframe src :'.print_r($iframeSrc, TRUE), 'ZywxappPlatformRegisterDisplay.display');
					}?>
					</div>
				</div>
			</div>
        </div>

        <script type="text/javascript">
            document.getElementById('zywxapp_activation_container').className = 'js wrap';
            var can_run = <?php echo (empty($errorsHtml)) ? 'true' :'false'; ?>;
            var got_critical_errors = <?php echo ($checker->foundCriticalIssues()) ? 'true' :'false'; ?>;
            jQuery(document).ready(function(){
            	zywxappRegisterAjaxErrorHandler();
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
            
            function loginSave(email) {
            	var params = {
	                    action: 'zywxapp_login_save',
	                    email:email,
	                };
				jQuery.post(ajaxurl,params,function(json){
                	var data = jQuery.parseJSON(json);
                	if (data.status) {
                		window.parent.location.href = "<?php echo $url;?>";
                	} else {
                		window.parent.showMessage(data.message);
                		document.reload();
                	}
                });
            }
            
    </script>
    	<?php
    }
    
    public function loginSave()
    {
    	$status = TRUE;
        $code = 200;
        ZywxappConfig::getInstance()->email_verified = TRUE;
        ZywxappConfig::getInstance()->app_email = $_REQUEST['email'];
        $cms = new ZywxappCms();
    	if (! ZywxappConfig::getInstance()->app_key) {
        	$registerApp = $cms->registerApp();
        	if ( ZywxappError::isError($registerApp) ){
        		$status = FALSE;
        		$code = 400;
	            $errors = $registerApp->getHTML();
        	}
    	}
        $header = array(
            'status' => $status,
            'code' => $code,
        	'message' => strip_tags($errors),
        );
        echo json_encode($header);
        exit;
    }
}
