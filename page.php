<?php

remove_action( 'genesis_after_post_content', 'jtsternberg_white_div' );
remove_action( 'genesis_after_post_content', 'genesis_do_post_title' );
remove_action( 'genesis_after_post_content', 'genesis_post_info' );
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
remove_action( 'genesis_after_post_content', 'jtsternberg_white_div_end' );

add_action( 'genesis_post_title', 'genesis_do_post_title' );
add_action( 'genesis_after_post_content', 'genesis_post_meta' );
add_action( 'genesis_before_post_content', 'genesis_post_info' );



	genesis(); // <- everything important: make sure to include this.
	?>