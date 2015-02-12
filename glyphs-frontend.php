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

		$typekit_id = get_option( 'typekit_id_custom' ) ? get_option( 'typekit_id_custom' ) : get_theme_mod( 'typekit-id' );

		if( $typekit_id ){
			// Loader
			wp_register_script(
				'glyphs-font-loader',
				$path . '/js/glyphs-font-loader.js'
			);


			// Add kit ID
			$kit_id = apply_filters( 'glyphs_typekit_kit_id', $typekit_id );
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
