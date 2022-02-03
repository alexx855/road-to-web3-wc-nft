<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Hackathon_Wc_Nft
 * @subpackage Hackathon_Wc_Nft/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hackathon_Wc_Nft
 * @subpackage Hackathon_Wc_Nft/admin
 * @author     Your Name <email@example.com>
 */
class Hackathon_Wc_Nft_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hackathon_wc_nft    The ID of this plugin.
	 */
	private $hackathon_wc_nft;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $hackathon_wc_nft       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $hackathon_wc_nft, $version ) {

		$this->hackathon_wc_nft = $hackathon_wc_nft;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hackathon_Wc_Nft_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hackathon_Wc_Nft_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->hackathon_wc_nft, plugin_dir_url( __FILE__ ) . 'css/hackathon-wc-nft-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hackathon_Wc_Nft_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hackathon_Wc_Nft_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->hackathon_wc_nft, plugin_dir_url( __FILE__ ) . 'js/hackathon-wc-nft-admin.js', array( 'jquery' ), $this->version, false );

	}

}
