<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Declicsweb_Slide_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;


	public $repeatable_fieldset_settings;

	public $settings;
	

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}
 

	/**
	 * Register the filters and actions with WordPress. 
	 *
	 * @since    1.0.0
	 */
	public function run() {
		// about the data - admin and front -> in wordpress : register_post_type
		$hook2 = $this->register_post_type_actions();

		// Load the widget
		//add_action( 'widgets_init', array( $this, 'load_custom_widgets' ) );


		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}


	// /***   */
	public function register_post_type_actions() {
		$parent = 'test'; // @TODO
		$post_type = 'declicsweb_slide';
        $name    =  'declicsweb_slide';
		$this->namePot = $post_type;
		$this->wp_nonce_field_token = $post_type;
		$this->plugin_prefix = $post_type;
		$this->single      = $name;
		$this->post_type      = $post_type;
		$this->description = '';
		$this->construct_repeatable_fieldset_settings();
		$this->construct_meta_data_settings();

		// Regsiter post type.
		add_action( 'init', array( $this, 'construct_register_post_type' ));
		//$this->loader->add_action('init', array( $this, 'main_register_post_type' ), null);


		$post_type = new Declicsweb_Slide_Post_Type('test', $post_type, $name, $this->settings, $this->repeatable_fieldset_settings);
		
		return $post_type;
	}


	public function load_custom_widgets() {
		register_widget( 'Declicsweb_Slide_Widget' );
	}


	/* Utility function for sanitizing form control values */
	public function sanitize_field( $value, $type ) {
		switch( $type ) {
			case 'text':
			case 'email':
			case 'password':
				$value = sanitize_text_field( $value );
			break;

			case 'number':
				$value = intval( $value );
			break;

			case 'float':
			case 'milliseconds':
			case 'percentage':
			case 'range':
				$value = floatVal( $value );
			break;

			case 'color':
				$value = sanitize_hex_color( $value );
			break;
			
			case 'url':
				$value = esc_url( $value );
			break;
			
			case 'textarea':
				$value = sanitize_textarea_field( $value );
			break;

			case 'html':
				$value = wp_kses( $value, array(
					'a' => array(
						'href' => array(),
						'title' => array(),
						'target' => array()
					),
					'img' => array(
						'src' => array(),
						'height' => array(),
						'width' => array()
					),
					'ol' => array(),
					'ul' => array(),
					'li' => array(),
					'br' => array(),
					'em' => array(),
					'strong' => array(),
				) );
			break;
			
			case 'tinymce':
				$value = $value;
			break;

			case 'checkbox':
					$value = intval( (bool) $value );
			break;
			
			case 'media_upload':
				$value = intval( $value );
			break;
		}
		
		return $value;
	}
	
	function getIfSet( &$var, $defaultValue ) {
		if(isset($var)) {
			return $var;
		} else {
			return $defaultValue;
		}
	}

	public function get_default_value( $id, $settings_array ) {
		return $this->$settings_array['fields'][$id]['default'];
	}

	/**
	 * Create Slider Shortcode
	 */
	function declicsweb_slide_shortcode( $atts ) {
		// Extract attributes passed to shortcode
		extract( shortcode_atts( array(
			'id' => ''
		), $atts ) );
		
		ob_start();
		//include( $this->parent->assets_dir .'/template-parts/slider.php' );
		return ob_get_clean();		
	}

	function create_slider( $id ) {
		ob_start();
		//include( $this->parent->assets_dir .'/template-parts/slider.php' );
		return ob_get_clean();
	}



	// CUSTUM DATAS FOR THE PLUGIN
	/**
	 * Register new post type, the postype aka the parent object the plugin will create
	 *
	 * @return void
	 */
	public function construct_register_post_type() {
        // Create the Custom Post Type and a Taxonomy for the 'declicsweb_slide' Post Type
        
		$labels = array(
            'label'              => $this->single,
            'name_admin_bar'     => $this->single,
			'name'               => $this->single,
			'singular_name'      => $this->single,
			'name_admin_bar'     => $this->single,
			'add_new'            => _x( 'Add New', $this->post_type, $this->namePot ),
			'add_new_item'       => sprintf( __( 'Add New %s', $this->namePot ), $this->single ),
			'edit_item'          => sprintf( __( 'Edit %s', $this->namePot ), $this->single ),
			'new_item'           => sprintf( __( 'New %s', $this->namePot ), $this->single ),
			'all_items'          => sprintf( __( 'All %s', $this->namePot ), $this->single ),
			'view_item'          => sprintf( __( 'View %s', $this->namePot ), $this->single ),
			'search_items'       => sprintf( __( 'Search %s', $this->namePot ), $this->single ),
			'not_found'          => sprintf( __( 'No %s Found', $this->namePot ), $this->single ),
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', $this->namePot ), $this->single ),
			'parent_item_colon'  => sprintf( __( 'Parent %s' ), $this->single ),
			'menu_name'          => $this->single,
		);
		//phpcs:enable

		$args = array(
			'labels'                => apply_filters( $this->post_type . '_labels', $labels ),
			'description'           => $this->description,
			'public'                => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'query_var'             => true,
			'can_export'            => true,
			'rewrite'               => true,
			'capability_type'       => 'post',
			'has_archive'           => false,
			'hierarchical'          => false,
			'show_in_rest'          => true,
			'rest_base'             => $this->post_type,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'              => array( 'title' ),
			'menu_position'         => 81,
			//'menu_icon'             => 'dashicons-admin-post',
		);

		$args = array_merge( $args, $labels );
		register_post_type($this->post_type, $args);
		//register_post_type( $this->post_type, apply_filters( $this->post_type . '_register_args', $args, $this->post_type ) );
	}


	public function construct_meta_data_settings (){

		$this->settings = array(
			'repeatable' => false,
			'fields' => array(
				$this->plugin_prefix.'_has_min_width' => array(
					'type'			=> 'checkbox',
					'default'		=> false,
					'hasDependents'	=> true,
					'class'			=> '',
					'description'	=> __( 'Slider has a minimum width', $this->namePot ),
				),
				$this->plugin_prefix.'_min_width' => array(
					'type'			=> 'pixels',
					'placeholder'	=> '',
					'suffix'		=> 'px',
					'class'			=> 'full-width',
					'default'		=> 600,
				),
				$this->plugin_prefix.'_speed' => array(
					'label'			=> __( 'Transition Speed', $this->namePot ),
					'type'			=> 'milliseconds',
					'placeholder'	=> '',
					'suffix'		=> 'ms',
					'class'			=> 'full-width',
					'default'		=> 450,
					'description' => __( 'The speed it takes to transition between slides in milliseconds. 1000 milliseconds equals 1 second.',  $this->namePot )
				)
			)
		);		
	}

	public function construct_repeatable_fieldset_settings (){
		
		$this->repeatable_fieldset_settings = array(
			'repeatable' => true,
			'fields' => array(
				$this->plugin_prefix.'_item_image' => array(
					'type'			=> 'media_upload',
					'class'			=> '',
					'description'	=> '',
				),
				$this->plugin_prefix.'_item_title' => array(
					'type'			=> 'text',
					'placeholder'	=> __( 'Heading', $this->namePot ),
					'class'			=> '',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_text' => array(
					'type'			=> 'html',
					'placeholder'	=> __( 'Text', $this->namePot ),
					'class'			=> '',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_button_1_text' => array(
					'type'			=> 'text',
					'placeholder'	=> __( 'Button Text', $this->namePot ),
					'class'			=> 'full-width text',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_button_1_link_content' => array(
					'type'			=> 'dropdown_pages_posts',
					'class'			=> 'full-width link_content',
					'description' => ''
				),
				$this->plugin_prefix.'_item_button_1_link_target' => array(
					'type'			=> 'select',
					'options'		=> array(
						'' 			  => __( 'Open the link in...', $this->namePot ),
						'same-window' => __( 'The Same Window', $this->namePot ),
						'new-window'  => __( 'A New Window', $this->namePot )
					),
					'placeholder'	=> '',
					'class'			=> 'full-width link_target',
					'default'		=> 'placeholder'
				),
				$this->plugin_prefix.'_item_button_1_link_custom_url' => array(
					'type'			=> 'url',
					'placeholder'	=> __( 'Add a URL to link to', $this->namePot ),
					'class'			=> 'full-with link_custom_url',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_image_title' => array(
					'type'			=> 'text',
					'placeholder'	=> __( 'Image Title Text', $this->namePot ),
					'class'			=> '',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_image_alt' => array(
					'type'			=> 'text',
					'placeholder'	=> __( 'Image Alt Text', $this->namePot ),
					'class'			=> '',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_overlay_opacity' => array(
					'label'			=> __( 'Overlay Opacity', $this->namePot ),
					'type'			=> 'range',
					'default'		=> 0,
					'show_labels'	=> true,
					'min_labels' 	=> true,
			    	'input_attrs' => array(
			    		'min'   => 0,
			    		'max'   => 1,
			    		'step'  => 0.1,
			    		'style' => 'color: #000000',
			    	),
					'placeholder'	=> '',
					'class'			=> '',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_text_overlay_opacity' => array(
					'label'			=> __( 'Text Overlay Opacity', $this->namePot ),
					'type'			=> 'range',
					'default'		=> 0.3,
					'show_labels'	=> true,
					'min_labels' 	=> true,
			    	'input_attrs' => array(
			    		'min'   => 0,
			    		'max'   => 1,
			    		'step'  => 0.1,
			    		'style' => 'color: #000000',
			    	),
					'placeholder'	=> '',
					'class'			=> '',
					'description'	=> ''
				),
				$this->plugin_prefix.'_item_text_overlay_text_shadow' => array(
					'label'			=> __( 'Text Shadow', $this->namePot ),
					'type'			=> 'checkbox',
					'default'		=> false,
					'class'			=> '',
					'description' 	=> __( 'Display a drop shadow on the text overlay text', $this->namePot ),
				)
			)
		);
	}

}
