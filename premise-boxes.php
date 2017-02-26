<?php
/**
 * Plugin Name: Premise Boxes
 * Description: This plugin is an attempt at reducing the gap there is between developers and project/content managers. The idea is to provide a UI that is user friendly for the ones who do not code, yet offer a code editor for developers who sometimes feel it is easier to make changes writting code.
 * Plugin URI:
 * Version:     1.0.0
 * Author:      @premisewp
 * Author URI:  http://premisewp.com
 * License:     GPL
 * Text Domain: pwp-boxes-text-domain
 *
 * @package Premise Boxes
 */

/**
 * Define constants for plugin's url and path
 */
define( 'PBOXES_PATH', plugin_dir_path(__FILE__) );
define( 'PBOXES_URL', plugin_dir_url(__FILE__) );

/**
 * Check for required plugins
 */
require PBOXES_PATH . 'plugins/premise-plugin-require.php';

/**
 * Intantiate and setup Premise Boxes
 */
add_action( 'plugins_loaded', array( Premise_Boxes::get_instance(), 'pboxes_setup' ) );

/**
 * The Premise Boxes Main Class
 */
class Premise_Boxes {

	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 * @type object
	 */
	protected static $instance = NULL;

	/**
	 * plugin url
	 *
	 * @var string
	 */
	public $plugin_url = PBOXES_URL;

	/**
	 * plugin path
	 *
	 * @var strin
	 */
	public $plugin_path = PBOXES_PATH;

	/**
	 * Constructor. Intentionally left empty and public.
	 *
	 * @see 	pboxes_setup()
	 * @since 	1.0
	 */
	public function __construct() {}

	/**
	 * Access this plugin’s working instance
	 *
	 * @since   1.0
	 * @return  object of this class
	 */
	public static function get_instance() {
		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Setup Premise Boxes
	 *
	 * @since   1.0
	 */
	public function pboxes_setup() {
		$this->do_includes();

		$this->pboxes_hooks();
	}

	/**
	 * Includes
	 *
	 * @since 1.0
	 */
	protected function do_includes() {

		// Load tinymce plugin class
		include PBOXES_PATH . 'controller/controller-pboxes-tinymce-plugin.php';

		// controller files
		include PBOXES_PATH . 'controller/controller-pboxes-shortcode.php';

		// library files
		include PBOXES_PATH . 'library/pboxes-library.php';
	}

	/**
	 * Premise Boxes Hooks
	 */
	public function pboxes_hooks() {

		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'pboxes_scripts' ) );

		// Add Shortcode button
		add_shortcode( 'pwp_boxes', array( PBoxes_Shortcode::get_instance(), 'init_shortcode' ) );

		// Insert editor for boxes
		add_action( 'admin_footer', array( $this, 'insert_editor' ) );

		// Insert Tinymce plugin
		add_action( 'admin_init', array( PBoxes_Tinymce_Plugin::get_instance(), 'init' ), 20 );

		// if Premise WP has been loaded
		if ( class_exists( 'Premise_WP' ) ) {
			add_action( 'admin_init', 'my_theme_add_editor_styles' );
			function my_theme_add_editor_styles() {
				$pboxes_editor_css = array(
					plugins_url('Premise-WP/', 'premise.php' ) . 'css/Premise-WP.min.css',
				);
				add_editor_style( $pboxes_editor_css );
			}
		}
	}

	/**
	 * Premise Boxes CSS & JS
	 */
	public function pboxes_scripts( $hook ) {
		//register styles
		wp_register_style(
			'pboxes_codemirror_monokai_theme_css'   ,
			PBOXES_URL . 'includes/CodeMirror/theme/monokai.css'
		);
		wp_register_style(
			'pboxes_style_css'   ,
			PBOXES_URL . 'css/Premise-Boxes.min.css',
			array( 'pboxes_codemirror_monokai_theme_css' )
		);

		//register scripts
		wp_register_script(
			'pboxes_codemirror_mode_js',
			PBOXES_URL . 'includes/CodeMirror/mode/javascript/javascript.js'
		);
		wp_register_script(
			'pboxes_script_js',
			PBOXES_URL . 'js/Premise-Boxes.min.js',
			array( 'jquery' )
		);

		// enqueue both
		if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) ) {
			wp_enqueue_style( 'pboxes_style_css' );
			wp_enqueue_script( 'pboxes_script_js' );
			wp_enqueue_script( 'pboxes_codemirror_mode_js' );
		}
	}

	/**
	 * Insert Pboxes editor
	 *
	 * @return string html for editor dialog
	 */
	public function insert_editor() {
		pboxes_new_box_dialog();
	}
}
?>