(function( $ ) {
	'use strict';
	
	$.fn.papspaSavePostAds= function() {
		var postId = $('input[name="post_ID"]').val();
		var products = $('select.papspa_product_select').val();
		var categories = $('select.papspa_category_select').val();
		var type = $('input[name="papspa_type"]').val();
		var nonce = $('input[name="_papspanonce"]').val();
		
		var data = {
			'action': 'papspa_save_pap_ads',
			'postId': postId,
			'type': type,
			'products': products,
			'categories': categories,
			'_papspanonce': nonce,
		};

		jQuery.post(ajaxurl, data, function(response) {
			$('.papspa_save_ads_spinner').removeClass('active');
		});
	};
	$.fn.papspaClearStats= function() {
		var postId = $('input[name="post_ID"]').val();
		var catId = $('input[name="tag_ID"]').val();
		var nonce = $('input[name="_papspanonce"]').val();
		var type = $('input[name="papspa_type"]').val();
		var data = {
			'action': 'papspa_clear_pap_stats',
			'postId': postId,
			'catId': catId,
			'type': type,
			'_papspanonce': nonce,
		};

		jQuery.post(ajaxurl, data, function(response) {
			$('.papspa_save_ads_spinner').removeClass('active');
			$('.papspa_stats').remove();
			$('.papspa_stats_container').removeClass('extended');
			$('.papspa_stats_message').removeClass('papspa_hide');
		});
	};
	
	$(window).on('load',function(){
		$('.papspa_select2').select2();
	});
	$(document.body).on('click','.papspa_select2_buttons .select_all_options',function(e){
		e.preventDefault();
		$(this).closest('.papspa_select2_container').find('.papspa_select2 option').prop("selected",true).trigger("change");
	});
	$(document.body).on('click','.papspa_select2_buttons .clear_all_options',function(e){
		e.preventDefault();
		$(this).closest('.papspa_select2_container').find('.papspa_select2 option').prop("selected",false).trigger("change");
	});
	$(document.body).on('change','.papspa_product_select',function(){
		$('.papspa_select2_container.categories').find('option').removeAttr('selected').trigger("change.select2");
	});
	$(document.body).on('change','.papspa_category_select',function(){
		$('.papspa_select2_container.products').find('option').removeAttr('selected').trigger("change.select2");
	});
	$(document.body).on('click','.papspa_clear_stats',function(e){
		e.preventDefault();
		$('.papspa_save_ads_spinner').addClass('active');
		$(this).papspaClearStats();
	});
	$(document.body).on('click','.papspa_save_ads',function(e){
		e.preventDefault();
		$('.papspa_save_ads_spinner').addClass('active');
		$(this).papspaSavePostAds();
	});
	$(document.body).on('click','.papspa_extend_stats',function(e){
		e.preventDefault();
		var target = $(this).closest('.papspa_stats_container');
		if(!target.hasClass('extended')) target.addClass('extended');
		else target.removeClass('extended');
	});

})( jQuery );


