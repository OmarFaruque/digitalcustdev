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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', "u0501458_devdb" );

/** MySQL database username */
define( 'DB_USER', "u0501458_u050145" );

/** MySQL database password */
define( 'DB_PASSWORD', "U0l0N3q5" );

/** MySQL hostname */
define( 'DB_HOST', "localhost" );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('WP_HOME', 'https://digitalcustdev.ru/dev/');
define('WP_SITEURL', 'https://digitalcustdev.ru/dev/');

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'cHN1DgpfL67CJNoyHbREIkivRwuy9YF37TK+M2bsd32EFRshMkkVWEfEsNMQDlhManOTJS7EQ6RPGOnAOJv0nw==');
define('SECURE_AUTH_KEY',  'HSj7ZZprEkHemO8s3OOsofAFPz7opLf/Jya5avsOz3Pgpi0zzyPDZo1bsTOSyeIqdMBfj6xgdVvYLo66JgI63A==');
define('LOGGED_IN_KEY',    '8qtktOxXeUgDM7DGOVKIB9QWXHEaRgmD/founvxiGzdrkYpjW2QHnPEb4QFqo8DoE/JyeK9RkcFPVIKipWyIRg==');
define('NONCE_KEY',        'KSLIUjU2hot9dPLghePBh4Jje6RUiCS4ZTMwQ9vXnfxL/tGQQtCOgBRMyhoGsI4V5cLciG1bmw1MyBzN+NppMw==');
define('AUTH_SALT',        'XxL3HCE0UyP4qefk9OLhgKlFZDtfaA0A6HxD7E+weRkQyHRuOLeoOqGVus3HnTLzt0CG2Pmd5k7+9NrD35XM8A==');
define('SECURE_AUTH_SALT', 'M3K34NHi/ezBcePkmmiWylWHimARAg6kUUlBG8todCuwKxoSp5HBuBYRaQInf2zRCGDDByzjydkvlkou/lCS4A==');
define('LOGGED_IN_SALT',   'l3Y5Eh1JCYvacFWxxSrwkSd0owr/GTQDMWi1dj6LGTlpgg0NzD7RLYCtSBM4RHKOMM+L6KM9AyQFfuxbqAntHA==');
define('NONCE_SALT',       'oXU7K8VfHL6xLtMqGICSs1UvLxyWC+y7McaRoujGRi93wxpzgPyuWQGF3Xu4SK1SgO1rK48poz6Lx92YgbJAmQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'sours8nl_';

// define( 'WP_MEMORY_LIMIT', '256M' );
define('CONCATENATE_SCRIPTS', false);
define( 'WP_DEBUG', false );
define('LP_DEBUG', false);
// define('WP_DEBUG_LOG', true);
// define('WP_DEBUG_DISPLAY', true);


/* That's all, stop editing! Happy blogging */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
