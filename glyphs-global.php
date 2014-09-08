<?php

if ( ! function_exists( 'glyphs_get_parent_typekit_id' ) ) :
/**
 * Get the Typekit ID from the parent theme mods.
 *
 * Since the Typekit ID is help in the parent theme's theme mods, look at that those theme mods to get the Typekit ID
 * for the child theme.
 *
 * @param	mixed		$value		The original value of the theme mod.
 * @return 	mixed					The modified value.
 */
function glyphs_get_parent_typekit_id( $value ) {
	$template	 = get_template();
	$stylesheet = get_stylesheet();

	if ( $template !== $stylesheet && empty( $value ) ) {
		$parent_mods = get_option( 'theme_mods_' . $template );

		if ( ! empty( $parent_mods ) && isset( $parent_mods['typekit-id'] ) ) {
			$value = $parent_mods['typekit-id'];
		}
	}

	return $value;
}
endif;

add_filter( 'theme_mod_typekit-id', 'glyphs_get_parent_typekit_id' );

/**
* Check license on server
*
* We send a remote request to check the validity of the
* license and update the status based on the response.
*
* @global $wp_version
*
* @uses get_option()
* @uses wp_remote_get()
* @uses add_query_arg()
* @uses is_wp_error()
* @uses json_decode()
* @uses wp_remote_retrieve_body()
*
* @return string valid or invalid
*
* @since 0.1
*/
function glyphs_check_license() {

	global $wp_version;

	if( ! defined( UPTHEMES_LICENSE_KEY ) )
		return;

	$license_data = get_transient( UPTHEMES_LICENSE_KEY . '_license_data' );

	if ( false === ( $license_data ) ) {

		$license = trim( get_option( UPTHEMES_LICENSE_KEY ) );

		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,
			'item_name' => urlencode( UPTHEMES_ITEM_NAME )
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, UPTHEMES_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		set_transient( UPTHEMES_LICENSE_KEY . '_license_data', $license_data );

	}

	if( $license_data->license == 'valid' ) {
		return 'valid';
		// this license is still valid
	} else {
		return 'invalid';
		// this license is no longer valid
	}
}