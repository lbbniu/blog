<?php
class ZywxappUpgradeDisplay
{
	/**
	 * 升级结束
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-7 下午04:40:16
	 * @author author
	 * 
	 */
    public function upgradingFinish()
    {
        ZywxappLog::getInstance()->write('debug', "The upgrading is finished, letting the admin know","post_upgrade.zywxapp_upgrading_finish");

        $ch = new ZywxappContentEvents();
        $ch->updateCacheTimestampKey();

        $status = TRUE;

        $header = array(
            'action' => 'upgrading_finish',
            'status' => $status,
            'code' => ($status) ? 200 : 500,
            'message' => '',
        );

        echo json_encode(array('header' => $header));
        exit;
    }

    /**
     * 升级数据库
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午04:17:41
     * @author author
     * 
     */
    public function upgradeDatabase()
    {
        $installer = new ZywxappInstaller();
        $status = $installer->upgradeDatabase();

        $header = array(
            'action' => 'upgrade_database',
            'status' => $status,
            'code' => ($status) ? 200 : 500,
            'message' => '',
        );

        echo json_encode(array('header' => $header));
        exit;
    }
    
	/**
	 * 升级配置文件
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-7 下午04:27:59
	 * @author author
	 * 
	 */
    public function upgradeConfiguration()
    {
        $installer = new ZywxappInstaller();
        $status = $installer->upgradeConfiguration();

        $header = array(
            'action' => 'upgrade_configuration',
            'status' => $status,
            'code' => ($status) ? 200 : 500,
            'message' => '',
        );

        echo json_encode(array('header' => $header));
        exit;
    }

