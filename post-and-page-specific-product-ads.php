<?php
/**
 * Post and Page Specific Product Ads by Grega Radelj
 *
 * @link              https://grrega.com/projects/post-and-page-specific-product-ads
 * @since             1.0.0
 * @package           PAPSPA
 *
 * @wordpress-plugin
 * Plugin Name:       Post and Page Specific Product Ads
 * Plugin URI:        https://grrega.com/projects/post-and-page-specific-product-ads
 * Description:       Advertise your products on your blog. This plugin allows you to select which products to show in a particular post, page or category.
 * Version:           1.0.4
 * Author:            Grega Radelj
 * Author URI:        https://grrega.com/
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.txt
 * Text Domain:       post-and-page-specific-product-ads
 * Domain Path:       /languages 
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5.0
 *
 * Copyright 2018 Grrega.com  (email : info@grrega.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'PAPSPA_VERSION', '1.0.4' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-papspa-activator.php
 */
function activate_papspa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-papspa-activator.php';
	PAPSPA_Activator::papspa_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-papspa-deactivator.php
 */
function deactivate_papspa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-papspa-deactivator.php';
	PAPSPA_Deactivator::papspa_deactivate();
}

register_activation_hook( __FILE__, 'activate_papspa' );
register_deactivation_hook( __FILE__, 'deactivate_papspa' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-papspa.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_post_specific_product_ads() {
	//currently supporting only woocommerce
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  {
		add_action('admin_notices', 'papspa_admin_notice_woocommerce_not_active'); return false; 
	}
	else{
		$plugin = new PAPSPA();
		$plugin->papspa_run();
	}

}
run_post_specific_product_ads();

function papspa_admin_notice_woocommerce_not_active(){
    echo '<div class="notice notice-error">
          <p>'.__('Post and Page Specific Product Ads is enabled but not effective. It requires WooCommerce in order to work.','post-and-page-specific-product-ads').'</p>
         </div>';
}

