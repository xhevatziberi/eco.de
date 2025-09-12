<?php
// Hide specific admin menu items (example: only for non-admins)
function hide_admin_menu_items() {

    // If you only want to hide for non-admin users, uncomment this:
    // if ( current_user_can( 'administrator' ) ) {
    //     return;
    // }

    // Skip contexts where the admin menu isn't built
    if (
        (defined('DOING_AJAX') && DOING_AJAX) ||
        (defined('REST_REQUEST') && REST_REQUEST) ||
        (defined('DOING_CRON') && DOING_CRON)
    ) {
        return;
    }

    // Top-level menu removals:
    remove_menu_page( 'admin.php?page=vamtam_theme_setup' );
    remove_menu_page( 'admin.php?page=envato-elements' );

    // If a target doesn't disappear, it might be a submenu instead.
    // In that case, use remove_submenu_page( $parent_slug, $menu_slug ), e.g.:
    // remove_submenu_page( 'themes.php', 'vamtam_theme_setup' );
    // remove_submenu_page( 'themes.php', 'envato-elements' );
}
// Run late so $menu is fully populated
add_action( 'admin_menu', 'hide_admin_menu_items', 999 );
