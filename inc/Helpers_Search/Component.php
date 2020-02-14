<?php
/**
 * Logger\Helpers_Search\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Helpers_Search;

use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use function Logger\logger;
use function add_action;
use function add_filter;
use function get_theme_mod;
use function add_section;
use function add_field;

/**
 * Class for managing WordPress Searches.
 */
class Component implements Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'setup_search';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'get_search_form', [ $this, 'registerSearchForm' ] );
        // add_action( 'template_redirect', [ $this, 'changeSearchUrl' ] );
    }

    /**
     * Generate custom search form
     *
     * @param string $form Form HTML.
     * @return string Modified form HTML.
     */
    public function registerSearchForm( $form )
    {
        $form  = '<form class="[ search-form ]" role="search" method="get" action="' . home_url( '/' ) . '">';
        $form .= '<div class="[ search-form__wrap ]">';
        $form .= '<input class="[ search-form__field ]" type="search" name="s" title="' . _x( 'Search...', 'label', 'logger' ) . '" placeholder="' . _x( 'Search...', 'label', 'logger' ) . '" />';
        // $form .= '<input type="hidden" value="post_type_key" name="post_type" />';
        $form .= '<label class="[ search-form__label ]">' . _x( 'search...', 'label', 'logger' ) . '</label>';
        $form .= '</div>';
        $form .= '<button class="[ search-form__button ]" type="submit"><i class="fal fa-search"></i></button>';
        $form .= '</form>';

        return $form;
    }

    /**
     * Change search page slug.
     */

    public function changeSearchUrl()
    {
        if ( is_search() && ! empty( $_GET['s'] ) ) {
            wp_redirect( home_url( '/search?' ) . urlencode( get_query_var( 's' ) ) );
            exit();
        }
    }

}
