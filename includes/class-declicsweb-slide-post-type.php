<?php

/**
 * The file that defines the post type 
 *
 * A class definition that includes attributes and functions for post type,Post type declaration file
 *
 * @link       @TODO
 * @since      1.0.0
 *
 * @package    Declicsweb_Slide
 * @subpackage declicsweb-slide/includes
 * @since      1.0.0
 * @package    Declicsweb_Slide
 * @subpackage declicsweb-slide/includes
 * @author     Alex P <declicsweb@gmail.com>

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post type declaration class.
 */
class Declicsweb_Slide_Post_Type {

	/**
	 * The single instance of Declicsweb_Slide_Post_Type.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;
	
	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;
	
	/**
	 * The name for the custom post type.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_type;

	/**
	 * The name for the custom post type.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $field;
	
	/**
	 * The plural name for the custom post type posts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plural;

	/**
	 * The singular name for the custom post type posts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $single;

	/**
	 * The description of the custom post type.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $description;

	/**
	 * The options of the custom post type.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $options;
	
	public $repeatable_fieldset_settings;

	/**
	 * The name for POT file (language).
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */	
    public $namePot = 'declicsweb_slide';

	/**
	 * The string to strengten the wp_nonce_field (is used to validate that the contents of the form came from the location on the current site and not somewhere else) 
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */	

	private $wp_nonce_field_token = 'declicsweb_slide'; // could be an array of 2 parameters

	/**
	 * Set the plugin name to variable to re-use code
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string
	 */	
	public $plugin_prefix = 'declicsweb_slide'; 
	/**
	 * Constructor
	 *
	 * @param string $post_type Post type.
	 * @param string $plural Post type plural name.
	 * @param string $single Post type singular name.
	 * @param string $description Post type description.
	 * @param array  $options Post type options.
	 */
	public function __construct( $parent, $post_type = '', $single = '', $description = '', $options = array() ) {

        $this->parent = $parent;
		
		// if ( ! $post_type || ! $single ) {
		// 	return;
		// }

		// Post type name and labels.
		$this->post_type   = $post_type;
		//$this->plural      = $plural;
		$this->single      = $single;
		$this->description = $description;
		$this->options     = $options;

		// Regsiter post type.
		add_action( 'init', array( $this, 'construct_register_post_type' ));
        //$this->loader->add_action('init', array( $this, 'main_register_post_type' ), null);

		// // Add custom meta boxes
		add_action( 'admin_init', array( $this, 'add_meta_boxes' ) );

		add_action( 'save_post_declicsweb_slide', array( $this, 'save_slides_meta' ) );
		// add_action( 'save_post_declicsweb_slide', array( $this, 'save_global_settings_meta' ) );

		// // Register shortcodes
		 add_shortcode( $this->plugin_prefix, array( $this, 'declicsweb_slide_shortcode' ) );

		// // Display custom update messages for posts edits.
		 add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}


	/*
	* Setup custom meta boxes
	*/
	public function add_meta_boxes() {

		// create the data fields
		$this->construct_repeatable_fieldset_settings();
		$this->construct_meta_data_settings();	

		// Create the Items Meta Boxes
		add_meta_box( $this->plugin_prefix.'-item-settings-group', __( 'Slides', $this->namePot ), array( $this, 'create_item_settings_meta_box' ), $this->post_type, 'normal', 'default' );
		//add_filter( 'postbox_classes_super-simple-slider_super-simple-slider-slide-settings-group', array( $this, 'add_metabox_classes' ) );
			
		// Create the Shortcode Meta Box
		add_meta_box( $this->plugin_prefix.'-shortcode-group', __( 'Shortcode', $this->namePot ), array( $this, 'create_shortcode_meta_box' ), $this->post_type, 'side', 'high' );
		
		// Create the Global Settings Meta Box
		add_meta_box( 'declicsweb-slider-global-settings-group', __( 'Global Settings', $this->namePot ), array( $this, 'create_global_settings_meta_box' ), $this->post_type, 'side', 'default' );
	}

