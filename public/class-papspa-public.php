<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/public
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA_Public extends PAPSPA {

	protected $papspa;

	protected $version;

	public function __construct() {

		$this->settings = $this->papspa_return_settings();
		$this->papspa_environment = $this->papspa_return_environment();
		$settings = $this->settings;

	}
	/**
	 * Register the stylesheets for the public area.
	 *
	 * @since	1.0.0
	 */
	public function papspa_public_enqueue_styles() {
		wp_enqueue_style( $this->papspa.'-public', plugin_dir_url( __FILE__ ) . 'css/papspa-public.css', array(), $this->version, 'all' );
	}
	/**
	 * Papspa shortcode
	 * 
	 * Available arguments: 
	 * show_description | show_price | show_button | button_text |
	 * desc_length | image_size | show_on_post | show_on_page | 
	 * show_on_blog | show_on_archive | show_on_search | layout
	 *
	 * @since     1.0.0
	 * @param     array    $args      Array of arguments.
	 * @return    html	   $return    HTML of a product ad.  
	 */
	public static function papspa_shortcode($args=array()){
		$plugin_public = new PAPSPA_Public();
		$settings = $plugin_public->settings;
		$return = '';
		$post_id = get_the_ID();
		if('' == $post_id) $post_id = FALSE;
		
		$type = $plugin_public->papspa_get_type();
		
		$show = $plugin_public->papspa_check_if_allowed_here($args,$type);
		if(!$show) return false;
		
		switch($type){
			case 'post' :
				$data = $plugin_public->papspa_get_to_show_id($post_id);
				$return = $plugin_public->papspa_get_ad($data['prodId'],$args,$post_id,FALSE,$data['stats'],$type);
				break;
			
			case 'page' :
				$data = $plugin_public->papspa_get_to_show_id($post_id);
				$return = $plugin_public->papspa_get_ad($data['prodId'],$args,$post_id,FALSE,$data['stats'],$type);
				break;
			
			case 'archive' :
				$cat_id = get_queried_object_id();
				$data = $plugin_public->papspa_get_to_show_id(FALSE,$cat_id);
				$return = $plugin_public->papspa_get_ad($data['prodId'],$args,FALSE,$cat_id,$data['stats'],$type);
				break;
			
			case 'blog' :
				$data = $plugin_public->papspa_get_to_show_id(FALSE);
				$return = $plugin_public->papspa_get_ad($data['prodId'],$args,FALSE,FALSE,FALSE,$type);
				break;
			
			case 'search' :
				$data = $plugin_public->papspa_get_to_show_id(FALSE);
				$return = $plugin_public->papspa_get_ad($data['prodId'],$args,$post_id,FALSE,FALSE,$type);
				break;
			
		}
		
		return $return;
	}
	/**
	 * Gather all the data and loads the ad template
	 *
	 * - Checks if ads are allowed on current page (show_on_...)
	 * - Gather data, check which elements to show
	 * - Checks environment
	 * - Loads template
	 * - Checks and runs tracking (add view)
	 * 
	 *
	 * @since     1.0.0
	 * @param     int	   $prodId       	Product id to show.
	 * @param     array	   $args        	Array of arguments.
	 * @param     int	   $post_id			Current post/page id.
	 * @param     int	   $cat_id			Current category id.
	 * @param     array	   $stats			Array of saved stats.
	 * @param     string   $type			Component IDs to include
	 * @param     string   $tpl			 	Type of page we're currently on (post,page,archive,blog,search)
	 * @return    html	   $html	  		Html of an ad or blank.
	 */
	function papspa_get_ad($prodId,$args,$post_id=FALSE,$cat_id=FALSE,$stats=FALSE,$type=FALSE,$tpl='shortcode_ad'){
		$settings = $this->settings;
		$html = $price = $desc = $button = '';
		
		if(!$type) $type = $this->papspa_get_type();
		
		if($tpl == 'shortcode_ad' && isset($args['layout'])) $tpl = $tpl .'-'. $args['layout'];
		else if($tpl == 'shortcode_ad' && !isset($args['layout'])) $tpl = $tpl .'-'. 'vertical';
		
		//image size
		if(isset($args['image_size'])){
			$imgSize = $args['image_size'];
		}
		else{
			if(isset($settings->image_size)) $imgSize = $settings->image_size;
			else $imgSize = 'medium';
		}
		//show desc
		if(isset($args['show_description'])){
			$item = $args['show_description'];
			if($item == 'true' || $item == 'TRUE' || $item == 1) $show_desc = TRUE;
			else if($item == 'false' || $item == 'FALSE' || $item == 0) $show_desc = FALSE;
		}
		else{
			if(isset($settings->show_description)) $show_desc = TRUE;
			else $show_desc = FALSE;
		}
		//show price
		if(isset($args['show_price'])){
			$item = $args['show_price'];
			if($item == 'true' || $item == 'TRUE' || $item == 1) $show_price = TRUE;
			else if($item == 'false' || $item == 'FALSE' || $item == 0) $show_price = FALSE;
		}
		else{
			if(isset($settings->show_price)) $show_price = TRUE;
			else $show_price = FALSE;
		}
		//show button
		if(isset($args['show_button'])){
			$item = $args['show_button'];
			if($item == 'true' || $item == 'TRUE' || $item == 1) $show_button = TRUE;
			else if($item == 'false' || $item == 'FALSE' || $item == 0) $show_button = FALSE;
		}
		else{
			if(isset($settings->show_button)) $show_button = TRUE;
			else $show_button = FALSE;
		}
		
		if($prodId){
			if($this->papspa_environment == 'woocommerce'){
				$product = wc_get_product($prodId);
				$data = $product->get_data();
				$name = $product->get_name();
				$link = get_permalink($prodId);
				if(isset($this->settings->click_tracking_enabled) && (bool) $this->settings->click_tracking_enabled){
					$pid = get_the_ID();
					if(NULL == $pid) $pid = get_queried_object_id();
					$link = add_query_arg('p_referer',$type.'-'.$pid,$link);
				}
				$imgId = get_post_thumbnail_id($prodId);
				$img = wp_get_attachment_image_src($imgId,$imgSize);
				$imgUrl = $img[0];
				
				if($show_desc) {
					$desc_length = isset($args['desc_length']) ? $args['desc_length'] : FALSE;
					if(!$desc_length) $desc_length = isset($settings->desc_length) && !empty($settings->desc_length) ? $settings->desc_length : 55;
					$desc = wp_strip_all_tags(wp_trim_words($data['short_description'],$desc_length));
					if(!$desc || $desc == '') {
						$desc = wp_strip_all_tags(wp_trim_words($data['description'],$desc_length));
					}
				}
				if($show_price) {
					$price = '<p class="papspa_price">'.$product->get_price() . get_woocommerce_currency_symbol().'</p>';
				}
				if($show_button) {
					$button_text = isset($args['button_text']) ? $args['button_text'] : FALSE;
					if(!$button_text) $button_text = isset($settings->button_text) && !empty($settings->button_text) ? $settings->button_text : __('Read More','post-and-page-specific-product-ads');
					$button = '<a href="'.$link.'" class="button papspa_read_more">'.$button_text.'</a>';
				}
				ob_start();
				$this->papspa_get_template($tpl.'.php',array(
					'prodId'=>$prodId,
					'link'=>$link,
					'imgUrl'=>$imgUrl,
					'price'=>$price,
					'product_name'=>$name,
					'product_desc'=>$desc,
					'button'=>$button,
					)
				);
				$html = ob_get_clean();
			}
			//tracking
			if(isset($this->settings->view_tracking_enabled) && (bool) $this->settings->view_tracking_enabled){
				if('post' == $type || 'page' == $type || 'archive' == $type){
					if($post_id) $this->papspa_add_stats($post_id,FALSE,$prodId,'views',$stats);
					else if($cat_id) $this->papspa_add_stats(FALSE,$cat_id,$prodId,'views',$stats);
				}
				else $this->papspa_add_else_stats($type,'views',$stats);
			}
		}
		return $html;
	}
	/**
	 * Gets the type of page we're currently on
	 * 
	 * Types: post | page | archive | blog | search
	 *
	 * @since     1.0.0
	 * @return    string	$type	Type of page.
	 */
	function papspa_get_type(){
		$type = is_single() ? 'post' : FALSE;
		$type = is_page() ? 'page' : $type;
		$type = !is_front_page() && is_home() ? 'blog' : $type;
		$type = is_archive() ? 'archive' : $type;
		$type = is_search() ? 'search' : $type;
		return $type;
	}
	/**
	 * Checks if ads are allowed here
	 *
	 * - First checks the passed arguments (shortcode args, widget settings)
	 * - If the arguments are not set it checks the settings for defaults
	 *
	 * @since     1.0.0
	 * @param     array	   $args       	Array of passed arguments.
	 * @param     string   $type		Type of page we're on.
	 * @return    bool	   			  	TRUE / FALSE
	 */
	function papspa_check_if_allowed_here($args=FALSE,$type=FALSE){
		$settings = $this->settings;
		if(!$type) $type = $this->papspa_get_type();
		
		switch($type){
			case 'post' :
				if($args && isset($args['show_on_post'])){
					$item = $args['show_on_post'];
					if($item == 'true' || $item == 'TRUE' || $item == 1) return TRUE;
					else if($item == 'false' || $item == 'FALSE' || $item == 0) return FALSE;
				}
				else{
					if(isset($settings->show_on_post) && (bool)$settings->show_on_post) return TRUE;
					else return FALSE;
				}
			break;

			case 'page' :
				if($args && isset($args['show_on_page'])){
					$item = $args['show_on_page'];
					if($item == 'true' || $item == 'TRUE' || $item == 1) return TRUE;
					else if($item == 'false' || $item == 'FALSE' || $item == 0) return FALSE;
				}
				else{
					if(isset($settings->show_on_page) && (bool)$settings->show_on_page) return TRUE;
					else return FALSE;
				}
			break;

			case 'archive' :
				if($args && isset($args['show_on_archive'])){
					$item = $args['show_on_archive'];
					if($item == 'true' || $item == 'TRUE' || $item == 1) return TRUE;
					else if($item == 'false' || $item == 'FALSE' || $item == 0) return FALSE;
				}
				else{
					if(isset($settings->show_on_archive) && (bool)$settings->show_on_archive) return TRUE;
					else return FALSE;
				}
			break;

			case 'blog' :
				if($args && isset($args['show_on_blog'])){
					$item = $args['show_on_blog'];
					if($item == 'true' || $item == 'TRUE' || $item == 1) return TRUE;
					else if($item == 'false' || $item == 'FALSE' || $item == 0) return FALSE;
				}
				else{
					if(isset($settings->show_on_blog) && (bool)$settings->show_on_blog) return TRUE;
					else return FALSE;
				}
			break;

			case 'search' :
				if($args && isset($args['show_on_search'])){
					$item = $args['show_on_search'];
					if($item == 'true' || $item == 'TRUE' || $item == 1) return TRUE;
					else if($item == 'false' || $item == 'FALSE' || $item == 0) return FALSE;
				}
				else{
					if(isset($settings->show_on_search) && (bool)$settings->show_on_search) return TRUE;
					else return FALSE;
				}
			break;

		}
		
	}

}
