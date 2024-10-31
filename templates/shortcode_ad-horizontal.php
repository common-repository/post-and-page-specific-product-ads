<?php
/**
 * Shortcode horizontal template
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/templates
 */
?>	
<div class="papspa_container horizontal">
	<div class="paspa_image paspa_left">
		<a href="<?php echo esc_url($link);?>">
			<img class="papspa_img" src="<?php echo esc_url($imgUrl);?>" alt="<?php echo esc_attr($product_name);?>" title="<?php echo esc_attr($product_name);?>"/>
		</a>
	</div>
	<div class="paspa_data paspa_right">
		<a href="<?php echo esc_url($link);?>">
			<span class="product-title papspa_title"><?php echo $product_name;?></span>
		</a>
		<p class="papspa_desc"><?php echo $product_desc;?></p>
		<?php echo $price;?>
		<?php echo $button;?>
	</div>
	<div class="clearfix"></div>
</div>
