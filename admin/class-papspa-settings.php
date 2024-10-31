<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/settings
 * @author     Grega Radelj <info@grrega.com>
 */
class PAPSPA_Settings extends PAPSPA {

	protected $papspa;

	protected $version;

	public function __construct( $papspa, $version ) {

		$this->papspa = $papspa;
		$this->version = $version;
		$this->settings = $this->papspa_return_settings();
		$this->papspa_environment = $this->papspa_return_environment();
	}
	/**
	 * Loads the settings page
	 * 
	 * @since      1.0.0
	 */
	function papspa_register_settings_page(){
		add_options_page('Post and Page Specific Product Ads', 'Post and Page Specific Product Ads', 'manage_options', 'post-and-page-specific-product-ads', array($this,'papspa_settings_page'));
	}
	/**
	 * Registers all settings
	 * 
	 * @since      1.0.0
	 */
	function papspa_settings_init() {
		
		register_setting('papspa_settings_page', 'papspa_settings');
		add_settings_section(
            'papspa_default_settings',
            __('Default Settings','post-and-page-specific-product-ads'),
            array($this, 'papspa_stats_section_sanitize'),
            'papspa_settings_page'
        ); 
		add_settings_section(
            'papspa_display_settings',
            __('Display Settings','post-and-page-specific-product-ads'),
            array($this, 'papspa_stats_section_sanitize'),
            'papspa_settings_page'
        ); 
		add_settings_section(
            'papspa_stats_settings',
            __('Stats Settings','post-and-page-specific-product-ads'),
            array($this, 'papspa_stats_section_sanitize'),
            'papspa_settings_page'
        );   
		
        add_settings_field(
            'show_description', 
            __('Show description','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_description_callback'), 
            'papspa_settings_page', 
            'papspa_default_settings'
        ); 
        add_settings_field(
            'show_price', 
            __('Show price','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_price_callback'), 
            'papspa_settings_page', 
            'papspa_default_settings'
        ); 
        add_settings_field(
            'show_button', 
            __('Show button','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_button_callback'), 
            'papspa_settings_page', 
            'papspa_default_settings'
        ); 
        add_settings_field(
            'desc_length', 
            __('Description length','post-and-page-specific-product-ads'), 
            array($this, 'papspa_desc_length_callback'), 
            'papspa_settings_page', 
            'papspa_default_settings'
        ); 
        add_settings_field(
            'button_text', 
            __('Button text','post-and-page-specific-product-ads'), 
            array($this, 'papspa_button_text_callback'), 
            'papspa_settings_page', 
            'papspa_default_settings'
        ); 
        add_settings_field(
            'default_behavior', 
            __('Default behavior','post-and-page-specific-product-ads'), 
            array($this, 'papspa_default_behavior_callback'), 
            'papspa_settings_page', 
            'papspa_default_settings'
        ); 
        add_settings_field(
            'image_size', 
            __('Image size','post-and-page-specific-product-ads'), 
            array($this, 'papspa_image_size_callback'), 
            'papspa_settings_page', 
            'papspa_default_settings'
        ); 
		
        add_settings_field(
            'show_on_post', 
            __('Show on posts','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_on_post_callback'), 
            'papspa_settings_page', 
            'papspa_display_settings'
        ); 
        add_settings_field(
            'show_on_page', 
            __('Show on pages','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_on_page_callback'), 
            'papspa_settings_page', 
            'papspa_display_settings'
        ); 
        add_settings_field(
            'show_on_archive', 
            __('Show on archive page','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_on_archive_callback'), 
            'papspa_settings_page', 
            'papspa_display_settings'
        ); 
        add_settings_field(
            'show_on_blog', 
            __('Show on blog page','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_on_blog_callback'), 
            'papspa_settings_page', 
            'papspa_display_settings'
        ); 
        add_settings_field(
            'show_on_search', 
            __('Show on search page','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_on_search_callback'), 
            'papspa_settings_page', 
            'papspa_display_settings'
        ); 

        add_settings_field(
            'view_tracking_enabled',
            __('Enable view tracking','post-and-page-specific-product-ads'),
            array($this, 'papspa_view_tracking_enabled_callback'),
            'papspa_settings_page',
            'papspa_stats_settings'    
        );  
        add_settings_field(
            'click_tracking_enabled',
            __('Enable click tracking','post-and-page-specific-product-ads'),
            array($this, 'papspa_click_tracking_enabled_callback'),
            'papspa_settings_page',
            'papspa_stats_settings'         
        );      
        add_settings_field(
            'keep_old_stats_on_update', 
            __('Keep old stats on update','post-and-page-specific-product-ads'), 
            array($this, 'papspa_keep_old_stats_on_update_callback'), 
            'papspa_settings_page', 
            'papspa_stats_settings'
        );   
        add_settings_field(
            'show_quick_stats', 
            __('Show quick stats','post-and-page-specific-product-ads'), 
            array($this, 'papspa_show_quick_stats_callback'), 
            'papspa_settings_page', 
            'papspa_stats_settings'
        ); 
		
	}
	/**
	 * Gets an array of saved settings and loads the settings page
	 *
	 * @since     1.0.0
	 */
	function papspa_settings_page() {
		$this->settings = get_option('papspa_settings');
		require_once plugin_dir_path(dirname( __FILE__ )) . 'includes/views/papspa_settings.php';
	}
	
