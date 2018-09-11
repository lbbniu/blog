</section>
<footer class="footer">
    <div class="footer-inner">
        <div class="copyright pull-left">
         <a href="http://www.cnsecer.com/" title="安全者">安全者</a> 版权所有，保留一切权利 <a href="http://www.cnsecer.com/sitemap.xml" title="站点地图">站点地图</a>   ·   基于WordPress构建   © 2011-2014  ·  
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
	echo'<script>with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>';
}  
if( dopt('d_footcode_b') ) echo dopt('d_footcode'); 
?>
</body>
</html>