<?php
define('WP_CACHE', true); // Added by WP Rocket
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'the_peak_beyond');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '9Zb9Kxcpz6Bn,c@W=N!NshP.|TOY0i*Yv1DjJNJ0HoXC6TC`>l~|83_+(BFeu7WZ');
define('SECURE_AUTH_KEY',  'WByE7(Rz|=]&e$l!VE%>4z${E6dTf$o@K {h)Cdy5vQ*!7xMEmFdul[mE1-_Jx[0');
define('LOGGED_IN_KEY',    'Z(IIqQX.hmB8k0R(!fs+IB@QdY}~&@P3Qs,S8@Z<GqlLDj9]//J{RX?l :]fZ/,[');
define('NONCE_KEY',        '3(7HRW}^,)2VQ83]XRv_dHk*gs2YWr]gLt^@E}*dpg[lFJ$0R#4.9KR-7=:8bb[+');
define('AUTH_SALT',        'Yd/C!OB %,]XyRcEoFh$.w=G0]Jy0DuuOJbC@e<s4sMX;`l9sK1moB3&DL{,I&[|');
define('SECURE_AUTH_SALT', 'R$aPyHKtan:D-N_`vn7ubJ46!~Neksx>qL/PSp|AgMMs&f}K$E}K/]s8,KBN8}R?');
define('LOGGED_IN_SALT',   'aXbaKBl<c`Mqf0SSKF,@a2{(W6?-K1_m5j3MAfSBMN0Npj@`n#-72;gxA.yI acF');
define('NONCE_SALT',       'm*p6o=$HRhCmGfMi`L(Bkeoq?dSg4*h6f(a,G(a..oNa<sy&}^!&$<(RlWRIjHJ=');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wptpb_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
