<?php
/**
 * Logger\Helpers_Editor\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Editor;

use Logger\Component_Interface;
use kirki;
use function add_action;
use function add_filter;
use function add_theme_support;
use function get_theme_mod;
use function add_config;
use function add_panel;
use function add_section;
use function add_field;

/**
 * Class for integrating with the block editor.
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
        return 'helpers_editor';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'init', [ $this, 'actionAddBlockEditorSupport' ] );
        add_filter( 'kirki/styles_array', [ $this, 'outputColorClassesForBlockEditor' ] );
    }

    /**
     * Adds support for various editor features.
     */
    public function actionAddBlockEditorSupport()
    {
        // Add support for default block styles.
        // add_theme_support( 'wp-block-styles' );

        // Add support for wide-aligned images.
        add_theme_support( 'align-wide' );

        /**
         * Add support for enqueuing the editor styles
         *
         * Use the add_editor_style function to enqueue and load CSS on the editor screen.
         * - Must use in conjunction with add_theme_support( 'editor-styles');
         * - Adds style-editor.css to the queue of stylesheets to be loaded in the editor.
         *
         * Uncomment to use.
         */
        add_theme_support( 'editor-styles' );
        add_editor_style( './assets/dist/styles/editor.css' );

        /**
         * Add support for color palettes.
         *
         * To preserve color behavior across themes, use these naming conventions:
         * - Use primary and secondary color for main variations.
         * - Use `theme-[color-name]` naming standard for standard colors (red, blue, etc).
         * - Use `custom-[color-name]` for non-standard colors.
         *
         * Add the line below to disable the custom color picker in the editor.
         * add_theme_support( 'disable-custom-colors' );
         */

        // add_theme_support( 'disable-custom-colors' );

        $colors = get_theme_mod( 'logger_color_palette_repeater' );
        $palette = [];
        foreach ( $colors as $color ) {
            $palette[] = [
                'name' => $color['logger_color_palette_color_name'],
                'slug' => 'theme-' . generateSlugVar( $color['logger_color_palette_color_name'] ),
                'color' => $color['logger_color_palette_color_code'],
            ];
        }
        add_theme_support( 'editor-color-palette', $palette );

        /*
         * Add support custom font sizes.
         *
         * Add the line below to disable the custom color picker in the editor.
         * add_theme_support( 'disable-custom-font-sizes' );
         *
         * Slugs must match core slugs to use CSS Custom props, this means you have 5 sizes to play with
         */
        add_theme_support('disable-custom-font-sizes');
        add_theme_support(
            'editor-font-sizes',
            [
                [
                    'name'      => __( 'Small', 'logger' ),
                    'size'      => 10,
                    'slug'      => 'small',
                ],
                [
                    'name'      => __( 'Normal', 'logger' ),
                    'size'      => 20,
                    'slug'      => 'normal',
                ],
                [
                    'name'      => __( 'Medium', 'logger' ),
                    'size'      => 30,
                    'slug'      => 'medium',
                ],
                [
                    'name'      => __( 'Large', 'logger' ),
                    'size'      => 40,
                    'slug'      => 'large',
                ],
                [
                    'name'      => __( 'Extra Large', 'logger' ),
                    'size'      => 50,
                    'slug'      => 'extra-large',
                ],
                [
                    'name'      => __( 'Extra Extra Large', 'logger' ),
                    'size'      => 60,
                    'slug'      => 'extra-extra-large',
                ],
            ]
        );
    }

    /**
     * Add block editor color classes to the Frontend
     *
     * These classes are generated on elements in the block editor.
     * - Lets make them do something.
     *
     * @param array $css Array of Kirki css vars.
     *
     * @return array $css Modified of array of Kirki css vars.
     */
    public function outputColorClassesForBlockEditor( $css )
    {
        $colors = get_theme_mod( 'logger_color_palette_repeater' );
        foreach ( $colors as $color ) {
            $cssTargetColor = '.has-theme-' . generateSlugVar( $color['logger_color_palette_color_name'] ) . '-color';
            $cssTargetBackgroundColor = '.has-theme-' . generateSlugVar( $color['logger_color_palette_color_name'] ) . '-background-color';

            $css['global'][ $cssTargetColor ]['color'] = $color['logger_color_palette_color_code'];
            $css['global'][ $cssTargetBackgroundColor ]['background-color'] = $color['logger_color_palette_color_code'];
        }
        return $css;
    }
}
