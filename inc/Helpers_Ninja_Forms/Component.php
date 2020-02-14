<?php
/**
 * Logger\Helpers_Ninja_Forms\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Ninja_Forms;

use Logger\Component_Interface;
use function add_action;
use function wp_dequeue_style;

/**
 * Class extending and configuring Ninja Forms
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'helpers_ninja_forms';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'nf_display_enqueue_scripts', [ $this, 'loggerRemoveNinjaFormsStyles' ] );
    }

    /**
     * Remove Ninja Forms Styles.
     *
     * @return void
     */
    public function loggerRemoveNinjaFormsStyles()
    {
        wp_dequeue_style( 'nf-display' );
    }


}
