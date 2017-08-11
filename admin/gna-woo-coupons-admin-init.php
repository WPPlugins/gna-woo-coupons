<?php
/* 
 * Inits the admin dashboard side of things.
 * Main admin file which loads all settings panels and sets up admin menus. 
 */
if (!class_exists('GNA_WooCoupons_Admin_Init')) {
	class GNA_WooCoupons_Admin_Init {
		var $main_menu_page;
		var $settings_menu;

		public function __construct() {
			$this->admin_includes();
			add_action('admin_menu', array(&$this, 'create_admin_menus'));

			if ( isset($_GET['page']) && (strpos($_GET['page'], GNA_WOO_COUPONS_MENU_SLUG_PREFIX ) !== false) ) {
				add_action('admin_print_scripts', array(&$this, 'admin_menu_page_scripts'));
				add_action('admin_print_styles', array(&$this, 'admin_menu_page_styles'));
			}
		}

		public function admin_menu_page_scripts() {
			wp_enqueue_script('jquery');
			wp_enqueue_script('postbox');
			wp_enqueue_script('dashboard');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('gna-wc-script', GNA_WOO_COUPONS_URL. '/assets/js/gna-woo-coupons.js', array(), GNA_WOO_COUPONS_VERSION);
		}

		function admin_menu_page_styles() {
			wp_enqueue_style('dashboard');
			wp_enqueue_style('thickbox');
			wp_enqueue_style('global');
			wp_enqueue_style('wp-admin');
			wp_enqueue_style('gna-woo-coupons-admin-css', GNA_WOO_COUPONS_URL. '/assets/css/gna-woo-coupons.css');
		}

		public function admin_includes() {
			include_once('gna-woo-coupons-admin-menu.php');
		}

		public function create_admin_menus() {
			$this->main_menu_page = add_menu_page( __('GNA WooCommerce Coupons', 'gna-woo-coupons'), __('GNA WooCommerce Coupon', 'gna-woo-coupons'), 'manage_options', 'gna-wc-settings-menu', array(&$this, 'handle_settings_menu_rendering'), GNA_WOO_COUPONS_URL . '/assets/images/gna_20x20.png' );

			add_submenu_page('gna-wc-settings-menu', __('Settings', 'gna-woo-coupons'),  __('Settings', 'gna-woo-coupons'), 'manage_options', 'gna-wc-settings-menu', array(&$this, 'handle_settings_menu_rendering'));

			add_action( 'admin_init', array(&$this, 'register_gna_woo_coupons_settings') );
		}

		public function register_gna_woo_coupons_settings() {
			register_setting( 'gna-woo-coupons-setting-group', 'g_woocoupons_configs' );
		}

		public function handle_settings_menu_rendering() {
			include_once('gna-woo-coupons-admin-settings-menu.php');
			$this->settings_menu = new GNA_WooCoupons_Settings_Menu();
		}
	}
}
