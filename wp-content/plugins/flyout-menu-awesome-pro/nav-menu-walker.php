<?php

class Push_Menu_Walker extends Walker_Nav_Menu
{

	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
	{

		global $wp_query;
	    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

	    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
	    $classes[] = 'menu-item-' . $item->ID;

	    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
	    $class_names = $class_names ? ' class="low ' . esc_attr( $class_names ) . '"' : '';

	    $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
	    $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

	    /**
	     * This counts the $menu_items and wraps if there are more then 5 items the
	     * remaining items into an extra <ul>
	     */

	    $output .= $indent . '<div' . $id . $class_names .'>';

	    $atts = array();
	    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
	    $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
	    $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
	    $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

	    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

	    $attributes = '';
	    foreach ( $atts as $attr => $value ) {
	      	if ( ! empty( $value ) ) {
	        	$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
	        	$attributes .= ' ' . $attr . '="' . $value . '"';
	      	}
	    }

	    $icon = carbon_get_nav_menu_item_meta( $item->ID, 'flyout_single_menu_icon' );
		$icon_img = carbon_get_nav_menu_item_meta( $item->ID, 'flyout_single_menu_icon_img' );
	    $flyout_menu_icon_position = carbon_get_theme_option('flyout_menu_icon_position');
	    $icon_left  = "";
	    $icon_right  = "";

	    if($flyout_menu_icon_position == 'left') {
			if(empty($icon_img)) {
	    		$icon_left  = ! empty( $icon ) ? '<i class="'.esc_attr( $icon['class'] ).'" ></i>' : '';
			} else {
				$icon_left  = ! empty( $icon_img ) ? '<i><img src="' . esc_url( $icon_img ) . '" /></i>' : '';
			}
	    } elseif ($flyout_menu_icon_position == 'right') {
			if(empty($icon_img)) {
	    		$icon_right  = ! empty( $icon ) ? '<i class="'.esc_attr( $icon['class'] ).'" ></i>' : '';
			} else {
				$icon_right  = ! empty( $icon_img ) ? '<i><img src="' . esc_url( $icon_img ) . '" /></i>' : '';
			}
	    }

	    $item_output = $args->before;
	    $item_output .= '<div class="djan"><a><span '. $attributes .'>';
	    $item_output .= $icon_left.$args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after.$icon_right;
	    $item_output .= '</span></a></div>';
	    $item_output .= $args->after;
	    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

	  }

	function end_el(&$output, $item, $depth=0, $args=array()) {
		$indent = str_repeat("\t", $depth);
	    $output .= "</div>\n";
	}


	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$flyout_menu_style_choice = carbon_get_theme_option( 'flyout_menu_style_choice' );

        $indent = str_repeat("\t", $depth);

        $flyout_menu_layout_logo_image = carbon_get_theme_option('flyout_menu_layout_logo_image');
		$flyout_logo = wp_get_attachment_image_src($flyout_menu_layout_logo_image, 'full');
		$flyout_logo_url = $flyout_logo[0];

		if(!empty($flyout_logo_url)) {
        	$menu_logo = '<div class="flyout-logo-head logo-img"><img src="'.$flyout_logo_url.'"></div>';
        } else {
        	$menu_logo = '<div class="flyout-logo-head"><h2 class="flyout-site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</h2></div>';
        }

        if($flyout_menu_style_choice == 'layout-1') {
        	$output .= "\n$indent<div class='mp-level'><div class='flyout-menus'>".$menu_logo."\n";
        } elseif ($flyout_menu_style_choice == 'layout-2') {
        	$output .= "\n$indent<div class='mp-level'><div class='flyout-menus'>".$menu_logo."<a class='mp-back'>back</a>\n";
        } elseif ($flyout_menu_style_choice == 'layout-3') {
        	$output .= "\n$indent<div class='mp-level'><div class='flyout-menus'>".$menu_logo."<a class='mp-back'>back</a>\n";
        } elseif ($flyout_menu_style_choice == 'layout-4') {
        	$output .= "\n$indent<div class='mp-level'><div class='flyout-menus'>".$menu_logo."<a class='mp-back'></a>\n";
        } elseif ($flyout_menu_style_choice == 'layout-5') {
        	$output .= "\n$indent<div class='mp-level'><div class='flyout-menus'>\n";
        } elseif ($flyout_menu_style_choice == 'layout-6') {
        	$output .= "\n$indent<div class='mp-level'><div class='flyout-menus'>\n";
        }
    }
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</div></div>\n";
    }
}
