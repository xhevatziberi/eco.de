<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;

add_action( 'carbon_fields_register_fields', 'flyout_menu_awesome_theme_options_opt' );
function flyout_menu_awesome_theme_options_opt() {

	// loader background
	$basic_flyout_menu_options_container = Container::make( 'theme_options', 'flyout_menu_awesome_opts', esc_html__( 'Flyout Menu', 'flyout-menu-awesome' ) )
	->add_tab('Layout', array(
		Field::make( 'radio_image', 'flyout_menu_style_choice', esc_html__('Select Layout', 'flyout-menu-awesome') )
		->add_options( array(
			'layout-1' => plugin_dir_url('README.txt') . FLYOUT_MENU_AWESOME_NAME . '/assets/menufly-1.jpg',
			'layout-2' => plugin_dir_url('README.txt') . FLYOUT_MENU_AWESOME_NAME . '/assets/menufly-2.jpg',
			'layout-3' => plugin_dir_url('README.txt') . FLYOUT_MENU_AWESOME_NAME . '/assets/menufly-3.jpg',
			'layout-4' => plugin_dir_url('README.txt') . FLYOUT_MENU_AWESOME_NAME . '/assets/menufly-4.jpg',
			'layout-5' => plugin_dir_url('README.txt') . FLYOUT_MENU_AWESOME_NAME . '/assets/menufly-5.jpg',
			'layout-6' => plugin_dir_url('README.txt') . FLYOUT_MENU_AWESOME_NAME . '/assets/menufly-6.jpg',
		) )

		->set_width( 100),
		Field::make( 'image', 'flyout_menu_layout_logo_image', esc_html__('Menu Logo Image', 'flyout-menu-awesome') )
		->set_width( 25 ),
		Field::make( 'image', 'flyout_menu_layout_background_image', esc_html__('Layout Background Image', 'flyout-menu-awesome') )
		->set_width( 25 ),
		Field::make( 'color', 'flyout_menu_layout_background_color', esc_html__('Layout Background Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),
		Field::make( 'color', 'flyout_menu_layout_background_color_sub', esc_html__('Submenu Background Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		Field::make( 'select', 'flyout_menu_layout_background_size', esc_html__( 'Background Image Size', 'flyout-menu-awesome' ) )
		->add_options( array(
			'cover' => esc_html__('Cover', 'flyout-menu-awesome'),
			'contain' => esc_html__('Contain', 'flyout-menu-awesome'),
			'inherit' => esc_html__('Inherit', 'flyout-menu-awesome'),
		) )
		->set_width( 33 ),
		Field::make( 'select', 'flyout_menu_layout_background_repeat', esc_html__( 'Background Image Repeat', 'flyout-menu-awesome' ) )
		->add_options( array(
			'repeat-x' => esc_html__('Repeat Horizontal', 'flyout-menu-awesome'),
			'repeat-y' => esc_html__('Repeat Vertical', 'flyout-menu-awesome'),
			'no-repeat' => esc_html__('No Repeat', 'flyout-menu-awesome'),
		) )
		->set_width( 33 ),
		Field::make( 'select', 'flyout_menu_layout_background_position', esc_html__( 'Background Image Position', 'flyout-menu-awesome' ) )
		->add_options( array(
			'center' => esc_html__('Center', 'flyout-menu-awesome'),
			'top' => esc_html__('Top', 'flyout-menu-awesome'),
			'bottom' => esc_html__('Bottom', 'flyout-menu-awesome'),
			'left' => esc_html__('Left', 'flyout-menu-awesome'),
			'right' => esc_html__('Right', 'flyout-menu-awesome'),
		) )
		->set_width( 33 ),
/*		Field::make( 'color', 'flyout_menu_layout_background_color_no_active', esc_html__('Layout Menu No Active  ', 'flyout-menu-awesome') )
		->set_width( 25 ),
		Field::make( 'color',
			'flyout_menu_layout_background_color_content_no_active', esc_html__('Layout Content No Active  ', 'flyout-menu-awesome') )
		->set_width( 25 ),*/
	) )

	->add_tab('Menu', array(
		Field::make( 'select', 'flyout_menu_menu_style_choice', esc_html__('Select Menu', 'flyout-menu-awesome') )
		->add_options( array(
			'wordpress-menu' => esc_html__('WordPress Menu', 'flyout-menu-awesome'),
			'manual' => esc_html__('Manual Menu', 'flyout-menu-awesome'),
		) ),

		Field::make( 'complex', 'flyout_menu_manual_menu', esc_html__('Menu', 'flyout-menu-awesome') )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_menu_style_choice',
				'value' => 'manual',
				'compare' => '=',
			),
		) )
		->set_layout( 'tabbed-vertical' )
		->add_fields( array(
			Field::make( 'text', 'flyout_menu_menu_label', esc_html__('Menu', 'flyout-menu-awesome') )
			->set_attribute( 'placeholder', esc_html__('Enter your label menu here', 'flyout-menu-awesome') )
			->set_width( 25 ),

			Field::make( 'text', 'flyout_menu_link_menu', esc_html__('Link', 'flyout-menu-awesome') )
			->set_attribute( 'placeholder', esc_html__('Enter menu url', 'flyout-menu-awesome') )
			->set_width( 25 ),

			Field::make( 'icon', 'flyout_menu_icon', esc_html__('Icon', 'flyout-menu-awesome') )
			->set_width( 25 ),

			Field::make( 'image', 'flyout_menu_icon_img', esc_html__('Icon Image', 'flyout-menu-awesome') )
			->set_width( 25 )
			->set_value_type( 'url' ),

			Field::make( 'complex', 'flyout_menu_manual_submenu', esc_html__('Submenu', 'flyout-menu-awesome') )
			->set_layout( 'tabbed-horizontal' )
			->add_fields( array(
				Field::make( 'text', 'flyout_menu_submenu_label', esc_html__('Menu', 'flyout-menu-awesome') )
				->set_attribute( 'placeholder', esc_html__('Enter your label menu here', 'flyout-menu-awesome') )
				->set_width( 20 ),

				Field::make( 'text', 'flyout_menu_link_submenu', esc_html__('Link', 'flyout-menu-awesome') )
				->set_attribute( 'placeholder', esc_html__('Enter menu url', 'flyout-menu-awesome') )
				->set_width( 20 ),

				Field::make( 'icon', 'flyout_menu_submenu_icon', esc_html__('Icon', 'flyout-menu-awesome') )
				->set_width( 20 ),

				Field::make( 'image', 'flyout_menu_submenu_icon_img', esc_html__('Icon Image', 'flyout-menu-awesome') )
				->set_width( 20 )
				->set_value_type( 'url' ),
			))
		) ),

		// menu font
		Field::make( 'color', 'flyout_menu_font_color', esc_html__('Menu Font Color', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_menu_active_color', esc_html__('Menu Active Font Color', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_style_choice',
				'value' => 'layout-6',
				'compare' => '=',
			),
			array(
				'field' => 'flyout_menu_style_choice',
				'value' => 'layout-5',
				'compare' => '=',
			),
		) ),

		Field::make( 'color', 'flyout_menu_font_hov_color', esc_html__('Menu Font Hover Color', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true ),

		Field::make( 'number', 'flyout_menu_font_size', esc_html__( 'Menu Font Size', 'flyout-menu-awesome' ) )
		->set_width( 20 ),

		Field::make( 'select', 'flyout_menu_font_weight', esc_html__( 'Menu Font Weight', 'flyout-menu-awesome' ) )
		->add_options( array(
			'400' => esc_html__('Normal', 'flyout-menu-awesome'),
			'700' => esc_html__('Bold', 'flyout-menu-awesome'),
		) )
		->set_width( 20 ),

		Field::make( 'number', 'flyout_menu_line_height', esc_html__( 'Line Height', 'flyout-menu-awesome' ) )
		->set_width( 20 ),

		// icon menu font
		Field::make( 'color', 'flyout_menu_icon_color', esc_html__('Icon Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),


		Field::make( 'color', 'flyout_menu_icon_bg_color', esc_html__('Icon Backround Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_style_choice',
				'value' => 'layout-6',
				'compare' => '=',
			),
			array(
				'field' => 'flyout_menu_style_choice',
				'value' => 'layout-5',
				'compare' => '=',
			),
		) ),

		Field::make( 'color', 'flyout_menu_icon_hov_color', esc_html__('Icon Hover Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		Field::make( 'number', 'flyout_menu_icon_size', esc_html__( 'Icon Size', 'flyout-menu-awesome' ) )
		->set_width( 25 ),

		Field::make( 'select', 'flyout_menu_icon_position', esc_html__('Icon Position', 'flyout-menu-awesome') )
		->add_options( array(
			'left' => esc_html__('Icon left', 'flyout-menu-awesome'),
			'right' => esc_html__('Icon right', 'flyout-menu-awesome'),
		) )
		->set_width( 25 ),

		// arrow menu font
		Field::make( 'color', 'flyout_menu_arrow_color', esc_html__('Arrow Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		// arrow menu font
		Field::make( 'color', 'flyout_menu_border_color', esc_html__('Border Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		// arrow menu font
		Field::make( 'color', 'flyout_menu_sub_menu_border_color', esc_html__('Sub Menu Border Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),
	) )

	->add_tab('Menu Button', array(
		Field::make( 'select', 'flyout_menu_menu_button_style_choice', esc_html__('Select Menu Location', 'flyout-menu-awesome') )
		->add_options( array(
			'wordpress-menu' => esc_html__('As WordPress Menu', 'flyout-menu-awesome'),
			'manual' => esc_html__('Floating Button Menu', 'flyout-menu-awesome'),
		) )
		->set_width( 50 ),

		Field::make( 'select', 'flyout_menu_button_menu_style', esc_html__('Menu Button Style', 'flyout-menu-awesome') )
		->add_options( array(
			'style-1' => esc_html__('Style 1', 'flyout-menu-awesome'),
			'style-2' => esc_html__('Style 2', 'flyout-menu-awesome'),
		) )
		->set_width( 50 ),

		Field::make( 'text', 'flyout_menu_custom_menu_label', esc_html__('Button Label', 'flyout-menu-awesome') )
		->set_attribute( 'placeholder', esc_html__('Text beside menu button', 'flyout-menu-awesome') )
		->set_width( 20 ),

		Field::make( 'select', 'flyout_menu_btn_icon_pos', esc_html__('Button Icon Position', 'flyout-menu-awesome') )
		->add_options( array(
			'left' => esc_html__('Icon left', 'flyout-menu-awesome'),
			'right' => esc_html__('Icon right', 'flyout-menu-awesome'),
		) )
		->set_width( 20 ),

		Field::make( 'number', 'flyout_menu_space_between', esc_html__('Space Between Text', 'flyout-menu-awesome') )
		->set_width( 20 ),

		Field::make( 'number', 'flyout_menu_btn_font_size', esc_html__('Button Text Size', 'flyout-menu-awesome') )
		->set_width( 20 ),

		Field::make( 'select', 'flyout_menu_btn_font_weight', esc_html__('Button Text Weight', 'flyout-menu-awesome') )
		->add_options( array(
			'400' => esc_html__('Normal', 'flyout-menu-awesome'),
			'700' => esc_html__('Bold', 'flyout-menu-awesome'),
		) )
		->set_width( 20 ),

		Field::make( 'number', 'flyout_menu_button_menu_width', esc_html__('Padding Horizontal Button', 'flyout-menu-awesome') )
		->set_width( 25 ),

		Field::make( 'number', 'flyout_menu_button_menu_height', esc_html__('Padding Vertical Button', 'flyout-menu-awesome') )
		->set_width( 25 ),

		Field::make( 'number', 'flyout_menu_button_menu_round', esc_html__('Button Border Radius', 'flyout-menu-awesome') )
		->set_width( 25 ),

		Field::make( 'select', 'flyout_menu_button_menu_round_unit', esc_html__('Radius Unit', 'flyout-menu-awesome') )
		->add_options( array(
			'px' => esc_html__('px', 'flyout-menu-awesome'),
			'%' => esc_html__('%', 'flyout-menu-awesome'),
		) )
		->set_width( 25 ),

		Field::make( 'color', 'flyout_menu_btn_txt_color', esc_html__('Button Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_menu_btn_txt_hov_color', esc_html__('Button Hover Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_menu_btn_bg_color', esc_html__('Button Background Color', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_menu_btn_bg_hov_color', esc_html__('Button Background Hover', 'flyout-menu-awesome') )
		->set_width( 25 )
    	->set_alpha_enabled( true ),

		// only for manual button
		Field::make( 'html', 'crb_information_text' )
		->set_html( '<h2 class="separator-title">Floating Menu Options</h2>' )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_menu_button_style_choice',
				'value' => 'manual',
				'compare' => '=',
			),
		) ),

		Field::make( 'number', 'flyout_menu_button_menu_margin_left', esc_html__('Margin Button Left', 'flyout-menu-awesome') )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_menu_button_style_choice',
				'value' => 'manual',
				'compare' => '=',
			),
		) )
		->set_width( 15 ),

		Field::make( 'number', 'flyout_menu_button_menu_margin_bottom', esc_html__('Margin Button Bottom', 'flyout-menu-awesome') )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_menu_button_style_choice',
				'value' => 'manual',
				'compare' => '=',
			),
		) )
		->set_width( 15 ),

		Field::make( 'number', 'flyout_menu_button_menu_margin_right', esc_html__('Margin Button Right', 'flyout-menu-awesome') )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_menu_button_style_choice',
				'value' => 'manual',
				'compare' => '=',
			),
		) )
		->set_width( 15 ),

		Field::make( 'number', 'flyout_menu_button_menu_margin_top', esc_html__('Margin Button Top', 'flyout-menu-awesome') )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_menu_button_style_choice',
				'value' => 'manual',
				'compare' => '=',
			),
		) )
		->set_width( 15 ),

		Field::make( 'select', 'flyout_menu_button_menu_position_screen', esc_html__('Position Select', 'flyout-menu-awesome') )
		->set_conditional_logic( array(
			'relation' => 'OR',
			array(
				'field' => 'flyout_menu_menu_button_style_choice',
				'value' => 'manual',
				'compare' => '=',
			),
		) )
		->add_options( array(
			'top-left' => esc_html__('Top Left Screen', 'flyout-menu-awesome'),
			'center-left' => esc_html__('Center Left Screen', 'flyout-menu-awesome'),
			'bottom-left' => esc_html__('Bottom Left Screen', 'flyout-menu-awesome'),
			'top-right' => esc_html__('Top Right Screen', 'flyout-menu-awesome'),
			'center-right' => esc_html__('Center Right Screen', 'flyout-menu-awesome'),
			'bottom-right' => esc_html__('Bottom Right Screen', 'flyout-menu-awesome'),
			'center-top' => esc_html__('Center Top Screen', 'flyout-menu-awesome'),
			'center-bottom' => esc_html__('Center Bottom Screen', 'flyout-menu-awesome'),
		) )
		->set_width( 40 ),
	) )
	->add_tab('Info Menu', array(
		Field::make( 'complex', 'flyout_info_social', esc_html__('Social', 'flyout-menu-awesome') )
		->set_layout( 'tabbed-vertical' )
		->add_fields( array(
			Field::make( 'text', 'flyout_social_url', esc_html__('Social Url', 'flyout-menu-awesome') )
			->set_attribute( 'placeholder', esc_html__('Enter social url', 'flyout-menu-awesome') )
			->set_width( 33 ),

			Field::make( 'icon', 'flyout_social_icon', esc_html__('Icon', 'flyout-menu-awesome') )
			->set_width( 33 )
		) ),

		Field::make( 'textarea', 'flyout_info_text', esc_html__('Info text', 'flyout-menu-awesome') )
		->set_attribute( 'placeholder', esc_html__('Enter info text', 'flyout-menu-awesome') )
		->set_width( 100 ),


		Field::make( 'color', 'flyout_icon_color', esc_html__('Icon Color', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_icon_color_hover', esc_html__('Icon Color Hover', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_bg_icon_color', esc_html__('Background Icon Color', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_bg_icon_color_hover', esc_html__('Background Icon Color Hover', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true ),

		Field::make( 'color', 'flyout_info_text_color', esc_html__('Info Text Color', 'flyout-menu-awesome') )
		->set_width( 20 )
    	->set_alpha_enabled( true ),
	) );

	// docs
	// Container::make( 'theme_options', 'flyout_menu_awesome_lic', esc_html__( 'TA License', 'flyout-menu-awesome' ) )
	// ->set_page_parent( $basic_flyout_menu_options_container ) // identificator of the "Appearance" admin section
	// ->add_fields( array(
	// 	Field::make( 'html', 'flyout_menu_awesome_opts', esc_html__( 'TA License', 'flyout-menu-awesome' ) )
	// 	->set_html( 'flyout_menu_awesome_ta_lic_temp' ),
	// ) );

	Container::make( 'theme_options', 'flyout_menu_awesome_doc', esc_html__( 'Documentation', 'flyout-menu-awesome' ) )
	->set_page_parent( $basic_flyout_menu_options_container ) // identificator of the "Appearance" admin section
	->add_fields( array(
	) );
}

function flyout_menu_awesome_ta_lic_temp() {
	// check user capabilities
	// if (!current_user_can('manage_options')) {
	// 	return;
	// }
	// ob_start();
	// include plugin_dir_path(__FILE__) . 'admin/partials/flyout-menu-awesome-admin-display.php';
	// $content = ob_get_clean();
	// return $content;
}