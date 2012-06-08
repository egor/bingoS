jQuery(function(){
//	document.write("var formFieldsId = new Array(")
//	for (var i = 0; i < document.order_form.elements.length; i++) {
//		
//		document.writeln("'"+document.order_form.elements[i].id+"',");
//	}
//	document.write(");")

	var formFieldsId =  [
		{field:'fio', val:false}, 
		{field: 'email', val:false}, 
		{field:'city', val:false},
		{field:'modile_phone', val:false},
		{field:'phone', val:false},
		{field:'street', val:false},
		{field:'home', val:false},
		{field:'apartment', val:false}
		];
	
	var testedLength = 1;
	
	var blockedOrderForm = function(isBlicked) {
		if (isBlicked) {
			jQuery('#submit').attr('class', 'button2 notactive').attr('disabled', 'disabled');
			
		} else {
			jQuery('#submit').attr('class', 'button2').attr('disabled', '').click(function(){
				document.order_form.submit();
			});
			
		}	
	}
	
//	var testField = function(id, val) {
//		for(var i = 0; i < formFieldsId.length; i++) {
//			field = formFieldsId[i];			
//			if (field.field == id) {
//				
//				
//				if (val != '' ) {
//					field.val = true;
//					testedLength++;
//				} else {
//					testedLength--;
//				}
//				alert(testedLength);
//			}
//			
//		}
//	}
//	
//	jQuery('input[type=text]').keypress(function(){
//		testField(jQuery(this).attr('id'),  jQuery(this).val());
//	});
//	

	
	
	
	blockedOrderForm(false); 
	
	if (jQuery(':radio[name=delivery_type]').filter(":checked").val() != 'pickup') {
		jQuery('.delivery').show();
	}
	
/*	
	var deliveryList = function () {
		
		jQuery('#div-delivery').hide();
			jQuery('#div-delivery1').show();	
			jQuery('#delivery-method-div').html('Способ доставки: <a href="#" id="back-to-list" style="margin-left:25px;">Вернуться к списку</a>');
			jQuery('#back-to-list').click(function(){
				jQuery('#delivery_service').val(0);	
				jQuery('#delivery-addres-div-text').html('Отделение службы:');		
				jQuery('#delivery-address-div-field').html('<input type="text" name="addr" value="" class="f-txt">').show();	
					
				jQuery('#delivery-method-div').html('Способ доставки: ');
				jQuery('#div-delivery').show();	
				jQuery('#delivery-departament-div').hide();			
				jQuery('#delivery-address-div-field').hide();
				jQuery('#div-delivery1').hide();
				
				return false;
			});
	}


	jQuery('#delivery_service').change(function(){
		
		if (jQuery(this).val() == 'other') {
			deliveryList();	
		}
		if (jQuery(this).val() != 'home' ) {
			//jQuery('#delivery-departament-div').show();	
			jQuery('#delivery-addres-div-text').text('Отделение службы:');	
			jQuery('#delivery-address-div-field').html('<input type="text" name="delivery_department" id="addr" value="" class="f-txt">');				
		} else {
			//jQuery('#delivery-departament-div').hide();		
		//	jQuery('#delivery-addres-div-text').html('<span>*</span> Адрес доставки (Улица, № дома, квартира / офис):');		
		//	jQuery('#delivery-address-div-field').html('<input type="text" name="addr" value="" class="f-txt">');	
		}
	});

	*/
	
	
	
	jQuery('#delivery-type-service').click(function() {		
		jQuery('.delivery').show();	
			
	});
	
	jQuery('#delivery-type-pickup').click(function() { 
		jQuery('.delivery').hide();	
	});

	
});