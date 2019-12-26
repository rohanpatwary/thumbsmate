<?php
/**
 * The template for displaying the home page.
 *
 * @package WordPress3.7
 * @subpackage Twenty_Fourteen
 */

get_header(); ?>

<div class="front-page-content-wrapper">

	<?php
		if ( twentyfourteen_has_featured_posts() )
			get_template_part( 'featured-content' );
	?>

		<div id="primary" class="content-area">

			<div id="top_fbox">
				<div id="content" class="site-content" role="main">

					<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('top_widgets') ) : ?>
					<?php endif; ?>


				</div><!-- #content .site-content -->
			</div>


		</div><!-- #primary .content-area -->

		<?php get_sidebar( 'content' ); ?>

</div><!-- .front-page-content-wrapper -->

<?php
get_sidebar();
get_footer();
