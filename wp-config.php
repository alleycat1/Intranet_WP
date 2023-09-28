<?php
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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'intranet' );

/** Database username */
define( 'DB_USER', 'admindb' );

/** Database password */
define( 'DB_PASSWORD', 'hbGV%953123' );

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
define( 'AUTH_KEY',         '&R6C,hSg2<>{l[{su]Rca,$jV1/%-mPOtOKCx1-amr:8}xoPafvZ.6[;]` XFA=x' );
define( 'SECURE_AUTH_KEY',  'L#hOSPWn, G`b u:i[(t|B&{=/k5<!bH%pnsz~Tg[[|fUl%ipHA|r}<@?~K_Z;t.' );
define( 'LOGGED_IN_KEY',    '^wf]thQo |J-%G 8>,/x7-vPZI]Sc/f?r`:*$X1Q&Z=Px &&kp,mq0}RIu6H0)eS' );
define( 'NONCE_KEY',        'd34CA14M+nKTJeWVlpY?GD2fO9v^Z7q,Ay8xu3+|5W=dK2iSz%Fx/r.u3(O?ILB;' );
define( 'AUTH_SALT',        '[~g>];[gl_n,gu1YcW9nZ)sTH%[VRZ 2U[=~e9H1A7-&bFf9}#_7{ZHjMVXVnS,3' );
define( 'SECURE_AUTH_SALT', '=J.UU*VXI6]x|=Hmw ;RQ+r#mQwTDK7..M$QVOR2-?5*)$j0V)}z0!bog,3}x.6b' );
define( 'LOGGED_IN_SALT',   'yv584Nsx#-?6IBk5*[.pFWu`<>y6Ab&IN95*ULw}5=Gr uAO~N*FQU/?4PS6HjM&' );
define( 'NONCE_SALT',       'lF_~EBh+p@nvs>gibcE#OxlU64I]Cxy|^F3VBu:C[]hJOQ1D<p4d9K-q&0aO2~~s' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
