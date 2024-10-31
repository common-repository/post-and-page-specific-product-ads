<?php
/**
 * Define the internationalization functionality.
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/includes
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA_i18n {

	public function papspa_load_plugin_textdomain() {

		load_plugin_textdomain(
			'post-and-page-specific-product-ads',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
