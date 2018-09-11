<?php
class ZywxappGeneratorDisplay
{
	/**
	 * 定制功能管理界面
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-6 下午08:36:43
	 * @author author
	 * 
	 */
    public function display()
    {
        $maint = FALSE;
        if ( function_exists("is_maintenance") ){
           $maint = is_maintenance();
        }
        if ( $maint ){
            ?>
                <div class="zywxapp_errors_container s_container" style="top:40%;">
                    <div class="errors">
                        <div class="zywxapp_error">
                            你的网站正在维护。在此模式时,AppCan-WP手机客户端插件不能运行。
                        </div>
                    </div>
                </div>
            <?php
        } else {
        	$jqueryJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/jquery.tools.min.js';
    		wp_register_script('zywxapp_jquery_tools_script', $jqueryJsFile, array('jquery'));
    		wp_enqueue_script('zywxapp_jquery_tools_script');
        	
    		wp_enqueue_script('custom-background');
			wp_enqueue_style('farbtastic');
			
			$checker = new ZywxappCompatibilitiesChecker();
        	$errorsHtml = '';
			$submit = $_POST['zywxapp_style_submit'];
			if ($submit) {
				if (! current_user_can('administrator')) {
	    			wp_die(__('您没有足够的操作权限。','zywxapp'));
	    		}
	    		ZywxappConfig::getInstance()->startBulkUpdate();//开始设定 配置信息
                ZywxappConfig::getInstance()->qq_appkey = $_POST['qq_appkey'];
	    		ZywxappConfig::getInstance()->qq_callback_url = $_POST['qq_callback_url'];
	    		ZywxappConfig::getInstance()->sina_appkey = $_POST['sina_appkey'];
	    		ZywxappConfig::getInstance()->sina_callback_url = $_POST['sina_callback_url'];
	    		ZywxappConfig::getInstance()->app_style = $_POST['app_style'];
	    		ZywxappConfig::getInstance()->app_color = substr($_POST['app_color'],1,7);
				if ($_POST['app_style'] == 'default') {
	    			ZywxappConfig::getInstance()->app_color = ZywxappConfig::getInstance()->defautl_color;
	    		}
                ZywxappConfig::getInstance()->bulkSave();//结束配置信息  保存到数据库中 
	    		$cms = new ZywxappCms();
	    		if (! ZywxappConfig::getInstance()->app_key) {
		    		$registerApp = $cms->registerApp();
			        if ( ZywxappError::isError($registerApp) ){
		            	$errorsHtml = $checker->getErrorButtonHtml($registerApp->getHTML());
		        	} else {
		        		$createApp = $cms->createApp();
		        		if ( ZywxappError::isError($createApp) ){
	            			$errorsHtml = $checker->getErrorButtonHtml($createApp->getHTML());
	        			} else {
	        				zywxapp_redirect(admin_url('admin.php?page='.$_GET['page'])); 
	        			}
		        	}
	    		} else {
	    			$createApp = $cms->createApp();
	        		if ( ZywxappError::isError($createApp) ){
            			$errorsHtml = $checker->getErrorButtonHtml($createApp->getHTML());
        			} else {
        				zywxapp_redirect(admin_url('admin.php?page='.$_GET['page'])); 
        			}
	    		}
			}
			echo "{$errorsHtml}";
        	?>
			<script type="text/javascript">
	        	var default_color = '#<?php echo ZywxappConfig::getInstance()->default_color;?>';
	        	var farbtastic;
	        	(function($){
	        		var pickColor = function(color) {
	        			farbtastic.setColor(color);
	        			$('#background-color').val(color);
	        			$('#zywxapp_phone_title').css('background-color', color);
	        		};
	
	        		$(document).ready( function(e) {
	        			farbtastic = $.farbtastic('#colorPickerDiv', pickColor);
	        			pickColor($('#background-color').val());
	        			
	        			jQuery("input[name=app_style]").click(function(e) {
	            			if (jQuery(this).val() == 'default') {
	            				jQuery('#set_custom_style').hide();
	            				pickColor(default_color);
	         				} else {
	         					jQuery('#set_custom_style').show();
	         					$('#colorPickerDiv').show();
	         				}  
	            		});
	        			$('#background-color').keyup( function() {
	        				var a = $('#background-color').val(),
	        					b = a;
	
	        				a = a.replace(/[^a-fA-F0-9]/, '');
	        				if ( '#' + a !== b )
	        					$('#background-color').val(a);
	        				if ( a.length === 3 || a.length === 6 )
	        					pickColor( '#' + a );
	        			});
	        		});
	        	})(jQuery);
			</script>
			<div id="zywxapp_activation_container" class="no_js">
            	<div id="zywxapp_js_disabled">
	                <div id="js_error" class="zywxapp_errors_container s_container">
	                    <div class="errors">
	                        <div class="zywxapp_error"><?php echo __('看来您的浏览器阻止使用JavaScript。请更改您的浏览器设置并再次尝试', 'zywxapp');?></div>
	                   </div>
	                </div>
            	</div>
            	<div id="zywxapp_js_enabled">
            		<div id="zywxapp_icon" class="icon32"></div>
					<h2>AppCan-WP手机客户端风格设置</h2>
					<div class="clear"></div>
            		<div id="zywxapp_clent" class="zywxapp_content">
            			<form name="zywxapp_style_form" id="zywxapp_style_form" action="" method="post">
	            			<div id="binding_weibo" class="stuffbox">
		            			<h3><label>绑定微博appkey<span class="tip">（请输入有效的appkey,如检测未生效则使用默认key）</span></label></h3>
	            				<div class="set">
	            					<p class="micor_bo">微博平台</p>
	            					<ul class="micorbo_name">
						        		<li>
						            		<p>
							            		<label for="qq_appkey">
							            			<!--<input type="checkbox" name="qq" id="qq" value="0" />&nbsp;&nbsp;&nbsp;&nbsp;-->
							            			腾讯
							            		</label>
							            	</p>
							                <div class="inside">
							                	<table class="form-table">
													<tbody>
														<tr>
															<th class="first"><label for="qq_appkey">appkey：</label></th>
															<td>
																<input type="text" class="regular-text" value="<?php echo ZywxappConfig::getInstance()->qq_appkey;?>" id="qq_appkey" name="qq_appkey">
																<span class="description">请输入申请的appkey</span>
															</td>
														</tr>
														<tr>
															<th class="first"><label for="qq_callback_url">应用回调页：</label></th>
															<td><input type="text" class="regular-text code" value="<?php echo ZywxappConfig::getInstance()->qq_callback_url;?>" id="qq_callback_url" name="qq_callback_url">
															<span class="description">请输入正确的地址格式，微博授权完毕返回页面将根据您输入的URL跳转</span>
														</td>
														</tr>
													</tbody>
												</table>
							                </div>
						            	</li>
            							<li>
							            	<p>
							            		<label for="sina_appkey">
							            			<!--<input type="checkbox" name="sina" id="sina" value="0" />&nbsp;&nbsp;&nbsp;&nbsp;-->
							            			新浪
							            		</label>
							            	</p>
						                	<div class="inside">
							                	<table class="form-table">
													<tbody>
														<tr>
															<th class="first"><label for="sina_appkey">appkey：</label></th>
															<td>
																<input type="text" class="regular-text" value="<?php echo ZywxappConfig::getInstance()->sina_appkey;?>" id="sina_appkey" name="sina_appkey">
																<span class="description">请输入申请的appkey</span>
															</td>
														</tr>
														<tr>
															<th class="first"><label for="sina_callback_url">应用回调页：</label></th>
															<td><input type="text" class="regular-text code" value="<?php echo ZywxappConfig::getInstance()->sina_callback_url;?>" id="sina_callback_url" name="sina_callback_url">
															<span class="description">请输入正确的地址格式，微博授权完毕返回页面将根据您输入的URL跳转</span>
														</td>
														</tr>
													</tbody>
												</table>
						                	</div>
						            	</li>
        							</ul>
	            				</div>
	            			</div>
		            		<div id="custom_style" class="stuffbox">
		            			<h3><label>自定义风格色彩</label></h3>
		            			<div class="set">
			            			<div class="style_select">
			            				<p>
			            					<label>
												<input type="radio" value="default" name="app_style" <?php if(ZywxappConfig::getInstance()->app_style == 'default') { echo 'checked="checked"';}?>><b>系统默认色彩</b>
											</label>
										</p><br>
										<p>
			            					<label>
												<input type="radio" value="custom" name="app_style" <?php if(ZywxappConfig::getInstance()->app_style == 'custom') { echo 'checked="checked"';}?>><b>自定义风格色彩</b>
											</label>
										</p>
							            <div id="set_custom_style" class="set_custom_style" <?php if(ZywxappConfig::getInstance()->app_style == 'default') { echo 'style="display:none;"';}?>>
							                <p class="mt20">
							                	<label>颜色值：</label>
							                	<input id="background-color" name="app_color" type="text" value="<?php echo '#'.ZywxappConfig::getInstance()->app_color;?>">
							                </p>
							                <div id="colorPickerDiv"></div>
							            </div>
							        </div>
							        <div class="zywxapp_phone_bg">
	            						<div class="zywxapp_phone_position">
						            		<div class="zywxapp_phone_wap">
						            			<div id="zywxapp_phone_top"></div>
						                		<div id="zywxapp_phone_title">近期文章</div>
						                		<div id="zywxapp_phone_feature"></div>
						                		<div id="zywxapp_phone_content"></div>
						            		</div>
						        		</div>
	        						</div>
						        	<div class="clear"></div>
		            			</div>
	            			</div>
					    	<p class="submit">
								<input type="submit" value="下一步" class="button-primary zywxapp_submit" id="zywxapp_style_submit" name="zywxapp_style_submit">
							</p>
						</form>
					</div>
				</div>
        	</div>
	        <script type="text/javascript">
	            document.getElementById('zywxapp_activation_container').className = 'js wrap';
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
    	<?php
        }
    }

