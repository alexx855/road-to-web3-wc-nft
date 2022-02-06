<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://hackathon-wc-nft.alexpedersen.dev
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
class Hackathon_Wc_Nft_Admin
{

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
	public function __construct($hackathon_wc_nft, $version)
	{

		$this->hackathon_wc_nft = $hackathon_wc_nft;
		$this->version = $version;
	}

	/**
	 * Include the setting page
	 *
	 * @since  1.0.0
	 * @access public
	 */
	function wc_nft_init()
	{
		if (!current_user_can('manage_options')) {
			return;
		}

		include plugin_dir_path(__FILE__) . 'partials/hackathon-wc-nft-admin-display.php';
	}

	public function wc_nft_plugin_setup_menu()
	{
		add_menu_page('WC NFT settings', 'WC NFT settings', 'manage_options', 'hackathon-wc-nft', array($this, 'wc_nft_init'), 'dashicons-welcome-learn-more');
	}

	public function register_wc_nft_plugin_settings()
	{
		// Add a General section
		add_settings_section(
			'wc_nft_general',
			__('General', 'hackathon-wc-nft'),
			array($this, 'wc_nft_general_cb'),
			'wc_nft'
		);

		// Add fields
		add_settings_field(
			'wc_nft_nftport_api_key',
			__('NFTPort API Key', 'hackathon-wc-nft'),
			array($this, 'wc_nft_nftport_api_key_cb'),
			'wc_nft',
			'wc_nft_general',
			array('label_for' => 'wc_nft_nftport_api_key')
		);

		// Add fields
		add_settings_field(
			'wc_nft_moralis_app_id',
			__('Moralis App ID', 'hackathon-wc-nft'),
			array($this, 'wc_nft_moralis_app_id_cb'),
			'wc_nft',
			'wc_nft_general',
			array('label_for' => 'wc_nft_moralis_app_id')
		);

		// Add fields
		add_settings_field(
			'wc_nft_moralis_server_url',
			__('Moralis Server URL', 'hackathon-wc-nft'),
			array($this, 'wc_nft_moralis_server_url_cb'),
			'wc_nft',
			'wc_nft_general',
			array('label_for' => 'wc_nft_moralis_server_url')
		);

		register_setting('wc_nft', 'wc_nft_nftport_api_key', 'string');
		register_setting('wc_nft', 'wc_nft_moralis_app_id', 'string');
		register_setting('wc_nft', 'wc_nft_moralis_server_url', 'string');
	}

	public function wc_nft_general_cb()
	{
		echo '<p>' . __('Please change the settings accordingly.', 'hackathon-wc-nft') . '</p>';
	}

	public function wc_nft_nftport_api_key_cb()
	{
		$val = get_option('wc_nft_nftport_api_key');
		echo '<input type="text" name="wc_nft_nftport_api_key" id="wc_nft_nftport_api_key" value="' . $val . '"> ' . __(' <a href="https://www.nftport.xyz/" target="_blank">get it from nftport</a>', 'hackathon-wc-nft');
	}
	public function wc_nft_moralis_app_id_cb()
	{
		$val = get_option('wc_nft_moralis_app_id');
		echo '<input type="text" name="wc_nft_moralis_app_id" id="wc_nft_moralis_app_id" value="' . $val . '"> ' . __(' <a href="https://moralis.io/" target="_blank">get it from moralis</a>', 'hackathon-wc-nft');
	}
	public function wc_nft_moralis_server_url_cb()
	{
		$val = get_option('wc_nft_moralis_server_url');
		echo '<input type="text" name="wc_nft_moralis_server_url" id="wc_nft_moralis_server_url" value="' . $val . '"> ' . __(' <a href="https://moralis.io/" target="_blank">get it from moralis</a>', 'hackathon-wc-nft');
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

		wp_enqueue_style($this->hackathon_wc_nft, plugin_dir_url(__FILE__) . 'css/hackathon-wc-nft-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		wp_enqueue_script($this->hackathon_wc_nft, plugin_dir_url(__FILE__) . 'js/hackathon-wc-nft-admin.js', array('jquery'), $this->version, false);
	}
}
