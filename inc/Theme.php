<?php
/**
 * Logger\Theme class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger;

use InvalidArgumentException;

/**
 * Main class for the theme.
 *
 * This class takes care of initializing theme features and available template tags.
 */
class Theme {

    /**
     * Associative array of theme components, keyed by their slug.
     *
     * @var array
     */
    protected $components = [];

    /**
     * The template tags instance, providing access to all available template tags.
     *
     * @var Template_Tags
     */
    protected $template_tags;

    /**
     * Constructor.
     *
     * Sets the theme components.
     *
     * @param array $components Optional. List of theme components. Only intended for custom initialization, typically
     *                          the theme components are declared by the theme itself. Each theme component must
     *                          implement the Component_Interface interface.
     *
     * @throws InvalidArgumentException Thrown if one of the $components does not implement Component_Interface.
     */
    public function __construct( array $components = [] )
    {
        if ( empty( $components ) ) {
            $components = $this->getDefaultComponents();
        }

        // Set the components.
        foreach ( $components as $component ) {
            // Bail if a component is invalid.
            if ( ! $component instanceof Component_Interface ) {
                throw new InvalidArgumentException(
                    sprintf(
                        /* translators: 1: classname/type of the variable, 2: interface name */
                        __( 'The theme component %1$s does not implement the %2$s interface.', 'logger' ),
                        gettype( $component ),
                        Component_Interface::class
                    )
                );
            }

            $this->components[ $component->getSlug() ] = $component;
        }

        // Instantiate the template tags instance for all theme templating components.
        $this->template_tags = new Template_Tags(
            array_filter(
                $this->components,
                function ( Component_Interface $component ) {
                    return $component instanceof Templating_Component_Interface;
                }
            )
        );
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     *
     * This method must only be called once in the request lifecycle.
     */
    public function initialize()
    {
        array_walk(
            $this->components,
            function ( Component_Interface $component ) {
                $component->initialize();
            }
        );
    }

    /**
     * Retrieves the template tags instance, the entry point exposing template tag methods.
     *
     * Calling `logger()` is a short-hand for calling this method on the main theme instance. The instance then allows
     * for actual template tag methods to be called. For example, if there is a template tag called `posted_on`, it can
     * be accessed via `logger()->posted_on()`.
     *
     * @return Template_Tags Template tags instance.
     */
    public function templateTags() : Template_Tags // phpcs:ignore
    {

        return $this->template_tags;
    }

    /**
     * Retrieves the component for a given slug.
     *
     * This should typically not be used from outside of the theme classes infrastructure.
     *
     * @param string $slug Slug identifying the component.
     * @return Component_Interface Component for the slug.
     *
     * @throws InvalidArgumentException Thrown when no theme component with the given slug exists.
     */
    public function component( string $slug ) : Component_Interface
    {
        if ( ! isset( $this->components[ $slug ] ) ) {
            throw new InvalidArgumentException(
                sprintf(
                    /* translators: %s: slug */
                    __( 'No theme component with the slug %s exists.', 'logger' ),
                    $slug
                )
            );
        }

        return $this->components[ $slug ];
    }

    /**
     * Gets the default theme components.
     *
     * This method is called if no components are passed to the constructor, which is the common scenario.
     *
     * @return array List of theme components to use by default.
     */
    protected function getDefaultComponents() : array
    {
        $components = [
            /* Timber & Twig Extensions */
            new Helpers_Timber\Component(),

            /* Site & Theme Setup */
            new Helpers_Theme\Component(),
            new Clean_Theme_Head\Component(),
            new WP_Emoji\Component(),
            new Mime_Types\Component(),
            new Enqueue_Assets\Component(),
            new Social_Media\Component(),

            /* Admin Setup & Modfications */
            new Helpers_Admin\Component(),
            new Helpers_Login\Component(),

            /* Third Party Integrations */
            new Analytics_Tools\Component(),
            new Google_Fonts\Component(),
            new Font_Awesome\Component(),

            /* Plugins and Plugin Extenions */
            // new Helpers_Acf\Component(),         // Comment this out if you are nor using Advanced Custom Fields
            // new Helpers_Ninja_Forms\Component(), // Comment this out if you are nor using Ninja Forms

            /* Logger Custom Features */
            new Reading_Time\Component(),
            new Page_Compression\Component(),


            // new Setup_Login\Component(),
            // new Setup_Custom_Logo\Component(),
            // new Setup_Customiser\Component(),
            // new Setup_Menus\Component(),
            // new Setup_Editor\Component(),
            // new Setup_Search\Component(),

            // // new Image_Sizes\Component(),
            // // new Sidebars\Component(),
            // // new Post_Thumbnails\Component(),
        ];

        return $components;
    }
}
