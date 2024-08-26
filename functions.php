<?php

add_action( 'wp_enqueue_scripts', 'stm_enqueue_parent_styles' );
function stm_enqueue_parent_styles() {

	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'stm-theme-style' ) );
	wp_register_script( 'app-select2', get_theme_file_uri( '/assets/js/parts/select2.js' ), 'stmselect2', time(), true );

}

if ( ! function_exists( 'stm_get_user_child_role' ) ) {
	function stm_get_user_child_role( $default, $user_id = null ) {
		$user_data = get_userdata( $user_id ? $user_id : get_current_user_id() );

		if ( ! empty( $user_data ) ) {
			$roles_to_check = ['stm_dealer', 'subscriber'];
			$user_roles = $user_data->roles;

			// Check if there's any intersection between user roles and the roles we're checking
			if ( array_intersect( $roles_to_check, $user_roles ) ) {
				return true;
			}
		}

		return false;
	}

	add_filter( 'stm_get_user_role', 'stm_get_user_child_role', 20, 2 );
}
