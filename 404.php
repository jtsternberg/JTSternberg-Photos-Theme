<?php
/**
 * WARNING: This file is part of the core Genesis framework. DO NOT edit
 * this file under any circumstances. Please do all modifications
 * in the form of a child theme.
 *
 * Handles display of 404 page.
 *
 * @package Genesis
 */

/** Remove default loop **/
remove_action( 'genesis_loop', 'genesis_do_loop' );

add_action( 'genesis_loop', 'genesis_404' );
/**
 * This function outputs a 404 "Not Found" error message
 *
 * @since 1.6
 */
function genesis_404() { ?>

	<div class="post hentry">

		<h1 class="entry-title"><?php _e( 'Oops, the Photo or Room you are looking for is not here.', 'genesis' ); ?></h1>
		<div class="entry-content">
			<p><?php printf( __( 'Not sure what happened, but what you are looking for is not here. <a href="%s">Try going back home</a> or see if you can find what you are looking for below.', 'genesis' ), home_url() ); ?></p>
            <div class="question"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/question.png" /></div>

			<div class="archive-page">

				<h4><?php _e( 'Categories:', 'genesis' ); ?></h4>
				<ul>
					<?php wp_list_categories( 'number=12&sort_column=name&title_li=' ); ?>
				</ul>

				<h4><?php _e( 'Monthly:', 'genesis' ); ?></h4>
				<ul>
					<?php wp_get_archives( 'type=monthly&limit=12' ); ?>
				</ul>

			</div><!-- end .archive-page-->

			<div class="archive-page">


				<h4><?php _e( 'Recent Posts:', 'genesis' ); ?></h4>
				<ul>
					<?php wp_get_archives( 'type=postbypost&limit=16' ); ?>
				</ul>

			</div><!-- end .archive-page-->

		</div><!-- end .entry-content -->
        <div class="frame_bottom"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/large-frame-bottom.png" /></div>

	</div><!-- end .postclass -->

<?php
}

genesis();