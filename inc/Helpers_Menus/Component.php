<?php
/**
 * Logger\Helpers_Menus\Component class
 *
 * @since 1.0.0
 *
 * @package logger
 */

namespace Logger\Helpers_Menus;

use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use function Logger\logger;
use function add_action;
use function register_nav_menus;
use function esc_html__;
use function has_nav_menu;
use function wp_nav_menu;

/**
 * Class for managing navigation menus.
 *
 * Exposes template tags:
 * * `Logger()->isPrimaryNavMenuActive()`
 * * `Logger()->displayPrimaryNavMenu( array $args = [] )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

    const PRIMARY_NAV_MENU_SLUG = 'primary';
    const SECONDARY_NAV_MENU_COL_ONE_SLUG = 'secondary_one';
    const SECONDARY_NAV_MENU_COL_TWO_SLUG = 'secondary_two';
    const SECONDARY_NAV_MENU_COL_THREE_SLUG = 'secondary_three';

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'setup_menus';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'after_setup_theme', [ $this, 'actionRegisterNavMenus' ] );
    }

    public function write_log($log) {
        if (is_array($log) || is_object($log)) {
        error_log(print_r($log, true));
        } else {
        error_log($log);
        }
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
            'isNavMenuActive'              => [ $this, 'isNavMenuActive' ],
            'displayPrimaryNavMenu'        => [ $this, 'displayPrimaryNavMenu' ],
            'displaySecondaryOneNavMenu'   => [ $this, 'displaySecondaryOneNavMenu' ],
            'displaySecondaryTwoNavMenu'   => [ $this, 'displaySecondaryTwoNavMenu' ],
            'displaySecondaryThreeNavMenu' => [ $this, 'displaySecondaryThreeNavMenu' ],
        ];
    }

    /**
     * Registers the navigation menus.
     */
    public function actionRegisterNavMenus()
    {
        register_nav_menus(
            [
                static::PRIMARY_NAV_MENU_SLUG => esc_html__( 'Primary | Masthead', 'logger' ),
                static::SECONDARY_NAV_MENU_COL_ONE_SLUG => esc_html__( 'Footer | Column One', 'logger' ),
                static::SECONDARY_NAV_MENU_COL_TWO_SLUG => esc_html__( 'Footer | Column Two', 'logger' ),
                static::SECONDARY_NAV_MENU_COL_THREE_SLUG => esc_html__( 'Footer | Column Three', 'logger' ),
            ]
        );
    }

    /**
     * Checks whether the primary navigation menu is active.
     *
     * @return bool True if the primary navigation menu is active, false otherwise.
     */
    public function isNavMenuActive( string $slug )
    {
        return (bool) has_nav_menu( $slug );
    }

    /**
     * Displays the primary navigation menu.
     *
     * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
     *                    arguments.
     */
    public function displayPrimaryNavMenu( array $args = [] )
    {
        if ( ! isset( $args['container'] ) ) {
            $args['container'] = 'ul';
        }

        $args['theme_location'] = static::PRIMARY_NAV_MENU_SLUG;
        $args['menu_class'] = '[ main-menu ]';
        wp_nav_menu( $args );
    }

    /**
     * Displays the secondary navigation menu - column one.
     *
     * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
     *                    arguments.
     */
    public function displaySecondaryOneNavMenu( array $args = [] )
    {
        if ( ! isset( $args['container'] ) ) {
            $args['container'] = 'ul';
        }

        $args['theme_location'] = static::SECONDARY_NAV_MENU_COL_ONE_SLUG;
        $args['menu_class'] = '[ secondary-menu ]';

        wp_nav_menu( $args );
    }
    /**
     * Displays the secondary navigation menu - column two.
     *
     * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
     *                    arguments.
     */
    public function displaySecondaryTwoNavMenu( array $args = [] )
    {
        if ( ! isset( $args['container'] ) ) {
            $args['container'] = 'ul';
        }

        $args['theme_location'] = static::SECONDARY_NAV_MENU_COL_TWO_SLUG;
        $args['menu_class'] = '[ secondary-menu ]';

        wp_nav_menu( $args );
    }
    /**
     * Displays the secondary navigation menu - column one.
     *
     * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
     *                    arguments.
     */
    public function displaySecondaryThreeNavMenu( array $args = [] )
    {
        if ( ! isset( $args['container'] ) ) {
            $args['container'] = 'ul';
        }

        $args['theme_location'] = static::SECONDARY_NAV_MENU_COL_THREE_SLUG;
        $args['menu_class'] = '[ secondary-menu ]';

        wp_nav_menu( $args );
    }
}
