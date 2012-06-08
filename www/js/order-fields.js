jQuery(function() {
	jQuery('#delivery-departament-div').hide();
	jQuery('#div-delivery1').hide();	
	
	
	
	var deliveryList = function () {
			
		jQuery('#div-delivery').hide();//html('<input type="text" name="delivery" value="" class="f-txt"><a href="#" id="back-to-list">Вернуться к списку</a>');	
			jQuery('#div-delivery1').show();	
			jQuery('#delivery-method-div').html('<span>*</span> Способ доставки: <a href="#" id="back-to-list" style="margin-left:25px;">Вернуться к списку</a>');
			jQuery('#back-to-list').click(function(){
				jQuery('#delivery').val(0);	
				jQuery('#delivery-addres-div-text').html('<span>*</span> Адрес доставки (Улица, № дома, квартира / офис):');		
			jQuery('#delivery-address-div-field').html('<input type="text" name="addr" value="" class="f-txt">');	
				jQuery('#delivery-method-div').html('<span>*</span> Способ доставки: ');
				jQuery('#div-delivery').show();	
				jQuery('#delivery-departament-div').hide();			
				jQuery('#div-delivery1').hide();
				
				return false;
			});
	}
	jQuery('#addr').click(function(){
		var txt = jQuery(this).val();		
		if (txt != '' && txt.match('/Улица|дома|квартира|офис/')) {
			jQuery(this).val('');	
		}
		
	});
	jQuery('#delivery').change(function(){
		
		if (jQuery(this).val() == 'other') {
				deliveryList();	
		}
		if (jQuery(this).val() != 'home' ) {
			//jQuery('#delivery-departament-div').show();	
			jQuery('#delivery-addres-div-text').text('Отделение службы доставки:');	
			jQuery('#delivery-address-div-field').html('<input type="text" name="delivery_department" id="addr" value="" class="f-txt">');				
		} else {
			//jQuery('#delivery-departament-div').hide();		
			jQuery('#delivery-addres-div-text').html('<span>*</span> Адрес доставки (Улица, № дома, квартира / офис):');		
			jQuery('#delivery-address-div-field').html('<input type="text" name="addr" value="" class="f-txt">');	
		}
	});
	
	var isShow = (jQuery('input:radio[name=delivery_office]:checked').val() == '1');
//	alert(isShow);
	//jQuery('#delivery-block').hide();	
		
	
	jQuery('input:radio[name=delivery_office]').click(function(){
		
		if (jQuery('input:radio[name=delivery_office]:checked').val() == '0') {
			jQuery('#delivery-block').hide();	
			
		} else {
			jQuery('#delivery').val(0);			
			jQuery('#delivery-block').show();			
			if (jQuery('#delivery').val() == 'other') {
				deliveryList();
			}
		}
		isShow = !isShow;
		
	});
	
	if (!isShow) {
			jQuery('#delivery-block').hide();			
		} else {			
			jQuery('#delivery-block').show();			
			if (jQuery('#delivery').val() == 'other') {
				deliveryList();
			}
		}
	
});
