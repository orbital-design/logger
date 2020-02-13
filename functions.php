<?php
/**
 * Theme functions file.
 *
 * This file is used to bootstrap the theme.
 *
 * @since 1.0.0
 *
 * @package   Logger
 * @author    Adam Cullen <adam.cullen@live.co.uk>
 * @copyright 2020 Adam Cullen
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/addzycullen/logger
 */

define( 'LOGGER_VERSION', '1.0.0' );
define( 'LOGGER_MINIMUM_WP_VERSION', '5.2' );
define( 'LOGGER_MINIMUM_PHP_VERSION', '7.2' );


// Compatibility check - Bail if reqs aren't met.
if ( version_compare( $GLOBALS['wp_version'], LOGGER_MINIMUM_WP_VERSION, '<' ) || version_compare( phpversion(), LOGGER_MINIMUM_PHP_VERSION, '<' ) ) {
    require_once get_template_directory() . 'inc/back-compat.php';
    return;
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/no-timber.html';
		}
	);
	return;
}

// Check if Kirki is loaded as a plugin, if not load the included version .
if ( ! class_exists( 'Kirki' ) ) {
    require_once get_template_directory() . '/inc/Kirki/kirki.php';
}

// Check if AQ_Resizer exists, if not load the included version.
if ( ! class_exists( 'Aq_Resize' ) ) {
    require_once get_template_directory() . '/inc/Aqua_Resizer/aq_resizer.php';
}

// Include WordPress shims - support newer functionality.
require get_template_directory() . '/inc/wordpress-shims.php';

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

// Bootstrap the theme.
require_once get_template_directory() . '/inc/bootstrap.php';

/* Omit closing PHP tag to avoid "Headers already sent" issues. */

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
// class StarterSite extends Timber\Site {
// 	/** Add timber support. */
// 	public function __construct() {
// 		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
//         add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );

// 		parent::__construct();
//     }
// 	/** This is where you add some context
// 	 *
// 	 * @param string $context context['this'] Being the Twig's {{ this }}.
// 	 */
// 	public function add_to_context( $context ) {
// 		$context['foo']   = 'bar';
// 		$context['stuff'] = 'I am a value set in your functions.php file';
// 		$context['notes'] = 'These values are available everytime you call Timber::context();';
// 		$context['menu']  = new Timber\Menu();
// 		$context['site']  = $this;
// 		return $context;
// 	}

// 	/** This Would return 'foo bar!'.
// 	 *
// 	 * @param string $text being 'foo', then returned 'foo bar!'.
// 	 */
// 	public function myfoo( $text ) {
// 		$text .= ' bar!';
// 		return $text;
// 	}

// 	/** This is where you can add your own functions to twig.
// 	 *
// 	 * @param string $twig get extension.
// 	 */
// 	public function add_to_twig( $twig ) {
// 		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
// 		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
// 		return $twig;
// 	}

// }

// new StarterSite();
