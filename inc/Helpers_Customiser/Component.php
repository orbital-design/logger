<?php
/**
 * Logger\Helpers_Customiser\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Customiser;

use Logger\Component_Interface;
use kirki;
use function add_action;
use function get_theme_mod;
use function add_section;
use function add_field;

/**
 * Class for Cleaning and setting up Customiser
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
        return 'helpers_customiser';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'customize_register', [ $this, 'cleanUpCustomiser' ], 20, 1 );
        add_action( 'init', [ $this, 'actionRegisterDefaultCustomiserSettings' ] );
    }

    /**
     * Edit or remove WP Core Customiser Panels, Sections & Controls.
     *
     * @param object $wp_customize Custimer Object.
     */
    public function cleanUpCustomiser( $wp_customize )
    {
        // Set site name and description to be previewed in real-time.
        $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
        $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
        // Move section to order - 1
        $wp_customize->get_section( 'title_tagline' )->priority = 10;
        // Move section to order - 3
        $wp_customize->get_panel( 'nav_menus' )->priority = 30;
        $wp_customize->get_panel( 'nav_menus' )->title = esc_attr__( 'Menu Options', 'logger' );

        // Move into Theme Options & rename
        $wp_customize->get_section( 'colors' )->panel = 'logger_theme_options';
        $wp_customize->get_section( 'colors' )->title = esc_attr__( 'Colour Options', 'logger' );
        $wp_customize->get_section( 'colors' )->priority = 10;
        ;
        // We don't need this in the customiser.
        $wp_customize->remove_section( 'static_front_page' );
        // Remove Site Icon/Favicon - this will be controlled via a Plugin.
        $wp_customize->remove_control( 'site_icon' );
        // Remove 'Custom CSS' Section - we want to limit user customisation.
        $wp_customize->remove_section( 'custom_css' );
        // Site doesn't require bg images
        $wp_customize->remove_section( 'background_image' );
        // Edit the custom logo control.
        $wp_customize->get_control( 'custom_logo' )->label       = esc_attr__( 'Site Logo', 'logger' );
        $wp_customize->get_control( 'custom_logo' )->description = esc_attr__( 'Please upload your websites logo here, it can be any common image format such as jpg, jpeg, png or svg etc.', 'logger' );
    }

    /**
     * Register Kirki Fields for Analytics Fields
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function actionRegisterDefaultCustomiserSettings()
    {
        // Add a base config for all fields
        Kirki::add_config(
            'base_setting',
            [
                'capability' => 'edit_theme_options',
                'option_type' => 'theme_mod',
                'gutenberg_support' => true,
            ]
        );

        // Add 'Analytics Options' Section to 'Initial' Panel
        Kirki::add_panel(
            'logger_theme_options',
            [
                'title' => esc_attr__( 'Theme Options', 'logger' ),
                'priority' => 20,
            ]
        );

        // Add 'Site color Palette' Panel to 'Editor Options' Panel
        Kirki::add_field(
            'base_setting',
            [
                'type' => 'repeater',
                'label' => esc_attr__( 'Site Colour Palette', 'logger' ),
                'section' => 'colors',
                'row_label' => [
                    'type' => 'text',
                    'value' => esc_attr__( 'Colour', 'logger' ),
                ],
                'button_label' => esc_attr__( 'Add a colour to your palette', 'logger' ),
                'settings' => 'logger_color_palette_repeater',
                'fields' => [
                    'logger_color_palette_color_name' => [
                        'type' => 'text',
                        'label' => esc_attr__( 'colour Name', 'logger' ),
                        'description' => esc_attr__( 'This will be added to the post editors color palette', 'logger' ),
                        'default' => 'Orange',
                    ],
                    'logger_color_palette_color_code' => [
                        'type' => 'color',
                        'label' => esc_attr__( 'colour Value', 'logger' ),
                        'description' => esc_attr__( 'This will be added to the post editors color palette', 'logger' ),
                        'default' => '#FF6500',
                    ],
                ],
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type'     => 'text',
                'settings' => 'logger_copyright_statement',
                'label'    => esc_html__( 'Copyright Statement', 'logger' ),
                'section'  => 'title_tagline',
                'default'  => esc_html( '%copy% %year% %sitename%', 'logger' ),                                               // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
                'description' => esc_attr( '%copy% = &copy; | %year% = Current Year | %sitename% = Site Title', 'logger' ),   // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
            ]
        );
    }
}
