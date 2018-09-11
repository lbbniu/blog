<?php

require_once ('../../../wp-load.php');
require_once (ABSPATH . '/wp-admin/includes/media.php');
require_once (ABSPATH . '/wp-admin/includes/file.php');
require_once (ABSPATH . '/wp-admin/includes/image.php');




// function new_filename($filename) {
// 	$info = pathinfo($filename);
// 	$ext = empty($info['extension']) ? 'jpg' : $info['extension'];
// 	return uniqid('pinjie_').'.'.$ext;
// }
// add_filter('sanitize_file_name', 'new_filename', 10);

// function custom_upload_filter($file) {
// 	$info = pathinfo($file['name']);
// 	$ext = empty($info['extension']) ? 'jpg' : $info['extension'];
// 	$file['name'] = uniqid('pinjie_').'.'.$ext;
// 	return $file;
// }
// add_filter('wp_handle_upload_prefilter', 'custom_upload_filter' );

if ( $_FILES['multi_image']['size'] ) {
	$info = pathinfo($_FILES['multi_image']['name']);
	$ext = empty($info['extension']) ? 'jpg' : $info['extension'];
	$new_filename = uniqid('pinjie_').'.'.$ext;
	$_FILES['multi_image']['name'] = $new_filename;

	// $attachmentId = media_handle_upload('multi_image', 6);
	$overrides = array( 'test_form' => false );
	$file = wp_handle_upload($_FILES['multi_image'], $overrides);	//单纯上传 不写数据库
	if ( is_array($file) ) {
		// $file_path = $file['file'];
		echo "[1, '$new_filename']";
	} else {
		echo "[0, '\u4E0A\u4F20\u56FE\u7247\u51FA\u9519\u8BF7 \u91CD\u65B0\u4E0A\u4F20']";
	}
	
	exit();
}