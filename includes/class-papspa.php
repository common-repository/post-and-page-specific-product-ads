<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/includes
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PAPSPA_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $papspa    The string used to uniquely identify this plugin.
	 */
	protected $papspa;

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
		if ( defined( 'PAPSPA_VERSION' ) ) {
			$this->version = PAPSPA_VERSION;
		} else {
			$this->version = '1.0.3';
		}
		$this->papspa = 'post-and-page-specific-product-ads';

		$this->load_dependencies();
		$this->papspa_set_locale();
		$this->papspa_define_admin_hooks();
		$this->papspa_define_public_hooks();
		
		$this->papspa_environment = $this->papspa_return_environment();
		
		$this->settings = $this->papspa_return_settings();
		
	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - PAPSPA_Loader. Orchestrates the hooks of the plugin.
	 * - PAPSPA_i18n. Defines internationalization functionality.
	 * - PAPSPA_Admin. Defines all hooks for the admin area.
	 * - PAPSPA_Components. Contains product components editing functionality.
	 * - PAPSPA_Public. Defines all hooks for the public side of the site.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-papspa-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-papspa-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-papspa-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-papspa-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-papspa-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-papspa-public.php';
		
		$this->loader = new PAPSPA_Loader();

	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PAPSPA_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function papspa_set_locale() {

		$plugin_i18n = new PAPSPA_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'papspa_load_plugin_textdomain');

	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function papspa_define_admin_hooks() {

		//if on admin page
		if (is_admin()){
			$plugin_admin = new PAPSPA_Admin( $this->papspa, $this->version );
			$plugin_settings = new PAPSPA_Settings( $this->papspa, $this->version );
			$settings = $this->papspa_return_settings();
		
			//settings
			$this->loader->add_action('admin_menu', $plugin_settings, 'papspa_register_settings_page');
			$this->loader->add_action('admin_init', $plugin_settings, 'papspa_settings_init');
			
			//widget
			$this->loader->add_action('widgets_init', $this, 'papspa_register_widget');
			
			//post/page/category list columns
			if(isset($settings->show_quick_stats) && (bool) $settings->show_quick_stats){
				$this->loader->add_action('admin_init', $plugin_admin, 'papspa_setup_wp_lists_columns');
			}
			
			//category form + save
			$this->loader->add_action('edit_category', $plugin_admin, 'papspa_save_category_ads', 10, 2);
			
			//post/page form + save
			$this->loader->add_action('add_meta_boxes', $plugin_admin, 'papspa_register_pap_metabox');
			$this->loader->add_action('save_post', $plugin_admin, 'papspa_save_pap_ads');
			
			//scripts
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'papspa_admin_enqueue_styles');
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'papspa_admin_enqueue_scripts');
			
			//plugin action links
			$this->loader->add_filter('plugin_action_links_'.$this->papspa.'/'.$this->papspa.'.php',$this,'papspa_action_links', 10, 3);
			$this->loader->add_filter('plugin_row_meta',$this,'papspa_plugin_desc_links', 10, 4);

			$this->loader->add_action('wp_ajax_papspa_clear_pap_stats',$plugin_admin, 'papspa_clear_pap_stats', 10, 2);
			$this->loader->add_action('wp_ajax_papspa_save_pap_ads',$plugin_admin, 'papspa_save_pap_ads_ajax', 10, 2);
		}
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function papspa_define_public_hooks() {

		$plugin_public = new PAPSPA_Public( $this->get_papspa(), $this->papspa_get_version() );
		$settings = $this->papspa_return_settings();

		//scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'papspa_public_enqueue_styles' );
		
		//widget
		$this->loader->add_action( 'widgets_init', $this, 'papspa_register_widget' );
		
		//click tracking
		if(isset($settings->click_tracking_enabled) && (bool) $settings->click_tracking_enabled){
			$this->loader->add_action( 'woocommerce_after_single_product', $this, 'papspa_add_click' );
		}
		
		//shortcode
		add_shortcode('papspa',array('PAPSPA_Public','papspa_shortcode'));
		
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function papspa_run() {
		$this->loader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_papspa() {
		return $this->papspa;
	}
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PAPSPA_Loader    Orchestrates the hooks of the plugin.
	 */
	public function papspa_get_loader() {
		return $this->loader;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function papspa_get_version() {
		return $this->version;
	}	
	/**
	 * Adds links to the plugin list
	 *
	 * @since     1.0.0
	 * @param 	array 	$links					
	 * @return 	array 	$link	
	 */
	function papspa_action_links($links) {
		$plugin_data = $this->papspa_get_plugin_data();
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=post-and-page-specific-product-ads') ) .'">Settings</a>';
		return $links;
	}
	/**
	 * Register the widget
	 *
	 * @since     1.0.0
	 * @param 	array 	$links					
	 * @return 	array 	$link	
	 */
	function papspa_register_widget() {
		register_widget('PAPSPA_Widget');
	}
	/**
	 * Adds links to the plugin list - meta section
	 *
	 * @since     1.0.0
	 * @param 	array 	$links					
	 * @return 	array 	$link	
	 */
	function papspa_plugin_desc_links($links, $file) {
		$newlinks = array();
		$plugin_data = $this->papspa_get_plugin_data();
		if(strpos($file,$plugin_data->plugin_reference.'.php') !== false){
			$newlinks[] = '<a href="'.$plugin_data->plugin_url.'" target="_blank">'.__('Documentation','post-and-page-specific-product-ads').'</a>';
			$links = array_merge($links,$newlinks);
		}
		return $links;
	}
	/**
	 * Locate template.
	 *
	 * Locate the called template.
	 * Search Order:
	 * 1. /themes/THEME/papspa/$template_name	(parent + child)
	 * 2. /themes/THEME/$template_name			(parent + child)
	 * 3. /plugins/post-and-page-specific-product-ads/templates/$template_name.
	 * 4. /plugins/woocommerce/templates/$template_name		(for overriding woocommerce templates if needed)
	 *
	 * @since 1.0.0
	 * @param 	string 	$template_name			Template to load.
	 * @param 	string 	$template_path			Path to templates.
	 * @param 	string	$default_path			Default path to template files.
	 * @return 	string 							Path to the template file.
	 */
	function papspa_locate_template($template_name, $template_path='', $default_path='') {
		// Set variable to search in post-and-page-specific-product-ads folder of theme.
		if ( ! $template_path ) {
			$template_path = 'post-and-page-specific-product-ads/';
		}
		// Set default plugin templates path.
		if ( ! $default_path ) {
			$default_path = str_replace('includes/','',plugin_dir_path( __FILE__ )) . 'templates/'; // Path to the template folder
		}
		// Search template file in theme folder.
		$template = locate_template( array(
			$template_path . $template_name,
			$template_name
		) );
		// Get plugins template file.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}
		
		// Get woocommerce template file.
		if ( !file_exists( $template ) ) {
			$template = WP_PLUGIN_DIR .'/'. $template_path .'/'. $template_name;
		}
		
		return apply_filters( 'papspa_locate_template', $template, $template_name, $template_path, $default_path );
	}
	/**
	 * Get template.
	 *
	 * Search for the given template and include the file.
	 *
	 * @since 1.0.0
	 * @param string 	$template_name			Template to load.
	 * @param array 	$args					Args passed for the template file.
	 * @param string 	$string $template_path	Path to templates.
	 * @param string	$default_path			Default path to template files.
	 */
	function papspa_get_template($template_name, $args=array(), $tempate_path='', $default_path='') {
		if ( is_array( $args ) && isset( $args ) ) {
			extract( $args );
		}
		$template_file = $this->papspa_locate_template( $template_name, $tempate_path, $default_path );
		if ( ! file_exists( $template_file ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), $this->version );
			return;
		}
		include $template_file;
	}
	/**
	 * WooCommerce template override
	 *
	 * Get woocommerce template, check if override exists anywhere (plugin, theme, parent theme)
	 * and return the override or original template.
	 *
	 * @since 1.0.0
	 * @param	string	$template	Template file that is being loaded.
	 * @return	string	$template	Template file that should be loaded (override if file exists else original).
	 */
	function papspa_template_loader($template) {
		$find = array();
		$file = '';
		$file = explode('\\',$template);
		$file = end($file);
		$file = explode('woocommerce/',$file);
		$file = end($file);
		$file = str_replace('templates/','',$file);
		
		$tpl = $this->papspa_locate_template( 'woocommerce/'.$file);
			
		if ( file_exists( $tpl ) ) {
			$template = $tpl;
			return $template;
		}
		return $template;
	}
	/**
	 * Creates default settings for the plugin
	 *
	 * @since     1.0.0
	 */
	function papspa_create_default_settings() {
		if (!$this->papspa_check_perms()) return FALSE;
		
		$defaults = array(
			'show_price' => 1,
			'show_description' => 1,
			'show_button' => 1,
			'desc_length' => 55,
			'button_text' => '',
			'default_behavior' => 'show_random',
			'image_size' => 'medium',
			'show_on_post' => 1,
			'show_on_page' => 1,
			'show_on_blog' => 1,
			'show_on_archive' => 1,
			'show_on_search' => 1,
			'view_tracking_enabled' => 1,
			'click_tracking_enabled' => 1,
			'keep_old_stats_on_update' => 1,
			'show_quick_stats' => 1,
		);
		$defaults = $defaults;
		add_option('papspa_settings',$defaults);
	}
	/**
	 * Gets the settings or creates default settings
	 *
	 * @since     1.0.0
	 * @return	array	$settings	PAPSPA settings.
	 */
	function papspa_get_settings() {
		$settings = get_option('papspa_settings');
		if(empty($settings)) {
			$this->papspa_create_default_settings();
			$settings = get_option('papspa_settings');
		}
		if(is_array($settings)) {
			$newSettings = new stdClass();
			foreach($settings as $key=>$value){
				$newSettings->$key = $value;
			}
			$settings = $newSettings;
		}
		return $settings;
	}
	/**
	 * Returns the settings
	 *
	 * @since     1.0.0
	 * @return	array	$settings	PAPSPA settings.
	 */
	function papspa_return_settings() {
		if(!isset($this->settings)) $this->settings = $this->papspa_get_settings();
		return $this->settings;
	}
	/**
	 * Checks if user can manage posts, pages and categories
	 *
	 * @since     1.0.0
	 * @return    bool	       TRUE / FALSE 
	 */
	function papspa_check_perms() {
		if (current_user_can('edit_posts') && current_user_can('edit_pages') && current_user_can('manage_categories')){
			return TRUE;
		}
		return FALSE;
	}
	/**
	 * Checks which environment we're working with - currently only WooCommerce is supported
	 *
	 * @since     1.0.0
	 * @return    string	   $env    Current environment.
	 */
	function papspa_return_environment() {
		$env = FALSE;
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  {
			$env = 'woocommerce';
		}
		return $env;
	}
	/**
	 * Gets an ID of a product to show
	 * 
	 * - Check if we're working with post/page or category and get an ID
	 * - Get products/categories associated with the current item (post/page/category)
	 * - If there are no products/categories linked to the current item, or if we're on a blog/search page get a random product (if enabled)
	 * - Gets stats of the current item
	 * - Return product ID and stats
	 *
	 * @since     1.0.0
	 * @param     int	    $post_id	Post id.
	 * @param     int	    $cat_id		Category id.
	 * @return    array	   				Array with product id and stats.
	 */
	function papspa_get_to_show_id($post_id=FALSE, $cat_id=FALSE) {
		$settings = $this->settings;
		$post_ads = $post_id ? $this->papspa_get_ads($post_id) : array();
		$cat_ads = $cat_id ? $this->papspa_get_ads(FALSE,$cat_id) : array();
		$prodId = FALSE;
		$stats = array();
		
		//post OR page AND has products/categories set
		if($post_id && count($post_ads) > 0){
			//dealing with products
			if(isset($post_ads['products'])){
				//stats enabled - get ID with least views
				if(isset($settings->view_tracking_enabled) && (bool)$settings->view_tracking_enabled){
					$stats = $this->papspa_get_stats($post_id);
					$prodId = $this->papspa_get_ad_from_stats($post_ads,$stats);
				}
				//stats disabled - get random ID
				else $prodId = $post_ads['products'][array_rand($post_ads['products'])];
			}
			//dealing with categories
			else if(isset($post_ads['categories'])){
				foreach($post_ads['categories'] as $catId){
					$products = $this->papspa_get_products_from_category($catId);
					//stats enabled - get ID with least views
					if(isset($settings->view_tracking_enabled) && (bool)$settings->view_tracking_enabled){
						$stats = $this->papspa_get_stats($post_id);
						$prodId = $this->papspa_get_ad_from_stats($products,$stats);
					}
					//stats disabled - get random ID
					else $prodId = $products[array_rand($products)];
					
				}
			}
		}
		//archive page AND has products/categories set
		else if($cat_id && count($cat_ads) > 0){
			//dealing with products
			if(isset($cat_ads['products'])){
				//stats enabled - get ID with least views
				if(isset($settings->view_tracking_enabled) && (bool)$settings->view_tracking_enabled){
					$stats = $this->papspa_get_stats(FALSE,$cat_id);
					$prodId = $this->papspa_get_ad_from_stats($cat_ads,$stats);
				}
				//stats disabled - get random ID
				else $prodId = $cat_ads['products'][array_rand($cat_ads['products'])];
			}
			//dealing with categories
			else if(isset($cat_ads['categories'])){
				foreach($cat_ads['categories'] as $catId){
					$products = $this->papspa_get_products_from_category($catId);
					//stats enabled - get ID with least views
					if(isset($settings->view_tracking_enabled) && (bool)$settings->view_tracking_enabled){
						$stats = $this->papspa_get_stats(FALSE,$cat_id);
						$prodId = $this->papspa_get_ad_from_stats($products,$stats);
					}
					//stats disabled - get random ID
					else $prodId = $products[array_rand($products)];
					
				}
			}
		}
		//no products set OR blog/search page
		if(!$prodId && $settings->default_behavior == 'show_random'){
			$prodId = $this->papspa_get_random_ad();
		}
		return array('prodId'=>$prodId,'stats'=>$stats);
	}
	/**
	 * Gets products/categories associated with the current post/page/category
	 *
	 * @since     1.0.0
	 * @param     int	    $post_id	Post id.
	 * @param     int	    $cat_id		Category id.
	 * @return    array	   	$return		Array of products/categories.
	 */
	function papspa_get_ads($post_id, $cat_id=FALSE) {
		$ads = array();
		if($post_id) $adsget = get_post_meta($post_id,'_papspa_ads_products');
		else if(!$post_id && $cat_id) $adsget = get_term_meta($cat_id,'_papspa_ads_products');
		
		
		if(isset($adsget[0][0])) $ads = $adsget[0];
		if(NULL == $adsget || !is_array($adsget) || count($adsget) == 0 || $ads == '[]'){
			if($post_id) $adsget = get_post_meta($post_id,'_papspa_ads_categories');
			else if(!$post_id && $cat_id)  $adsget = get_term_meta($cat_id,'_papspa_ads_categories');
			
			if(count($adsget) == 1 && isset($adsget[0]) && $adsget[0] == '[]') return FALSE;
			
			if(isset($adsget[0][0])) $ads = $adsget[0];
			$return['categories'] = $ads;
		}
		else $return['products'] = $ads;
		return $return;
	}
	/**
	 * Gets stats for the current post/page/category
	 *
	 * @since     1.0.0
	 * @param     int	    $post_id	Post id.
	 * @param     int	    $cat_id		Category id.
	 * @return    array	   	$stats		Array of stats.
	 */
	function papspa_get_stats($post_id=FALSE, $cat_id=FALSE) {
		$stats = NULL;
		if($post_id) $stats = get_post_meta($post_id,'_papspa_ads_stats');
		else if(!$post_id && $cat_id) $stats = get_term_meta($cat_id,'_papspa_ads_stats');
		
		if($stats && NULL !== $stats && !is_array($stats)) {
			$stats = json_decode($stats,TRUE);
		}
		if(isset($stats[0])) $stats = $stats[0];
		return $stats;
	}
	/**
	 * Gets stats for the current blog/search page
	 *
	 * @since     1.0.0
	 * @param     int	    $source		Source (blog page / search page).
	 * @return    array	   	$stats		Array with product id and stats 
	 */
	function papspa_get_else_stats($source) {
		$stats = NULL;
		$stats = get_option('_papspa_ads_stats-'.$source);
		
		if($stats && NULL !== $stats && !is_array($stats)) {
			$stats = json_decode($stats,TRUE);
		}
		if(isset($stats[0])) $stats = $stats[0];
		return $stats;
	}
	/**
	 * Checks which of the given products was shown the least and returns the ID
	 *
	 * @since     1.0.0
	 * @param     array    $ads			Array of associated ads.
	 * @param     array    $stats		Array of saved stats.
	 * @return    int	   $active		Product ID or FALSE.
	 */
	function papspa_get_ad_from_stats($ads, $stats){
		$products = array();
		
		if(isset($ads['products'])){
			$products = $ads['products'];
		}
		else if(isset($ads['categories'])){
			foreach($ads['categories'] as $catId){
				$prs = $this->papspa_get_products_from_category($catId);
				$products = array_merge($prs,$products);
			}
		}
		
		if(count($products) > 0){
			//default product
			$active = $products[0];
			$minViews = isset($stats['products'][$active]['views']) ? $stats['products'][$active]['views'] : 0;
				
			foreach($products as $prodId){
				//stats for this product exist - check if minumum
				if(isset($stats['products'][$prodId])){
					if($stats['products'][$prodId]['views'] < $minViews) $active = $prodId;
				}
				//no stats yet - hasn't been shown yet
				else{
					$active = $prodId;
				}
			}
			return $active;
		}
		return FALSE;
	}
	/**
	 * Gets a random product from the database
	 *
	 * @since     1.0.0
	 * @return    int	   $rndad		Random product ID.
	 */
	function papspa_get_random_ad(){
		$rnd_ad = FALSE;
		if($this->papspa_environment == 'woocommerce'){
			$args = array(
				'posts_per_page'   => 1,
				'orderby'          => 'rand',
				'post_type'        => 'product' ); 

			$rnd_ad = get_posts($args);
			if(isset($rnd_ad[0])) $rnd_ad = $rnd_ad[0];
			if(isset($rnd_ad->ID)) $rnd_ad = $rnd_ad->ID;
		}
		return $rnd_ad;
	}
	/**
	 * Updates stats for the given product
	 *
	 * @since     1.0.0
	 * @param     int	   $post_id			Current post/page id.
	 * @param     int	   $cat_id			Current category id.
	 * @param     int	   $prodId			Product id.
	 * @param     string   $type			Type of stats to add.
	 * @param     array    $stats			Array of saved stats.
	 */
	function papspa_add_stats($post_id=FALSE, $cat_id=FALSE, $prodId, $type='views', $stats=FALSE){
		if(!$stats && $post_id !== FALSE) $stats = $this->papspa_get_stats($post_id);
		else if(!$stats && $cat_id !== FALSE) $stats = $this->papspa_get_stats(FALSE,$cat_id);
		
		if(isset($stats['products'][$prodId])){
			$count = (int)$stats['products'][$prodId][$type] + 1;
			$stats['products'][$prodId][$type] = $count;
			if($post_id) update_post_meta($post_id,'_papspa_ads_stats',$stats);
			else if(!$post_id && $cat_id) update_term_meta($cat_id,'_papspa_ads_stats',$stats);
		}
		else{
			$stats['products'][$prodId] = array('views'=>0,'clicks'=>0);
			$stats['products'][$prodId][$type] = 1;
			if($post_id) update_post_meta($post_id,'_papspa_ads_stats',$stats);
			else if(!$post_id && $cat_id) update_term_meta($cat_id,'_papspa_ads_stats',$stats);
		}
	}
	/**
	 * Updates stats for the given product for blog and search pages
	 *
	 * @since     1.0.0
	 * @param     string   $source			Source of tracking (blog/search page).
	 * @param     int	   $prodId			Product id.
	 * @param     array    $type			Type of stats to add.
	 */
	function papspa_add_else_stats($source,$prodId,$type='views'){
		$stats = $this->papspa_get_else_stats($source);
		
		if(isset($stats['products'][$prodId])){
			$count = (int)$stats['products'][$prodId][$type] + 1;
			$stats['products'][$prodId][$type] = $count;
			update_option('_papspa_ads_stats-'.$source,$stats);
		}
		else{
			$stats['products'][$prodId] = array('views'=>0,'clicks'=>0);
			$stats['products'][$prodId][$type] = 1;
			update_option('_papspa_ads_stats-'.$source,$stats);
		}
	}
	/**
	 * Adds a click when a user gets to the full product page after clicking on a link
	 * Hooked into woocommerce_after_single_product
	 *
	 * @since     1.0.0
	 */
	function papspa_add_click() {
		$prodId = get_the_ID();	
		if(!isset($_GET['p_referer'])) return false;
		
		else if(isset($_GET['p_referer'])) {
			$ref = urldecode($_GET['p_referer']);
			$ar = explode('-',$ref);
			
			if(count($ar) !== 2) return false;
			$source = $ar[0];
			$ref_id = $ar[1];
			if($source == 'post' || $source == 'page'){
				$this->papspa_add_stats($ref_id,FALSE,$prodId,'clicks');
			}
			else if($source == 'archive'){
				$this->papspa_add_stats(FALSE,$ref_id,$prodId,'clicks');
			}
			else if($source == 'blog' || $source == 'search'){
				$this->papspa_add_else_stats($source,$prodId,'clicks');
			}
		}
	}
	/**
	 * Gets an array of products from a given category ID
	 *
	 * @since     1.0.0
	 * @param     int	   $catId       	Component IDs to include.
	 * @param     bool     $includeData 	Component IDs to include.
	 * @return    array	   			  		Array of components objects.
	 */
	function papspa_get_products_from_category($catId,$includeData=FALSE) {
		$ids = array();
		$args = array(
			'post_type'             => 'product',
			'post_status'           => 'publish',
			'ignore_sticky_posts'   => 1,
			'posts_per_page'        => '12',
			'tax_query'             => array(
				array(
					'taxonomy'      => 'product_cat',
					'field' 		=> 'term_id', //This is optional, as it defaults to 'term_id'
					'terms'         => $catId,
				)
			)
		);
		$products = new WP_Query($args);
		
		foreach($products->posts as $prod){
			$ids[] = $prod->ID;
			if($includeData) $prods[$prod->ID] = $prod;
		}
		
		if($includeData) return array('products'=>$ids,'data'=>$prods);
		return array('products'=>$ids);
	}
	/**
	 * Get plugin data for verification
	 *
	 * @since     1.0.0
	 * @return    object	$data    Plugin data.
	 */
	static function papspa_get_plugin_data() {
		
		$data = new stdClass();
		$data->plugin_reference = 'post-and-page-specific-product-ads';
		$data->plugin_name = 'Post and Page Specific Product Ads';
		$data->plugin_url = 'https://grrega.com/projects/post-and-page-specific-product-ads';
		//$data->subscription_url = 'https://grrega.com/my-account/view-subscription/'.get_option('grr_subscription_id');
		$data->documentation_url = 'https://grrega.com/documentation/post-and-page-specific-product-ads-docs';
		return $data;
	}
	
}
