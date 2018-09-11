<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappCmsUserAccountHandler
{
	/**
	 * 登入功能
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-1-12 下午06:26:39
	 * @author mxg<jiemack@163.com>
	 * @return array|object 
	 */
	public function login()
	{
		$credentials['user_login'] = trim($_REQUEST['user_login']);
		$credentials['user_password'] = trim($_REQUEST['user_pass']);
		$credentials['remember'] = $_REQUEST['remember'] ? $_REQUEST['remember'] : true ;
		$user = wp_signon($credentials);
		if (is_wp_error($user)) {
			return $user;
		}
		$return['user_id'] = $user->ID;
		$return['user_login'] = $user->user_login;
		$return['nickname'] = $user->nickname;
		$return['user_level'] = $user->user_level;
		$return['user_role'] = $user->roles[0];
		return $return;
	}
	
	/**
	 * 检测用户登入的用户名/名人号和密码是否正确
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-1-14 下午01:23:50
	 * @author mxg<jiemack@163.com>
	 * 
	 */
	public function check()
	{
		$username = trim($_REQUEST['user_login']);
        $password = trim($_REQUEST['user_pass']);

        if ( strpos( $username, '@' ) ) {
			$user_data = get_user_by('email', $username);
			if (empty($user_data)) {
				return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
			}
			$username = $user_data->user_login;
		} 
		$user = wp_authenticate($username, $password);
		if (is_wp_error($user)) {
        	return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));
		}
		$return['user_id'] = $user->ID;
		$return['user_login'] = $username;
		$return['nickname'] = $user->nickname;
		return $return;
	}
	
	/**
	 * 注册功能
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-1-11 下午08:57:00
	 * @author mxg<jiemack@163.com>
	 * @return array|object 
	 */
    public function register()
    {
		$_REQUEST['action'] = '';
		$username = $_REQUEST['user_login'];
		$email = $_REQUEST['user_email'];
		ob_start();
		require_once ABSPATH . 'wp-includes/registration.php';
		require_once ABSPATH . 'wp-login.php';
		ob_end_clean();
		ZywxappLog::getInstance()->write('INFO', 'Before register user: ' . $username, 'ZywxappCmsUserAccountHandler.register');
		$user_id = register_new_user($username, $email);
		ZywxappLog::getInstance()->write('INFO', 'After register user: ' . $username, 'ZywxappCmsUserAccountHandler.register');
    	
		if (is_wp_error($user_id)) {
			return $user_id;
		}
		return true;
    }
    
    /**
     * 忘记密码
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-1-12 下午05:38:11
     * @author mxg<jieamck@163.com。
     * @return bool
     */
    public function forgotPassword()
    {
        $status = $this->_retrievePassword();
        return $status;
    }
    
    /**
     * 修改wordpress找回密码方法
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-1-14 上午11:15:19
     * @author mxg<jiemack@163.com>
     * @return bool|WP_Error True: when finish. WP_Error on error
     */
	private function _retrievePassword()
	{
		global $wpdb, $current_site;
		
		if ( empty( $_REQUEST['user_login'] ) ) {
			return new WP_Error('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));
		} else if ( strpos( $_REQUEST['user_login'], '@' ) ) {
			$user_data = get_user_by( 'email', trim( $_REQUEST['user_login'] ) );
			if ( empty( $user_data ) )
				return new WP_Error('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
		} else {
			$login = trim($_REQUEST['user_login']);
			$user_data = get_user_by('login', $login);
		}
		do_action('lostpassword_post');
	
		if ( !$user_data ) {
			return new WP_Error('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.'));
		}
		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;

		do_action('retreive_password', $user_login);  // Misspelled and deprecated
		do_action('retrieve_password', $user_login);
	
		$allow = apply_filters('allow_password_reset', true, $user_data->ID);
		if ( ! $allow )
			return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
		else if ( is_wp_error($allow) )
			return $allow;
	
		$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
		if ( empty($key) ) {
			// Generate something random for a key...
			$key = wp_generate_password(20, false);
			do_action('retrieve_password_key', $user_login, $key);
			// Now insert the new md5 key into the db
			$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
		}
		$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
		$message .= network_site_url() . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
		$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
	
		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
		$title = sprintf( __('[%s] Password Reset'), $blogname );
	
		$title = apply_filters('retrieve_password_title', $title);
		$message = apply_filters('retrieve_password_message', $message, $key);
	
		if ( $message && !wp_mail($user_email, $title, $message) ) {
			return new WP_Error('e-mail_could_not_sent', __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );
		}
		return true;
	}
}
