<?php
add_action( 'init', 'jtsternberg_taxonomies_register', 0 );
/**
 * Setup our custom taxonomy registration and custom metabox registration
 *
 */
function jtsternberg_taxonomies_register() {

	jtsternberg_taxonomies();

	jtsternberg_orientation_select_metabox();
}

function jtsternberg_taxonomies() {

	$name = _x( 'Orientation', 'taxonomy general name' );
	$labels = array(
		'name'                       => $name,
		'singular_name'              => $name,
		'search_items'               => __( 'Search Orientations' ),
		'popular_items'              => __( 'Common Orientations' ),
		'all_items'                  => __( 'All Orientations' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Orientation' ),
		'update_item'                => __( 'Update Orientation' ),
		'add_new_item'               => __( 'Add New Orientation' ),
		'new_item_name'              => __( 'New Orientation Name' ),
		'separate_items_with_commas' => __( 'Separate Orientations with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Orientations' ),
		'choose_from_most_used'      => __( 'Choose from the most used Orientations' )
	);

	$defaults = array(
		'hierarchical' => true,
		'labels'       => $labels,
		'show_ui'      => true,
		'query_var'    => true,
		'rewrite'      => array( 'slug' => 'orientation' ),
	);

	$args = wp_parse_args( apply_filters( 'art_show_orientation_taxonomy_args', array() ), $defaults );
	$post_types = apply_filters( 'art_show_orientation_taxonomy_args', array( 'post' ) );

	register_taxonomy( 'orientation', $post_types, $args );
}

function jtsternberg_taxonomy_columns($columns){
	$newcolumns = array(
		'jt-orientation' => 'Photo Orientation',
	);
	$columns = array_merge( $columns, $newcolumns );
	return $columns;
}
add_filter( 'manage_edit-post_columns', 'jtsternberg_taxonomy_columns' );

function jtsternberg_taxonomy_columns_display( $column ) {
	global $post;
	switch ( $column ) {
		case 'jt-orientation';
			$categories = get_the_terms( $post->ID, 'orientation' );
			if ( ! empty( $categories ) ) {
				$out = array();
				foreach ( $categories as $c ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'orientation' => $c->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'category', 'display' ) )
					);
				}
				echo join( ', ', $out );
			} else {
				_e( 'No Orientation Specified' );
			}
		break;
	}
}
add_action( 'manage_posts_custom_column' , 'jtsternberg_taxonomy_columns_display' );

function jtsternberg_orientation_select_metabox() {
	if ( ! is_admin() ) {
		return;
	}
	add_action( 'admin_menu', 'jtsternberg_add_orientation_select_metabox' );
	add_action( 'save_post', 'jtsternberg_save_orientation_taxonomy_data' );
}

function jtsternberg_add_orientation_select_metabox() {

	remove_meta_box( 'orientationdiv','post','core' );
	add_meta_box( 'orientation_dropdown', 'Orientation', 'jtsternberg_add_orientation_dropdown_box_function', 'post', 'side', 'high' );
}

function jtsternberg_add_orientation_dropdown_box_function( $post ) {

	$taxonomy = 'orientation';

	echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' .
	wp_create_nonce( 'taxonomy_'. $taxonomy ) . '" />';

	$checked = $editor_picks_checked = '';
	// Get all orientation taxonomy terms
	$terms = get_terms( $taxonomy, 'hide_empty=0' );
	$names = wp_get_object_terms( get_the_ID(), $taxonomy );
	if ( ! count( $names ) ) {
		$checked = 'checked';
	}

	echo "<div style='margin-bottom: 5px;'><input style='margin-right: 5px;' type='radio' name='orientation_option' id='option-research' value='' ". $checked ."/><label for='option-research'>No Orientation Specified</label></div>\n";
	foreach ( $terms as $term ) {

		if ( $term->slug == 'research' ) {
			continue;
		}

		echo "<div style='margin-bottom: 5px;'><input style='margin-right: 5px;' type='radio' name='orientation_option' id='option-".  $term->slug ."' value='" . $term->slug . "'";
		if ( ! is_wp_error( $names ) && ! empty( $names ) && ! strcmp( $term->slug, $names[0]->slug ) ) {
			echo ' checked';
		}
		echo "/><label for='option-". $term->slug ."'>". $term->name ."</label></div>\n";
	}
}

function jtsternberg_save_orientation_taxonomy_data( $post_id ) {
	// verify this came from our screen and with proper authorization.
	$taxonomy = 'orientation';

	if ( isset($_POST['taxonomy_noncename']) && ! wp_verify_nonce( $_POST['taxonomy_noncename'], 'taxonomy_'. $taxonomy ) ) {
		return $post_id;
	}

	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check permissions
	if ( 'post' != $_POST['post_type'] ) {
		  return $post_id;
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// OK, we're authenticated: we need to find and save the data
	$orientation = ( $_POST['orientation_option'] ) ? $_POST['orientation_option'] : '';

	if ( ! empty( $orientation ) ) {
		wp_set_object_terms( $post_id, sanitize_text_field( $orientation ), 'orientation' );
	}

	return $orientation;
}
