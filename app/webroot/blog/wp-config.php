<?php
include('../../noserve/config.inc');
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
global $db_hostname;
global $db_username;
global $db_password;

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'blog');

/** MySQL database username */
define('DB_USER', $db_username);

/** MySQL database password */
define('DB_PASSWORD', $db_password);

/** MySQL hostname */
define('DB_HOST', $db_hostname);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'k~~^Ppk~`]%L?v/%!+o|:jU?OFbd.k%BItilZ05<aQ#V9SZ8#uQXjB?YH>&KdUtv');
define('SECURE_AUTH_KEY',  'Dy?%&l%IZ2f!v;uGsi*-<8-|XsCseZJHUfrc#OTpuE_hu73*MwFQ-j2y2>79s]G2');
define('LOGGED_IN_KEY',    'A{3$e-Y/WXm_$9-bQRa2x7(bHxICEi+G6R&yb$}1+jNft7&nC?i01-axX|&w8>O&');
define('NONCE_KEY',        'f=SxnX2{i?Qz*[WW=Hb--}}lRI_C DSYDprHdFCe~PI+|xhNhBUYYOf_^~K{1:^6');
define('AUTH_SALT',        'LbOr[Wk]cyGg*9|]#[s=wgZK5QiSjx^c/jpM7k}zCYE-uJe2-#8>(ANMa1E$Am>O');
define('SECURE_AUTH_SALT', 'rMxK*g+wOOJ>~y+ebEyC1VZh1;$SQf>^n*f%%pTsL&Ih<z,rbd`}L,5|rDI3Oc<i');
define('LOGGED_IN_SALT',   '_V-`don6!!c.a~y8.NQ&jQ(uoJ1-7|_+;H6M=x0%dq8 H`hIx+CjLw*o(.hEXMh%');
define('NONCE_SALT',       '#P<L}=N><h|F%40lz|,Q.?S>Y@.O:aX_%M_lMrAi~- o36Otgd_p^jZSm%a>1+`a');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
