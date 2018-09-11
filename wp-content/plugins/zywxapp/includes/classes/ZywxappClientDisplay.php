<?php
class ZywxappClientDisplay
{
    /**
     * 客户端升级设置
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-5-8 下午02:27:26
     * @author 
     * 
     */
 	public function dispaly()
    {
    	$submit = $_POST['zywxapp_client_submit'];
    	if ($submit) {
    		if (! current_user_can('administrator')) {
    			wp_die(__('您没有足够的操作权限。'));
    		}
    		$iphone_status = ZywxappConfig::getInstance()->saveUpdate('iphone_path', $_POST['iphone_path']);
    		$android_status = ZywxappConfig::getInstance()->saveUpdate('android_path', $_POST['android_path']);
    		$version_status = ZywxappConfig::getInstance()->saveUpdate('upgrade_version', $_POST['upgrade_version']);
    		if (!$iphone_status || !$android_status || !$version_status) {
    			$error = __('无法更新设置', 'zywxapp');
    		} else {
    			$message = __('设置更新成功', 'zywxapp');
    		}
    	}
		?>
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
				<h2>AppCan-WP手机客户端升级设置</h2>
				<?php if ( isset( $error ) && !empty($error)) : ?>
					<div class="error"><p><?php echo $error; unset( $error );?></p></div>
				<?php elseif(isset( $message ) && !empty($message)) :?>
					<div id="message" class="updated"><p><?php echo $message;unset( $message ); ?></p></div>
				<?php endif;?>
            	<div id="zywxapp_register" class="stuffbox zywxapp_content">
					<form name="zywxapp_register_form" id="zywxapp_register_form" action="" method="post">
					
						<table class="form-table">
							<tbody>
								<tr>
									<th><label for="iphone_path">Iphone路径</label></th>
									<td><input type="text" class="regular-text" value="<?php echo ZywxappConfig::getInstance()->iphone_path;?>" id="iphone_path" name="iphone_path"></td>
								</tr>
								
								<tr>
									<th><label for="android_path">android路径</label></th>
									<td><input type="text" class="regular-text" value="<?php echo ZywxappConfig::getInstance()->android_path;?>" id="android_path" name="android_path"></td>
								</tr>
								
								<tr>
									<th><label for="upgrade_version">版本号</label></th>
									<td><input type="text" class="regular-text" value="<?php echo ZywxappConfig::getInstance()->upgrade_version;?>" id="upgrade_version" name="upgrade_version"></td>
								</tr>
							</tbody>
						</table>
						<div class="zywxapp_line">
							备注：<br />
							1.客户端应用升级设置是让用户手机客户端应用自动升级到你设置的版本<br />
							2.客户端安装包路径可以是全路径 例：http://www.xxx.com/xxx/iphone.ipa <br />
							  &nbsp;&nbsp;&nbsp;或是包名称 例：iphone.ipa 但是包文件必须放在zywxapp/uploades文件目录下<br />
							3.两个安装包共用一个版本号<br />
							4.版本号填写请与打包时版本号一致<br />
						</div>
						<p class="submit">
							<input type="submit" value="提交" class="button-primary zywxapp_submit" id="zywxapp_client_submit" name="zywxapp_client_submit">
						</p>
					</form>
				</div>
			</div>
        </div>

        <script type="text/javascript">
            document.getElementById('zywxapp_activation_container').className = 'js wrap';
		</script>
    	<?php
    }

    /**
     * 推广设置
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-6-12 下午05:56:12
     * @author author
     * 
     */
    public function promotionDisplay()
    {
    	$jqueryJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/jquery.tools.min.js';
    	wp_register_script('zywxapp_jquery_tools_script', $jqueryJsFile, array('jquery'));
    	wp_enqueue_script('zywxapp_jquery_tools_script');
    	$submit = $_POST['zywxapp_promotion_submit'];
    	if ($submit) {
    		if (! current_user_can('administrator')) {
    			wp_die(__('您没有足够的操作权限。'));
    		}
    		$promotion_status = ZywxappConfig::getInstance()->saveUpdate('zywxapp_promotion_status', $_POST['status']);
    		if (!$promotion_status) {
    			$error = __('无法更新设置', 'zywxapp');
    		} else {
    			$message = __('设置更新成功', 'zywxapp');
    		}
    	}
		?>
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
				<h2>AppCan-WP手机客户端推广设置</h2>
				<?php if ( isset( $error ) && !empty($error)) : ?>
					<div class="error"><p><?php echo $error; unset( $error );?></p></div>
				<?php elseif(isset( $message ) && !empty($message)) :?>
					<div id="message" class="updated"><p><?php echo $message;unset( $message ); ?></p></div>
				<?php endif;?>
            	<div id="zywxapp_promotion" class="stuffbox zywxapp_content kaiqi">
					<form name="zywxapp_promotion_form" id="zywxapp_promotion_form" action="" method="post">
						<div class="yulan"><strong>开启右下角弹出框</strong><span>（选择“是”进入首页会弹出您生成手机客户端安装应用包下载提示）</span><a id="preview" href="javascript:void(0);">样式预览</a></div>
						<input type="radio" name="status" value="1" <?php if (ZywxappConfig::getInstance()->zywxapp_promotion_status) { echo 'checked="checked"';}?>/><label>是</label>&nbsp;&nbsp;
						<input type="radio" name="status" value="0" <?php if (!ZywxappConfig::getInstance()->zywxapp_promotion_status) { echo 'checked="checked"';}?>/><label>否</label>
						<p class="submit">
							<input type="submit" value="提交" class="button-primary zywxapp_submit" id="zywxapp_promotion_submit" name="zywxapp_promotion_submit">
						</p>
					</form>
				</div>
			</div>
			<div id="zywxapp_promotion_preview" class="zywxapp_promotion_preview hidden">
				<img src="<?php echo ZYWXAPP_IMAGES_URL?>/promotion_preview.jpg" style="width:628px; height:238px;"/>
			</div>
        </div>
		
        <script type="text/javascript">
            document.getElementById('zywxapp_activation_container').className = 'js wrap';
            
            jQuery(document).ready(function(){
            	var $box = jQuery('#zywxapp_promotion_preview');
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
            	jQuery('#preview').click(function() {
            		$box.show();
            		if (jQuery('#zywxapp_error_mask').length == 0 ) {
           				$box.overlay(overlayParams);
            		} else {
            			jQuery('#zywxapp_error_mask').show();
            		}
                });
            	jQuery('#zywxapp_promotion_preview').click(function() {
            		$box.hide();
            		jQuery('#zywxapp_error_mask').hide();
                });
                
            });
		</script>
    	<?php
    }
     
}
