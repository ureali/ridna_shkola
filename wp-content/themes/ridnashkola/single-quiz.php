<?php
/**
 * The template for displaying all single posts
 *
 */
get_header();
?>
    <div class="content clearfix">

        <div class="container">

            <div id="primary" class="content-area primary-section-quiz">
                <main id="main" class="site-main">
                    <section class="ed-blog">
                        <div class="wrap">
                            <div class="ed-blog-wrap layout-2">
                            <?php
                            if ( have_posts() ) :?>

                                <!-- Start the Loop. -->
                                <?php while ( have_posts() ) :
                                    the_post();

                                    get_template_part( 'template-parts/post/content', 'single' );
                                    ?>
                                   <div class="quiz-title">
                                       <?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
                                   </div>
                                <?php
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

                                the_post_navigation();
                            else :

                                get_template_part( 'template-parts/post/content', 'none' );

                            endif;
                            ?>
                            </div>
                        </div>
                    </section>
                </main><!-- #main -->
            </div><!-- #primary -->

        </div>
    </div>

<?php get_footer();
