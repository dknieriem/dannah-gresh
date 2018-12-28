<?php
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
define('DB_NAME','dannahgr_wp945_st1');

/** MySQL database username */
define('DB_USER','dannahgr_st1');

/** MySQL database password */
define('DB_PASSWORD','5dkASvKmlpxHcX-Fy4.b');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'RoaOVtSGKf5xnrraE9pVO/TYcdU/SllxFPGXHEo0tZ4/0UtcveL0SelyD9mW5SMifspzEpDno7UILbZnUkh9eA==');
define('SECURE_AUTH_KEY',  'T7rCZLC/uwPrcbtEOejPlAQeGbVz4n7DxG6Jx1ZMNR6njc+Ri5e2mOdzkYgrgGJ4PSAD4xZlMs3Hynd3h2y6uQ==');
define('LOGGED_IN_KEY',    '9fr6T/sgKgg765fPlYfOF+EiDSAPhiUwMTVS44D1uMtz0dWwvQkYQYH6uC2+x8UK2Agh4bxnTEA4LvKoUYIT5Q==');
define('NONCE_KEY',        'FmEGxnIK6YxMn2wGxoMZxlvhLsSPirPpRc3WvzicopOwRM9O0L4Rq+H2eSt19dxpDPS/m5vt5/vQZ6nIDUiTeQ==');
define('AUTH_SALT',        'XHtopOyqrdsJ60dcEi0X4MXtjT04PntJhIfOVeriSFwTxLRXJVTxoqLlyZrAYe3ryPBhZVd/dFm4ujvn6zLReg==');
define('SECURE_AUTH_SALT', 'MoD8TyW3+TuBBXzLZ2kjZfTE0HfK86unVZuIhl1PqcMV+bXn2LeLoZUe9nvQ1VQfT1j0rkuScf6/ufahk5lJhQ==');
define('LOGGED_IN_SALT',   'HmBD3vNwxOonTDZJHXwyVIotzyFm23VVLdKU3WwE4wzP6jZ9kiJXqj/i8XbWxneJ6qXc9BdFi1KbLftqm4N60g==');
define('NONCE_SALT',       'B3M2y1upsxGPpss5yP907+MlXAtsPTW9nNzR4LD8T2XItWFG5BEsqE8Z9/EpeZJf3iv3fqa/wugkmeLoB66LHg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
define('WP_CACHE_KEY_SALT', 'DlHYQfqs67KjU75l6dX0MQ');
$table_prefix = 'wp_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

/* Inserted by Local by Flywheel. Fixes $is_nginx global for rewrites. */
if (strpos($_SERVER['SERVER_SOFTWARE'], 'Flywheel/') !== false) {
	$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.10.1';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

# Disables all core updates. Added by SiteGround Autoupdate:
define( 'WP_AUTO_UPDATE_CORE', false );
