<?php
/**
 * Logger\Page_Compression\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */


namespace Logger\Page_Compression;

use Logger\Component_Interface;
use function Logger\logger;
use function is_admin;
use function add_action;

require_once get_template_directory() . '/inc/Markup_Compression/TidyMinify.php';
use TinyMinify;

/**
 * Class for compressing page output
 *
 * Adds actions to following Hooks:
 * * `before_template_render`
 * * `after_template_render`
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'page_compression';
    }

     /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize() {
        add_action( 'before_template_render', [ $this, 'loggerCompressionBufferStart' ] );
        add_action( 'after_template_render', [ $this, 'loggerCompressionBufferFinish' ] );

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