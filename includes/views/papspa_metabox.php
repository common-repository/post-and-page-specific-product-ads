<?php
/**
 * Metabox for the post/page edit screen
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/includes/views
 */
?>	
<div class="papspa_wrap">
	<input type="hidden" name="_papspanonce" value="<?php echo $nonce;?>" />
	<input type="hidden" name="papspa_type" value="<?php echo $type;?>" />
	<div class="papspa_select2_container products">
		<label for="papspa_select2_products[]"><?php echo __('Products','post-and-page-specific-product-ads'); ?>:</label>
		<select class="papspa_product_select papspa_select2" multiple="multiple" name="papspa_select2_products[]">
			<?php echo $prod_options; ?>
		</select>
		<div class="papspa_select2_buttons">
			<button class="button plus select_all_options"><?php echo __('Select all','post-and-page-specific-product-ads'); ?></button>
			<button class="button minus clear_all_options"><?php echo __('Select none','post-and-page-specific-product-ads'); ?></button>
		</div>
	</div>
	<div class="papspa_select2_container categories">
		<label for="papspa_select2_categories[]"><?php echo __('Categories','post-and-page-specific-product-ads'); ?>:</label>
		<select class="papspa_category_select papspa_select2" multiple="multiple" name="papspa_select2_categories[]">
			<?php echo $cat_options; ?>
		</select>
		<div class="papspa_select2_buttons">
			<button class="button plus select_all_options"><?php echo __('Select all','post-and-page-specific-product-ads'); ?></button>
			<button class="button minus clear_all_options"><?php echo __('Select none','post-and-page-specific-product-ads'); ?></button>
		</div>
	</div>
	<div class="papspa_form_buttons">
		<button class="button papspa_clear_stats"><?php echo __('Clear stats','post-and-page-specific-product-ads'); ?></button>
		<button class="button button-primary papspa_save_ads"><?php echo __('Save','post-and-page-specific-product-ads'); ?></button>
		<div class="spinner papspa_save_ads_spinner"></div>
	</div>
</div>
<?php echo $stats_container;?>
<div class="clear"></div>