    public function advertisementDispaly()
    {
    	$logos = is_array(ZywxappConfig::getInstance()->logo) ? ZywxappConfig::getInstance()->logo : array();
    	$effectCommonvJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/effect_commonv1.1.js';
    	wp_register_script('zywxapp_effectCommonv_script', $effectCommonvJsFile);
    	wp_enqueue_script('zywxapp_effectCommonv_script');
    	
    	$jqueryOcuploadJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/jquery.ocupload-1.1.4.js';
    	wp_register_script('zywxapp_jqueryOcupload_script', $jqueryOcuploadJsFile);
    	wp_enqueue_script('zywxapp_jqueryOcupload_script');
    	
    	$ajaxUploadJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/ajaxfileupload.js';
    	wp_register_script('zywxapp_ajaxUploadJsFile_script', $ajaxUploadJsFile);
    	wp_enqueue_script('zywxapp_ajaxUploadJsFile_script');
    	
    	$baseJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/admin_base.js';
    	wp_register_script('zywxapp_base_script', $baseJsFile);
    	wp_enqueue_script('zywxapp_base_script');
    	
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
	            <div id="icon-options-general" class="icon32"><br /></div>
				<h2>AppCan-WP手机客户端宣传图片设置</h2>
				<div id="message" class="hidden error">
					<p><strong></strong></p>
				</div>
		        <div id="zywxapp_clent" class="stuffbox zywxapp_content">
					<div class="zywxapp_logo">
						<div class="zywxapp_phone_bg">
            				<div class="zywxapp_phone_position">
			            		<div class="zywxapp_phone_wap">
			            			<div id="zywxapp_phone_top"></div>
			                		<div id="zywxapp_phone_title">近期文章</div>
			                		<div id="zywxapp_phone_feature" class="zywxapp_phone_feature">
			                			<ul id="zywxapp_logo_slideplay">
		                    				<?php foreach($logos as $key => $logo) {
					    						echo '<li id="'.$key.'"><img src="'.$logo.'" /></li>';
					    					}?>
	            						</ul>
			                		</div>
			                		<div id="zywxapp_phone_content"></div>
			            		</div>
			        		</div>
        				</div>
					    <div class="zywxapp_upload_img">
					    	<div id="zywxapp_logo_images">
					    	<?php foreach($logos as $key => $logo) {?>
					    		<div class="zywxapp_logo_item">
					    			<img id="<?php echo $key;?>" src="<?php echo $logo;?>" width="200" height="130" />
					    			<a onclick="deleteLogo(this);" class="zywxapp_delete_image_icon" href="javascript:void(0)"></a>
					    		</div>
					    	<?php }?>
					    	</div>
					    	<div class="clear"></div>
					        <!-- <p>上传自定义图标（上传图像不超过1M，尺寸为190*320，格式为png、jpg、gif）</p> -->
					        <p>上传自定义图片（上传图像请按照长宽比3.2：2计算，建议图片大小为480*320，格式为png、jpg，gif）</p>
					        <div class="zywxapp_upload_btn" id="zywxapp_upload_btn">
					        	<input id="zywxapp_upload" type="button" value="上传图片" />
					        </div>
					    </div>
					    <div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			document.getElementById('zywxapp_activation_container').className = 'js wrap';
		 	var imageUrl = "<?php echo ZYWXAPP_IMAGES_URL;?>";
		 	var uploadUrl = "<?php echo get_bloginfo('url')?>" + "/?<?php echo ZYWXAPP_DIR_NAME;?>/system/logo";
		 	var color = '#<?php echo ZywxappConfig::getInstance()->app_color;?>';
            jQuery(document).ready(function(){
            	zywxappRegisterAjaxErrorHandler();
            	jQuery('#zywxapp_phone_title').css('background-color', color);
            	if (jQuery('#zywxapp_logo_slideplay li').length > 1) {
        			new dk_slideplayer("#zywxapp_logo_slideplay",{width:"257px",height:"157px",fontsize:"0",time:"4000"});
        		}
            	activeUpload();
                jQuery('#zywxapp_upload').click(activeUpload);
            });

            function deleteLogo(event) {
				var id = jQuery(event).prev().attr('id');
				var src = jQuery(event).prev().attr('src');
				var params = {
	                    action: 'zywxapp_logo_delete',
	                    type : 'delete',
	                    logo_url:src,
	                };
				jQuery.post(ajaxurl,params,function(json){
                	var data = jQuery.parseJSON(json);
                	if (! data.status) {
                		showMessage(data.message);
                	} else {
                		jQuery(event).parent().remove();
                		jQuery('.dkTitleBg').next().remove();
                		jQuery('.dkTitleBg').remove();
                		jQuery('.dkTitle').remove();
                		jQuery('#zywxapp_logo_slideplay').find('li#'+id).remove();
                		if (jQuery('#zywxapp_logo_slideplay li').length > 1) {
                			new dk_slideplayer("#zywxapp_logo_slideplay",{width:"257px",height:"157px",fontsize:"0",time:"4000"});
                		}
                	}
                });
            }
            
            function activeUpload()
            {
                var upload = jQuery('#zywxapp_upload').upload(
                {
                    name: 'logo',
                    autoSubmit:false,
                    action:uploadUrl+'&type=add',
                    onSelect:function()
                    {
                        var file=this.filename();                             
                        var ext=(/[.]/.exec(file))?/[^.]+$/.exec(file.toLowerCase()):"";
                        if(!(ext&&/^(jpg|png|jpeg|gif)$/.test(ext)))
                        {
                            alert("目前不支持此文件类型");
                            return;
                        }
                        this.submit();
                    },
                    onComplete:function(response)
                    {
                    	var data = jQuery.parseJSON(response);
                    	if (! data.status) {
                    		showMessage(data.message);
                    		return false;
                    	} else {
                    		var imageBox = jQuery('#zywxapp_logo_images');
                        	var prevId = parseInt(imageBox.find('img:last').attr('id'),10);
                        	if (prevId != NaN && prevId) {
                        		var newId = prevId + 1;
                        	} else {
                            	var newId = 1;
                        	}
                        	var imgBox = '<div class="zywxapp_logo_item">'+
                            	'<img id="'+ newId +'" src="'+data.items+'" width="200" height="130" />'+
                        		'<a onclick="deleteLogo(this);" class="zywxapp_delete_image_icon" href="javascript:void(0)"></a></div>';
                    		jQuery('#zywxapp_logo_images').append(imgBox);

                    		var liBox = '<li id="'+ newId +'"><img src="'+data.items+'" /></li>';
                    		jQuery('#zywxapp_logo_slideplay').append(liBox);
                    		jQuery('#zywxapp_logo_slideplay li').attr('style','');
                    		jQuery('.dkTitleBg').next().remove();
                    		jQuery('.dkTitleBg').remove();
                    		jQuery('.dkTitle').remove();
                    		if (jQuery('#zywxapp_logo_slideplay li').length > 1) {
                    			new dk_slideplayer("#zywxapp_logo_slideplay",{width:"257px",height:"157px",fontsize:"0",time:"4000"});
                    		}
                    	}
                    }
                });
            }
    	</script>
        <?php 
    }
    	
}