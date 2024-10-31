<?php
/**
 * Widget template
 *
 * @since      1.0.0
 * @package    PAPSPA
 * @subpackage PAPSPA/templates
 */
?>	
<div class="papspa_container widget">
	<a href="<?php echo $link;?>">
		<img class="papspa_img" src="<?php echo esc_url($imgUrl);?>" alt="<?php echo esc_attr($product_name);?>" title="<?php echo esc_attr($product_name);?>"/>
		<span class="product-title papspa_title"><?php echo $product_name;?></span>
	</a>
	<p class="papspa_desc"><?php echo $product_desc;?></p>
	<?php echo $price;?>
	<?php echo $button;?>
</div>
