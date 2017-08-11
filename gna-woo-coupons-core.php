<?php
if (!class_exists('GNA_WooCoupons')) {
	class GNA_WooCoupons {
		var $plugin_url;
		var $admin_init;
		var $configs;

		public function __construct() {
			$this->load_configs();
			$this->define_constants();
			$this->define_variables();
			$this->includes();
			$this->loads();

			add_action('admin_init', array(&$this, 'plugin_admin_init'));
			add_action('admin_notices', array(&$this, 'plugin_admin_notices'));
			add_action('init', array(&$this, 'plugin_init'), 0);
			add_filter('plugin_row_meta', array(&$this, 'filter_plugin_meta'), 10, 2);
			
			add_filter('woocommerce_coupon_data_tabs', array(&$this, 'gna_coupon_options_tabs'), 50, 1);
			add_action('woocommerce_coupon_data_panels', array(&$this, 'gna_coupon_options_panels'), 50, 0);
			add_action('gna_wc_available_day_metabox_products', array(&$this, 'gna_wc_available_day_metabox_products'), 50, 2 );
			add_action('woocommerce_process_shop_coupon_meta', array(&$this, 'gna_process_shop_coupon_meta'), 50, 2 );
		}

		public function load_configs() {
			include_once('inc/gna-woo-coupons-config.php');
			$this->configs = GNA_WooCoupons_Config::get_instance();
		}

		public function define_constants() {
			define('GNA_WOO_COUPONS_VERSION', '0.9.2');

			define('GNA_WOO_COUPONS_BASENAME', plugin_basename(__FILE__));
			define('GNA_WOO_COUPONS_URL', $this->plugin_url());

			define('GNA_WOO_COUPONS_MENU_SLUG_PREFIX', 'gna-ga-settings-menu');
		}

		public function define_variables() {
		}

		public function includes() {
			if ( is_admin() ) {
				include_once('admin/gna-woo-coupons-admin-init.php');
			}
		}

		public function loads() {
			if ( is_admin() ) {
				$this->admin_init = new GNA_WooCoupons_Admin_Init();
			}
		}

		function plugin_admin_init() {
		}

		public function plugin_init() {
			load_plugin_textdomain('gna-woo-coupons', false, dirname(plugin_basename(__FILE__ )) . '/languages/');
		}

		public function plugin_url() {
			if ($this->plugin_url) return $this->plugin_url;
			return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
		}

		function plugin_admin_notices() {
			if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$gna_plugin = plugin_basename(str_replace('-core', '', __FILE__));
				deactivate_plugins($gna_plugin);
				GNA_WooCoupons_Admin_Menu::show_msg_error_st( __('GNA WooCommerce Coupons plugin is deactivated. It needs to have activated WooCommerce!', 'gna-woo-coupons') );
			}
		}

		public function filter_plugin_meta($links, $file) {
			if( strpos( GNA_WOO_COUPONS_BASENAME, str_replace('.php', '', $file) ) !== false ) { /* After other links */
				$links[] = '<a target="_blank" href="https://profiles.wordpress.org/chris_dev/" rel="external">' . __('Developer\'s Profile', 'gna-woo-coupons') . '</a>';
			}

			return $links;
		}

		public function install() {
		}

		public function uninstall() {
		}

		public function activate_handler() {
		}

		public function deactivate_handler() {
		}

		public static function get_woo_version_number() {
			// If get_plugins() isn't available, require it
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			// Create the plugins folder and file variables
			$plugin_folder = get_plugins( '/' . 'woocommerce' );
			$plugin_file = 'woocommerce.php';

			// If the plugin version number is set, return it 
			if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
				return $plugin_folder[$plugin_file]['Version'];
			} else {
				// Otherwise return null
				return null;
			}
		}
		
		public function gna_coupon_options_tabs( $tabs ) {
			$tabs['gna_avaiable_day'] = array(
				'label'  => __( 'Available Day of Week', 'gna-woo-coupons' ),
				'target' => 'gna_wc_available_day',
				'class'  => 'gna_wc_available_day',
			);

			return $tabs;
		}

		public function gna_coupon_options_panels() {
			global $thepostid, $post;
			$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		?>
			<div id="gna_wc_available_day" class="panel woocommerce_options_panel">
				<?php
					do_action( 'gna_wc_available_day_metabox_products', $thepostid, $post );
				?>
			</div>
		<?php		
		}
		
		public function gna_wc_available_day_metabox_products( $thepostid, $post ) {
		?>
			<div class="options_group">
		<?php
			$p = get_post_meta( $thepostid, '_gna_available_day_of_week', true );
			
			woocommerce_wp_checkbox( array(
				'id' => '_gna_available_day_of_week_mon',
				'name' => '_gna_available_day_of_week[mon]',
				'class' => '_gna_available_day_of_week',
				'label' => __( 'Monday', 'gna-woo-coupons' ),
				'description' => __( 'Check this box if this coupone is available to use on Monday.', 'gna-woo-coupons' )
			) );

			woocommerce_wp_checkbox( array(
				'id' => '_gna_available_day_of_week_tue',
				'name' => '_gna_available_day_of_week[tue]',
				'class' => '_gna_available_day_of_week',
				'label' => __( 'Tuesday', 'gna-woo-coupons' ),
				'description' => __( 'Check this box if this coupone is available to use on Tuesday.', 'gna-woo-coupons' )
			) );

			woocommerce_wp_checkbox( array(
				'id' => '_gna_available_day_of_week_wed',
				'name' => '_gna_available_day_of_week[wed]',
				'class' => '_gna_available_day_of_week',
				'label' => __( 'Wednesday', 'gna-woo-coupons' ),
				'description' => __( 'Check this box if this coupone is available to use on Wednesday.', 'gna-woo-coupons' )
			) );

			woocommerce_wp_checkbox( array(
				'id' => '_gna_available_day_of_week_thu',
				'name' => '_gna_available_day_of_week[thu]',
				'class' => '_gna_available_day_of_week',
				'label' => __( 'Thursday', 'gna-woo-coupons' ),
				'description' => __( 'Check this box if this coupone is available to use on Thursday.', 'gna-woo-coupons' )
			) );

			woocommerce_wp_checkbox( array(
				'id' => '_gna_available_day_of_week_fri',
				'name' => '_gna_available_day_of_week[fri]',
				'class' => '_gna_available_day_of_week',
				'label' => __( 'Friday', 'gna-woo-coupons' ),
				'description' => __( 'Check this box if this coupone is available to use on Friday.', 'gna-woo-coupons' )
			) );

			woocommerce_wp_checkbox( array(
				'id' => '_gna_available_day_of_week_sat',
				'name' => '_gna_available_day_of_week[sat]',
				'class' => '_gna_available_day_of_week',
				'label' => __( 'Saturday', 'gna-woo-coupons' ),
				'description' => __( 'Check this box if this coupone is available to use on Saturday.', 'gna-woo-coupons' )
			) );

			woocommerce_wp_checkbox( array(
				'id' => '_gna_available_day_of_week_sun',
				'name' => '_gna_available_day_of_week[sun]',
				'class' => '_gna_available_day_of_week',
				'label' => __( 'Sunday', 'gna-woo-coupons' ),
				'description' => __( 'Check this box if this coupone is available to use on Sunday.', 'gna-woo-coupons' )
			) );
		?>
			</div>
		<?php
		}

		public function gna_process_shop_coupon_meta( $post_id, $post ) {
			$gna_available_day_of_week = isset( $_POST['_gna_available_day_of_week'] ) ? $_POST['_gna_available_day_of_week'] : '';
			update_post_meta( $post_id, '_gna_available_day_of_week', $gna_available_day_of_week );
		}
	}
}
$GLOBALS['g_woocoupons'] = new GNA_WooCoupons();
