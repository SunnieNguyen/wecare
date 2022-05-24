<?php
define( 'WP_CACHE', false /* Modified by NitroPack */ );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wecare' );

/** Database username */
define( 'DB_USER', 'wecare' );

/** Database password */
define( 'DB_PASSWORD', '123' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'zK)pU/*|4zdR*Bi-`xSFIp/6u-QUL|=BvdQ:0V|kKyY^7 ><W$GzIKO/lLx22F_{' );
define( 'SECURE_AUTH_KEY',  '8R7>1@8d(2]Ir/bk(],frrt!t.n62=>+qTcn1?DWI6[A&dc$08[;CLy]z]^$]BUt' );
define( 'LOGGED_IN_KEY',    '%;WS[c6#1v9]wKoLBhUmAzuw#G>J6C{X~K$&TLxbjNU:9 O}fTSiB[<A&P[qphWZ' );
define( 'NONCE_KEY',        'GQg=SyO+9uEar}^)e;i{DkG]SDh]$$Z  ,l_[Kk*sn4pW<ca2I%MP6IOZ.@=Sll2' );
define( 'AUTH_SALT',        'Wr/e~`X8$aaW7z/j8*T6EB_[tM/YM  ,jZij>0,HnN)Mue]:5X]1UtXl$e@9 6AO' );
define( 'SECURE_AUTH_SALT', 'bGyyJ* >cB1SQKV-7x~>K>KGV+JkI-1=~q+w5h3SNqS2A65a@#gpfF6U)HZYS15/' );
define( 'LOGGED_IN_SALT',   'RQ{ &qTk~e4pjSo{SScK0b1*3c<)bqqj1$.jj(q*$~|`E=[9j7@2#|lbDmPvr`Dm' );
define( 'NONCE_SALT',       '_I&yS|4iK/bPpkhKZ31^U-Q{h3yk@)onOXzqQ1?xk7WqW5BU[c(A;7@NsPwzNS#6' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
