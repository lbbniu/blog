var progressTimer = null;
var progressWait = 30;

function zywxappRegisterAjaxErrorHandler(){
    jQuery.ajaxSetup({
        timeout: 60*1000,
        error:function(req, error){
       		clearTimeout(progressTimer);
         	if (error == 'timeout'){
        	 	var message = "连接错误。请再试一次，<a href='javscript:void(0);' class='retry'>重试</a>";
        	 	showMessage(message);
            } else if(req.status == 0){
            	var message = "连接错误。请再试一次，<a href='javscript:void(0);' class='retry'>重试</a>";
            	showMessage(message);
            } else if(req.status == 404){
            	var message = "请求页面出现丢失，请联系技术支持";
            	showMessage(message);
            } else if(req.status == 500){
            	var message = "服务器处理请求出现错误，请联系技术支持";
            	showMessage(message);
            } else if(error == 'parsererror'){
            	var message = "加载向导时出现错误，请联系技术支持";
            	showMessage(message);
            } else {
            	var message = "加载向导时出现错误，请联系技术支持";
            	showMessage(message);
            }
        }
    });
};

function showMessage(message,style)
{
    var style = style ? "zywxapp_success" : "error";
    document.getElementById('message').className = style;
	jQuery("#message").show().find('strong').html(message);
	progressTimer = setTimeout(hideMessage, 300 * progressWait);
}
function hideMessage()
{
	jQuery("#message").hide();
}

function showDeleteImgIcon(event) {
	jQuery(event).find('a').show();
}

function hideDeleteImgIcon(event) {
	jQuery(event).find('a').hide();
}