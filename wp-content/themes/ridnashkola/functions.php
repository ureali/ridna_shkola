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
?>

