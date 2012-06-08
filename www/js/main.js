
$(document).ready(function(){
	$('.products_list .p_tobusket .p_add').click(function(){
		$(this).next('.count').hide();
		$(this).hide();
		$(this).parent().find('.p_link_basket').show();
	});

	$('.wrap_product_item .attributes .add').click(function(){
		$(this).parent('.v').prev('.n').find('.count').hide();
		$(this).hide();
		$(this).next('.link_basket').show();
	});
	$('.filter label input:checked').parent('label').addClass('checked');
	$('.filter label input.checkbox').click(function(){
		$(this).parent('label').toggleClass('checked');
	});
	$('.filter label input.radio').click(function(){
		var name = $(this).attr('name');
		//alert('.filter label input.radio[name='+name+']');
		$('.filter label input.radio[name='+name+']').parent('label').removeClass('checked');
		$(this).parent('label').addClass('checked');
	});
	
	

	
});