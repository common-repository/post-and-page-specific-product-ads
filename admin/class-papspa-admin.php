<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/admin
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA_Admin extends PAPSPA {

	protected $papspa;

	protected $version;

	public function __construct( $papspa, $version ) {

		$this->papspa = $papspa;
		$this->version = $version;
		$this->settings = $this->papspa_return_settings();
		$this->papspa_environment = $this->papspa_return_environment();
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since	1.0.0
	 */
	public function papspa_admin_enqueue_styles() {
		if (!$this->papspa_check_perms()) return FALSE;
		
		wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css' );
		wp_enqueue_style( $this->papspa.'-admin', plugin_dir_url( __FILE__ ) . 'css/papspa-admin.css', array(), $this->version, 'all' );
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since	1.0.0
	 */
	public function papspa_admin_enqueue_scripts() {
		if (!$this->papspa_check_perms()) return FALSE;
		
		wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js');
		wp_enqueue_script( $this->papspa.'-admin', plugin_dir_url( __FILE__ ) . 'js/papspa-admin.js', array('jquery', 'select2'), $this->version, true );
	}
	/**
	 * Register meta boxes for the post and page edit screen.
	 */
	function papspa_register_pap_metabox() {
		if (!$this->papspa_check_perms()) return FALSE;
		
		add_meta_box('papspa-ads', __( 'Post Specific Product Ads', 'post-and-page-specific-product-ads' ), array($this,'papspa_pap_metabox'), 'post', 'side');
		add_meta_box('papspa-ads', __( 'Post Specific Product Ads', 'post-and-page-specific-product-ads' ), array($this,'papspa_pap_metabox'), 'page', 'side');
	}
	/**
	 * Setup taxonomy hooks (add/edit category page)
	 *
	 * @since	1.0.0
	 */
	function papspa_setup_wp_lists_columns(){
		if (!$this->papspa_check_perms()) return FALSE;
		
		//category page
		add_action( 'category_edit_form_fields', array($this, 'papspa_edit_category_ads_field'), 10, 2 );
		add_filter( 'manage_edit-category_columns', array( $this, 'papspa_ads_header' ) );
		add_filter( 'manage_category_custom_column', array( $this, 'papspa_category_ads_column' ), 10, 3 );
			
		//posts page
		add_action( 'manage_posts_custom_column', array($this, 'papspa_post_ads_column'), 10, 2 );
		add_filter( 'manage_posts_columns', array( $this, 'papspa_ads_header' ), 10, 3 );
			
		//pages page
		add_action( 'manage_pages_custom_column', array($this, 'papspa_post_ads_column'), 10, 2 );
		add_filter( 'manage_pages_columns', array( $this, 'papspa_ads_header' ), 10, 3 );
	}
	/**
	 * Display metabox on post and page edit screen.
	 *
	 * @since	1.0.0
	 * @param	WP_Post		$post 	Current post object.
	 */
	function papspa_pap_metabox($post){
		$type = 'post';
		$post_id = $post->ID;
		$prod_options = $cat_options = $html = $stats_container = '';
		$nonce = wp_create_nonce('papspa_post_form_nonce');
		
		$type = is_single() ? 'post' : $type;
		$type = is_page() ? 'page' : $type;
		$type = is_archive() ? 'archive' : $type;
		$type = is_search() ? 'search' : $type;
		
		$data = $this->papspa_get_select_form_data($post_id);
		$prod_options = $data['prod_options'];
		$cat_options = $data['cat_options'];
		
		if(isset($this->settings->view_tracking_enabled) && (bool) $this->settings->view_tracking_enabled || isset($this->settings->click_tracking_enabled) && (bool) $this->settings->click_tracking_enabled){
			$stats = $this->papspa_get_stats($post_id);
			$stats_table = $this->papspa_display_stats_table($stats);
			$stats_container = '<div class="papspa_stats_container">
					<a href="#" class="papspa_extend_stats">Show stats</a>
					'.$stats_table.'
				</div>';
		}
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/views/papspa_metabox.php';
	}
	
	/**
	 * Adds a form to add category page
	 *
	 * @since	1.0.0
	 * @param	array	$term	Current taxonomy object.
	 */
	function papspa_add_category_ads_field($term) {
		$prod_options = array();
		$cat_options = array();
		$nonce = wp_create_nonce('papspa_post_form_nonce');
		$stats_table = '';
		
		$html = '<tr class="form-field term-ads-wrap">
		 <th scope="row">
		   <label for="papspa_category_ads">'.__('Product ads','post-and-page-specific-product-ads').'</label>
		 </th>
		 <td>
		   <div id="papspa_ads_wrapper">';
				
				ob_start();
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/views/papspa_category_form.php';
				$html .= ob_get_clean();
				
		$html .=
		   '</div>
		 </td>
	   </tr>';
		echo $html;
	}
	/**
	 * Adds a form to edit category page
	 *
	 * @since	1.0.0
	 * @param	array	$term	Current taxonomy object.
	 */
	function papspa_edit_category_ads_field($term) {
		$cat_id = $term->term_id;
		$data = $this->papspa_get_select_form_data($cat_id,'category');
		$prod_options = $data['prod_options'];
		$cat_options = $data['cat_options'];
		$nonce = wp_create_nonce('papspa_post_form_nonce');
		$stats = $this->papspa_get_stats(FALSE,$cat_id);
		$stats_table = $this->papspa_display_stats_table($stats);
		
		$html = '<tr class="form-field term-ads-wrap">
		 <th scope="row">
		   <label for="papspa_category_ads">'.__('Product ads','post-and-page-specific-product-ads').'</label>
		 </th>
		 <td>
		   <div id="papspa_ads_wrapper">';
				
				ob_start();
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/views/papspa_category_form.php';
				$html .= ob_get_clean();
				
		$html .=
		   '</div>
		 </td>
	   </tr>';
		echo $html;
	}
	/**
	 * Adds a header to post,page and category list page
	 *
	 * @since     1.0.0
	 * @param	  array	   $columns		Old columns.
	 * @return	  array	   				New columns.
	 */
	function papspa_ads_header($columns) {
		$current = get_current_screen();
		if(isset($current->id)){
			if($current->id != 'edit-post' && $current->id != 'edit-page' && $current->id != 'edit-category') return $columns;
			if($current->post_type !== 'post' && $current->post_type !== 'page') return $columns;
		}
		$columns['papspa_ads'] = __('Product ads', 'post-and-page-specific-product-ads');
		return $columns;
	}
	/**
	 * Adds a column to category list page
	 *
	 * @since     1.0.0
	 * @param	  array	   $columns		Array of columns.
	 * @param	  string   $column		Slug of column.
	 * @param	  int	   $id			Term id.
	 * @return	  html	   $columns		New columns.
	 */
	function papspa_category_ads_column($columns, $column, $id) {
		if ($column == 'papspa_ads') {
			$stats = $this->papspa_get_stats(FALSE,$id);
			if(isset($stats['products']) && count($stats['products']) > 0){
				$views = $clicks = 0;
				foreach($stats['products'] as $prod){
					$views = $views + (int)$prod['views'];
					$clicks = $clicks + (int)$prod['clicks'];
				}
				$columns .= '<span>'.__('Views','post-and-page-specific-product-ads').': <b>'.$views.'</b></span>&nbsp;&nbsp;&nbsp;';
				$columns .= '<span>'.__('Clicks','post-and-page-specific-product-ads').': <b>'.$clicks.'</b></span>';
			}
		}
		return $columns;
	}
	/**
	 * Adds a column to posts/pages list page
	 *
	 * @since     1.0.0
	 * @param	  string   $column		Slug of column.
	 * @param	  int	   $id			Term id.
	 * @return	  html	   $html		New columns.
	 */
	function papspa_post_ads_column($column, $id) {
		$current = get_current_screen();
		if(isset($current->id)){
			if($current->id != 'edit-post' && $current->id != 'edit-page') return false;
			if($current->post_type !== 'post' && $current->post_type !== 'page') return false;
		}
		$html = '';
		if ($column == 'papspa_ads') {
			$stats = $this->papspa_get_stats($id);
			if(isset($stats['products']) && count($stats['products']) > 0){
				$views = $clicks = 0;
				foreach($stats['products'] as $prod){
					$views = $views + (int)$prod['views'];
					$clicks = $clicks + (int)$prod['clicks'];
				}
				$html .= '<span>'.__('Views','post-and-page-specific-product-ads').': <b>'.$views.'</b></span>&nbsp;&nbsp;&nbsp;';
				$html .= '<span>'.__('Clicks','post-and-page-specific-product-ads').': <b>'.$clicks.'</b></span>';
			}
		}
		echo $html;
	}
	/**
	 * Display a statistics table for the edit post/page/category page.
	 *
	 * @since     1.0.0
	 * @param	  array	   $statistics	Statistics.
	 * @return	  html	   $return		Statistics table.
	 */
	function papspa_display_stats_table($statistics) {
		$product_name = $return = '';
		
		if(isset($statistics['products']) && count($statistics['products']) > 0){
			$return .= '<div class="papspa_stats_message papspa_hide">'.__('There are no stats to show','post-and-page-specific-product-ads').'</div>';
			$return .= '<table class="papspa_stats">
			<tbody>
			<tr>
				<th>'.__('Product','post-and-page-specific-product-ads').'</th>';
			if(isset($this->settings->view_tracking_enabled) && (bool) $this->settings->view_tracking_enabled) $return .= '<th>'.__('Views','post-and-page-specific-product-ads').'</th>';
			if(isset($this->settings->click_tracking_enabled) && (bool) $this->settings->click_tracking_enabled) $return .= '<th>'.__('Clicks','post-and-page-specific-product-ads').'</th>';
			$return .= '</tr>';
			
			foreach($statistics['products'] as $prodId=>$stats){
				if($this->papspa_environment == 'woocommerce') {
					$product = wc_get_product($prodId);
					$product_name = $product->get_name();
				}
				$return .= '
				<tr>
					<td>'.$product_name.'</td>';
					if(isset($this->settings->view_tracking_enabled) && (bool) $this->settings->view_tracking_enabled) $return .= '<td>'.$stats['views'].'</td>';
					if(isset($this->settings->click_tracking_enabled) && (bool) $this->settings->click_tracking_enabled) $return .= '<td>'.$stats['clicks'].'</td>';
				$return .= '</tr>';
			}
			$return .= '</tbody></table>';
		}
		else $return = '<div class="papspa_stats_message">'.__('There are no stats to show','post-and-page-specific-product-ads').'</div>';
		
		return $return;
	}
	/**
	 * Get options for the product/category select.
	 *
	 * @since     1.0.0
	 * @param	  int	   $id		  	Post or category ID.
	 * @param	  string   $type		post OR category.
	 * @return	  array	   				Array of options (products and categories).
	 */
	function papspa_get_select_form_data($id,$type='post') {
		$prod_options = $cat_options = $html = '';
		if($this->papspa_environment == 'woocommerce'){
			$products = wc_get_products(array());
			 $args = array(
				'taxonomy' => 'product_cat',
				'orderby' => 'name',
				'hide_empty' => 0,
			);
			$categories = get_categories($args);
			if($type == 'post'){
				$ads_prods = get_post_meta($id,'_papspa_ads_products');
				$ads_cats = get_post_meta($id,'_papspa_ads_categories');
			}
			else if($type == 'category'){
				$ads_prods = get_term_meta($id,'_papspa_ads_products');
				$ads_cats = get_term_meta($id,'_papspa_ads_categories');
			}
			if(isset($ads_prods[0][0])) $ads_prods = $ads_prods[0];
			if(isset($ads_cats[0][0])) $ads_cats = $ads_cats[0];
		}
		else return false;
		
		if(!is_array($products)) $products = json_decode($products,true);
		if(!is_array($categories)) $categories = json_decode($categories,true);
		if(!is_array($ads_prods)) $ads_prods = json_decode($ads_prods,true);
		if(!is_array($ads_cats)) $ads_cats = json_decode($ads_cats,true);
		
		if(!is_array($products)) $products = array();
		if(!is_array($categories)) $categories = array();
		if(!is_array($ads_prods)) $ads_prods = array();
		if(!is_array($ads_cats)) $ads_cats = array();
		
		foreach( $products as $prod) {
			$data = $prod->get_data();
			$selected = (in_array((int) $data['id'], (array) $ads_prods ) ) ? ' selected="selected"' : '';
			$prod_options .= '<option value="' . $data['id'] . '"' . $selected . '>' . $data['name'] . '</option>';
		}
		foreach( $categories as $cat) {
			$selected = (in_array((int) $cat->term_id, (array) $ads_cats ) ) ? ' selected="selected"' : '';
			$cat_options .= '<option value="' . $cat->term_id . '"' . $selected . '>' . $cat->name . '</option>';
		}
		return array('prod_options'=>$prod_options,'cat_options'=>$cat_options);
	}
	/**
	 * Saves the category adds
	 *
	 * @since     1.0.0
	 * @param	  array	   $term_id		Term id.
	 * @param	  array	   $tt_id		Passed from the hook.
	 * @param	  array	   $taxonomy	Taxonomy - passed from the hook.
	 */
	function papspa_save_category_ads($term_id, $tt_id) {
		if(!isset($_POST['_papspanonce'])) return false;
		$noncecheck = wp_verify_nonce($_POST['_papspanonce'], 'papspa_post_form_nonce');
		if(!$noncecheck || !$this->papspa_check_perms()) return false;
		
		$products = isset($_POST['papspa_select2_products']) ? $_POST['papspa_select2_products'] : FALSE;
		$categories = isset($_POST['papspa_select2_categories']) ? $_POST['papspa_select2_categories'] : FALSE;
			
		$this->papspa_save_pap_ads(FALSE,$term_id,$products,$categories);
	}
	 
	/**
	 * Save meta box content.
	 *
	 * @since 1.0.0
	 * @param	  string   $post_id		Post ID.
	 * @param	  string   $cat_id		Category ID.
	 * @param	  string   $products	Array of products.
	 * @param	  string   $categories	Array of categories.
	 */
	function papspa_save_pap_ads($post_id=FALSE,$cat_id=FALSE,$products=FALSE,$categories=FALSE) {
		if(!isset($_POST['_papspanonce'])) return false;
		$noncecheck = wp_verify_nonce($_POST['_papspanonce'], 'papspa_post_form_nonce');
		if(!$noncecheck || !$this->papspa_check_perms()) return false;
		
		$post_id = !$post_id ? isset($_POST['postId']) ? $_POST['postId'] : FALSE : $post_id;
		$cat_id = !$cat_id ? isset($_POST['catId']) ? $_POST['catId'] : FALSE : $cat_id;
			
		if(!$products) $products = isset($_POST['papspa_select2_products']) ? $_POST['papspa_select2_products'] : FALSE;
		if(!$categories) $categories = isset($_POST['papspa_select2_categories']) ? $_POST['papspa_select2_categories'] : FALSE;
		$newProds = $newCats = array();
			
		//validate
		foreach($products as $key=>$prod) $products[$key] = intval($prod);
		foreach($categories as $key=>$cat) $categories[$key] = intval($cat);
			
		if($products && $products != '[]' && $products != '') {
			if($post_id){
				update_post_meta($post_id,'_papspa_ads_products',$products);
				update_post_meta($post_id,'_papspa_ads_categories','[]');
			}
			else if($cat_id){
				update_term_meta($cat_id,'_papspa_ads_products',$products);
				update_term_meta($cat_id,'_papspa_ads_categories','[]');
			}
			//build array for stats
			foreach($products as $prodId){
				$newProds[$prodId]= array('views'=>0,'clicks'=>0);
			}
		}
		else if($categories && $categories !== '[]' && $categories !== '') {
			if($post_id){
				update_post_meta($post_id,'_papspa_ads_categories',$categories);
				update_post_meta($post_id,'_papspa_ads_products','[]');
			}
			else if($cat_id){
				update_term_meta($cat_id,'_papspa_ads_categories',$categories);
				update_term_meta($cat_id,'_papspa_ads_products','[]');
			}
			//build array for stats
			foreach($categories as $catId){
				$newCats[]= $catId;
			}
		}
		else if(!$categories && !$products){
			if($post_id){
				update_post_meta($post_id,'_papspa_ads_categories','[]');
				update_post_meta($post_id,'_papspa_ads_products','[]');
			}
			else if($cat_id){
				update_term_meta($cat_id,'_papspa_ads_categories','[]');
				update_term_meta($cat_id,'_papspa_ads_products','[]');
			}
		}
		//if there are stats for this post/page/category add empty stats for new products/categories to existing array
		//else create empty stats and save
		if(isset($this->settings->view_tracking_enabled) && (bool) $this->settings->view_tracking_enabled){
			$stats = $this->papspa_get_stats($post_id);
			if($stats && is_array($stats) && count($stats) > 0 && isset($this->settings->keep_old_stats_on_update) && (bool) $this->settings->keep_old_stats_on_update){
				if(count($products) > 0){
					foreach($products as $prodId){
						if(isset($stats['products']) && !isset($stats['products'][$prodId])) $stats['products'][$prodId] = array('views'=>0,'clicks'=>0);
					}
				}
				else if(count($categories) > 0){
					foreach($categories as $catId){
						if(isset($stats['categories']) && !isset($stats['categories'][$catId])) $stats['categories'][] = $catId;
					}
				}
			}
			else $stats = array('products'=>$newProds,'categories'=>$newCats);
				
			//save stats
			if($post_id) update_post_meta($post_id,'_papspa_ads_stats',$stats);
			else if($cat_id) update_term_meta($post_id,'_papspa_ads_stats',$stats);
		}
	}
	/**
	 * Save meta box content.
	 *
	 * @since 1.0.0
	 * @param int $post_id Post ID
	 */
	function papspa_clear_pap_stats() { // AJAX
		if(!isset($_POST['_papspanonce'])) wp_die();
		$noncecheck = wp_verify_nonce($_POST['_papspanonce'], 'papspa_post_form_nonce');
		if(!$noncecheck || !$this->papspa_check_perms()) wp_die();
		
		$post_id = isset($_POST['postId']) ? $_POST['postId'] : FALSE;
		$cat_id = isset($_POST['catId']) ? $_POST['catId'] : FALSE;
		if($post_id) delete_post_meta($post_id,'_papspa_ads_stats');
		else if($cat_id) delete_term_meta($cat_id,'_papspa_ads_stats');
		wp_die();
	}
	/**
	 * Save meta box content.
	 *
	 * @since 1.0.0
	 * @param int $post_id Post ID
	 */
	function papspa_save_pap_ads_ajax() { // AJAX
		if(!isset($_POST['_papspanonce'])) wp_die();
		$noncecheck = wp_verify_nonce($_POST['_papspanonce'], 'papspa_post_form_nonce');
		if(!$noncecheck || !$this->papspa_check_perms()) wp_die();
		
		$post_id = isset($_POST['postId']) ? $_POST['postId'] : FALSE;
		$cat_id = isset($_POST['catId']) ? $_POST['catId'] : FALSE;
		$products = isset($_POST['products']) ? $_POST['products'] : FALSE;
		$categories = isset($_POST['categories']) ? $_POST['categories'] : FALSE;
			
		$this->papspa_save_pap_ads($post_id,$cat_id,$products,$categories);
		wp_die();
	}
	
}
