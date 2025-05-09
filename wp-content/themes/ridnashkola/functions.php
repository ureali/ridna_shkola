<?php
function educenter_child_enqueue_styles() {
    wp_enqueue_style('educenter-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('educenter-child-style', get_stylesheet_directory_uri() . '/style.css', array('educenter-parent-style'), wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'educenter_child_enqueue_styles');

/**
 * Main header area
 */
if ( ! function_exists( 'educenter_main_header' ) ) {

    function educenter_main_header() { ?>

        <div class="bottom-header clearfix">
            <div class="container">
                <div class="header-middle-inner">
                    <div class="site-branding logo">

<!--                        --><?php //the_custom_logo(); ?>

                        <div class="brandinglogo-wrap">
                            <h1 class="site-title">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                                    <?php bloginfo( 'name' ); ?>
                                </a>
                            </h1>
                            <?php
                            $description = get_bloginfo( 'description', 'display' );
                            if ( $description || is_customize_preview() ) : ?>
                                <p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
                            <?php endif;  ?>
                        </div>

                        <button class="header-nav-toggle" data-toggle-target=".header-mobile-menu"  data-toggle-body-class="showing-menu-modal" aria-expanded="false" data-set-focus=".close-nav-toggle">
                            <div class="one"></div>
                            <div class="two"></div>
                            <div class="three"></div>
                        </button><!-- Mobile navbar toggler -->

                    </div><!-- .site-branding -->

                    <div class="box-header-nav main-menu-wapper">
                        <?php
                        wp_nav_menu( array(
                                'theme_location'  => 'menu-1',
                                'menu'            => 'primary-menu',
                                'container'       => '',
                                'container_class' => '',
                                'container_id'    => '',
                                'menu_class'      => 'main-menu',
                            )
                        );
                        ?>
                    </div>

                    <?php do_action('educenter_nav_buttons'); ?>
                </div>
            </div>
        </div>

        <?php
    }
}


add_action( 'educenter_header', 'educenter_main_header', 20 );

/**
 * Footer
 */

if ( ! function_exists( 'educenter_button_footer_before' ) ) {

    function educenter_button_footer_before(){ ?>

        <div class="bottom-footer clearfix">

            <div class="container">

                <div class="footer-bottom-left">

                    <p><?php  echo esc_html($content = esc_html__('Copyright  &copy; ','educenter') . date( 'Y' ) . ' - ' . get_bloginfo( 'name' )); ?></p>

                </div>

            </div>

        </div>

        <?php
    }
}
add_action( 'educenter_button_footer', 'educenter_button_footer_before', 15 );

function register_footer_menu() {
    register_nav_menus( array(
        'footer-menu' => esc_html__( 'Footer Menu (Ridna Shkola, Please Use This)', 'educenter' ),
    ) );
}
add_action( 'after_setup_theme', 'register_footer_menu' );


/**
 * Add Wordwall as an oEmbed provider.
 */
function my_theme_add_wordwall_oembed_provider()
{
    wp_oembed_add_provider(
        '#https?://(www\.)?wordwall\.net/resource/.*#i',
        'https://wordwall.net/api/oembed',
        true
    );

    // Optional: Register provider for play URLs if needed
    wp_oembed_add_provider(
        '#https?://(www\.)?wordwall\.net/play/.*#i',
        'https://wordwall.net/api/oembed',
        true
    );
}

add_action('init', 'my_theme_add_wordwall_oembed_provider');


if ( ! function_exists( 'educenter_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function educenter_posted_on( $author = 'enable', $post_date = 'enable') {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
        esc_attr( get_the_date( 'c' ) ),
        esc_html( get_the_date() ),
        esc_attr( get_the_modified_date( 'c' ) ),
        esc_html( get_the_modified_date() )
    );

    $author_link_html = '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>';

    if ( get_locale() === 'uk' ) {
        $byline_format = 'Автор: %s';
        $byline = sprintf( $byline_format, $author_link_html );

        $posted_on = sprintf(
        /* translators: %s: post date. */
            esc_html_x( 'Опубліковано: %s', 'post date', 'educenter' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

    } else {
        $byline_format = esc_html_x( 'By %s', 'post author', 'educenter' );
        $byline = sprintf( $byline_format, $author_link_html );

        $posted_on = sprintf(
        /* translators: %s: post date. */
            esc_html_x( 'On %s', 'post date', 'educenter' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

    }

    if( $author == 'enable' ){
        echo '<div class="ed-author">
					<span class="byline"> ' . $byline . '</span>
				</div>'; // WPCS: XSS OK - $byline contains pre-escaped HTML via esc_url/esc_html within $author_link_html.
    }

    if( $post_date == 'enable' ){
        echo '<div class="ed-date"><span class="posted-on">' . $posted_on . '</span></div>'; // WPCS: XSS OK - $posted_on contains pre-escaped HTML.
    }
}
endif;

function educenter_add_breadcrumb() {

    // Bail if home page.
    if ( is_front_page() || is_home() ) {
        return;
    }  ?>

    <div class="ed-breadcrumb">
        <div class="ed-overlay"></div>
        <div class="container">
            <div class="breadcrumb-list">
                <h2 class="ed-header-title">
                    <?php
                    if ( is_category() || is_archive() ) {
                        the_archive_title( '<span>', '</span>' );

                    } elseif ( is_search() ) {

                        /* translators: %s: search query. */
                        if ( get_locale() === 'uk' ) {
                            printf(
                                'Результати пошуку за запитом: %s',
                                '<span>' . esc_html( get_search_query() ) . '</span>'
                            );
                        } else {
                            printf(
                                esc_html__( 'Search Results for: %s', 'educenter' ),
                                '<span>' . esc_html( get_search_query() ) . '</span>'
                            );
                        }

                    } elseif ( is_404() ) {

                        if ( get_locale() === 'uk' ) {
                            echo 'Сторінка помилки';
                        } else {
                            esc_html_e( 'Error Page', 'educenter' );
                        }

                    } else {

                        the_title();

                    }
                    ?>
                </h2>
                <div id="breadcrumb" class="bread-list">
                    <?php educenter_breadcrumb(); ?>
                </div>

            </div>
        </div>
    </div>

    <?php
}
add_action( 'educenter_add_breadcrumb', 'educenter_add_breadcrumb', 10 );
function my_breadcrumb_trail_labels_uk( $labels ) {
    if ( get_locale() === 'uk' ) {
        $labels['home']      = 'Головна';                                      // Home
        $labels['browse']    = 'Перегляд:';                                    // Browse:
        $labels['search']    = 'Результати пошуку за запитом: %s';              // Search results for: %s
        $labels['error_404'] = '404 Не знайдено';                               // 404 Not Found
    }
    return $labels;
}
add_filter( 'breadcrumb_trail_labels', 'my_breadcrumb_trail_labels_uk' );

/**
 * Translate default block‑widget titles into Ukrainian.
 */
function educenter_translate_widget_headings_uk( $widget_block ) {
    if ( get_locale() !== 'uk' || ! is_array( $widget_block ) ) {
        return $widget_block;
    }

    // Map English → Ukrainian labels:
    $translations = array(
        'Recent Posts'    => 'Останні записи',
        'Recent Comments' => 'Останні коментарі',
        'Archives'        => 'Архіви',
        'Categories'      => 'Категорії',
    );

    foreach ( $widget_block as $id => $widget ) {
        if ( isset( $widget['content'] ) ) {
            foreach ( $translations as $en => $uk ) {
                $widget['content'] = str_replace(
                    "<!-- wp:heading --><h2>{$en}</h2><!-- /wp:heading -->",
                    "<!-- wp:heading --><h2>{$uk}</h2><!-- /wp:heading -->",
                    $widget['content']
                );
            }
            $widget_block[ $id ]['content'] = $widget['content'];
        }
    }

    return $widget_block;
}
add_filter( 'option_widget_block', 'educenter_translate_widget_headings_uk' );
