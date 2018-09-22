</section>
<footer class="footer">
    <div class="footer-inner">
        <div class="copyright pull-left">
         <a href="https://www.lbbniu.com/" title="无限飞翔">无限飞翔</a> 版权所有，保留一切权利 · <a href="https://www.lbbniu.com/sitemap.xml" title="站点地图">站点地图</a>   ·   基于WordPress构建   © 2011-2014<!--  ·   托管于 <a rel="nofollow" target="_blank" href="http://yusi123.com/go/aliyun">阿里云主机</a> & <a rel="nofollow" target="_blank" href="http://yusi123.com/go/qiniu">七牛云存储</a>-->
		 <a rel="nofollow" target="_blank" href="http://www.miitbeian.gov.cn/">京ICP备14053647号-1</a>
		 
        </div>
        <div class="trackcode pull-right">
            <?php if( dopt('d_track_b') ) echo dopt('d_track'); ?>
        </div>
    </div>
</footer>

<?php 
wp_footer(); 
global $dHasShare; 
if($dHasShare == true){ 
	echo'<script>with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>';
}  
if( dopt('d_footcode_b') ) echo dopt('d_footcode'); 
?>
</body>
</html>