	/** 
     * Show description
     */
    public function papspa_show_description_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_description" name="papspa_settings[show_description]" value="1" %s />',
            isset($this->settings['show_description']) && (bool)$this->settings['show_description'] ? 'checked="checked"' : ''
        );
    }
	/** 
     * Show price
     */
    public function papspa_show_price_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_price" name="papspa_settings[show_price]" value="1" %s />',
            isset($this->settings['show_price']) && (bool)$this->settings['show_price'] ? 'checked="checked"' : ''
        );
    }
	/** 
     * Show button
     */
    public function papspa_show_button_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_button" name="papspa_settings[show_button]" value="1" %s />',
            isset($this->settings['show_button']) && (bool)$this->settings['show_button'] ? 'checked="checked"' : ''
        );
		echo '<span><em>'.__('Show a "Read More" button?','post-and-page-specific-product-ads').'</em></span>';
    }
	/** 
     * Desc length
     */
    public function papspa_desc_length_callback()
    {
        echo '<input autocomplete="off"  type="number" id="desc_length" name="papspa_settings[desc_length]" value="'.$this->settings['desc_length'].'" />
		<span class="middle">
			<em>
				'.__('Maximum length (number of words) of product description.','post-and-page-specific-product-ads').'
			</em>
		</span>';
    }
	/** 
     * Button text
     */
    public function papspa_button_text_callback()
    {
        echo '<input autocomplete="off"  type="text" id="button_text" name="papspa_settings[button_text]" value="'.$this->settings['button_text'].'" />
		<span class="middle">
			<em>
				'.__('Default "Read More" button text. If you want this text translated leave it blank and edit the translation files.','post-and-page-specific-product-ads').'
			</em>
		</span>';
    }
	/** 
     * Default behavior
     */
    public function papspa_default_behavior_callback()
    {
		$sel1 = $sel2 = '';
		if($this->settings['default_behavior'] == 'show_random') $sel1 = 'selected="selected"';
		else if($this->settings['default_behavior'] == 'dont_show') $sel2 = 'selected="selected"';
		
        echo '<select autocomplete="off" id="default_behavior" name="papspa_settings[default_behavior]" value="'.$this->settings['default_behavior'].'">
			<option '.$sel1.' value="show_random">'.__('Show random ads','post-and-page-specific-product-ads').'</option>
			<option '.$sel2.' value="dont_show">'.__('Don\'t show anything','post-and-page-specific-product-ads').'</option>
		</select>
		<span class="middle">
			<em>
				'.__('Show random ads if the page/post/category has no ads set?','post-and-page-specific-product-ads').'
			</em>
		</span>';
    }
	/** 
     * Image size
     */
    public function papspa_image_size_callback()
    {
		$img_sizes = '';
		$image_size = isset($settings->image_size) ? $settings->image_size : 'medium';
		$all_img_sizes = get_intermediate_image_sizes();
		foreach($all_img_sizes as $key=>$img){
			$selected = $img == $image_size ? 'selected="selected"' : '';
			$img_sizes .= '<option value="'.$img.'" '.$selected.'>'.$img.'</option>';
		}
        echo '<select autocomplete="off"  id="image_size" name="papspa_settings[image_size]" value="'.$this->settings['image_size'].'">
			'.$img_sizes.'
		</select>';
    }
	/** 
     * Show on post
     */
    public function papspa_show_on_post_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_on_post" name="papspa_settings[show_on_post]" value="1" %s />',
            isset($this->settings['show_on_post']) && (bool)$this->settings['show_on_post'] ? 'checked="checked"' : ''
        );
    }
	/** 
     * Show on page
     */
    public function papspa_show_on_page_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_on_page" name="papspa_settings[show_on_page]" value="1" %s />',
            isset($this->settings['show_on_page']) && (bool)$this->settings['show_on_page'] ? 'checked="checked"' : ''
        );
    }
	/** 
     * Shwo on blog
     */
    public function papspa_show_on_blog_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_on_blog" name="papspa_settings[show_on_blog]" value="1" %s />',
            isset($this->settings['show_on_blog']) && (bool)$this->settings['show_on_blog'] ? 'checked="checked"' : ''
        );
		echo '<span><em>'.__('This will show random ads on the blog page.','post-and-page-specific-product-ads').'</em></span>';
    }
	/** 
     * Show on archive
     */
    public function papspa_show_on_archive_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_on_archive" name="papspa_settings[show_on_archive]" value="1" %s />',
            isset($this->settings['show_on_archive']) && (bool)$this->settings['show_on_archive'] ? 'checked="checked"' : ''
        );
    }
	/** 
     * Show on search
     */
    public function papspa_show_on_search_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_on_search" name="papspa_settings[show_on_search]" value="1" %s />',
            isset($this->settings['show_on_search']) && (bool)$this->settings['show_on_search'] ? 'checked="checked"' : ''
        );
		echo '<span><em>'.__('This will show random ads on the search page.','post-and-page-specific-product-ads').'</em></span>';
    }
	
	/** 
     * View tracking enabled
     */
    public function papspa_view_tracking_enabled_callback()
    {
        printf(
            '<input autocomplete="off"  class="double" type="checkbox" id="view_tracking_enabled" name="papspa_settings[view_tracking_enabled]" value="1" %s />',
            isset($this->settings['view_tracking_enabled']) && (bool)$this->settings['view_tracking_enabled'] ? 'checked="checked"' : ''
        );
		echo '<span class="double"><em>'.__('This allows the plugin to show ads with the least amount of views.<br/>If you uncheck this the plugin will show products randomly.','post-and-page-specific-product-ads').'</em></span>';
    }
	/** 
     * Click tracking enabled
     */
    public function papspa_click_tracking_enabled_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="click_tracking_enabled" name="papspa_settings[click_tracking_enabled]" value="1" %s />',
            isset($this->settings['click_tracking_enabled']) && (bool)$this->settings['click_tracking_enabled'] ? 'checked="checked"' : ''
        );
    }
	/** 
     * Keep old stats on update
     */
    public function papspa_keep_old_stats_on_update_callback()
    {
        printf(
            '<input autocomplete="off"  class="double" type="checkbox" id="keep_old_stats_on_update" name="papspa_settings[keep_old_stats_on_update]" value="1" %s />',
            isset($this->settings['keep_old_stats_on_update']) && (bool)$this->settings['keep_old_stats_on_update'] ? 'checked="checked"' : ''
        );
		echo '<span class="double"><em>'.__('Keep old statistics data when updating posts/pages/categories.<br/>If you uncheck this stats will be reset every time you update the post/page/category.','post-and-page-specific-product-ads').'</em></span>';
    }
	/** 
     * Show quick stats
     */
    public function papspa_show_quick_stats_callback()
    {
        printf(
            '<input autocomplete="off"  type="checkbox" id="show_quick_stats" name="papspa_settings[show_quick_stats]" value="1" %s />',
            isset($this->settings['show_quick_stats']) && (bool)$this->settings['show_quick_stats'] ? 'checked="checked"' : ''
        );
		echo '<span><em>'.__('This will show total views and clicks on post/page/category list.','post-and-page-specific-product-ads').'</em></span>';
    }
	
	/**
	 * Validate the settings page
	 *
	 * @since     1.0.0
	 * @param     array	   $input       	Array of values.
	 * @return    array	   $new_input  		Array of validated values.
	 */
	function papspa_stats_section_sanitize($input) {
        $new_input = array();
        if(isset($input['show_description']))
            $new_input['show_description'] = absint($input['show_description']);
        if(isset($input['show_price']))
            $new_input['show_price'] = absint($input['show_price']);
        if(isset($input['show_button']))
            $new_input['show_button'] = absint($input['show_button']);
        if(isset($input['show_on_post']))
            $new_input['show_on_post'] = absint($input['show_on_post']);
        if(isset($input['show_on_page']))
            $new_input['show_on_page'] = absint($input['show_on_page']);
        if(isset($input['show_on_archive']))
            $new_input['show_on_archive'] = absint($input['show_on_archive']);
        if(isset($input['show_on_blog']))
            $new_input['show_on_blog'] = absint($input['show_on_blog']);
        if(isset($input['show_on_search']))
            $new_input['show_on_search'] = absint($input['show_on_search']);
        if(isset($input['view_tracking_enabled']))
            $new_input['view_tracking_enabled'] = absint($input['view_tracking_enabled']);
        if(isset($input['click_tracking_enabled']))
            $new_input['click_tracking_enabled'] = absint($input['click_tracking_enabled']);
        if(isset($input['keep_old_stats_on_update']))
            $new_input['keep_old_stats_on_update'] = absint($input['keep_old_stats_on_update']);
        if(isset($input['show_quick_stats']))
            $new_input['show_quick_stats'] = absint($input['show_quick_stats']);

        if(isset($input['desc_length']))
            $new_input['desc_length'] = intval(sanitize_text_field($input['desc_length']));
        if(isset($input['button_text']))
            $new_input['button_text'] = sanitize_text_field($input['button_text']);

        return $new_input;
	}
	
}