	/*
	* Create repeatable slide fieldset
	*/
	public function create_item_settings_meta_box() {
		global $post;
		
		$slide_settings = get_post_meta( $post->ID, $this->plugin_prefix.'-item-settings-group', true );

		wp_nonce_field( $this->wp_nonce_field_token, $this->wp_nonce_field_token );
		?>
		
		<div class="<?php echo $this->plugin_prefix; ?>-postbox-container">

			<table class="<?php echo $this->plugin_prefix; ?>panel-container multi sortable repeatable" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody class="container">
					<?php
					// $hidden_panel = false;
					
					if ( $slide_settings ) :
						foreach ( $slide_settings as $setting ) {
							$this->field = $setting;
							include(plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/repeatable-panel-item.php' );
						}
					else : 
						// show a blank one
						include( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/repeatable-panel-item.php' );
					endif;
					
					$this->field = null;
					
					// Empty hidden panel used for creating a new panel
					$hidden_panel = true;
					include( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/repeatable-panel-item.php' );
					?>
				</tbody>
			</table>

			<div class="footer">				
				<div class="right">
					<a class="button add-repeatable-panel" href="#"><?php esc_html_e( 'Add Another Slide', $this->namePot ); ?></a>
				</div>
			</div>
			
		</div>
		
	<?php
	}
	
	public function add_metabox_classes( $classes ) {
		array_push( $classes, $this->plugin_prefix.'-postbox', 'seamless' );
		return $classes;
	}
	
	/*
	* Create global settings meta box
	*/
	public function create_global_settings_meta_box() {
		global $post;
		
		include( plugin_dir_path( dirname( __FILE__ ) ).'/admin/partials/global-settings.php' );
	}
	
	/*
	* Create Shortcode meta box
	*/
	public function create_shortcode_meta_box() {
		global $post;
	?>
		<div class="text-input-with-button-container copyable">
			<input name="<?php echo $this->plugin_prefix; ?>_shortcode" value="<?php esc_html_e( '['.$this->plugin_prefix.' id="' . $post->ID . '"]' ); ?>" readonly />
			<!-- <div class="icon copy">
				<i class=""></i>
			</div>  -->
			<div class="message"><?php esc_html_e( 'Copied to clipboard', $this->namePot ); ?></div>
		</div>
	<?php
	}
	
	/*
	* Save slides meta
	*/
	public function save_slides_meta( $post_id ) {
		// if ( !isset( $_POST[$this->wp_nonce_field_token] ) || !wp_verify_nonce( $_POST[$this->wp_nonce_field_token], $this->wp_nonce_field_token ) )
		// 	return;
		
		// if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		// 	return;

		// if ( !current_user_can( 'edit_post', $post_id ) )
		// 	return;

		$sss_old = get_post_meta( $post_id, $this->plugin_prefix.'-item-settings-group', true );
		$sss_new = array();
		
		$repeatable_fieldset_settings = $this->repeatable_fieldset_settings['fields'];
		//$repeatable_fieldset_settings_array = array();
		
        foreach ( $repeatable_fieldset_settings as $name => $config ) {
			$values_array = wp_unslash( $_POST[ $name ] );
			
			for ( $i=0; $i<count( $values_array ); $i++ ) {
				$sss_new[$i][ $name ] = $this->sanitize_field( $values_array[$i], $config['type'] );
			}
        }
        
		if ( !empty( $sss_new ) && $sss_new != $sss_old ) {
			update_post_meta( $post_id, $this->plugin_prefix.'-item-settings-group', $sss_new );
		} elseif ( empty( $sss_new ) && $sss_old ) {
			delete_post_meta( $post_id, $this->plugin_prefix.'-item-settings-group', $sss_old );
		}
	}
	
	/*
	* Save global settings meta
	*/
	public function save_global_settings_meta( $post_id ) {
		if ( !isset( $_POST[$this->wp_nonce_field_token] ) || !wp_verify_nonce( $_POST[$this->wp_nonce_field_token], $this->wp_nonce_field_token ) )
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		
		$settings = $this->settings['fields'];
		$value;
		
        foreach ( $settings as $name => $config ) {
			$post = '';
			
			if ( isset( $_POST[ $name ] ) ) {
				$post = $_POST[ $name ];
			}

			$value = $this->sanitize_field( wp_unslash( $post ), $config['type'] );
			update_post_meta( $post_id, $name, $value);
        }
	}
	
	/* Utility function for creating form controls */
	public function create_dcs_form_control( $id, $settings ) {
		global $post;
		
		$value = '';
		$formControl = null;
		
		$repeatable 	   = $this->getIfSet( $settings['repeatable'], false);
		$parent_field_type = $this->getIfSet($settings['type'], '');
		$field_counter 	   = $this->getIfSet($settings['field_counter'], '');
		$settings 		   = $settings['fields'][$id];
		$field_type 	   = $settings['type'];
		
		if ( ( $repeatable || $parent_field_type == 'repeatable_fieldset' ) && isset( $this->field[$id] ) ) {
			$value = $this->field[$id];
		} else if ( !$repeatable ) {
			$value = get_post_meta( $post->ID, $id, true );
		}

		if ( !is_numeric( $value ) && empty( $value ) && isset( $settings['default'] ) ) {		
			$value = $settings['default'];
		}
		
		$formControl = new Declicsweb_Slide_Form_Control( $id, $this, $repeatable, $settings, $value, $field_counter );
		
		return $formControl;
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
	
	/**
	 * Set up admin messages for post type
	 *
	 * @param  array $messages Default message.
	 * @return array           Modified messages.
	 */
	public function updated_messages( $messages = array() ) {
		global $post, $post_ID;
		//phpcs:disable
		$messages[ $this->post_type ] = array(
			0  => '',
			1  => sprintf( __( '%1$s updated. %2$sView %3$s%4$s.', $this->namePot ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			2  => __( 'Custom field updated.', $this->namePot ),
			3  => __( 'Custom field deleted.', $this->namePot ),
			4  => sprintf( __( '%1$s updated.', $this->namePot ), $this->single ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s.', $this->namePot ), $this->single, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( '%1$s published. %2$sView %3$s%4s.', $this->namePot ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			7  => sprintf( __( '%1$s saved.', $this->namePot ), $this->single ),
			8  => sprintf( __( '%1$s submitted. %2$sPreview post%3$s%4$s.', $this->namePot ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
			9  => sprintf( __( '%1$s scheduled for: %2$s. %3$sPreview %4$s%5$s.', $this->namePot ), $this->single, '<strong>' . date_i18n( __( 'M j, Y @ G:i', $this->namePot ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			10 => sprintf( __( '%1$s draft updated. %2$sPreview %3$s%4$s.', $this->namePot ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
		);
		//phpcs:enable

		return $messages;
	}

	/**
	 * Set up bulk admin messages for post type
	 *
	 * @param  array $bulk_messages Default bulk messages.
	 * @param  array $bulk_counts   Counts of selected posts in each status.
	 * @return array                Modified messages.
	 */
	public function bulk_updated_messages( $bulk_messages = array(), $bulk_counts = array() ) {

		//phpcs:disable
		$bulk_messages[ $this->post_type ] = array(
			'updated'   => sprintf( _n( '%1$s %2$s updated.', '%1$s %3$s updated.', $bulk_counts['updated'], $this->namePot ), $bulk_counts['updated'], $this->single, $this->single ),
			'locked'    => sprintf( _n( '%1$s %2$s not updated, somebody is editing it.', '%1$s %3$s not updated, somebody is editing them.', $bulk_counts['locked'], $this->namePot ), $bulk_counts['locked'], $this->single, $this->single ),
			'deleted'   => sprintf( _n( '%1$s %2$s permanently deleted.', '%1$s %3$s permanently deleted.', $bulk_counts['deleted'], $this->namePot ), $bulk_counts['deleted'], $this->single, $this->single ),
			'trashed'   => sprintf( _n( '%1$s %2$s moved to the Trash.', '%1$s %3$s moved to the Trash.', $bulk_counts['trashed'], $this->namePot ), $bulk_counts['trashed'], $this->single, $this->single ),
			'untrashed' => sprintf( _n( '%1$s %2$s restored from the Trash.', '%1$s %3$s restored from the Trash.', $bulk_counts['untrashed'], $this->namePot ), $bulk_counts['untrashed'], $this->single, $this->single ),
		);
		//phpcs:enable

		return $bulk_messages;
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

	
	/**
	 * Main Super_Simple_Slider_Post_Type Instance
	 *
	 * Ensures only one instance of Super_Simple_Slider_Post_Type is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Super_Simple_Slider()
	 * @return Main Super_Simple_Slider_Post_Type instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

}
