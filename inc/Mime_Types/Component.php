<?php
/**
 * Logger\Mime_Types\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Mime_Types;

use Logger\Component_Interface;
use function add_filter;

/**
 * Class for adding basic theme support for more mime types.
 *
 * Adds actions to following Hooks:
 * * `upload_mimes`
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'mime_types';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_filter( 'upload_mimes', [ $this, 'addSvgSupport' ] );
    }

    /**
     * SVG Support
     *
     * Adds SVG to the mime types supported (useful for gallery uploads in the WP Backend).
     */
    public function addSvgSupport($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }
}
