<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themesawesome.com/
 * @since             1.0.0
 * @package           Flyout_Menu_Awesome
 *
 * @wordpress-plugin
 * Plugin Name:       Flyout Menu Awesome Pro
 * Plugin URI:        https://flyoutmenu.themesawesome.com/
 * Description:       WordPress menu interface element with custom layouts and effects.
 * Version:           1.0.3
 * Author:            Themes Awesome
 * Author URI:        https://themesawesome.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       flyout-menu-awesome
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('FLYOUT_MENU_AWESOME_VERSION', '1.0.3');

define('FLYOUT_MENU_AWESOME', __FILE__);

define('FLYOUT_MENU_AWESOME_BASENAME', plugin_basename(FLYOUT_MENU_AWESOME));

define('FLYOUT_MENU_AWESOME_NAME', trim(dirname(FLYOUT_MENU_AWESOME_BASENAME), '/'));

define('FLYOUT_MENU_AWESOME_DIR', untrailingslashit(dirname(FLYOUT_MENU_AWESOME)));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-flyout-menu-awesome-activator.php
 */
function activate_flyout_menu_awesome()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-flyout-menu-awesome-activator.php';
    Flyout_Menu_Awesome_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-flyout-menu-awesome-deactivator.php
 */
function deactivate_flyout_menu_awesome()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-flyout-menu-awesome-deactivator.php';
    Flyout_Menu_Awesome_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_flyout_menu_awesome');
register_deactivation_hook(__FILE__, 'deactivate_flyout_menu_awesome');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-flyout-menu-awesome.php';
require plugin_dir_path(__FILE__) . 'nav-menu-walker.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_flyout_menu_awesome()
{

    $plugin = new Flyout_Menu_Awesome();
    $plugin->run();
}
run_flyout_menu_awesome();

// init carbon field
add_action('after_setup_theme', 'flyout_menu_awesome_crb_load');
function flyout_menu_awesome_crb_load()
{
    require_once('vendor/autoload.php');
    \Carbon_Fields\Carbon_Fields::boot();
}

// all themesawesome flyout-menu-awesome options
require plugin_dir_path(__FILE__) . 'flyout-menu-awesome-options.php';

