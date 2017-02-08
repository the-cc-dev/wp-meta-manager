<?php

/**
 * Meta Manager Admin
 *
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Adds the Metadata menu under Tools
 *
 * @since 1.0
 */
function wp_meta_manager_admin_menu() {

	$hook = add_management_page( __( 'Meta Manager', 'wp-meta-manager' ), __( 'Meta Manager', 'wp-meta-manager' ), 'manage_options', 'wp-meta-manager', 'wp_meta_manager_admin' );

	add_action( 'load-' . $hook, 'wp_meta_manager_admin_help' );

}

/**
 * Render Meta Manager admin
 *
 * @since 1.0
 */
function wp_meta_manager_admin() {
?>

	<div class="wrap">
		<h1><?php esc_html_e( 'Meta Manager', 'wp-meta-manager' ); ?></h1>
		<h2 class="nav-tab-wrapper"><?php wp_meta_admin_tabs( 'post' ); ?></h2>
		
	</div>
<?php
}

/**
 * Render Meta Manager admin help
 *
 * @since 1.0
 */
function wp_meta_manager_admin_help() {

}

/**
 * Output the tabs in the admin area
 *
 * @since 1.0
 *
 * @param string $active_tab Name of the tab that is active
 */
function wp_meta_admin_tabs( $active_tab = '' ) {
	echo wp_meta_get_admin_tab_html( $active_tab );
}

/**
 * Output the tabs in the admin area
 *
 * @since 1.0
 *
 * @param string $active_tab Name of the tab that is active
 */
function wp_meta_get_admin_tab_html( $active_tab = '' ) {

	// Declare local variables
	$tabs_html    = '';
	$idle_class   = 'nav-tab';
	$active_class = 'nav-tab nav-tab-active';

	// Setup core admin tabs
	$tabs = wp_meta_get_admin_tabs();

	// Loop through tabs and build navigation
	foreach ( $tabs as $tab ) {

		// Setup tab HTML
		$is_current = (bool) ( $tab == $active_tab );
		$tab_class  = $is_current ? $active_class : $idle_class;
		$tab_url    = get_admin_url( '', add_query_arg( array( 'tab' => $tab, 'page' => 'wp-meta-manager' ), 'tools.php' ) );
		$tabs_html .= '<a href="' . esc_url( $tab_url ) . '" class="' . esc_attr( $tab_class ) . '">' . esc_html( ucfirst( $tab ) ) . '</a>';
	}

	// Output the tabs
	return $tabs_html;
}

/**
 * Return possible admin tabs
 *
 * @since 1.0
 *
 * @return array
 */
function wp_meta_get_admin_tabs() {

	$types = wp_get_meta_types();
	$tabs  = wp_list_pluck( $types, 'name' );

	return apply_filters( 'wp_meta_admin_tabs', $tabs );
}