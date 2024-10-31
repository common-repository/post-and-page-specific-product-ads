<?php
/**
 * Widget class
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/includes
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA_Widget extends WP_Widget {
	 
	function __construct() {
		parent::__construct(
			//id
			'papspa_widget',
			//name
			__('Post and Page Specific Product Ads', 'post-and-page-specific-product-ads'),
			//desc
			array('description' => __( 'Select which ads to show on a particular post, page, or category.', 'post-and-page-specific-product-ads' ),)
		);
	}
	/**
	 * Displays the widget on frontend
	 *
	 * Widget settings: 
	 * show_description | show_price | show_button | button_text |
	 * desc_length | image_size | show_on_post | show_on_page | 
	 * show_on_blog | show_on_archive | show_on_search
	 * 
	 * @since     1.0.0
	 * @param     array	   $args       	Array of arguments passed from the widgets page.
	 * @param     array    $instance 	Widget instance.
	 */
	public function widget($args, $instance) {
		$plugin_public = new PAPSPA_Public();
		$widget_title = apply_filters('widget_title', $instance['widget_title']);
		$args['show_description'] = apply_filters('show_description', $instance['show_description']);
		$args['show_price'] = apply_filters('show_price', $instance['show_price']);
		$args['show_button'] = apply_filters('show_button', $instance['show_button']);
		$args['button_text'] = apply_filters('button_text', $instance['button_text']);
		$args['desc_length'] = apply_filters('desc_length', $instance['desc_length']);
		$args['image_size'] = apply_filters('image_size', $instance['image_size']);
		$args['show_on_post'] = apply_filters('show_on_post', $instance['show_on_post']);
		$args['show_on_page'] = apply_filters('show_on_page', $instance['show_on_page']);
		$args['show_on_blog'] = apply_filters('show_on_blog', $instance['show_on_blog']);
		$args['show_on_archive'] = apply_filters('show_on_archive', $instance['show_on_archive']);
		$args['show_on_search'] = apply_filters('show_on_search', $instance['show_on_search']);
		
		$type = $plugin_public->papspa_get_type();
		$show = $plugin_public->papspa_check_if_allowed_here($args,$type);
		if(!$show) return false;
		
		//show by post/page ID
		if(in_array($type,array('post','page'))){
			$post_id = get_the_ID();
			$data = $plugin_public->papspa_get_to_show_id($post_id);
		}
		//show by category ID
		else if($type == 'archive'){
			$cat_id = get_queried_object_id();
			$data = $plugin_public->papspa_get_to_show_id(FALSE,$cat_id);
		}
		//show random
		else $data = $plugin_public->papspa_get_to_show_id();
		
		$html = $plugin_public->papspa_get_ad($data['prodId'],$args,$post_id,$cat_id,FALSE,FALSE,'widget_ad');
		 
		// before and after widget arguments - defined by themes
		echo $args['before_widget'];
		if ( ! empty( $widget_title ) )
		echo $args['before_title'] . $widget_title . $args['after_title'];
		 
		echo $html;
		
		echo $args['after_widget'];
	}
	/**
	 * Displays a widget form on widgets page
	 *
	 * @since     1.0.0
	 * @param     array	   $instance       	Widget instance.
	 */
	public function form($instance) {
		$plugin = new PAPSPA();
		$settings = $plugin->settings;
		$img_sizes = '';
		
		if(!isset($instance['image_size'])){
			$show_description = $instance['show_description'] = isset($settings->show_description) ? $settings->show_description : 1;
			$show_price = isset($settings->show_price) ? $settings->show_price : 1;
			$show_button = isset($settings->show_button) ? $settings->show_button : 1;
			$widget_title = '';
			$button_text = isset($settings->button_text) ? $settings->button_text : '';
			$desc_length = isset($settings->desc_length) ? $settings->desc_length : 55;
			$image_size = $instance['image_size'] = isset($settings->image_size) ? $settings->image_size : 'medium';
			$show_on_post = isset($settings->show_on_post) ? $settings->show_on_post : 1;
			$show_on_page = isset($settings->show_on_page) ? $settings->show_on_page : 1;
			$show_on_blog = isset($settings->show_on_blog) ? $settings->show_on_blog : 1;
			$show_on_archive = isset($settings->show_on_archive) ? $settings->show_on_archive : 1;
			$show_on_search = isset($settings->show_on_search) ? $settings->show_on_search : 1;
		}
		else{
			$show_description = $instance['show_description'];
			$show_price = $instance['show_price'];
			$show_button = $instance['show_button'];
			$widget_title = $instance['widget_title'];
			$button_text = $instance['button_text'];
			$desc_length = $instance['desc_length'];
			$image_size = $instance['image_size'];
			$show_on_post = $instance['show_on_post'];
			$show_on_page = $instance['show_on_page'];
			$show_on_blog = $instance['show_on_blog'];
			$show_on_archive = $instance['show_on_archive'];
			$show_on_search = $instance['show_on_search'];
		}
		
		$chk1 = $chk2 = $chk3 = $chk4 = $chk5 = $chk6 = $chk7 = $chk8 = '';
		$chkd = 'checked="checked"';
		if((bool)$show_description) $chk1 = $chkd;
		if((bool)$show_price) $chk2 = $chkd;
		if((bool)$show_button) $chk3 = $chkd;
		if((bool)$show_on_post) $chk4 = $chkd;
		if((bool)$show_on_page) $chk5 = $chkd;
		if((bool)$show_on_blog) $chk6 = $chkd;
		if((bool)$show_on_archive) $chk7 = $chkd;
		if((bool)$show_on_search) $chk8 = $chkd;
		
		
		$all_img_sizes = get_intermediate_image_sizes();
		foreach($all_img_sizes as $key=>$img){
			$selected = $img == $image_size ? 'selected="selected"' : '';
			$img_sizes .= '<option value="'.$img.'" '.$selected.'>'.$img.'</option>';
		}
		
		$html = '
		<p>
			<label for="'.$this->get_field_id( 'show_description' ) .'">'.__( 'Show description', 'post-and-page-specific-product-ads' ).'</label> 
			<input  id="'.$this->get_field_id( 'show_description' ).'" name="'.$this->get_field_name( 'show_description' ).'" type="checkbox" '.$chk1.' value="'.$show_description.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'show_price' ).'">'.__( 'Show price:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'show_price' ).'" name="'.$this->get_field_name( 'show_price' ).'" type="checkbox" '.$chk2.' value="'.$show_price.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'show_button' ).'">'.__( 'Show button:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'show_button' ).'" name="'.$this->get_field_name( 'show_button' ).'" type="checkbox" '.$chk3.' value="'.$show_button.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'show_on_post' ).'">'.__( 'Show on posts:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'show_on_post' ).'" name="'.$this->get_field_name( 'show_on_post' ).'" type="checkbox" '.$chk4.' value="'.$show_on_post.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'show_on_page' ).'">'.__( 'Show on pages:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'show_on_page' ).'" name="'.$this->get_field_name( 'show_on_page' ).'" type="checkbox" '.$chk5.' value="'.$show_on_page.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'show_on_blog' ).'">'.__( 'Show on blog page:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'show_on_blog' ).'" name="'.$this->get_field_name( 'show_on_blog' ).'" type="checkbox" '.$chk6.' value="'.$show_on_blog.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'show_on_archive' ).'">'.__( 'Show on archive page:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'show_on_archive' ).'" name="'.$this->get_field_name( 'show_on_archive' ).'" type="checkbox" '.$chk7.' value="'.$show_on_archive.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'show_on_search' ).'">'.__( 'Show on search page:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'show_on_search' ).'" name="'.$this->get_field_name( 'show_on_search' ).'" type="checkbox" '.$chk8.' value="'.$show_on_search.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'widget_title' ).'">'.__( 'Widget title:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'widget_title' ).'" name="'.$this->get_field_name( 'widget_title' ).'" type="text" value="'.$widget_title.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'button_text' ).'">'.__( 'Button text:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'button_text' ).'" name="'.$this->get_field_name( 'button_text' ).'" type="text" value="'.$button_text.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'desc_length' ).'">'.__( 'Description length:', 'post-and-page-specific-product-ads' ).'</label> 
			<input id="'.$this->get_field_id( 'desc_length' ).'" name="'.$this->get_field_name( 'desc_length' ).'" type="number" value="'.$desc_length.'" />
		</p>
		<p>
			<label for="'.$this->get_field_id( 'image_size' ).'">'.__( 'Image size:', 'post-and-page-specific-product-ads' ).'</label> 
			<select id="'.$this->get_field_id( 'image_size' ).'" name="'.$this->get_field_name( 'image_size' ).'">
				'.$img_sizes.'
			</select>
		</p>';
		echo $html;
	} 
	/**
	 * Update widget
	 *
	 * @since     1.0.0
	 * @param     array	   $new_instance       	New widget instance.
	 * @param     array	   $old_instance       	Old widget instance.
	 * @return    array	   $instance			New widget instance.
	 */
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['show_description'] = isset($new_instance['show_description']) ? 1 : FALSE;
		$instance['show_price'] = isset($new_instance['show_price']) ? 1 : FALSE;
		$instance['show_button'] = isset($new_instance['show_button']) ? 1 : FALSE;
		$instance['show_on_post'] = isset($new_instance['show_on_post']) ? 1 : FALSE;
		$instance['show_on_page'] = isset($new_instance['show_on_page']) ? 1 : FALSE;
		$instance['show_on_blog'] = isset($new_instance['show_on_blog']) ? 1 : FALSE;
		$instance['show_on_archive'] = isset($new_instance['show_on_archive']) ? 1 : FALSE;
		$instance['show_on_search'] = isset($new_instance['show_on_search']) ? 1 : FALSE;
		$instance['widget_title'] = !empty($new_instance['widget_title']) ? sanitize_text_field($new_instance['widget_title']) : '';
		$instance['button_text'] = !empty($new_instance['button_text']) ? sanitize_text_field($new_instance['button_text']) : '';
		$instance['desc_length'] = !empty($new_instance['desc_length']) ? $new_instance['desc_length'] : '';
		$instance['image_size'] = !empty($new_instance['image_size']) ? $new_instance['image_size'] : '';
		return $instance;
	}
}
