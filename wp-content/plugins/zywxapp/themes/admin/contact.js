var speed=10;
var currentpos=0,alt=1,curpos1=0,curpos2=-1;
function initialize(){
	startit();
}
function scrollwindow(){
	if (document.all) {
		temp=document.documentElement.scrollTop;
	} else {
		temp=window.pageYOffset;
	}
	if (alt==0) {
		alt=1;
	} else {
		alt=0
	}
	if (alt==0) {
		curpos1=temp;
	} else {
		curpos2=temp;
	}
	if (curpos1!=curpos2){
		if (document.all){
			currentpos=document.documentElement.scrollTop-speed;
		}else {
			currentpos=window.pageYOffset-speed;
		}
		window.scroll(0,currentpos)
	} else{
		currentpos=0;
		window.scroll(0,currentpos);
	}
	if(document.documentElement.scrollTop<5){  
		window.clearInterval(intervalID); 
	}
}
function startit(){
	intervalID=setInterval("scrollwindow()",5);
}
lastScrollY=0;
function heartBeat(){ 
	var diffY;
	if (document.documentElement && document.documentElement.scrollTop) {
		diffY = document.documentElement.scrollTop;
	} else if (document.body){
		diffY = document.body.scrollTop;
	} else {
		/*Netscape stuff*/
	}

	percent=.1*(diffY-lastScrollY); 
	if(percent>0){
		percent=Math.ceil(percent); 
	} else {
		percent=Math.floor(percent); 
	}
	document.getElementById("full").style.top=parseInt(document.getElementById("full").style.top)+percent+"px";

	lastScrollY=lastScrollY+percent; 
}
suspendcode ="<div id=\"full\" style='left:8px; top:255px; position:absolute;'><table width='20' border='0' cellpadding='0' cellspacing='0'><tr><td align='center'><a href='javascript:initialize()' onfocus='blur()'><img border='0' src='../../../wp-content/plugins/Contact/image/icon_top.gif' alt='Back to Top' /></a></td></tr><tr><td align='center'><a href='mailto:lomo1984@gmail.com' target='blank'><img border='0' src='../../../wp-content/plugins/Contact/image/icon_email.gif' alt='E-Mail' /></a></td></tr><tr><td align='center'><a href='javascript:window.scroll(0,1024)' onfocus='blur()'></a></td></tr><tr><td align='center'><a href='http://wpa.qq.com/msgrd?V=1&Uin=188710065&Site=Darki&Menu=yes' target='blank'><img src='../../../wp-content/plugins/Contact/image/icon_oicq.gif' border='0' alt='Oicq' /></a></td></tr><tr><td align='center'><a href='msnim:chat?contact=shamqu@hotmail.com' target='blank'><img src='../../../wp-content/plugins/Contact/image/icon_msn.gif' border='0' /></a></td></tr><tr><td align='center'><a href='javascript:window.scroll(0,99999)' onfocus='blur()'><img border='0' src='../../../wp-content/plugins/Contact/image/icon_bottom.gif' alt='Back to Bottom' /></a></td></tr></table></div>"

document.write(suspendcode);
window.setInterval("heartBeat()",1);