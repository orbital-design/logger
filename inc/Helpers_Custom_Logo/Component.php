<?php
/**
 * Logger\Helpers_Custom_Logo\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Custom_Logo;

use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use function add_action;
use function add_theme_support;
use function apply_filters;

/**
 * Class for adding custom logo support.
 *
 * @link https://codex.wordpress.org/Theme_Logo
 *
 * Exposes template tags:
 * `logger()->loggerBrandingLogoLinkEscape()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'setup_custom_logo';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'after_setup_theme', [ $this, 'actionAddCustomLogoSupport' ] );
    }

    /**
     * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `logger()`.
     *
     * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
     *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
     *               adding support for further arguments in the future.
     */
    public function templateTags() : array
    {
        return [
            'loggerBrandingLogoLinkEscape'       => [ $this, 'loggerBrandingLogoLinkEscape' ],
        ];
    }

    /**
     * Adds support for the Custom Logo feature.
     */
    public function actionAddCustomLogoSupport()
    {
        add_theme_support(
            'custom-logo',
            apply_filters(
                'logger_custom_logo_args',
                [
                    // 'height'      => 250,
                    // 'width'       => 250,
                    'flex-width'  => true,
                    'flex-height' => true,
                ]
            )
        );
    }

    /**
     * Adds Escaping support for the Custom Logo feature.
     */
    public static function loggerBrandingLogoLinkEscape()
    {
        return array(
            'a' => array(
                'href' => true,
                'class' => true,
                'title' => true,
            ),
            'img' => array(
                'src' => true,
                'alt' => true,
            ),
            'svg' => array(
                'viewbox' => true,
                'xmlns' => true,
            ),
            'g' => array(
                'id' => true,
                'fill' => true,
            ),
            'path' => array(
                'class' => true,
                'd' => true,
            ),
        );
    }
}
