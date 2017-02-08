<?php

/**
 * Meta Manager Admin
 *
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register meta types
 *
 * @since 1.0
 */
function _wp_register_meta_types() {

	// Register post meta table
	wp_register_meta_type( 'post', array(
		'table_name' => 'postmeta'
	) );

	// Register comment meta table
	wp_register_meta_type( 'comment', array(
		'table_name' => 'commentmeta'
	) );

	// Register term meta table
	wp_register_meta_type( 'term', array(
		'table_name' => 'termmeta'
	) );

	// Register user meta table
	wp_register_meta_type( 'user', array(
		'table_name' => 'usermeta',
		'columns'    => array(
			'meta_id' => 'umeta_id'
		)
	) );

	do_action( 'wp_register_meta_types' );
}

/**
 * Get a list of all registered meta type objects.
 *
 * @since 2.9.0
 *
 * @global array $wp_post_types List of meta types.
 *
 * @see register_post_type() for accepted arguments.
 *
 * @param array|string $args     Optional. An array of key => value arguments to match against
 *                               the meta type objects. Default empty array.
 * @param string       $output   Optional. The type of output to return. Accepts post type 'names'
 *                               or 'objects'. Default 'names'.
 * @param string       $operator Optional. The logical operation to perform. 'or' means only one
 *                               element from the array needs to match; 'and' means all elements
 *                               must match; 'not' means no elements may match. Default 'and'.
 * @return array A list of post type names or objects.
 */
function wp_get_meta_types( $args = array(), $output = 'names', $operator = 'and' ) {
	global $wp_meta_types;

	$field = ( 'names' === $output )
		? 'name'
		: false;

	return wp_filter_object_list( $wp_meta_types, $args, $operator, $field );
}

/**
 * Retrieves a meta type object by name.
 *
 * @since 1.0.0
 *
 * @global array $wp_meta_types List of meta types.
 *
 * @see wp_register_meta_type()
 *
 * @param string $object_type The name of a registered meta type.
 * @return WP_Meta_Type|null WP_Meta_Type object if it exists, null otherwise.
 */
function wp_get_meta_type( $object_type = '' ) {
	global $wp_meta_types;

	if ( ! is_scalar( $object_type ) || empty( $wp_meta_types[ $object_type ] ) ) {
		return null;
	}

	return $wp_meta_types[ $object_type ];
}


/**
 * Registers a meta type.
 *
 * Note: Meta type registrations should be hooked in as early as possible.
 * Also, all primary object types should be registered first.
 *
 * @since 1.0.0
 *
 * @global array $wp_meta_types List of meta types.
 *
 * @param string $object_type Meta type key. Must not exceed 20 characters and may
 *                          only contain lowercase alphanumeric characters, dashes,
 *                          and underscores. See sanitize_key().
 * @param array|string $args {
 *     Array or string of arguments for registering a meta type.
 *
 *     @type bool        $global                Whether this metadata is for a global object.
 *                                              Default is false.
 *     @type string      $tablename             The name of the meta-data table, un-prefixed.
 *                                              Default is value of $labels['name'].
 *     @type array       $columns               Array of database table columns.
 *                                              Keys: meta_id, object_id, meta_key, meta_value
 * }
 * @return WP_Meta_Type|WP_Error The registered meta type object, or an error object.
 */
function wp_register_meta_type( $object_type = '', $args = array() ) {
	global $wp_meta_types;

	// Maybe instantiate the global
	if ( ! is_array( $wp_meta_types ) ) {
		$wp_meta_types = array();
	}

	// Sanitize the object type
	$object_type = sanitize_key( $object_type );

	if ( empty( $object_type ) || strlen( $object_type ) > 20 ) {
		_doing_it_wrong( __FUNCTION__, __( 'Meta type names must be between 1 and 20 characters in length.' ), '1.0.0' );
		return new WP_Error( 'meta_type_length_invalid', __( 'Meta type names must be between 1 and 20 characters in length.' ) );
	}

	// Add meta type object
	$wp_meta_types[ $object_type ] = new WP_Meta_Type( $object_type, $args );

	/**
	 * Fires after a meta type is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string       $object_type        Meta type.
	 * @param WP_Meta_Type $object_type_object Meta type object.
	 * @param array        $args               Arguments used to register the meta type.
	 */
	do_action( 'wp_register_meta_type', $object_type, $wp_meta_types[ $object_type ], $args );

	return $wp_meta_types[ $object_type ];
}
