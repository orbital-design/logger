<?php
/**
 * Logger\Reading_Time\Component class
 *
 * @since 1.0.0
 *
 * @package Logger
 */

namespace Logger\Reading_Time;

use Logger\Component_Interface;
use Logger\Templating_Component_Interface;
use kirki;
use function Logger\logger;
use function add_action;
use function add_filter;
use function get_theme_mod;
use function add_section;
use function add_field;

/**
 * Class for outputting post estimated read time.
 *
 * Exposes template tags:
 * * `logger()->loggerCalculatePostReadingTime( $post_id | int )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

    /**
     * Gets the unique identifier for the theme component.
     *
     * @return string Component slug.
     */
    public function getSlug() : string
    {
        return 'reading_time';
    }

    /**
     * Adds the action and filter hooks to integrate with WordPress.
     */
    public function initialize()
    {
        add_action( 'init', [ $this, 'actionRegisterUserCookieCustomiserSettings' ] );
        add_action( 'post_updated', [ $this, 'loggerAddReadingTimePostMeta' ], 10, 3 );
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
            'getReadingTime' => [ $this, 'getReadingTime' ],
        ];
    }

    /**
     * Register Kirki Fields for Reading Time Fields
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function actionRegisterUserCookieCustomiserSettings()
    {

        // Add 'Analytics Options' Section to 'Initial' Panel
        Kirki::add_section(
            'logger_reading_time_options',
            [
                'title' => esc_attr__( 'Post/Page Reading Time', 'logger' ),
                'description' => esc_attr__( 'Here you can customise what\'s output when you echo the getReadingTime() fn inside a post/page template.', 'logger' ),
                'priority' => 80,
                'panel' => 'logger_theme_options',
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type'        => 'number',
                'settings'    => 'logger_rt_speed',
                'label'       => __( 'Reading Time - Speed (Words per Minute)', 'logger' ),
                'section'     => 'logger_reading_time_options',
                'description' => esc_attr__( 'The average reading speed of most adults is around 250 to 300 words per minute, speed readers average about 1000.', 'logger' ),
                'default'     => __( '275', 'logger' ),
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type'        => 'text',
                'settings'    => 'logger_rt_label',
                'label'       => __( 'Reading Time - Label', 'logger' ),
                'section'     => 'logger_reading_time_options',
                'description' => esc_attr__( 'This appears directly before the reading time. You can use a clock icon by using: %clock%', 'logger' ), // phpcs:ignore
                'default'     => __( '%clock%', 'logger' ), // phpcs:ignore
            ]
        );

        Kirki::add_field(
            'base_setting',
            [
                'type'        => 'text',
                'settings'    => 'logger_rt_suffix',
                'label'       => __( 'Reading Time - Suffix', 'logger' ),
                'section'     => 'logger_reading_time_options',
                'description' => esc_attr__( 'This appears directly after the reading time.', 'logger' ),
                'default'     => __( ' min read', 'logger' ),
            ]
        );
    }

    /**
     * Returns Posts approximate reading time
     *
     * @param int  $post Post ID, doesn't need supplying if inside loop.
     * @param bool $strip_output Defaults to false
     * - false outputs value surrounded by label and suffix with html tags.
     * - true just returns the value.
     *
     * @return $output can return as int or str depending on $strip_output
     */
    public static function getReadingTime( int $post = null, bool $strip_output = false )
    {
        $_post = get_post( $post );
        $post_ID = $_post->ID;

        if ( null == $post_ID ) {
            return;
        }

        $reading_time_options = [
            'time'   => ( ! empty( get_post_meta( $post_ID, 'logger_reading_time' ) ) ) ? get_post_meta( $post_ID, 'logger_reading_time' ) : logger()->loggerCalculatePostReadingTime( $post_ID ),
            'label'  => ( ! empty( get_theme_mod( 'logger_rt_label' ) ) ) ? get_theme_mod( 'logger_rt_label' ) : '%clock%',
            'suffix' => ( ! empty( get_theme_mod( 'logger_rt_suffix' ) ) ) ? get_theme_mod( 'logger_rt_suffix' ) : 'min read',
        ];

        if ( true === $strip_output ) {
            $output = esc_html( $reading_time_options['time'][0] );
        } else {
            $output = sprintf(
                '<span class="[ reading-time ]"><span class="[ reading-time__label ]">%1$s</span><span class="[ reading-time__value ]">%2$s</span><span class="[ reading-time__suffix ]">%3$s</span></span>',
                esc_html( $reading_time_options['label'] ),
                esc_html( $reading_time_options['time'][0] ),
                esc_html( $reading_time_options['suffix'] )
            );

            $output = str_replace( '%clock%', '<i class="fal fa-stopwatch"></i>', $output );
        }

        return $output;
    }

    /**
     * Add Post Reading Time to post meta
     * - Updates postmeta if content is updated.
     *
     * @param int    $post_ID          ID of post being updated.
     * @param string $post_after    Content After Update.
     * @param [type] $post_before   Content Before Update.
     *
     * @return void
     */
    public function loggerAddReadingTimePostMeta( $post_ID, $post_after, $post_before )
    {

        if ( $post_after->post_content !== $post_before->post_content ) {
             write_log( 'function callsd' );
            $post_reading_time = $this->loggerCalculatePostReadingTime( $post_ID );
            write_log( $post_reading_time );
            if ( ! add_post_meta( $post_ID, 'logger_reading_time', $post_reading_time, true ) ) {
                update_post_meta( $post_ID, 'logger_reading_time', $post_reading_time );
            }
        }
    }

    /**
     * Calculate the reading time of a given post.
     *
     * Retrieves post content -> counts images, strips shortcodes & tags.
     *  - Counts words
     *  - Converts images to a word count.
     *  - And outputs total reading time in mins.
     *
     * @param int $post_id The Post ID.
     *
     * @return string|int Total reading time for the post.
     */
    public function loggerCalculatePostReadingTime( int $post_id )
    {
        $wpm = ( ! empty( get_theme_mod( 'logger_rt_speed' ) ) ) ? get_theme_mod( 'logger_rt_speed' ) : 275;

        $content    = trim( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );   // 1. Get post Content Field & Strip any shortcodes.
        $img_count  = substr_count( strtolower( $content ), '<img ' );                          // 2. Calculate amount of images in the post.
        $content    = wp_strip_all_tags( $content );                                            // 3. Strip any HTML Tags so we're left with a simple string.
        $word_count = str_word_count( $content );                                               // 4. Calculate Word Count from our simple string.

        $words_per_seconds = ( $word_count / $wpm ) * 60;

        $i = 12;
        foreach ( range( 1, $img_count ) as $number ) {
            $words_per_seconds = $words_per_seconds + $i;

            if ( $i > 3 ) {
                $i--;
            }
        }

        $words_per_minute = $words_per_seconds / 60;
        if ( $words_per_minute < 1 ) {
            $words_per_minute = 1;
        }

        $reading_time = round( $words_per_minute );

        return $reading_time;
    }
}
