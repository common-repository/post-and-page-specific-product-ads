<?php
/**
 * Settings page
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/includes/views
 */
?>	
<div class="wrap">
<h1><?php echo __('Post and Page Specific Product Ads','post-and-page-specific-product-ads'); ?></h1>

<form method="post" action="options.php">
	<?php settings_fields( 'papspa_settings_page' ); ?>
	<?php do_settings_sections( 'papspa_settings_page' ); ?>
    
    <?php submit_button(); ?>

</form>
</div>