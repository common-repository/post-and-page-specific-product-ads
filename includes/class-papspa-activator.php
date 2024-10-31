<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/includes
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA_Activator {

	public static function papspa_activate() {
		$plugin = new PAPSPA();
		flush_rewrite_rules();
		$plugin->papspa_create_default_settings();
		update_option('papspa_version',PAPSPA_VERSION);
	}
	
}
