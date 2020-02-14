<?php
/**
 * Logger\Social_Media\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Social_Media;

use Logger\logger;
use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use kirki;
use function add_action;
use function get_theme_mod;
use function add_section;
use function add_field;

/**
 * Class for integrating Social Media Icons.
 *
 * * Exposes template tags:
 * * `Logger()->outputSocialMediaIcons( string $socialClassName )`
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface, Templating_Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'social_media';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'init', [ $this, 'actionRegisterSocialMediaCustomiserSettings' ] );
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
            'outputSocialMediaIcons' => [ $this, 'outputSocialMediaIcons' ],
        ];
    }

    /**
     * Gets the data from the Social Media Panel of the Customiser & outpts as a list.
     *
     * @param string $socialClassName Passes through the class name to add to the ul tag.
     */
    public function outputSocialMediaIcons( string $socialClassName )
    {
        $loggerSocialToggle = get_theme_mod( 'logger_toggle_social_icons' );
        // Check 'social icons' toggle state inside customiser & display/hide based on state.
        if ( true == $loggerSocialToggle ) {
            // Pass Class name to template part.
            set_query_var( 'socialClassName', $socialClassName );
            $output = include( get_template_directory() . '/components/reusables/social-icons.php' );
            $allowed_html = $this->escapeSocialIconsOutput();

            echo wp_kses( $output, $allowed_html );
        }


        return;
    }

    /**
     * Register Kirki Fields for Social Media
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function actionRegisterSocialMediaCustomiserSettings()
    {
        Kirki::add_section(
            'logger_section_socials',
            [
                'title' => esc_attr__( 'Social Media Icons', 'logger' ),
                'panel' => 'logger_theme_options',
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'        => 'toggle',
                'settings'    => 'logger_toggle_social_icons',
                'label'       => esc_attr__( 'Hide/Show Social Media Icons', 'logger' ),
                'section'     => 'logger_section_socials',
                'default'     => '1',
                'description' => esc_attr__( 'Even if you choose to show the icons, they will still only display if the url field for a network is filled out below.', 'logger' ),
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'select',
                'settings'        => 'logger_select_icon_type',
                'label'           => __( 'Icon Type', 'logger' ),
                'section'         => 'logger_section_socials',
                'default'         => 'ring',
                'multiple'        => 0,
                'choices'         => [
                    'standalone'  => esc_attr__( 'Standalone', 'logger' ),
                    'circle'      => esc_attr__( 'Circle', 'logger' ),
                    'ring'        => esc_attr__( 'Ring', 'logger' ),
                    'square-full' => esc_attr__( 'Square', 'logger' ),
                    'square'      => esc_attr__( 'Rounded Square', 'logger' ),
                ],
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'email',
                'settings'        => 'logger_url_email',
                'label'           => esc_attr__( 'Email Address', 'logger' ),
                'section'         => 'logger_section_socials',
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'url',
                'settings'        => 'logger_url_facebook',
                'label'           => esc_attr__( 'Facebook URL', 'logger' ),
                'section'         => 'logger_section_socials',
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'url',
                'settings'        => 'logger_url_twitter',
                'label'           => esc_attr__( 'Twitter URL', 'logger' ),
                'section'         => 'logger_section_socials',
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'url',
                'settings'        => 'logger_url_instagram',
                'label'           => esc_attr__( 'Instagram Profile URL', 'logger' ),
                'section'         => 'logger_section_socials',
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'url',
                'settings'        => 'logger_url_youtube',
                'label'           => esc_attr__( 'Youtube Channel URL', 'logger' ),
                'section'         => 'logger_section_socials',
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'url',
                'settings'        => 'logger_url_linkedin',
                'label'           => esc_attr__( 'LinkedIn  URL', 'logger' ),
                'section'         => 'logger_section_socials',
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
        Kirki::add_field(
            'base_setting',
            [
                'type'            => 'url',
                'settings'        => 'logger_url_tripadvisor',
                'label'           => esc_attr__( 'Trip Advisor Profile URL', 'logger' ),
                'section'         => 'logger_section_socials',
                'active_callback' => [
                    [
                        'setting'  => 'logger_toggle_social_icons',
                        'operator' => '==',
                        'value'    => '1',
                    ],
                ],
            ]
        );
    }

    /**
     * Escape Social Media Icons Output
     *
     * @return $html Modified HTML
     */
    public function escapeSocialIconsOutput()
    {

        return array(
            'ul' => array(
                'class' => true,
            ),
            'li' => array(),
            'a' => array(
                'href' => true,
                'class' => true,
                'title' => true,
                'target' => true,
            ),
            'span' => array(
                'class' => true,
            ),
            'i' => array(
                'class' => true,
                'data-fa-transform' => true,
            ),
        );
    }
}
