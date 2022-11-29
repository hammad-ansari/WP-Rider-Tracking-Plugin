<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://dev.rapidev.tech/
 * @since      1.0.0
 *
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Aw_Rider_Traking
 * @subpackage Aw_Rider_Traking/includes
 * @author     Abdul Wahab <admin@dev.rapidev.tech>
 */
class Aw_Rider_Traking {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Aw_Rider_Traking_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'AW_RIDER_TRAKING_VERSION' ) ) {
			$this->version = AW_RIDER_TRAKING_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'aw-rider-traking';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Aw_Rider_Traking_Loader. Orchestrates the hooks of the plugin.
	 * - Aw_Rider_Traking_i18n. Defines internationalization functionality.
	 * - Aw_Rider_Traking_Admin. Defines all hooks for the admin area.
	 * - Aw_Rider_Traking_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aw-rider-traking-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aw-rider-traking-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aw-rider-traking-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-aw-rider-traking-public.php';

		$this->loader = new Aw_Rider_Traking_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Aw_Rider_Traking_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Aw_Rider_Traking_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Aw_Rider_Traking_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'init_functions' );
		$this->loader->add_action( 'woocommerce_admin_order_data_after_order_details', $plugin_admin, 'add_custom_field_to_orders_page' );
		$this->loader->add_action( 'woocommerce_process_shop_order_meta', $plugin_admin, 'save_custom_field' );
		$this->loader->add_action( 'woocommerce_account_menu_items', $plugin_admin, 'add_rider_tab_myaccount_page' );
		$this->loader->add_action( 'woocommerce_account_assigned-orders_endpoint', $plugin_admin, 'my_account_rider_page_content' );
		$this->loader->add_action( 'woocommerce_account_aw-order-traking_endpoint', $plugin_admin, 'order_traking_page_content' );
		$this->loader->add_filter( 'woocommerce_my_account_my_orders_actions', $plugin_admin, 'add_my_account_my_orders_order_traking', 10, 2 );

		$this->loader->add_action("restrict_manage_posts", $plugin_admin, "add_filter");
		$this->loader->add_action("admin_init", $plugin_admin, "admin_init");
		
		$this->loader->add_filter( 'manage_shop_order_posts_columns', $plugin_admin, 'adding_columns_to_shop_order' );
		$this->loader->add_action("manage_shop_order_posts_custom_column", $plugin_admin, "smashing_shop_order_column", 10, 2);
		

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Aw_Rider_Traking_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'wp_footer_functions' );
		$this->loader->add_action( 'wp_ajax_save_lat_lng_to_user', $plugin_public, 'save_lat_lng_to_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_save_lat_lng_to_user', $plugin_public, 'save_lat_lng_to_user' );

		$this->loader->add_action( 'wp_ajax_update_lat_lng_to_user', $plugin_public, 'update_lat_lng_to_user' );
		$this->loader->add_action( 'wp_ajax_nopriv_update_lat_lng_to_user', $plugin_public, 'update_lat_lng_to_user' );



	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Aw_Rider_Traking_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
