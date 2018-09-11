<?php
if ( !empty($_GET['tpl']) ) {
	$tpl = "-".trim($_GET['tpl']);
} else {
	$tpl = "";
}
include( "ui/ie{$tpl}.css" );

$thisurl = dirname($_SERVER['PHP_SELF']);
echo <<<pie
#main .topbox,
#main .notice,
#pagination
{ behavior: url({$thisurl}/ui/PIE.htc); }
pie;
?>