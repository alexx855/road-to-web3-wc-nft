<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://hackathon-wc-nft.alexpedersen.dev
 * @since             1.0.1
 * @package           Hackathon_Wc_Nft
 *
 * @wordpress-plugin
 * Plugin Name:       Hackathon WC NFT
 * Plugin URI:        https://hackathon-wc-nft.alexpedersen.dev/
 * Description:       An EthGlobal hackathon project plugin: create, mint, and sell NFTs directly from your current wordpress website
 * Version:           1.0.0
 * Author:            @alexx855
 * Author URI:        https://alexpedersen.dev/
 * License:           No licence
 * Text Domain:       hackathon-wc-nft
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

// define('HACKATHON_WC_NFT_VERSION', time());
define('HACKATHON_WC_NFT_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hackathon-wc-nft-activator.php
 */
function activate_hackathon_wc_nft()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-hackathon-wc-nft-activator.php';
	Hackathon_Wc_Nft_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hackathon-wc-nft-deactivator.php
 */
function deactivate_hackathon_wc_nft()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-hackathon-wc-nft-deactivator.php';
	Hackathon_Wc_Nft_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_hackathon_wc_nft');
register_deactivation_hook(__FILE__, 'deactivate_hackathon_wc_nft');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-hackathon-wc-nft.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_hackathon_wc_nft()
{

	$plugin = new Hackathon_Wc_Nft();
	$plugin->run();
}
run_hackathon_wc_nft();
