<?php
/**
 * Logger\Helpers_Timber\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */


namespace Logger\Helpers_Timber;

use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use Timber\Twig_Function;
use function Logger\logger;
use function add_filter;
use function add_action;
use function is_admin;

/**
 * Class for adding to and extending TimberWP and Twig
 *
 * Exposes template tags:
 * * `logger()->before_template_render()`
 * * `logger()->after_template_render()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'helpers_timber';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize() {
        add_filter('timber_context', [ $this, 'constructGlobalContext' ] );
        // add_filter( 'timber/twig', [ $this, 'addCompressionHooksToTwig' ] );
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
            'before_template_render'  => [ $this, 'before_template_render' ],
            'after_template_render'         => [ $this, 'after_template_render' ]
        ];
    }

    public function constructGlobalContext($context){

        $context['branding'] = $this->constructBrandingContext($context);

        return $context;
    }

    public function constructBrandingContext( $context ) {

        $branding = [];

        $allowed_html = logger()->loggerBrandingLogoLinkEscape();

        $site_name = $context['site']->title;
        $site_url  = $context['site']->home_url;

        $site_logo_id = get_theme_mod( 'custom_logo' );

        if ( empty( $site_logo_id ) ) {
            return $site_name;
        }

        $site_logo_url = wp_get_attachment_image_src( $site_logo_id )[0];
        $site_logo_ext = pathinfo( $site_logo_url, PATHINFO_EXTENSION );

        if ( 'svg' === $site_logo_ext && isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
            $file = wp_get_attachment_image_url( $site_logo_id );
            $svg_file = str_replace( [get_site_url(), '../'], ['', ''], $file );

            $branding['type'] = 'svg';
            $branding['src']  = wp_kses( file_get_contents( sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) . $svg_file ), $allowed_html );
        } else {
            $branding['type'] = 'img';
            $branding['src'] = new Image($site_logo_id);
        }


        return $branding;
    }

    /**
     * My custom Twig functionality.
     *
     * @param \Twig\Environment $twig
     * @return \Twig\Environment
     */
    public function addCustomHooksToTwig( $twig ) {

        // Adding a function.
        $twig->addFunction( new Twig_Function( 'before_template_render', [ $this, 'before_template_render' ] ) );
        $twig->addFunction( new Twig_Function( 'after_template_render', [ $this, 'after_template_render' ] ) );

        return $twig;
    }

    /**
     * Custom hook for 'before_template_render'
     *
     * Use it template .php files before Timber::render
     * logger()->before_template_render();
     *
     * @return void
     */
    public function before_template_render() {
        do_action('before_template_render');
    }

    /**
     * Custom hook for 'after_template_render'
     *
     * Use it template .php files after Timber::render
     * logger()->after_template_render();
     *
     * @return void
     */
    public function after_template_render() {
        do_action('after_template_render');
    }
}