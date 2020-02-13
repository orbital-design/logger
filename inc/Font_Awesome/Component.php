<?php
/**
 * Logger\Font_Awesome\Component class
 *
 * @package Logger
 */

namespace Logger\Font_Awesome;

use Logger\Component_Interface;
use kirki;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function wp_enqueue_script;
use function get_theme_mod;
use function add_section;
use function add_field;

/**
 * Class for managing scripts & stylesheets.
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'font_awesome';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'init', [ $this, 'registerFontAwesomeCustomiserFields' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueFontAwesome' ] );
        add_filter( 'script_loader_tag',  [ $this, 'enableFontAwesomePsuedoElements'], 10, 3 );
    }

    /**
     * Add fields to customiser for Font Awesome.
     */
    public function registerFontAwesomeCustomiserFields()
    {
        Kirki::add_section(
            'logger_font_awesome_section',
            array(
                'title' => esc_attr__( 'Font Awesome', 'logger' ),
                'panel' => 'logger_theme_options',
            )
        );

        Kirki::add_field(
            'base_setting',
            array(
                'type'        => 'toggle',
                'settings'    => 'logger_toggle_font_awesome',
                'label'       => esc_attr__( 'Embed Font Awesome Pro', 'logger' ),
                'section'     => 'logger_font_awesome_section',
                'default'     => '1',
                'description' => esc_attr__( 'This theme requires Font Awesome to work, by default we add it to your site. If you already have it installed, turn ours off here.', 'logger' ),
            )
        );

        Kirki::add_field(
            'base_setting',
            [
                'type'      => 'url',
                'settings'  => 'logger_url_font_awesome',
                'label'     => esc_html__( 'Font Awesome Pro - Kit URL', 'logger' ),
                'section'   => 'logger_font_awesome_section',
                'default'   => esc_html__( 'https://kit.fontawesome.com/98dc895808.js', 'logger' ),
                'description' => esc_attr__( 'Replace our Font Awesome Kit URL with your here.', 'logger' ),
                'active_callback'  => [
                    [
                        'setting'  => 'logger_toggle_font_awesome',
                        'operator' => '==',
                        'value'    => 1,
                    ],
                ],
            ]
        );
    }

    /**
     * Enqueue Font Awesome
     *
     * @return void
     */
    public function enqueueFontAwesome()
    {
        $logger_fa_toggle = get_theme_mod( 'logger_toggle_font_awesome' );
        $logger_fa_url    = get_theme_mod( 'logger_url_font_awesome' );
        if ( 1 === $logger_fa_toggle || ! empty( $logger_fa_url ) ) {
            wp_enqueue_script( 'font-awesome', esc_url( $logger_fa_url ), array(), '5.9.8', 'true' );
        }
    }

    public function enableFontAwesomePsuedoElements( $tag, $handle, $src ) {
        $psuedo = array(
            'font-awesome',
        );
        if ( in_array( $handle, $psuedo ) ) {
            return '<script data-search-pseudo-elements src="' . $src . '" defer type="text/javascript"></script>' . "\n";
        }

        return $tag;
    }

}
