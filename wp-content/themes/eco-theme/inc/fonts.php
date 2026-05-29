<?php

add_filter( 'elementor/fonts/groups', function( $font_groups ) {
	$font_groups['eco_fonts'] = __( 'Eco Fonts', 'eco-theme' );

	return $font_groups;
} );

add_filter( 'elementor/fonts/additional_fonts', function( $additional_fonts ) {
	$additional_fonts['Museo Sans'] = 'eco_fonts';
	$additional_fonts['Museo Sans Condensed'] = 'eco_fonts';

	return $additional_fonts;
} );