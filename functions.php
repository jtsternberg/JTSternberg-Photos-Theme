<?php
/**
 * Art Show
 *
 * TODO:
 * Clean up/organize files
 *
 * @version  0.1.0
 *
 */

/** Start the engine **/
require_once( TEMPLATEPATH.'/lib/init.php' );
require_once( CHILD_DIR.'/lib/taxonomies.php' );

class artshow_theme {
	public static $instance = null;

	public $meta_box = array(
		'id'       => 'frame-style',
		'title'    => 'Frame Style',
		'pages'    => array( 'post' ), // multiple post types, accept custom post types
		'context'  => 'side',
		'priority' => 'default',
		'fields'   => array(
			array(
				'name'    => 'Frame Color',
				'id'      => 'frame_color_radio',
				'type'    => 'radio',
				'options' => array(
					array( 'name' => '&nbsp;&nbsp;Default Picture Frame<br />', 'value' => 'default_frame' ),
					array( 'name' => '&nbsp;&nbsp;Brown Picture Frame<br />', 'value' => 'brown_frame' ),
					array( 'name' => '&nbsp;&nbsp;Black Picture Frame<br />', 'value' => 'black_frame' ),
				)
			),
			array(
				'name'    => 'Mat Color',
				'id'      => 'mat_color_radio',
				'type'    => 'radio',
				'options' => array(
					array( 'name' => '&nbsp;&nbsp;White Mat<br />', 'value' => 'white_mat' ),
					array( 'name' => '&nbsp;&nbsp;Black Mat<br />', 'value' => 'black_mat' ),
					array( 'name' => '&nbsp;&nbsp;Custom Mat Color<br />', 'value' => 'custom_mat' ),
				)
			),
			array(
				'name' => 'Choose Mat Color',
				'id'   => 'choose_mat_color_radio',
				'type' => 'text',
			),
		),
	);

