<?php

/**
 *
 * @link              @TODO
 * @since             1.0.0
 * @package           Declicsweb_Slide
 *
 * @wordpress-plugin
 * Plugin Name:       Declicsweb Slide
 * Plugin URI:        @TODO
 * Description:       My new plugin for displaying content.
 * Version:           1.0.0
 * Author:            Alex P
 * Author URI:        http://declics.eu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DECLICSWEB_SLIDE_VERSION', '1.0.0' );
define( 'DECLICSWEB_SLIDE_DEBUG', true );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-declicsweb_slide-activator.php
 */
function activate_declicsweb_slide() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-declicsweb-slide-activator.php';
	Declicsweb_Slide_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-declicsweb_slide-deactivator.php
 */
function deactivate_declicsweb_slide() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-declicsweb-slide-deactivator.php';
	Declicsweb_Slide_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_declicsweb_slide' );
register_deactivation_hook( __FILE__, 'deactivate_declicsweb_slide' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-declicsweb-slide.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_declicsweb_slide() {

	$plugin = new Declicsweb_Slide();
	$plugin->run();

}
run_declicsweb_slide();
