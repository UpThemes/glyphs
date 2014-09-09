<?php

if ( ! function_exists( 'glyphs_font_setup' ) ) :
/**
 * Print scripts for Typekit fonts
 *
 * These scripts should be printed at the top of wp_head before
 * other scripts and stylesheets.
 */
function glyphs_font_setup() {
	if ( ! wp_script_is( 'glyphs-font-loader', 'registered' ) ) {
		// Determine the path of the font directory relative to the theme directory
		$path = get_template_directory();
		if ( is_link( $path ) ) {
			$path = readlink( $path );
		}
		$path = trailingslashit( get_template_directory_uri() ) . ltrim( preg_replace( '#' . $path . '#', '', dirname( __FILE__ ) ), '/' );

		if( function_exists( 'glyphs_typekit_is_valid' ) && 'invalid' !== glyphs_typekit_is_valid() ){
			// Loader
			wp_register_script(
				'glyphs-font-loader',
				$path . '/js/glyphs-font-loader.js'
			);

			// Add kit ID
			$kit_id = apply_filters( 'glyphs_typekit_kit_id', get_theme_mod( 'typekit-id' ) );
			if ( false !== $kit_id ) {
				wp_localize_script(
					'glyphs-font-loader',
					'GlyphsFontKit',
					$kit_id
				);
			}

			wp_print_scripts( array( 'glyphs-font-loader' ) );
		}
	}
}
endif;

add_action( 'wp_head', 'glyphs_font_setup', 2 );

/**
 * Dequeues the default fonts so we can override with Typekit fonts.
 */
function glyphs_dequeue_fonts(){
	if ( glyphs_typekit_is_valid() ){
		wp_dequeue_style( 'upthemes-default-fonts' );
	}
}

add_action( 'wp_enqueue_scripts', 'glyphs_dequeue_fonts', 120 );

if ( ! function_exists( 'glyphs_typekit_is_valid' ) ) :
/**
 * Checks validity of typekit ID.
 */
function glyphs_typekit_is_valid(){

	if( defined( 'UPTHEMES_LICENSE_KEY' ) && get_option( UPTHEMES_LICENSE_KEY . '_status' ) ){
		$license_status = glyphs_check_license();

		if( $license_status !== 'valid' ){
			delete_option( UPTHEMES_LICENSE_KEY . '_status' );
		}

		return $license_status;
	}
}
endif;