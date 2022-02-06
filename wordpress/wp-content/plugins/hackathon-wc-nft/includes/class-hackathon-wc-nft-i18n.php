<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://hackathon-wc-nft.alexpedersen.dev
 * @since      1.0.0
 *
 * @package    Hackathon_Wc_Nft
 * @subpackage Hackathon_Wc_Nft/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Hackathon_Wc_Nft
 * @subpackage Hackathon_Wc_Nft/includes
 * @author     Your Name <email@example.com>
 */
class Hackathon_Wc_Nft_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'hackathon-wc-nft',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
