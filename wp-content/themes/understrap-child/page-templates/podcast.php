<?php /* Template Name: Podcast Page */ ?>
<?php
get_header();
$container   = get_theme_mod( 'understrap_container_type' );
$featuredImage = wp_get_attachment_url(get_post_thumbnail_id($post->ID, 'large'));
$obj = get_post_type_object('mom-moments-episode');
$podcastSlug = types_render_field( "page-podcast-slug", array( "output" => "raw") );
// $args = array(
//     'post_type'        => 'podcast',
//    'posts_per_page'   => 25,
//    'orderby' => 'post_date',
//   'order' => 'ASC'
//    );
// $query = new WP_Query( $args );

?>

<div class="wrapper" id="single-wrapper">
    <!-- Page header -->
    <div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">
        <div class="podcast-intro">
                <div class="podcast-intro__post-image-wrapper">
                    <img class="podcast-intro__post-image" src="<?php echo $featuredImage;?>" alt="Featured image" />
                </div>
                <div class="podcast-intro__post-meta">
                    <p class="podcast-intro__subtitle">Podcast</p>
                    <h1 class="podcast-intro__title"><?php the_title(); ?></h1>
                    <div class="podcast-intro__description-wrapper">
                        <p class="podcast-intro__description"><?php echo  types_render_field( "podcast-description", array( "output" => "raw"));?></p>
                    </div>
                </div>
            </div>
    <!-- End page header -->
    <section class="page-section">
        <div class="page-section__content-wrapper">
            <div class="row">
                <div class="page-section__two-third-column text-center">
                <?php
                    if (have_posts()) {
                        while (have_posts()) {
                            the_post();
                            ?>
                            <p><?php the_content(); ?></p> 
                        <?php 
                    } // end while
                    }
                    ?>
                    <?php 
                        $args = array(
                        'post_type'        => $podcastSlug,
                        'posts_per_page'   => 25,
                        'orderby' => 'post_date',
                        'order' => 'ASC'
                        );
                        $query = new WP_Query( $args );
                    ?>
                    <?php while ( $query->have_posts() ) : the_post(); ?>
                        <?php $query->the_post(); ?>
                        <div class="podcast-summary">
                            <div class="podcast-summary__meta">
                                <div class="podcast-summary__date">
                                    <?php echo get_the_date(); ?>
                                </div>
                                <h2 class="podcast-summary__title"><?php the_title(); ?></h2>
                                <p class="podcast-summary__description"><?php the_content(); ?></p>
                            </div>  
                        </div>                      
                        
                    <?php endwhile; // end of the loop. ?>
                </div>
                <div class="page-section__one-third-column" style="margin-top:6rem;">
                    <div class="sidebar-cta-card">
                        <h2 class="sidebar-cta-card__heading">Want Dannah to speak at your event?</h2>
                        <p class="sidebar-cta-card__text">Dannah delivers meaty biblical truth in a transparent, conversational style that brings her audience into the presentation. Your audience will laugh. They may cry. They will love the research-based, user-friendly information on popular topics such as biblical womanhood, modesty, purity, raising children to reflect biblical values, the power of prayer, intimacy with God, emotional and sexual healing, and parent-child communication.</p>
                        <a href="<?php echo get_site_url();?>/speaking-requests/" class="button">Book Dannah</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="page-section">
        <div class="page-section__content-wrapper">
            <div class="row">
                <div class="page-section__single-column">
                    <?php echo types_render_field( "shopify-button-code", array("output" => "raw" ) ) ?>
                </div>
            </div>
        </div>
    </section>

  


</div><!-- Container end -->

</div><!-- Wrapper end -->


<?php get_footer(); ?>