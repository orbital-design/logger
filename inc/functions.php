<?php
/**
 * The `logger()` function.
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger;

/**
 * Provides access to all available template tags of the theme.
 *
 * When called for the first time, the function will initialize the theme.
 *
 * @return Template_Tags Template tags instance exposing template tag methods.
 */
function logger() : Template_Tags
{
    static $theme = null;

    if ( null === $theme ) {
        $theme = new Theme();
        $theme->initialize();
    }

    return $theme->templateTags();
}