function flyout_menu_open_body()
{
    $flyout_menu_menu_button_style_choice = carbon_get_theme_option('flyout_menu_menu_button_style_choice');
    $flyout_menu_custom_menu_label = carbon_get_theme_option('flyout_menu_custom_menu_label');

    $flyout_menu_button_menu_style = carbon_get_theme_option('flyout_menu_button_menu_style');
    $flyout_menu_btn_icon_pos = carbon_get_theme_option('flyout_menu_btn_icon_pos');

    // menu layout
    $flyout_menu_style_choice = carbon_get_theme_option('flyout_menu_style_choice');

    if (!empty($flyout_menu_custom_menu_label)) {
        $ta_flyout_btn_label = esc_html($flyout_menu_custom_menu_label);
    } else {
        $ta_flyout_btn_label = esc_html__('Show Menu', 'flyout-menu-awesome');
    } ?>

    <?php
    if ($flyout_menu_style_choice == 'layout-1') {
        $class_name_style = 'fly-style-hiji';
    } elseif ($flyout_menu_style_choice == 'layout-2') {
        $class_name_style = 'fly-style-dua';
    } elseif ($flyout_menu_style_choice == 'layout-3') {
        $class_name_style = 'fly-style-tilu';
    } elseif ($flyout_menu_style_choice == 'layout-4') {
        $class_name_style = 'fly-style-opat';
    } elseif ($flyout_menu_style_choice == 'layout-5') {
        $class_name_style = 'fly-style-lima';
    } elseif ($flyout_menu_style_choice == 'layout-6') {
        $class_name_style = 'fly-style-genep';
    }

    if ($flyout_menu_style_choice != 'layout-5') { ?>
        <div class="flyout-container <?php echo esc_attr($class_name_style); ?>">
            <div class="mp-pusher" id="mp-pusher"><!-- wrapper needed for scroll -->

                    <div class="scroller"><!-- this is for emulating position fixed of the nav -->
                        <div class="scroller-inner">
                        <?php flyout_menu_awesome_menu_button_style();
    } else {
        $flyout_menu_menu_style_choice = carbon_get_theme_option('flyout_menu_menu_style_choice');
        $flyout_info_social = carbon_get_theme_option('flyout_info_social');
        $flyout_info_text = carbon_get_theme_option('flyout_info_text');
        
        // menu layout
        $flyout_menu_style_choice = carbon_get_theme_option('flyout_menu_style_choice');
        $flyout_menu_icon_position = carbon_get_theme_option('flyout_menu_icon_position');
        
        $flyout_menu_layout_logo_image = carbon_get_theme_option('flyout_menu_layout_logo_image');
        $flyout_logo = wp_get_attachment_image_src($flyout_menu_layout_logo_image, 'full');
        $flyout_logo_url = $flyout_logo[0];
        flyout_menu_awesome_menu_button_style(); ?>
            <div class="flyout-style5">
                <?php if ($flyout_menu_menu_style_choice == 'manual') {
                    $flyout_menu_manual_menu = carbon_get_theme_option('flyout_menu_manual_menu');  ?>
                    <div class="flyout-menus">
                        <div class="flyout-logo-head">
                            <?php if (!empty($flyout_logo_url)) { ?>
                                <img src="<?php echo esc_url($flyout_logo_url); ?>" alt="<?php echo esc_html(get_bloginfo('name')); ?>">
                            <?php } else {
                                echo '<h2 class="flyout-site-title">' . esc_html(get_bloginfo('name')) . '</h2>';
                            } ?>
                        </div>
                        <?php if ($flyout_menu_style_choice == 'layout-1' || $flyout_menu_style_choice == 'layout-3') { ?>
                        <div class="icon icon-display">
                            <i></i>
                        </div>
                        <?php } ?>
                        <?php foreach ($flyout_menu_manual_menu as $menu_item) {
                            $flyout_menu_manual_submenu = $menu_item['flyout_menu_manual_submenu']; ?>
                            <div class="low icon icon-arrow-left<?php if (!empty($flyout_menu_manual_submenu)) {
                                ?> has-sub-menu<?php } ?>">
                                <div class="djan" href="#">
                                    <a href="<?php echo esc_url($menu_item['flyout_menu_link_menu']); ?>" class="ta-flyout-menu-item-cust">
                                        <span href="<?php echo esc_url($menu_item['flyout_menu_link_menu']); ?>">
                                        <?php if ($flyout_menu_icon_position == 'left') { ?>
                                            <i class="<?php echo esc_attr($menu_item['flyout_menu_icon']['class']); ?>"></i>
                                            <?php echo esc_html($menu_item['flyout_menu_menu_label']); ?>
                                        <?php } elseif ($flyout_menu_icon_position == 'right') { ?>
                                            <?php echo esc_html($menu_item['flyout_menu_menu_label']); ?>
                                            <i class="<?php echo esc_attr($menu_item['flyout_menu_icon']['class']); ?>"></i>
                                        <?php } ?>
                                        </span>
                                    </a>
                                </div>

                                <?php
                                if (!empty($flyout_menu_manual_submenu)) { ?>
                                <div class="mp-level">
                                    <div class="flyout-menus">
                                        <?php if ($flyout_menu_style_choice != 'layout-5' && $flyout_menu_style_choice != 'layout-6') { ?>
                                        <div class="flyout-logo-head">
                                            <?php if (!empty($flyout_logo_url)) { ?>
                                                <img src="<?php echo esc_url($flyout_logo_url); ?>" alt="<?php echo esc_html(get_bloginfo('name')); ?>">
                                            <?php } else {
                                                echo '<h2 class="flyout-site-title">' . esc_html(get_bloginfo('name')) . '</h2>';
                                            } ?>
                                        </div>
                                        <?php } ?>
                                        <?php if ($flyout_menu_style_choice == 'layout-2' || $flyout_menu_style_choice == 'layout-3') { ?>
                                        <a class="mp-back" href="#">back</a>
                                        <?php } ?>
                                        <?php if ($flyout_menu_style_choice == 'layout-4') { ?>
                                        <a class="mp-back" href="#"></a>
                                        <?php } ?>
                                        <div>
                                            <?php foreach ($flyout_menu_manual_submenu as $submenu_item) { ?>
                                                <div class="low">
                                                    <div class="djan" href="#">
                                                        <a href="<?php echo esc_url($submenu_item['flyout_menu_link_submenu']); ?>">
                                                            <span>
                                                            <?php if ($flyout_menu_icon_position == 'left') { ?>
                                                                <i class="<?php echo esc_attr($submenu_item['flyout_menu_submenu_icon']['class']); ?>"></i>
                                                                <?php echo esc_html($submenu_item['flyout_menu_submenu_label']); ?>
                                                            <?php } elseif ($flyout_menu_icon_position == 'right') { ?>
                                                                <?php echo esc_html($submenu_item['flyout_menu_submenu_label']); ?>
                                                                <i class="<?php echo esc_attr($submenu_item['flyout_menu_submenu_icon']['class']); ?>"></i>
                                                            <?php } ?>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (!empty($flyout_info_social) || !empty($flyout_info_text)) { ?>
                        <div class="info-web-wrap">
                            <?php if (!empty($flyout_info_social)) { ?>
                            <div class="social-wrap-menu">
                                <?php foreach ($flyout_info_social as $social_item) { ?>
                                <div class="social-item">
                                    <a href="<?php echo esc_url($social_item['flyout_social_url']); ?>"><i class="<?php echo esc_attr($social_item['flyout_social_icon']['class']); ?>"></i></a>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <?php if (!empty($flyout_info_text)) { ?>
                            <div class="copyright-text">
                                <?php echo wp_specialchars_decode($flyout_info_text); ?>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <script>
                        (function( $ ) {
                        'use strict';
                            $(document).ready(function(){
                                $('#trigger').on('click', function(){
                                    $('.flyout-style5').toggleClass('active');
                                });
                                if($('.has-sub-menu')[0]) {
                                    $('.flyout-menus .djan a').on('click', function(){
                                        var iconIco = $(this).find('i'),
                                            theIcon = iconIco.attr('class');

                                        if($('.icon-display i').hasClass("act")) {
                                            $('.icon-display i').removeClass();
                                            $('.icon-display i').addClass(theIcon);
                                            $('.icon-display i').addClass('act');
                                        }
                                        else {
                                            $('.icon-display i').addClass(theIcon);
                                            $('.icon-display i').addClass('act');
                                        }
                                    });
                                    $('#mp-pusher').on('click', function(){
                                        $('.icon-display i').removeClass();
                                    });
                                }
                            })
                        })( jQuery );
                        </script>
                    </div>
                    <div class="flyout5-pattern">
                        <div class="pattern-bg-menu"></div>
                    </div>
                    <div class="pattern-bg-menu-after"></div>
                <?php } else { ?>
                    <div class="flyout-menus">
                        <?php
                        $menu_name = 'flyout-menu';

                        if (( $locations = get_nav_menu_locations() ) && isset($locations[ $menu_name ])) {
                            $menu = wp_get_nav_menu_object($locations[ $menu_name ]);

                            $menu_items = wp_get_nav_menu_items($menu->term_id);
                            if (!empty($flyout_logo_url)) {
                                $menu_logo = '<div class="flyout-logo-head logo-img"><img src="'.$flyout_logo_url.'"></div>';
                            } else {
                                $menu_logo = '<div class="flyout-logo-head"><h2 class="flyout-site-title">' . esc_html(get_bloginfo('name')) . '</h2></div>';
                            }

                            if ($flyout_menu_style_choice == 'layout-1' || $flyout_menu_style_choice == 'layout-3') {
                                $head_menu = $menu_logo.'<div class="icon icon-display"><i></i></div>';
                            } else {
                                $head_menu = $menu_logo;
                            }

                            wp_nav_menu(
                                array(
                                    'container'         => false,
                                    'theme_location'    => $menu_name,
                                    'items_wrap'        => '<div class="%2$s">'.$head_menu.' %3$s</div>',
                                    'walker'            => new Push_Menu_Walker()
                                )
                            );
                        } ?>
                        <?php if (!empty($flyout_info_social) || !empty($flyout_info_text)) { ?>
                        <div class="info-web-wrap">
                            <?php if (!empty($flyout_info_social)) { ?>
                            <div class="social-wrap-menu">
                                <?php foreach ($flyout_info_social as $social_item) { ?>
                                <div class="social-item">
                                    <a href="<?php echo esc_url($social_item['flyout_social_url']); ?>"><i class="<?php echo esc_attr($social_item['flyout_social_icon']['class']); ?>"></i></a>
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <?php if (!empty($flyout_info_text)) { ?>
                            <div class="copyright-text">
                                <?php echo wp_specialchars_decode($flyout_info_text); ?>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="flyout5-pattern">
                        <div class="pattern-bg-menu"></div>
                    </div>
                    <div class="pattern-bg-menu-after"></div>
                    <script>
                        (function( $ ) {
                        'use strict';
                            $(document).ready(function(){
                                $('#trigger').on('click', function(){
                                    $('.flyout-style5').toggleClass('active');
                                });
                            })
                        })( jQuery );
                        </script>
                <?php } ?>

                <?php if ($flyout_menu_style_choice == 'layout-5') { ?>
                <div class="close-menu-wrapper">
                    <span></span>
                </div>
                <?php } ?>
            </div>
            <div id="flyout-overlay-wrap">
                <div id="flyout-overlay"></div>
            </div>
    <?php } ?>

<?php }
add_action('wp_body_open', 'flyout_menu_open_body');


function flyout_menu_close_footer()
{
    $flyout_menu_menu_style_choice = carbon_get_theme_option('flyout_menu_menu_style_choice');
    $flyout_info_social = carbon_get_theme_option('flyout_info_social');
    $flyout_info_text = carbon_get_theme_option('flyout_info_text');

    // menu layout
    $flyout_menu_style_choice = carbon_get_theme_option('flyout_menu_style_choice');
    $flyout_menu_icon_position = carbon_get_theme_option('flyout_menu_icon_position');

    $flyout_menu_layout_logo_image = carbon_get_theme_option('flyout_menu_layout_logo_image');
    $flyout_logo = wp_get_attachment_image_src($flyout_menu_layout_logo_image, 'full');
    $flyout_logo_url = $flyout_logo[0];

    if ($flyout_menu_style_choice != 'layout-5') {
        ?>
        </div>
    </div> <!-- end of scroller -->
        <?php /*if($flyout_menu_style_choice == 'layout-1') { ?>
        <nav id="outer-nav-wrap" class="outer-nav left vertical">
    <?php }*/ ?>
    <nav id="mp-menu" class="mp-menu">
        <div class="mp-level">

            <?php if ($flyout_menu_menu_style_choice == 'manual') {
                $flyout_menu_manual_menu = carbon_get_theme_option('flyout_menu_manual_menu'); ?>
                <div class="flyout-menus">
                    <div class="flyout-logo-head">
                        <?php if (!empty($flyout_logo_url)) { ?>
                            <img src="<?php echo esc_url($flyout_logo_url); ?>" alt="<?php echo esc_html(get_bloginfo('name')); ?>">
                        <?php } else {
                            echo '<h2 class="flyout-site-title">' . esc_html(get_bloginfo('name')) . '</h2>';
                        } ?>
                    </div>
                    <?php if ($flyout_menu_style_choice == 'layout-1' || $flyout_menu_style_choice == 'layout-3') { ?>
                    <div class="icon icon-display">
                        <i></i>
                    </div>
                    <?php } ?>
                    <?php foreach ($flyout_menu_manual_menu as $menu_item) {
                        $flyout_menu_manual_submenu = $menu_item['flyout_menu_manual_submenu']; ?>
                        <div class="low icon icon-arrow-left<?php if (!empty($flyout_menu_manual_submenu)) {
                            ?> has-sub-menu<?php
                                                            } ?>">
                            <div class="djan" href="#">
                                <a href="<?php echo esc_url($menu_item['flyout_menu_link_menu']); ?>" class="ta-flyout-menu-item-cust">
                                    <span href="<?php echo esc_url($menu_item['flyout_menu_link_menu']); ?>">
                                    <?php if ($flyout_menu_icon_position == 'left') { 
                                        if(!empty($menu_item['flyout_menu_icon']['class'])) { ?>
                                            <i class="<?php echo esc_attr($menu_item['flyout_menu_icon']['class']); ?>"></i>
                                        <?php } elseif(!empty($menu_item['flyout_menu_icon_img'])) { ?>
                                            <i>
                                              <img src="<?php echo esc_url($menu_item['flyout_menu_icon_img']); ?>" alt="">
                                            </i>
                                        <?php } else {
                                            
                                        } ?>
                                        <?php echo esc_html($menu_item['flyout_menu_menu_label']); ?>
                                    <?php } elseif ($flyout_menu_icon_position == 'right') { ?>
                                        <?php echo esc_html($menu_item['flyout_menu_menu_label']); ?>
                                        <?php 
                                            if(!empty($menu_item['flyout_menu_icon']['class'])) { ?>
                                                <i class="<?php echo esc_attr($menu_item['flyout_menu_icon']['class']); ?>"></i>
                                            <?php } elseif(!empty($menu_item['flyout_menu_icon_img'])) { ?>
                                                <i>
                                                <img src="<?php echo esc_url($menu_item['flyout_menu_icon_img']); ?>" alt="">
                                                </i>
                                            <?php } else {
                                                
                                            } ?>
                                    <?php } ?>
                                    </span>
                                </a>
                            </div>

                            <?php
                            if (!empty($flyout_menu_manual_submenu)) { ?>
                            <div class="mp-level">
                                <div class="flyout-menus">
                                    <?php if ($flyout_menu_style_choice != 'layout-6') { ?>
                                    <div class="flyout-logo-head">
                                        <?php if (!empty($flyout_logo_url)) { ?>
                                            <img src="<?php echo esc_url($flyout_logo_url); ?>" alt="<?php echo esc_html(get_bloginfo('name')); ?>">
                                        <?php } else {
                                            echo '<h2 class="flyout-site-title">' . esc_html(get_bloginfo('name')) . '</h2>';
                                        } ?>
                                    </div>
                                    <?php } ?>
                                    <?php if ($flyout_menu_style_choice == 'layout-2' || $flyout_menu_style_choice == 'layout-3') { ?>
                                    <a class="mp-back" href="#">back</a>
                                    <?php } ?>
                                    <?php if ($flyout_menu_style_choice == 'layout-4') { ?>
                                    <a class="mp-back" href="#"></a>
                                    <?php } ?>
                                    <div>
                                        <?php foreach ($flyout_menu_manual_submenu as $submenu_item) { ?>
                                            <div class="low">
                                                <div class="djan" href="#">
                                                    <a href="<?php echo esc_url($submenu_item['flyout_menu_link_submenu']); ?>">
                                                        <span>
                                                        <?php if ($flyout_menu_icon_position == 'left') { ?>
                                                            <?php if(!empty($submenu_item['flyout_menu_submenu_icon']['class'])) { ?>
                                                                <i class="<?php echo esc_attr($submenu_item['flyout_menu_submenu_icon']['class']); ?>"></i>
                                                            <?php } elseif(!empty($submenu_item['flyout_menu_submenu_icon_img'])) { ?>
                                                                <i>
                                                                    <img src="<?php echo esc_url($submenu_item['flyout_menu_submenu_icon_img']); ?>" alt="">
                                                                </i>
                                                            <?php } else {
                                                                
                                                            } ?>
                                                            <?php echo esc_html($submenu_item['flyout_menu_submenu_label']); ?>
                                                        <?php } elseif ($flyout_menu_icon_position == 'right') { ?>
                                                            <?php echo esc_html($submenu_item['flyout_menu_submenu_label']); ?>
                                                            <?php if(empty($menu_item['flyout_menu_icon_img'])) { ?>
                                                                <i class="<?php echo esc_attr($submenu_item['flyout_menu_submenu_icon']['class']); ?>"></i>
                                                            <?php } else { ?>
                                                                <i>
                                                                    <img src="<?php echo esc_url($submenu_item['flyout_menu_submenu_icon_img']); ?>" alt="">
                                                                </i>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($flyout_info_social) || !empty($flyout_info_text)) { ?>
                    <div class="info-web-wrap">
                        <?php if (!empty($flyout_info_social)) { ?>
                        <div class="social-wrap-menu">
                            <?php foreach ($flyout_info_social as $social_item) { ?>
                            <div class="social-item">
                                <a href="<?php echo esc_url($social_item['flyout_social_url']); ?>"><i class="<?php echo esc_attr($social_item['flyout_social_icon']['class']); ?>"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php if (!empty($flyout_info_text)) { ?>
                        <div class="copyright-text">
                            <?php echo wp_specialchars_decode($flyout_info_text); ?>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <script>
                    (function( $ ) {
                    'use strict';
                        $(document).ready(function(){
                            if($('.has-sub-menu')[0]) {
                                $('.flyout-menus .djan a').on('click', function(){
                                    var iconIco = $(this).find('i'),
                                        theIcon = iconIco.attr('class');

                                    if($('.icon-display i').hasClass("act")) {
                                        $('.icon-display i').removeClass();
                                        $('.icon-display i').addClass(theIcon);
                                        $('.icon-display i').addClass('act');
                                    }
                                    else {
                                        $('.icon-display i').addClass(theIcon);
                                        $('.icon-display i').addClass('act');
                                    }
                                });
                                $('#mp-pusher').on('click', function(){
                                    $('.icon-display i').removeClass();
                                });
                            }
                        })
                    })( jQuery );
                    </script>
                </div>
            <?php } else { ?>
                <div class="flyout-menus">
                    <?php
                    $menu_name = 'flyout-menu';

                    if (( $locations = get_nav_menu_locations() ) && isset($locations[ $menu_name ])) {
                        $menu = wp_get_nav_menu_object($locations[ $menu_name ]);

                        $menu_items = wp_get_nav_menu_items($menu->term_id);
                        if (!empty($flyout_logo_url)) {
                            $menu_logo = '<div class="flyout-logo-head logo-img"><img src="'.$flyout_logo_url.'"></div>';
                        } else {
                            $menu_logo = '<div class="flyout-logo-head"><h2 class="flyout-site-title">' . esc_html(get_bloginfo('name')) . '</h2></div>';
                        }

                        if ($flyout_menu_style_choice == 'layout-1' || $flyout_menu_style_choice == 'layout-3') {
                            $head_menu = $menu_logo.'<div class="icon icon-display"><i></i></div>';
                        } else {
                            $head_menu = $menu_logo;
                        }

                        wp_nav_menu(
                            array(
                                'container'         => false,
                                'theme_location'    => $menu_name,
                                'items_wrap'        => '<div class="%2$s">'.$head_menu.' %3$s</div>',
                                'walker'            => new Push_Menu_Walker()
                            )
                        );
                    } ?>
                    <?php if (!empty($flyout_info_social) || !empty($flyout_info_text)) { ?>
                    <div class="info-web-wrap">
                        <?php if (!empty($flyout_info_social)) { ?>
                        <div class="social-wrap-menu">
                            <?php foreach ($flyout_info_social as $social_item) { ?>
                            <div class="social-item">
                                <a href="<?php echo esc_url($social_item['flyout_social_url']); ?>"><i class="<?php echo esc_attr($social_item['flyout_social_icon']['class']); ?>"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php if (!empty($flyout_info_text)) { ?>
                        <div class="copyright-text">
                            <?php echo wp_specialchars_decode($flyout_info_text); ?>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </nav>
</div><!-- /perspective -->
</div>
    <?php } ?>
    <?php if ($flyout_menu_style_choice == 'layout-1') { ?>
    <script>
        new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'trigger' ) );
    </script>
    <?php } elseif ($flyout_menu_style_choice == 'layout-2') { ?>
    <script>
        new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'trigger' ), {
            type : 'cover'
        } );
    </script>
    <?php } elseif ($flyout_menu_style_choice == 'layout-3') { ?>
    <script>
        new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'trigger' ), {
        } );
    </script>
    <?php } elseif ($flyout_menu_style_choice == 'layout-4') { ?>
    <script>
        new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'trigger' ), {
            type : 'cover'
        } );
    </script>
    <?php } elseif ($flyout_menu_style_choice == 'layout-6') { ?>
    <script>
        new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'trigger' ), {
            type : 'cover'
        } );
    </script>
    <?php }
}

add_action('wp_footer', 'flyout_menu_close_footer', 100);

// Try Carbon Field for Menu
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'flyout_menu_wp_menu_field');
function flyout_menu_wp_menu_field()
{

    Container::make('nav_menu_item', esc_html__('Menu Settings', 'flyout-menu-awesome'))
    ->add_fields(array(
        Field::make('icon', 'flyout_single_menu_icon', esc_html__('Icon', 'flyout-menu-awesome')),
        Field::make( 'image', 'flyout_single_menu_icon_img', esc_html__('Icon Image', 'flyout-menu-awesome') )
        ->set_width( 25 )
        ->set_value_type( 'url' ),
    ));
}

//Register Menus
add_action('after_setup_theme', 'flyout_menu_awesome_register_my_menu');

function flyout_menu_awesome_register_my_menu()
{
    register_nav_menu('flyout-menu', esc_html__('Flyout Menu', 'flyout_menu_awesome'));
}

//MAIN MENU
function flyout_menu_awesome_main_nav_menu()
{
    wp_nav_menu(array(
        'theme_location' => 'flyout-menu',
    ));
}

$flyout_menu_menu_button_style_choice = get_option('_flyout_menu_menu_button_style_choice');
function flyout_menu_awesome_custom_menu_btn($items, $args)
{
    $flyout_menu_button_menu_style = get_option('_flyout_menu_button_menu_style');
    $flyout_menu_custom_menu_label = get_option('_flyout_menu_custom_menu_label');
    $flyout_menu_button_menu_position_screen = get_option('_flyout_menu_button_menu_position_screen');

    if (!empty($flyout_menu_custom_menu_label)) {
        $flyout_menu_custom_menu_label = $flyout_menu_custom_menu_label;
    } else {
        $flyout_menu_custom_menu_label = '';
    }

    $flyout_menu_btn_icon_pos = get_option('_flyout_menu_btn_icon_pos');

    if ($flyout_menu_button_menu_style == 'style-1') {
        wp_enqueue_style('flyout-menu-awesome-btn1', plugin_dir_url(__FILE__) . 'public/css/flyout-menu-awesome-btn1.css', array(), '', 'all');
        $items .= '<li class="menu-item">'
            . '<a id="trigger" href="#" class="btn-activate-flyout-tgt menu-trigger '.esc_attr($flyout_menu_button_menu_position_screen).'">'
            . '<div id="burger-ta-trd1">'
            . '<div class="line"></div>'
            . '<div class="line"></div>'
            . '<div class="line"></div>'
            . '</div>'
            . ''.esc_html($flyout_menu_custom_menu_label).''
            . '</a>'
            . '</li>';
    } elseif ($flyout_menu_button_menu_style == 'style-2') {
        wp_enqueue_style('flyout-menu-awesome-btn2', plugin_dir_url(__FILE__) . 'public/css/flyout-menu-awesome-btn2.css', array(), '', 'all');
        $items .= '<li class="menu-item">'
            . '<a id="trigger" href="#" class="btn-activate-flyout-tgt menu-trigger '.esc_attr($flyout_menu_button_menu_position_screen).'">'
            . '<label for="toggle" class="burger-menu">'
            . '<div class="burger-menu-lines">'
            . '<div class="line"></div>'
            . '<div class="line"></div>'
            . '<div class="line"></div>'
            . '</div>'
            . '</label>'
            . '<input type="checkbox" id="toggle" class="invisible">'
            . ''.esc_html($flyout_menu_custom_menu_label).''
            . '</a>'
            . '</li>';
    } else {
        $items .= '<li class="menu-item">'
            . '<a id="trigger" href="#">'
            . ''.esc_html($flyout_menu_custom_menu_label).''
            . '</a>'
            . '</li>';
    }
    return $items;
}

if ($flyout_menu_menu_button_style_choice == 'wordpress-menu') {
    add_filter('wp_nav_menu_items', 'flyout_menu_awesome_custom_menu_btn', 10, 2);
}

function flyout_menu_awesome_menu_button_style()
{

    $flyout_menu_menu_button_style_choice = carbon_get_theme_option('flyout_menu_menu_button_style_choice');
    $flyout_menu_button_menu_style = carbon_get_theme_option('flyout_menu_button_menu_style');
    $flyout_menu_btn_icon_pos = carbon_get_theme_option('flyout_menu_btn_icon_pos');
    $flyout_menu_button_menu_position_screen = carbon_get_theme_option('flyout_menu_button_menu_position_screen');

    $flyout_menu_custom_menu_label = carbon_get_theme_option('flyout_menu_custom_menu_label');

    if (!empty($flyout_menu_custom_menu_label)) {
        $ta_flyout_btn_label = esc_html($flyout_menu_custom_menu_label);
    } else {
        $ta_flyout_btn_label = '';
    }

    if ($flyout_menu_menu_button_style_choice == 'manual') {
        if ($flyout_menu_button_menu_style == 'style-1') {
            wp_enqueue_style('flyout-menu-awesome-btn1', plugin_dir_url(__FILE__) . 'public/css/flyout-menu-awesome-btn1.css', array(), '', 'all'); ?>

            <button id="trigger" class="btn-activate-flyout btn-activate-flyout-tgt menu-trigger <?php echo esc_attr($flyout_menu_button_menu_position_screen); ?>">
                <?php if ($flyout_menu_btn_icon_pos == 'left') { ?>
                    <div id="burger-ta-trd1">
                        <div class="line"></div>
                        <div class="line"></div>
                        <div class="line"></div>
                    </div>
                <?php } ?>
                <div class="text-btn-klas">
                    <?php echo esc_html($ta_flyout_btn_label); ?>
                </div>
                <?php if ($flyout_menu_btn_icon_pos == 'right') { ?>
                    <div id="burger-ta-trd1">
                        <div class="line"></div>
                        <div class="line"></div>
                        <div class="line"></div>
                    </div>
                <?php } ?>
            </button>
        <?php }
        if ($flyout_menu_button_menu_style == 'style-2') {
            wp_enqueue_style('flyout-menu-awesome-btn2', plugin_dir_url(__FILE__) . 'public/css/flyout-menu-awesome-btn2.css', array(), '', 'all'); ?>

            <button id="trigger" class="btn-activate-flyout btn-activate-flyout-tgt menu-trigger <?php echo esc_attr($flyout_menu_button_menu_position_screen); ?>">
                <?php if ($flyout_menu_btn_icon_pos == 'left') { ?>
                    <label for="toggle" class="burger-menu">
                        <div class="burger-menu-lines">
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                        </div>
                        <div class="burger-menu-close-icon"></div>
                    </label>
                    <input type="checkbox" id="toggle" class="invisible">
                <?php } ?>
                <div class="text-btn-klas">
                    <?php echo esc_html($ta_flyout_btn_label); ?>
                </div>
                <?php if ($flyout_menu_btn_icon_pos == 'right') { ?>
                    <label for="toggle" class="burger-menu">
                        <div class="burger-menu-lines">
                            <div class="line"></div>
                            <div class="line"></div>
                            <div class="line"></div>
                        </div>
                        <div class="burger-menu-close-icon"></div>
                    </label>
                    <input type="checkbox" id="toggle" class="invisible">
                <?php } ?>
            </button>
        <?php }
    }
}

// flyout_menu_awesome head css
add_action('wp_head', 'flyout_menu_awesome_color_custom_styles', 100);
function flyout_menu_awesome_color_custom_styles()
{
    ?>

 <style>
        <?php
        $flyout_menu_layout_background_color = carbon_get_theme_option('flyout_menu_layout_background_color');
        $flyout_menu_layout_background_color_sub = carbon_get_theme_option('flyout_menu_layout_background_color_sub');
        $flyout_menu_layout_background_image = carbon_get_theme_option('flyout_menu_layout_background_image');
        $flyout_menu_layout_background_size = carbon_get_theme_option('flyout_menu_layout_background_size');
        $flyout_menu_layout_background_repeat = carbon_get_theme_option('flyout_menu_layout_background_repeat');
        $flyout_menu_layout_background_position = carbon_get_theme_option('flyout_menu_layout_background_position');

        $flyout_menu_layout_background_color_no_active = carbon_get_theme_option('flyout_menu_layout_background_color_no_active');
        $flyout_menu_layout_background_color_content_no_active = carbon_get_theme_option('flyout_menu_layout_background_color_content_no_active');
        $flyout_menu_font_color = carbon_get_theme_option('flyout_menu_font_color');
        $flyout_menu_font_hov_color = carbon_get_theme_option('flyout_menu_font_hov_color');
        $flyout_menu_font_size = carbon_get_theme_option('flyout_menu_font_size');
        $flyout_menu_font_weight = carbon_get_theme_option('flyout_menu_font_weight');
        $flyout_menu_line_height = carbon_get_theme_option('flyout_menu_line_height');

        $flyout_menu_icon_position = carbon_get_theme_option('flyout_menu_icon_position');
        $flyout_menu_icon_color = carbon_get_theme_option('flyout_menu_icon_color');
        $flyout_menu_icon_hov_color = carbon_get_theme_option('flyout_menu_icon_hov_color');
        $flyout_menu_icon_size = carbon_get_theme_option('flyout_menu_icon_size');
        $flyout_menu_button_menu_margin_left = carbon_get_theme_option('flyout_menu_button_menu_margin_left');
        $flyout_menu_button_menu_margin_bottom = carbon_get_theme_option('flyout_menu_button_menu_margin_bottom');
        $flyout_menu_button_menu_margin_right = carbon_get_theme_option('flyout_menu_button_menu_margin_right');
        $flyout_menu_button_menu_margin_top = carbon_get_theme_option('flyout_menu_button_menu_margin_top');
        $flyout_menu_button_menu_round = carbon_get_theme_option('flyout_menu_button_menu_round');
        $flyout_menu_button_menu_round_unit = carbon_get_theme_option('flyout_menu_button_menu_round_unit');
        $flyout_menu_btn_txt_color = carbon_get_theme_option('flyout_menu_btn_txt_color');
        $flyout_menu_btn_bg_color = carbon_get_theme_option('flyout_menu_btn_bg_color');
        $flyout_menu_btn_txt_hov_color = carbon_get_theme_option('flyout_menu_btn_txt_hov_color');
        $flyout_menu_btn_bg_hov_color = carbon_get_theme_option('flyout_menu_btn_bg_hov_color');
        $flyout_menu_button_menu_position_screen = carbon_get_theme_option('flyout_menu_button_menu_position_screen');
        $flyout_menu_button_menu_width = carbon_get_theme_option('flyout_menu_button_menu_width');
        $flyout_menu_button_menu_height = carbon_get_theme_option('flyout_menu_button_menu_height');
        $flyout_menu_btn_icon_pos = carbon_get_theme_option('flyout_menu_btn_icon_pos');
        $flyout_menu_space_between = carbon_get_theme_option('flyout_menu_space_between');
        $flyout_menu_btn_font_size = carbon_get_theme_option('flyout_menu_btn_font_size');
        $flyout_menu_btn_font_weight = carbon_get_theme_option('flyout_menu_btn_font_weight');
        $flyout_menu_arrow_color = carbon_get_theme_option('flyout_menu_arrow_color');
        $flyout_menu_border_color = carbon_get_theme_option('flyout_menu_border_color');
        $flyout_menu_sub_menu_border_color = carbon_get_theme_option('flyout_menu_sub_menu_border_color');
        $flyout_menu_active_color = carbon_get_theme_option('flyout_menu_active_color');
        $flyout_menu_icon_bg_color = carbon_get_theme_option('flyout_menu_icon_bg_color');

        
        $flyout_icon_color = carbon_get_theme_option('flyout_icon_color');
        $flyout_icon_color_hover = carbon_get_theme_option('flyout_icon_color_hover');
        $flyout_bg_icon_color = carbon_get_theme_option('flyout_bg_icon_color');
        $flyout_bg_icon_color_hover = carbon_get_theme_option('flyout_bg_icon_color_hover');
        $flyout_info_text_color = carbon_get_theme_option('flyout_info_text_color');

        ?>
        <?php
        if (!empty($flyout_menu_layout_background_color)) { ?>
            .mp-menu .mp-level {
                background-color: <?php echo esc_html($flyout_menu_layout_background_color); ?>;
            }

            .flyout5-pattern {
                background-color: <?php echo esc_html($flyout_menu_layout_background_color); ?>;
            }
        <?php } 
        if (!empty($flyout_menu_layout_background_color_sub)) { ?>
            .mp-menu .mp-level .mp-level {
                background: <?php echo esc_html($flyout_menu_layout_background_color_sub); ?>;
            }

            .fly-style-genep .mp-level .mp-level {
                background-color: <?php echo esc_html($flyout_menu_layout_background_color_sub); ?>;
            }

            .flyout-style5 .mp-level {
                background-color: <?php echo esc_html($flyout_menu_layout_background_color_sub); ?>;
            }
        <?php }
        if (!empty($flyout_menu_layout_background_image)) {
            $flyout_menu_bg_img = wp_get_attachment_image_src($flyout_menu_layout_background_image, 'full'); ?>
            .mp-menu .mp-level,
            .pattern-bg-menu {
                background-image: url(<?php echo esc_html($flyout_menu_bg_img[0]); ?>);
                <?php if (!empty($flyout_menu_layout_background_size)) { ?>
                    background-size: <?php echo esc_html($flyout_menu_layout_background_size); ?>;
                <?php }
                if (!empty($flyout_menu_layout_background_repeat)) { ?>
                    background-repeat: <?php echo esc_html($flyout_menu_layout_background_repeat); ?>;
                <?php }
                if (!empty($flyout_menu_layout_background_position)) { ?>
                    background-position: <?php echo esc_html($flyout_menu_layout_background_position); ?>;
                <?php } ?>
            }
        <?php }
        if (!empty($flyout_menu_layout_background_color_no_active)) {?>
            .mp-level.mp-level-overlay.mp-level::before{
            background-color: <?php echo esc_html($flyout_menu_layout_background_color_no_active);?>;
            }
        <?php }
        if (!empty($flyout_menu_layout_background_color_content_no_active)) {?>
        .mp-pusher::after, .mp-level::after{
            background-color: <?php echo esc_html($flyout_menu_layout_background_color_content_no_active);?>;}
        }
        <?php }
        if (!empty($flyout_menu_font_color)) { ?>
            .mp-menu .flyout-menus .low a {
                color: <?php echo esc_html($flyout_menu_font_color); ?>;
            }
        <?php }
        if (!empty($flyout_menu_font_hov_color)) { ?>
            .mp-menu .flyout-menus .low a:hover {
                color: <?php echo esc_html($flyout_menu_font_hov_color); ?>;
            }
        <?php }
        if (!empty($flyout_menu_font_size)) { ?>
            .mp-menu .flyout-menus .low a {
                font-size: <?php echo esc_html($flyout_menu_font_size); ?>px;
            }
        <?php }
        if (!empty($flyout_menu_font_weight)) { ?>
            .mp-menu .flyout-menus .low a {
                font-weight: <?php echo esc_html($flyout_menu_font_weight); ?>;
            }
        <?php }
        if (!empty($flyout_menu_line_height)) { ?>
            .mp-menu .flyout-menus .low a {
                line-height: <?php echo esc_html($flyout_menu_line_height); ?>;
            }
        <?php }

        // menu icon
        if ($flyout_menu_icon_position == 'left') { ?>
            .mp-menu .flyout-menus .low a i {
                margin-right: 20px;
            }
        <?php } elseif ($flyout_menu_icon_position == 'right') { ?>
            .mp-menu .flyout-menus .low a i {
                margin-left: 20px;
            }
        <?php }
        if (!empty($flyout_menu_icon_color)) { ?>
            .mp-menu .flyout-menus .low a i {
                color: <?php echo esc_html($flyout_menu_icon_color); ?>;
            }
        <?php }
        if (!empty($flyout_menu_icon_hov_color)) { ?>
            .mp-menu .flyout-menus .low a:hover i {
                color: <?php echo esc_html($flyout_menu_icon_hov_color); ?>;
            }
        <?php }
        if (!empty($flyout_menu_icon_size)) { ?>
            .mp-menu .flyout-menus .low i {
                font-size: <?php echo esc_html($flyout_menu_icon_size); ?>px;
            }
            .mp-menu .flyout-menus .low a i img {
                width: <?php echo esc_html($flyout_menu_icon_size); ?>px;
                height: <?php echo esc_html($flyout_menu_icon_size); ?>px;
            }

            .flyout-style5 .low a i img {
                width: <?php echo esc_html($flyout_menu_icon_size); ?>px;
                height: <?php echo esc_html($flyout_menu_icon_size); ?>px;
            }

            .flyout-style5 .low a i {
                font-size: <?php echo esc_html($flyout_menu_icon_size); ?>px;
            }
        <?php }
        else { ?>
            .mp-menu .flyout-menus .low a i img {       
                width: 22px;
                height: 22px;
            }
        <?php }

        // menu button margin
        if (!empty($flyout_menu_button_menu_margin_left)) { ?>
            .btn-activate-flyout-tgt {
                margin-left: <?php echo esc_html($flyout_menu_button_menu_margin_left); ?>px;
            }
        <?php }
        if (!empty($flyout_menu_button_menu_margin_bottom)) { ?>
            .btn-activate-flyout-tgt {
                margin-bottom: <?php echo esc_html($flyout_menu_button_menu_margin_bottom); ?>px;
            }
        <?php }
        if (!empty($flyout_menu_button_menu_margin_right)) { ?>
            .btn-activate-flyout-tgt {
                margin-right: <?php echo esc_html($flyout_menu_button_menu_margin_right); ?>px;
            }
        <?php }
        if (!empty($flyout_menu_button_menu_margin_top)) { ?>
            .btn-activate-flyout-tgt {
                margin-top: <?php echo esc_html($flyout_menu_button_menu_margin_top); ?>px;
            }
        <?php }

        // button border radius
        if (!empty($flyout_menu_button_menu_round)) { ?>
            .btn-activate-flyout-tgt {
                border-radius: <?php echo esc_html($flyout_menu_button_menu_round); ?><?php echo esc_html($flyout_menu_button_menu_round_unit); ?>;
            }
        <?php }

        // Menu Button Text Color
        if (!empty($flyout_menu_btn_txt_color)) { ?>
            .btn-activate-flyout-tgt {
                color: <?php echo esc_html($flyout_menu_btn_txt_color); ?>;
            }
            .btn-activate-flyout-tgt #burger-ta-trd1 .line, .btn-activate-flyout-tgt .burger-menu-lines .line::before, .btn-activate-flyout-tgt .burger-menu-lines .line::after {
                background-color: <?php echo esc_html($flyout_menu_btn_txt_color); ?>;
            }
        <?php }
        if (!empty($flyout_menu_btn_bg_color)) { ?>
            .btn-activate-flyout-tgt {
                background-color: <?php echo esc_html($flyout_menu_btn_bg_color); ?>;
            }
        <?php }
        if (!empty($flyout_menu_btn_txt_hov_color)) { ?>
            .btn-activate-flyout-tgt:hover {
                color: <?php echo esc_html($flyout_menu_btn_txt_hov_color); ?>;
            }
            .btn-activate-flyout-tgt:hover .burger-menu-lines .line::before, .btn-activate-flyout-tgt:hover .burger-menu-lines .line::after, .btn-activate-flyout-tgt:hover #burger-ta-trd1 .line {
                background: <?php echo esc_html($flyout_menu_btn_txt_hov_color); ?>;
            }
        <?php }

        if (!empty($flyout_menu_btn_bg_hov_color)) { ?>
            .btn-activate-flyout-tgt:hover {
                background: <?php echo esc_html($flyout_menu_btn_bg_hov_color); ?>;
            }
        <?php }

        // button menu position

        if (!empty($flyout_menu_button_menu_width)) { ?>
            .btn-activate-flyout-tgt {
                padding-left: <?php echo esc_html($flyout_menu_button_menu_width); ?>px;
                padding-right: <?php echo esc_html($flyout_menu_button_menu_width); ?>px;
            }
        <?php }
        if (!empty($flyout_menu_button_menu_height)) { ?>
            .btn-activate-flyout-tgt {
                padding-top: <?php echo esc_html($flyout_menu_button_menu_height); ?>px;
                padding-bottom: <?php echo esc_html($flyout_menu_button_menu_height); ?>px;
            }
        <?php }

        // menu button icon
        if ($flyout_menu_btn_icon_pos == 'left') {
            if (!empty($flyout_menu_space_between)) { ?>
                .btn-activate-flyout-tgt #burger-ta-trd1 {
                    margin-right: <?php echo esc_html($flyout_menu_space_between); ?>px;
                }
            <?php }
        } elseif ($flyout_menu_btn_icon_pos == 'right') {
            if (!empty($flyout_menu_space_between)) { ?>
                .btn-activate-flyout-tgt #burger-ta-trd1 {
                    margin-left: <?php echo esc_html($flyout_menu_space_between); ?>px;
                }
            <?php }
        }

        if (!empty($flyout_menu_btn_font_size)) { ?>
            .btn-activate-flyout-tgt {
                font-size: <?php echo esc_html($flyout_menu_btn_font_size); ?>px;
            }
        <?php }
        if (!empty($flyout_menu_btn_font_weight)) { ?>
            .btn-activate-flyout-tgt {
                font-weight: <?php echo esc_html($flyout_menu_btn_font_weight); ?>;
            }
        <?php }
        if (!empty($flyout_menu_arrow_color)) { ?>
            .flyout-container .mp-menu .flyout-menus .low.has-sub-menu:before, 
            .flyout-container .mp-menu .flyout-menus .low.menu-item-has-children:before,
            .flyout-container .mp-menu .flyout-menus .mp-back::after,
            .mp-menu .flyout-menus .low.menu-item-has-children .djan a:before {
                color: <?php echo esc_html($flyout_menu_arrow_color); ?>;
            }
        <?php }
        if (!empty($flyout_icon_color)) { ?>
            .info-web-wrap .social-wrap-menu .social-item a {
                color: <?php echo esc_html($flyout_icon_color); ?>;
            }
        <?php }
        if (!empty($flyout_icon_color_hover)) { ?>
            .info-web-wrap .social-wrap-menu .social-item a:hover {
                color: <?php echo esc_html($flyout_icon_color_hover); ?>;
            }
        <?php }
        if (!empty($flyout_bg_icon_color)) { ?>
            .info-web-wrap .social-wrap-menu .social-item a {
                background-color: <?php echo esc_html($flyout_bg_icon_color); ?>;
            }
        <?php }
        if (!empty($flyout_bg_icon_color_hover)) { ?>
            .info-web-wrap .social-wrap-menu .social-item a:hover {
                background-color: <?php echo esc_html($flyout_bg_icon_color_hover); ?>;
            }
        <?php }
        if (!empty($flyout_info_text_color)) { ?>
            .info-web-wrap .copyright-text span,
            .info-web-wrap .copyright-text {
                color: <?php echo esc_html($flyout_info_text_color); ?>;
            }
        <?php }
        if(!empty($flyout_menu_border_color)) { ?>
            .mp-menu .flyout-menus .low a {
                box-shadow: inset 0 -1px <?php echo esc_html($flyout_menu_border_color); ?>;
            }
        <?php }
        if(!empty($flyout_menu_sub_menu_border_color)) { ?>
            .mp-menu .mp-level .mp-level .low a {
                box-shadow: inset 0 -1px <?php echo esc_html($flyout_menu_sub_menu_border_color); ?>;
            }
        <?php }
        if(!empty($flyout_menu_active_color)) { ?>
            .fly-style-genep .mp-menu .low.active-menu > .djan a {
                background-color: <?php echo esc_html($flyout_menu_active_color); ?>;
            }

            .flyout-style5 .low.active-menu > .djan a {
                background-color: <?php echo esc_html($flyout_menu_active_color); ?>;
            }
        <?php }
        if(!empty($flyout_menu_icon_bg_color)) { ?>
            .fly-style-genep .mp-menu .low a i {
                background-color: <?php echo esc_html($flyout_menu_icon_bg_color); ?>;
            }

            .flyout-style5 .low a i {
                background-color: <?php echo esc_html($flyout_menu_icon_bg_color); ?>;
            }
        <?php } ?>

        <?php wp_reset_postdata(); ?>
    </style>

<?php }
