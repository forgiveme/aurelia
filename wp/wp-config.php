<?php
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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'aureliaskincare_blog');

/** MySQL database username */
define('DB_USER', 'aurelia_wpblog');

/** MySQL database password */
define('DB_PASSWORD', 'Dbe@54321');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'M%tp WlP_)x>2yh@d}td{OZzozO751sJVx+o:DUY#R8S.iOh9q?g{QGB9[$^QH4i');
define('SECURE_AUTH_KEY',  ':=JV:g4b~t3Kqtx9Mx#O]nN)H]P|6WMsyhk# F#K1|f^7!DSL3nqfKEsF#xw~bO7');
define('LOGGED_IN_KEY',    'y|^cODHvB0j/HBE|@YOa(1+P|yq#~|eA!]Kk&W7U}3:oY&|6W@(Y;>e!A=*_jo{1');
define('NONCE_KEY',        '#$&(zG-XS]<#a/ZGKZS3VueCyj^Z;U(<qi~=sm<x:zr.<OJ;(kV;|o_=Ml:@JX;L');
define('AUTH_SALT',        'tD/R>f+dvBCI7D3DLg$|ci8cFSr;MDx(Lykp:^Tqw93!<PG_Pbh][f]?;`S6A^hI');
define('SECURE_AUTH_SALT', 'Yw~--Hn>!_%;>x146B /) -`jd<w]Ul(hg}+$1+8|`&!l[Vp%+CshU<*&+-0%^J#');
define('LOGGED_IN_SALT',   '7Y4-|42UCxR^fAOUG@^Bmi+[: KQnf!0#A%F?b*e~0)Ml0oKcKA.Ubmkh+{Oi+ra');
define('NONCE_SALT',       '?+5jT8rR|)wBf<m>3l=gnL +5<@X2Xf&^:hmEd*7^G |T?*qUKuS~Ja6XD7Su:|5');

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
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'en_GB');

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