	public static function go() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function __construct() {

		// Jetpack infinite scroll
		add_theme_support( 'infinite-scroll', array(
			'container' => 'content',
			'footer'    => 'nav',
			'render'    => array( $this, 'render_infinite_scroll' ) ,
		) );

		// Remove Genesis functions
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
		remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
		remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );
		remove_action( 'genesis_post_title', 'genesis_do_post_title' );
		remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );

		// Add support for 3-column footer widgets
		add_theme_support( 'genesis-footer-widgets', 3 );
		// title tag
		add_theme_support( 'title-tag' );
		add_image_size( 'instagram', 612, 612, true );
		add_image_size( 'fullsize', 820, 804 );
		add_action( 'genesis_meta', array( $this, 'meta' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_styles' ) );

		add_filter( 'og_twittercard_image', array( $this, 'fallback_image' ) );
		add_action( 'genesis_before_post_content', array( $this, 'mat_color' ) );
		add_action( 'genesis_after_post_content', array( $this, 'white_div' ) );
		add_action( 'genesis_after_post_content', 'genesis_do_post_title' );
		add_action( 'genesis_after_post_content', 'genesis_post_info' );
		add_action( 'genesis_after_post_content', 'genesis_post_meta' );
		add_action( 'genesis_after_post_content', array( $this, 'white_div_end' ) );

		remove_action( 'genesis_post_content', 'genesis_do_post_content' );
		add_action( 'genesis_post_content', array( $this, 'do_post_content' ) );
		add_action( 'genesis_loop_else', 'genesis_do_noposts' );
		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_data' ) );
		add_filter( 'post_class', array( $this, 'frame_class' ), 15 );
		add_filter( 'post_class', array( $this, 'mat_class' ), 15 );

		// Genesis Default Nav updated
		remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
		add_action( 'genesis_after_endwhile', array( $this, 'posts_nav' ) );
		// Custom Nav
		add_filter( 'genesis_older_link_text', array( $this, 'older_link_text' ) );
		add_filter( 'genesis_newer_link_text', array( $this, 'newer_link_text' ) );
		// change comments to notes
		add_filter( 'genesis_title_comments', array( $this, 'title_comments' ) );
		// change to "leave a note"
		add_filter( 'genesis_comment_form_args', array( $this, 'comment_form_args' ) );
		// Modify credits section
		add_filter( 'genesis_footer_creds_text', array( $this, 'footer_creds_text' ) );
		add_action( 'wp_footer', array( $this, 'googleanalytics' ) );
		add_filter( 'infinite_scroll_credit', array( $this, 'infinite_scroll_credit' ) );
		// Set Pressgram terms
		add_action( 'save_post', array( $this, 'set_terms_on_save' ), 22 );

		// Keep 'personal' category out of homepage.
		add_action( 'pre_get_posts', array( $this, 'remove_personal_from_homepage' ), 22 );
	}

	/**
	* Add Google Font API
	* @since  0.1.0
	*/
	function meta() {
			// echo "<link href='https://fonts.googleapis.com/css?family=Josefin+Sans:600,bold,bolditalic' rel='stylesheet' type='text/css'>";
			echo '<meta name="google-site-verification" content="rX3Aj4eDqSU0-Q0hBW-qsxVYaIUgxshD-kKYjSQAVb0" />';
			?>
			<div id="fb-root"></div>
			<script type='text/javascript'>
				// Facebook Button
				(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=198125846901828";
				fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));

				//Twitter button
				!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
			</script>

			<?php
			if ( is_singular() ) {
				global $post;
				$img2 = genesis_get_image( array( 'format' => 'url') );
				echo '<link rel="image_src" href="' . $img2 . '" />';
			} else { ?>
				<link rel="image_src" href="<?php echo get_stylesheet_directory_uri(); ?>/images/site_image.jpg" />
			<?php }
	}

	function scripts_styles() {
		if ( is_admin() ) {
			return;
		}

			wp_enqueue_script( 'pinterest', 'https://assets.pinterest.com/js/pinit.js' );

			// Add Stylesheets for IE
			wp_enqueue_style(
				'google-font',
				'https://fonts.googleapis.com/css?family=Josefin+Sans:600,bold,bolditalic'
			);

	}

	function admin_scripts_styles() {
		if ( get_current_screen()->post_type == 'post' ) {
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_script( 'farbtastic' );
		}
	}

	function fallback_image( $img ) {
		if ( empty( $img ) ) {
			return get_stylesheet_directory_uri().'/images/site_image.jpg';
		}

		return $img;
	}

	function mat_color() {
		global $post;

		$mat_color = get_post_meta( $post->ID, 'mat_color_radio', true );
		if ( ! empty( $mat_color ) ) {
			$mat_color = '';
			if ( 'black_mat' === $mat_color ) {
				$mat_color = '#000';
			} elseif ( 'custom_mat' === $mat_color ) {
				$mat_color = get_post_meta( $post->ID, 'choose_mat_color_radio', true );
			}
		}
		$colorcheck = is_string( $mat_color ) && isset( $mat_color[3] ) ? strtolower( $mat_color[3] ) : false;
		$sig = ( $colorcheck > 3 || is_numeric( $colorcheck ) === false ) ? 'sig' : 'sig_white';

		if ( ! empty( $mat_color ) ) {
			?>
			<style type="text/css">
					.post-<?php the_ID(); ?> .entry-content {
						background-color: <?php echo $mat_color; ?> !important;
					}
					.post-<?php the_ID(); ?> .frame_bottom, .post-<?php the_ID(); ?>.black_frame .frame_bottom, .post-<?php the_ID(); ?>.default_frame .frame_bottom {
						background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/<?php echo $sig; ?>.png) !important;
					}
					.post-<?php the_ID(); ?>.orientation-portrait span.portrait:after, .post-<?php the_ID(); ?>.orientation-square div.entry-content:after {
						content: url(<?php echo get_stylesheet_directory_uri(); ?>/images/<?php echo $sig; ?>.png) !important;
					}

			</style>
			<?php
		}

	}

	function white_div() {

		if ( is_object_in_term( get_the_ID(), 'orientation', 'landscape' ) ) {
			$frame_color = get_post_meta( get_the_ID(), 'frame_color_radio', true );
			$frame = '-default';
			if ( $frame_color == 'black_frame' ) {
				$frame = '-black';
			} elseif ( $frame_color == 'brown_frame' ) {
				$frame = '';
			}

			echo '<div class="frame_bottom"><img src="'. get_stylesheet_directory_uri() .'/images/large-frame-bottom'. $frame .'.png" /></div>';
		} ?>
		<div class="white_box">
		<?php
	}

	function white_div_end() {

		if ( function_exists( 'sharing_display' ) ) {
			echo sharing_display();
		}
		else {
			$this->fallback_sharing();
		}

		if ( class_exists( 'Jetpack_Likes' ) && is_callable( array( 'Jetpack_Likes', 'init' ) ) ) {
			$likes = Jetpack_Likes::init();

			echo $likes->post_likes( '' );
		}

		?>
		</div><!-- .white_box -->
		<?php
	}

	function fallback_sharing() {
		$permalink = get_permalink();
		$twitter = $facebook = $standard = $permalink;

		if ( function_exists( 'dsgnwrks_bitly_short_link' ) ) {
			$urls = get_post_meta( get_the_ID(), '_dsgnwrks_bitlylinks', true );
			$twitter = ! empty( $urls['twitter'] ) ? esc_url( $urls['twitter'] ) : esc_url( dsgnwrks_bitly_short_link( add_query_arg( 'source', 'twitter', $permalink ) ) );
			$facebook = ! empty( $urls['facebook'] ) ? esc_url( $urls['facebook'] ) : esc_url( dsgnwrks_bitly_short_link( add_query_arg( 'source', 'facebook', $permalink ) ) );
			$standard = ! empty( $urls['standard'] ) ? esc_url( $urls['standard'] ) : esc_url( dsgnwrks_bitly_short_link( $permalink ) );
		}

		?>
		<div class="fb-like" data-href="<?php echo esc_attr( $facebook ); ?>" data-send="true" data-layout="button_count" data-width="100" data-show-faces="false"></div>

		<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-url="<?php echo esc_attr( $twitter ); ?>" data-text="<?php echo esc_attr( get_the_title() ); ?>" data-via="Jtsternberg">Tweet</a>
		<?php if ( has_post_thumbnail( get_the_ID() ) ) {
			$img_data = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			?>
			<a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode( $standard ); ?>&media=<?php echo urlencode( $img_data[0] ); ?>&description=<?php echo urlencode( get_the_excerpt() ); ?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
		<?php } ?>

		<?php
	}

	/**
	 * Post Content
	 */
	function do_post_content() {
		if ( is_singular() ) {

			if ( is_page() ) {
					edit_post_link( __( '(Edit)', 'artshow' ), '', '' );
			} else {

				$this->image_loop( true );

				if ( is_single() && get_option( 'default_ping_status' ) == 'open' ) {
					echo '<!--'; trackback_rdf(); echo '-->' ."\n";
				}
			}
		}

		else {
			$this->image_loop();
		}

		wp_link_pages( array( 'before' => '<p class="pages">' . __( 'Pages:', 'artshow' ), 'after' => '</p>' ) );

	}

	function image_loop( $single_post = false ) {
		$permalink = $image_array = false;
		$anchor_class = '';
		if ( ! $single_post ) {
			$permalink = get_permalink();
		} else {
			$insta_link = get_post_meta( get_the_ID(), 'instagram_link', true );
			$permalink = $insta_link ? esc_url( $insta_link ) : false;
			$anchor_class = $insta_link ? 'popup' : '';
		}
		$image = '';
		$is_portrait = has_term( 'portrait', 'orientation' );
		$is_landscape = has_term( 'landscape', 'orientation' );
		$frame_color = get_post_meta( get_the_ID(), 'frame_color_radio', true );

		$image_size = ( $is_landscape || $is_portrait || $single_post )
				? 'fullsize'
				: 'instagram';

		if ( $thumbnail_id = get_post_thumbnail_id() ) {
			$image_array = $this->image_src( $thumbnail_id, compact( 'image_size', 'anchor_class', 'permalink' ) );
			$image = isset( $image_array['markup'] ) ? $image_array['markup'] : $image;
		}

		if ( ! $image && ( $images = get_children( array( 'post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 1 ) ) ) ) {

			$images = array_values( $images );
			if ( ! isset( $images[0] ) ) {
				return;
			}

			$image_array = $this->image_src( $images[0]->ID, compact( 'image_size', 'anchor_class', 'permalink' ) );
			$image = isset( $image_array['markup'] ) ? $image_array['markup'] : $image;

		}

		if ( ! $image ) {
			global $post;
			$title = the_title_attribute( 'echo=0' );
			$anchor_class = $anchor_class ? ' class="' . esc_attr( $anchor_class ) . '"' : '';

			$output = preg_match_all( '/< *img[^>]*src *= *["\']?([^"\']*)/', $post->post_content, $matches );

			if ( isset( $matches[1][0] ) ) {
				$image = '<span class="sig"></span><span class="portrait">';
				if ( $permalink ) {
					$image .= '<a'. $anchor_class .' href="'. esc_attr( $permalink ) .'">';
				}
				$image .= '<img src="' . esc_url( $matches[1][0] ) . '" alt="'. esc_attr( $title ) .'" title="'. esc_attr( $title ) .'" />';
				if ( $permalink ) {
					$image .= '</a>';
				}
				$image .= '</span>';
			}
		}

		$style = '';
		if ( $is_portrait && $image_array ) {
			$frame_width = $frame_color == 'black_frame' ? 178 : 156;
			$frame_width = $frame_color == 'default_frame' ? 177 : $frame_width;
			$style = ' style="width:'.($image_array[1] + $frame_width).'px; height:'.($image_array[2] + 175).'px;"';
		}

		if ( ! empty( $image ) ) {
			echo '<div'. $style .' class="the_photo">'. $image .'</div>';
		}

	}

	function image_src( $attach_id, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'image_size'   => 'fullsize',
			'anchor_class' => '',
			'permalink'    => '',
		) );

		$imgurl = wp_get_attachment_image_src( $attach_id, $args['image_size'] );

		if ( ! $imgurl || is_wp_error( $imgurl ) ) {
			return '';
		}

		$anchor_class = $args['anchor_class'] ? ' class="' . esc_attr( $args['anchor_class'] ) . '"' : '';

		$title = the_title_attribute( 'echo=0' );

		$image = '<span class="sig"></span><span class="portrait">';
		if ( $args['permalink'] ) {
			$image .= '<a '. $anchor_class .' href="'. esc_attr( $args['permalink'] ) .'">';
		}
		$image .= '<img class="size-'. esc_attr( $args['image_size'] ) .'" width="' . $imgurl[1] . 'px" height="' . $imgurl[2] . 'px" src="' . $imgurl[0] . '" alt="'. esc_attr( $title ) .'" title="'. esc_attr( $title ) .'" />';
		if ( $args['permalink'] ) {
			$image .= '</a>';
		}
		$image .= '</span>';

		$imgurl['markup'] = $image;

		return $imgurl;
	}

	// Add meta box
	function add_meta_box() {
		foreach ( $this->meta_box['pages'] as $page ) {
				add_meta_box( $this->meta_box['id'], $this->meta_box['title'], array( $this, 'metabox_display' ), $page, $this->meta_box['context'], $this->meta_box['priority'] );
		}
	}

	// Callback function to show fields in meta box
	function metabox_display() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="jtsternberg_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

		echo '<table class="form-table picture_options">';

		foreach ( $this->meta_box['fields'] as $field ) {
			// get current post meta data
			$meta = get_post_meta( $post->ID, $field['id'], true );
			$desc = isset( $field['desc'] ) ? $field['desc'] : '';
			echo '<tr>',
						'<th style="width:65px;"><b><label for="', $field['id'], '">', $field['name'], '</label></b></th>',
						'<td>';                '<td>';
			switch ( $field['type'] ) {
				case 'text':
					echo '<label><input style="width: 100%;" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : '', '" size="30" />', '<br />', $desc, '</label>';
					break;
				case 'textarea':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : '', '</textarea>', '<br />', $desc;
					break;
				case 'select':
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					foreach ( $field['options'] as $option ) {
						echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
					echo '</select>';
					break;
				case 'radio':
					foreach ( $field['options'] as $option ) {
						echo '<label><input type="radio" name="', $field['id'], '" id="', $field['id'] .'_'. $option['value'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'], '</label>';
					}
					break;
				case 'checkbox':
					echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
					break;
			}
			echo     '<td>',
					'</tr>';
		}

		echo '</table>';
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($){

			var box = document.createElement('div'),
			input = $('#choose_mat_color_radio'),
			black = $('#mat_color_radio_black_mat'),
			white = $('#mat_color_radio_white_mat'),
			radio = $('#mat_color_radio_custom_mat');
			radios = $('input:radio[name=mat_color_radio]');

			$(input).css('background-color', input.val());

			box.className = 'wds-upli-color-picker';

			$(box).insertAfter($('.picture_options'));

			box = $('.wds-upli-color-picker');

			box.hide();

			function pickColor(color) {
				farbtastic.setColor(color);
				input.val(color);
				$(input).css('background-color', color);
			}

			farbtastic = $.farbtastic('.wds-upli-color-picker', function(color) {
				pickColor(color);
			});

			$(input).click(function(){
				box.slideToggle();
				radio.attr('checked', true);
				$('.farbtastic').css('margin', 'auto');

			});
			$(radio).click(function(){
				box.slideToggle();
				$('.farbtastic').css('margin', 'auto');

			});

			$(black).click(function(){
				box.hide();
			});
			$(white).click(function(){
				box.hide();
			});

		});
		</script>
		<?php
	}


	// Save data from meta box
	function save_data($post_id) {

		// verify nonce
		if ( ! isset( $_POST['jtsternberg_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['jtsternberg_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
		}

		// check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		foreach ( $this->meta_box['fields'] as $field ) {
			$old = get_post_meta( $post_id, $field['id'], true );
			$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : '';

			if ( $new && $new != $old ) {
				update_post_meta( $post_id, $field['id'], $new );
			} elseif ( '' == $new && $old ) {
				delete_post_meta( $post_id, $field['id'], $old );
			}
		}
	}

	function frame_class( $classes ) {
		global $post;
		$new_class = get_post_meta( $post->ID, 'frame_color_radio', true );
		if ( $new_class ) {
			$classes[] = $new_class;
		}

		$orientations = wp_get_object_terms( $post->ID, 'orientation' );
		if ( ! empty( $orientations ) ) {
			foreach ( $orientations as $orientation ) {
				$classes[] = 'orientation-'. $orientation->slug;
			}
		} elseif (
			$default_orientation = apply_filters( 'art_show_default_orientation_for_post_class', '' ) ) {
			$classes[] = "orientation-$default_orientation";
		}

		return $classes;
	}

	function mat_class( $classes ) {
		global $post;
		if ( $new_class = get_post_meta( $post->ID, 'mat_color_radio', true ) ) {
			$classes[] = $new_class;
		}
		return $classes;
	}

	/*if ( get_post_meta( get_the_ID(), 'hide-comments', true) == 'yes' ) { ?><br /><br /><p>Comments closed on this post.</p><?php } else { comments_template(); }*/

	/**
	 * Display numeric posts navigation (similar to WP-PageNavi)
	 *
	 * @since 0.2.3
	 */
	function posts_nav() {
		if ( is_singular() ) {
			return; // do nothing
		}

		global $wp_query;

		// Stop execution if there's only 1 page
		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}

		$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
		$max = intval( $wp_query->max_num_pages );

		//  add current page to the array
		if ( $paged >= 1 ) {
			$links[] = $paged;
		}

		//  add the pages around the current page to the array
		if ( $paged >= 3 ) {
			$links[] = $paged - 1;
			$links[] = $paged - 2;
		}

		if ( ( $paged + 2 ) <= $max ) {
			$links[] = $paged + 2;
			$links[] = $paged + 1;
		}

		genesis_markup( array(
			'html5'   => '<div %s>',
			'xhtml'   => '<div class="navigation">',
			'context' => 'archive-pagination',
		) );

		$before_number = function_exists( 'genesis_a11y' ) && genesis_a11y( 'screen-reader-text' ) ? '<span class="screen-reader-text">' . __( 'Page ', 'artshow' ) .  '</span>' : '';

		echo '<ul>';

		//  Previous Post Link
		if ( get_previous_posts_link() ) {
			printf( '<li class="view_room">%s</li>' . "\n", get_previous_posts_link( __( '<strong class="larr">&larr;</strong> Previous Room', 'artshow' ) ) );
		}

		// Link to first Page, plus ellipeses, if necessary
		if ( ! in_array( 1, $links ) ) {

			$class = 1 == $paged ? ' class="active"' : '';

			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), $before_number . '1' );

			if ( ! in_array( 2, $links ) ) {
				echo '<li class="pagination-omission">&#x02026;</li>' . "\n";
			}
		}

		//  Link to Current page, plus 2 pages in either direction (if necessary).
		sort( $links );
		foreach ( (array) $links as $link ) {
			$class = $paged == $link ? ' class="active"  aria-label="' . __( 'Current page', 'artshow' ) . '"' : '';
			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $before_number . $link );
		}

		//  Link to last Page, plus ellipses, if necessary
		if ( ! in_array( $max, $links ) ) {

			if ( ! in_array( $max - 1, $links ) ) {
				echo '<li class="pagination-omission">&#x02026;</li>' . "\n";
			}

			$class = $paged == $max ? ' class="active"' : '';
			printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $before_number . $max );
		}

		//  Next Post Link
		if ( get_next_posts_link() ) {
			printf( '<li class="view_room">%s</li>' . "\n", get_next_posts_link( __( 'Next Room <strong class="rarr">&rarr;</strong>', 'artshow' ) ) );
		}

		echo '</ul></div>' . "\n";
	}

	function older_link_text() {
		$olderlink = __( '<strong>&larr;</strong> ', 'artshow' ) . 'Older Photos';
		return $olderlink;
	}
	function newer_link_text() {
		$newerlink = 'Newer Photos' . __( ' <strong>&rarr;</strong', 'artshow' );
		return $newerlink;
	}

	function title_comments() {
		$comment_title = __( '<h3>Notes</h3>', 'artshow' );
		return $comment_title;
	}

	function comment_form_args($leave_comment) {
		$leave_comment['title_reply'] = 'Leave a Note';
		return $leave_comment;
	}


	function footer_creds_text($creds) {
		$creds = '[footer_copyright] <a href="https://about.me/jtsternberg" target="_blank">jtsternberg</a> &bull; Built on the [footer_genesis_link] &bull; Powered by [footer_wordpress_link]';
		return $creds;
	}
	function googleanalytics() {
		$ga_account_id = apply_filters( 'art_show_theme_googleanalytics_account_id', '' );
		if ( ! $ga_account_id ) {
			return $ga_account_id;
		}
		?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo esc_attr( $ga_account_id ); ?>']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		  jQuery(document).ready(function($){

			function popup(url) {
				var width = 1016;
				var height = 850;
				var left = Math.floor((screen.width - width) / 2);
				var top = Math.floor((screen.height - height) / 2);
				window.open(url, 'popup', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',menubar=no,status=no,location=no,menubar=no toolbar=no,scrollbars=yes,directories=no,resizable=no');
			}

			$('a.popup').attr('target', 'popup').on('click', function (event) {
				event.preventDefault();
				popup($(this).attr('href'));
			});

		  });

		</script>
		<?php
	}

	function render_infinite_scroll() {
		do_action( 'genesis_before_loop' );
		do_action( 'genesis_loop' );
		do_action( 'genesis_after_loop' );
	}

	function infinite_scroll_credit( $credits ) {

		$credits = '<a href="https://about.me/jtsternberg" rel="author" target="_blank">About me</a> | <a href="https://wordpress.org/" rel="generator">Powered by WordPress</a> | <a class="hosted-by" href="http://j.ustin.co/wpengine_affiliate" target="_blank">Hosted by WPEngine</a>';
		return $credits;
	}

	/**
	 * make Pressgram posts square
	 * @since 0.1.0
	 */
	function set_terms_on_save( $post_id ) {

		$terms = get_the_terms( $post_id, 'category' );
		if ( ! is_array( $terms ) ) {
			return;
		}

		$terms = wp_list_pluck( $terms, 'slug' );

		if ( is_array( $terms ) && in_array( 'pressgram', $terms ) ) {
			wp_set_object_terms( $post_id, array( 'Square' ), 'orientation' );
		}
	}

	public function remove_personal_from_homepage( $query ) {
		// Only ignore on homepage main query.
		if ( ! $query->is_home() || ! $query->is_main_query() ) {
			return;
		}

		// Filter categories to ignore on the homepage.
		$category_ids_to_ignore = (array) apply_filters( 'art_show_category_ids_to_remove_from_homepage', array() );

		if ( empty( $category_ids_to_ignore ) && false !== $category_ids_to_ignore ) {
			$term = get_term_by( 'name', 'Personal', 'category' );
			$category_ids_to_ignore = isset( $term->term_id ) ? array( $term->term_id ) : array();
		}

		if ( ! empty( $category_ids_to_ignore ) ) {
			$query->set( 'category__not_in', $category_ids_to_ignore );
		}
	}

}
artshow_theme::go();
