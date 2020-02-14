<?php
/**
 * Logger\Timber_Config\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */


namespace Logger\Timber_Config;

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
        return 'timber_config';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize() {
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