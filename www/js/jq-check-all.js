jQuery(function() { 
	jQuery('.check-all').click(function(){ 	
		jQuery ('.check-import-export').attr('checked', jQuery(this).is(':checked'));
	});

	jQuery('.check-import-export').click(function(){
		jQuery ('.check-all').attr('checked', false);
	});
});	