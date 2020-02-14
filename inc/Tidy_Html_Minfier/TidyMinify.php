<?php

// TODO: use namespace:
// namespace Minifier;
// use Minifier\TinyHtmlMinifier;

require_once get_template_directory() . '/inc/Tidy_Html_Minfier/TinyHtmlMinifier.php';

class TinyMinify
{
    public static function html(string $html, array $options = []) : string
    {
        $minifier = new TinyHtmlMinifier($options);
        return $minifier->minify($html);
    }
}