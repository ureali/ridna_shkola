<?php
/**
 * The template for displaying all single posts
 *
 */
get_header();
?>

    <div class="wrap">
        <div class="content-area">
            <main id="main" class="site-main">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8">
                            <?php
                            if ( have_posts() ) :?>

                                <!-- Start the Loop. -->
                                <?php while ( have_posts() ) :
                                    the_post();

                                    get_template_part( 'template-parts/post/content', 'single' );

                                    // get wordwall url
                                    $wordwall_url = get_post_meta( get_the_ID(), 'wordwall_link', true );

                                    if ( ! empty( $wordwall_url ) ) :

                                        // using wp way to get oembed
                                        $embed_code = wp_oembed_get( $wordwall_url );

                                        if ( $embed_code ) {
                                            echo '<div class="wordwall-embed-container wp-block-embed__wrapper">';
                                            echo $embed_code;
                                            echo '</div>';
                                        } else {
                                            echo '<p class="wordwall-error">' . esc_html__( 'Could not embed the Wordwall activity.', 'kidsworld' ) . '</p>';
                                        }

                                    else :
                                        echo '<p class="wordwall-notice">' . esc_html__( 'No Wordwall link provided for this quiz.', 'kidsworld' ) . '</p>';
                                    endif;
                                endwhile;?>
                                <?php

                                the_post_navigation(
                                    array(
                                        /* translators: Hidden accessibility text. */
                                        'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'kidsworld' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'kidsworld' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>%title</span>',
                                        /* translators: Hidden accessibility text. */
                                        'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'kidsworld' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'kidsworld' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper"><i class="fa fa-arrow-right" aria-hidden="true"></i></span></span>',
                                    )
                                );

                            else :

                                get_template_part( 'template-parts/post/content', 'none' );

                            endif;
                            ?>
                        </div>
                    </div><!-- .row -->
                </div><!-- .container -->
            </main><!-- #main -->
        </div><!-- .content -->
    </div><!-- .wrap -->
<?php
get_footer();
