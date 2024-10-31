<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/includes
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA_Deactivator {

	public static function papspa_deactivate() {
		flush_rewrite_rules();

	}

}
