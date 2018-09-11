<?php if ( post_password_required() ) return; ?>
<?php if ( have_comments() ) : ?>
	<a name="comments"></a>
	<ul class="commentlist">
		<?php wp_list_comments('style=ul&type=comment&avatar_size=20&callback=yeti_theme_comment'); ?>
	</ul><!-- .commentlist -->
	<div id="comments-navi">
		<span class="loading"></span>
		<?php paginate_comments_links('prev_text=上一页&next_text=下一页');?>
	</div>
<?php endif; // have_comments() ?>
<?php
	add_action('comment_form', 'float_clear');
	function float_clear() {
		echo '<div class="clear"></div>';
	}
	$user = wp_get_current_user();
	$user_ID = $user->exists() ? $user->ID : 0;
	global $comment_notice;
	// var_dump( get_comments_pagenum_link(get_comment_pages_count()) );
	$args = array(
		'title_reply' => '',	//标题[发布评论]
		'logged_in_as' => '<div class="logged-in-as"><a href="'.admin_url( 'profile.php' ).'" target="_blank">'.get_avatar($user_ID, 36).'</a></div>',
		'comment_field' => '<div class="comment-form-comment"><textarea id="comment" name="comment" rows="1" aria-required="true">'.$comment_notice.'</textarea></div>',
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'id_submit' => 'comment_submit',
		'label_submit' => "发表评论",
		'fields' => array(
			'author' => '<input id="author" type="hidden" aria-required="true" value="" name="author">',
			'url' => '<input id="url" type="hidden" value="" name="url">',
			'email' => '<input id="avatar" type="hidden" value="" name="avatar"><div class="commenter"><a href="javascript:;"><img src="'.get_bloginfo('template_url').'/ui/userlogin.png" /></a></div>'
		)
		
	);
	comment_form( $args );
?>