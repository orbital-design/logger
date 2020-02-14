<?php
/**
 * Logger\Minify_Site\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */


namespace Logger\Minify_Site;

use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use function Logger\logger;
use function is_admin;

require_once get_template_directory() . '/inc/Tidy_Html_Minfier/TidyMinify.php';
use TinyMinify;

/**
 * Class for W3C Fix and HTML minifier
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
        return 'minify_site';
    }

     /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize() {}

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
            'loggerCompressionBufferStart'  => [ $this, 'loggerCompressionBufferStart' ],
            'loggerCompressionBufferFinish'         => [ $this, 'loggerCompressionBufferFinish' ]
        ];
    }

    /**
     * Start Page Output.
     *
     * @return void
     */
    public function loggerCompressionBufferStart()
    {
        ob_start();
    }

    /**
     * Finish Page output.
     */
    public function loggerCompressionBufferFinish()
    {
       $page_buffer = ob_get_contents();

       $page_buffer = TinyMinify::html($page_buffer, $options = [
            'collapse_whitespace' => true,
            'disable_comments' => false,
        ]);

        ob_end_clean();

        echo $page_buffer;
    }
}