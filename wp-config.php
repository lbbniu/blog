<?php
/**
 * WordPress基础配置文件。
 *
 * 本文件包含以下配置选项：MySQL设置、数据库表名前缀、密钥、
 * WordPress语言设定以及ABSPATH。如需更多信息，请访问
 * {@link http://codex.wordpress.org/zh-cn:%E7%BC%96%E8%BE%91_wp-config.php
 * 编辑wp-config.php}Codex页面。MySQL设置具体信息请咨询您的空间提供商。
 *
 * 这个文件被安装程序用于自动生成wp-config.php配置文件，
 * 您可以手动复制这个文件，并重命名为“wp-config.php”，然后填入相关信息。
 *
 * @package WordPress
 */
#define('WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST']);
#define('WP_HOME', 'https://' . $_SERVER['HTTP_HOST']);
// ** MySQL 设置 - 具体信息来自您正在使用的主机 ** //
/** WordPress数据库的名称 */
define('DB_NAME', 'blog');

/** MySQL数据库用户名 */
define('DB_USER', getenv('DB_USER'));

/** MySQL数据库密码 */
define('DB_PASSWORD', getenv('DB_PASSWORD'));

/** MySQL主机 */
define('DB_HOST', getenv('DB_HOST'));

/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8');

/** 数据库整理类型。如不确定请勿更改 */
define('DB_COLLATE', '');
/* 强制后台和登录使用 SSL */
define('FORCE_SSL_LOGIN', true);
define('FORCE_SSL_ADMIN', true);
/**#@+
 * 身份认证密钥与盐。
 *
 * 修改为任意独一无二的字串！
 * 或者直接访问{@link https://api.wordpress.org/secret-key/1.1/salt/
 * WordPress.org密钥生成服务}
 * 任何修改都会导致所有cookies失效，所有用户将必须重新登录。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '-WtF+Voj#A@<wJ[~;`hUPk4)pNs0[ &^Zu 5y/9O]|qE5CF-s2)uRBhK5HMU+g-#');
define('SECURE_AUTH_KEY',  'LGt^BEB&B~3#LS11%(5W`hFQX Nnuh7?>*MXU969cPKhrx+2$`_j+EfIDs39z(l{');
define('LOGGED_IN_KEY',    'E/{/v>|_u_d/-2]^oU~|k,)+nh*gay*VFk_-nhkHHoR%fZ3L|,OW-JTs<Rw,u1%A');
define('NONCE_KEY',        ')wQ]<`9O`ag^7NKl|fow@$yC3-]xKV*gu|uL>QTpkZhv<@|)9~Ty|1OD0~%Daz m');
define('AUTH_SALT',        '~m61Lk(rI37;@1!z4daEd.k~Km*a7vk;N}(}F/Rk@o|=0I86xk]Xz)<.]b476ApL');
define('SECURE_AUTH_SALT', '+aIh1*Lla_4a-Q:F-<vN;~ <o?M1d6(?k-J{QHX-hWVICKf$@5&uv|~ E#`=ToJT');
define('LOGGED_IN_SALT',   '(gnaV:A?{{.pb@,`M+afiwsC&l8Oi<|PksleT|VQB=,rih)i!PuVr[>~Hb{A`ju:');
define('NONCE_SALT',       'F|5%Qd`FC%zedyki*S]Mnb=~i#G>l.GIPc4#0T%ljj|#je-H  )I/bPd=Rj|&#JP');

/**#@-*/

/**
 * WordPress数据表前缀。
 *
 * 如果您有在同一数据库内安装多个WordPress的需求，请为每个WordPress设置
 * 不同的数据表前缀。前缀名只能为数字、字母加下划线。
 */
$table_prefix  = 'wp_';

/**
 * WordPress语言设置，中文版本默认为中文。
 *
 * 本项设定能够让WordPress显示您需要的语言。
 * wp-content/languages内应放置同名的.mo语言文件。
 * 例如，要使用WordPress简体中文界面，请在wp-content/languages
 * 放入zh_CN.mo，并将WPLANG设为'zh_CN'。
 */
define('WPLANG', 'zh_CN');

/**
 * 开发者专用：WordPress调试模式。
 *
 * 将这个值改为true，WordPress将显示所有用于开发的提示。
 * 强烈建议插件开发者在开发环境中启用WP_DEBUG。
 */
define('WP_DEBUG', false);

/**
 * zh_CN本地化设置：启用ICP备案号显示
 *
 * 可在设置→常规中修改。
 * 如需禁用，请移除或注释掉本行。
 */
define('WP_ZH_CN_ICP_NUM', true);

/* 好了！请不要再继续编辑。请保存本文件。使用愉快！ */
define('FS_METHOD', 'direct');
/** WordPress目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** 设置WordPress变量和包含文件。 */
require_once(ABSPATH . 'wp-settings.php');
