<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Declicsweb_Slide
 * @subpackage declicsweb-slide/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Declicsweb_Slide
 * @subpackage declicsweb-slide/includes
 * @author     Alex P <declicsweb@gmail.com>
 * 
 */

class Declicsweb_Slide_i18n {
	/**
	 * The name for POT file (language).
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */	
    public $namePot = 'declicsweb_slide';

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->namePot,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