	/**
	 * 升级菜单功能
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-6 下午08:13:46
	 * @author author
	 * 
	 */
    public function display()
    {
    	$jqueryJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/jquery.tools.min.js';
    	wp_register_script('zywxapp_jquery_tools_script', $jqueryJsFile, array('jquery'));
    	wp_enqueue_script('zywxapp_jquery_tools_script');
        ?>
        <div id="zywxapp_activation_container">
            <div id="just_a_moment"></div>
            <p id="zywx_be_patient" class="text_label"><?php echo __('请耐心等候,当我们升级插件，这可能需要花费几分钟的时间。', 'zywxapp');?></p>
            <div id="zywx_icon_wrapper">
                <div id="zywx_icon_processing"></div>
                <div id="current_progress_label" class="text_label"><?php echo __('正在初始化...', 'zywxapp'); ?></div>
            </div>
            <div id="main_progress_bar_container">
                <div id="main_progress_bar"></div>
                <div id="main_progress_bar_bg"></div>
            </div>
            <p id="current_progress_indicator" class="text_label"></p>

            <p id="zywxapp_finalize_title" class="text_label"><?php echo __('如果页面不改变在几秒钟内点击', 'zywxapp'); ?><span id="finializing_activation"><?php echo __('这里', 'zywxapp'); ?></span></p>

            <div id="error_activating" class="zywxapp_error hidden"><div class="icon"></div><div class="text"><?php echo __('加载向导时出现错误，请联系技术支持', 'zywxapp');?></div></div>
            <div id="internal_error" class="zywxapp_error hidden"><div class="icon"></div><div class="text"><?php echo __('连接错误。请再试一次。,', 'zywxapp');?> <a href="javscript:void(0);" class="retry_processing"><?php echo __('重试', 'zywxapp'); ?></a></div></div>
            <div id="internal_error_2" class="zywxapp_error hidden"><div class="icon"></div><div class="text"><?php echo __('仍然有错误, 请联系技术支持', 'zywxapp');?></div></div>
            <div id="error_network" class="zywxapp_error hidden"><div class="icon"></div><div class="text"><?php echo __('连接错误。请再试一次。', 'zywxapp');?> <a href="javscript:void(0);" class="retry_processing"><?php echo __('重试', 'zywxapp'); ?></a></div></div>

            <div id="error_upgrading_db" class="zywxapp_error hidden"><div class="icon"></div><div class="text"><?php echo __('有一个升级问题，请联系技术支持', 'zywxapp');?></div></div>
            <div id="error_upgrading_config" class="zywxapp_error hidden"><div class="icon"></div><div class="text"><?php echo __('有更新的问题，请联系技术支持', 'zywxapp');?></div></div>
        </div>

        <script type="text/javascript">
			var progressTimer = null;
            var progressWait = 30;
            var upgrade_step = 0;
            var upgrade_steps = [requestDatabaseUpgrade, requestConfigurationUpgrade, requestFinalizingProcessing];

            jQuery(document).ready(function(){
                zywxappRegisterAjaxErrorHandler();
                jQuery(".retry_processing").bind("click", retryRequest);
                // Start sending requests to generate content till we are getting a flag showing we are done
                startProcessing();
            });

            function retryRequest(event){
                event.preventDefault();
                var $el = jQuery(this);

                $el.parents('.zywxapp_error').hide();

                var request = $el.parents('.zywxapp_error').data('reqObj');
                delete request.context;
                delete request.accepts;

                jQuery.ajax(request);

                $el = null;
                return false;
            }

            function retryingFailed(req, error){
                jQuery("#internal_error_2").show();
            }

            function startProcessing(){
                upgrade_steps[upgrade_step].call();
            }

            function zywxappRegisterAjaxErrorHandler(){
                jQuery.ajaxSetup({
                    timeout: 60*1000,
                    error:function(req, error){
                        clearTimeout(progressTimer);
                        if (error == 'timeout'){
                            //jQuery("#internal_error").data('reqObj', this).show();
                            startProcessing();
                        } else if(req.status == 0){
                            jQuery("#error_network").data('reqObj', this).show();
                        } else if(req.status == 404){
                            jQuery("#error_activating").show();
                        } else if(req.status == 500){
                        	startProcessing();
                        } else if(error == 'parsererror'){
                            jQuery("#error_activating").show();
                        } else {
                            jQuery("#error_activating").show();
                        }
                    }
                });
            };

            function cleanArray(arr){
                var newArr = new Array();
                for (k in arr) {
                    if (arr.hasOwnProperty(k)){
                        if(arr[k])
                            newArr.push(arr[k]);
                    }
                }
                return newArr;
            }

            function requestDatabaseUpgrade(){
                var params = {
                    action: 'zywxapp_upgrade_database'
                };

                jQuery.post(ajaxurl, params, handleDatabaseUpgrade, 'json');
                progressTimer = setTimeout(updateProgressBarByTimer, 1000 * progressWait);
            };

            function updateProgressBarByTimer(){
                var current = jQuery("#current_progress_indicator").text();

                if (current.length == 0){
                    current = 0;
                } else if (current.indexOf('%') != -1){
                    current.replace('%', '');
                }

                current = parseInt(current) + 1;

                if (current != 100){
                    jQuery("#main_progress_bar").css('width', current + '%');
                    jQuery("#current_progress_indicator").text(current + '%');
                    //progressTimer = setTimeout(updateProgressBarByTimer, 1000*progressWait);
                }
            };

            function updateProgressBar(){
                clearTimeout(progressTimer);
                progressTimer = null;

                var total_items = upgrade_steps.length

                var done = ((upgrade_step) / total_items) * 100;

                if (upgrade_step < upgrade_steps.length){
                    jQuery("#current_progress_label").text("<?php echo __('升级中...', 'zywxapp'); ?>");
                } else {
                    jQuery("#current_progress_label").text("<?php echo __('最后确定...', 'zywxapp'); ?>");
                }
                jQuery("#main_progress_bar").css('width', done + '%');
                jQuery("#current_progress_indicator").text(Math.floor(done) + '%');
            };

            function handleDatabaseUpgrade(data){
                ++upgrade_step;
                // Update the progress bar
                updateProgressBar();
                if ( typeof(data) == 'undefined'  || !data ){
                    // The request failed from some reason... skip it
                    jQuery("#error_upgrading_db").show();
                    return;
                }

                if (data.header.status){
                    startProcessing();
                } else {
                    jQuery("#error_upgrading_db").show();
                }
            }

            function requestConfigurationUpgrade(){
                var params = {
                    action: 'zywxapp_upgrade_configuration'
                };

                jQuery.post(ajaxurl, params, handleConfigurationUpgrade, 'json');
                progressTimer = setTimeout(updateProgressBarByTimer, 1000 * progressWait);
            };

            function handleConfigurationUpgrade(data){
                ++upgrade_step;
                // Update the progress bar
                updateProgressBar();
                if ( typeof(data) == 'undefined'  || !data ){
                    // The request failed from some reason... skip it
                    jQuery("#error_upgrading_config").show();
                    return;
                }

                if (data.header.status){
                    startProcessing();
                } else {
                    jQuery("#error_upgrading_config").show();
                }
            }

            function requestFinalizingProcessing(){
                var params = {
                    action: 'zywxapp_upgrading_finish'
                };

                jQuery.post(ajaxurl, params, handleFinalizingProcessing, 'json');
            };

            function handleFinalizingProcessing(data){
                ++upgrade_step;
                // Update the progress bar
                updateProgressBar();
                jQuery("#zywxapp_finalize_title").show();
                document.location.reload();
            }
        </script>
        <?php
    }
}