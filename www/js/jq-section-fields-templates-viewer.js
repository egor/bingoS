
jQuery(function(){    
    
    
    var isValue = function(value) {
        var ret = false;
        jQuery('#section-field-templates-selected-field option').each(function(){               
            if (value == this.value) { 
                ret = true;
                return;
            }
        }); 
        return ret;
    }
    
    
    jQuery('#send-one').click(function(){         
        jQuery('#section-field-templates-selected-field').attr('disabled', false);
        var text = jQuery('#section-field-templates-list option:selected').text();         
        var value = jQuery('#section-field-templates-list option:selected').val();         
        var isFieldExists = false;
            
        jQuery('#section-field-templates-selected-field option').each(function(){             
            if (this.text == text) {
                isFieldExists = true;                              
            } 
        });

        if (isFieldExists) {              
            alert('Поле '+text+' уже существует');
        } else {                 
            var str = "<option value='"+value+"'>"+text+"</option>";
            jQuery('#section-field-templates-selected-field').append(str);               
        }        

        if ((jQuery('#section-field-templates-list').get(0).selectedIndex = (jQuery('#section-field-templates-list').get(0).selectedIndex + 1)) == 
            jQuery('#section-field-templates-list option').size()) {
            jQuery('#section-field-templates-list').get(0).selectedIndex = 0;
        }
            
    });
    
    jQuery('#send-all').click(function(){                  

        jQuery('#section-field-templates-list option').each(function(){
            var value = this.value;
            var text = this.text;

            if (!isValue(value)) {
                var str = "<option value='"+value+"'>"+text+"</option>";
                jQuery('#section-field-templates-selected-field').append(str);
            }             
        });
        jQuery('#section-field-templates-list').get(0).selectedIndex = 0;
        jQuery('#section-field-templates-selected-field').get(0).selectedIndex = 0;
        jQuery('#section-field-templates-selected-field').attr({
            'size': '15', 
            'disabled':false
        });
        jQuery('#div-section-field-templates-selected-type').show(); 
    });
    
    
    jQuery('#back-all').click(function(){
        jQuery('#div-section-field-templates-selected-type').hide();  
        jQuery('#section-field-templates-selected-field option').remove();
        jQuery('#section-field-templates-selected-field').attr({
            'size': '15', 
            'disabled':true
        });
    });
    
    
    jQuery('#back-one').click(function(){
        
        jQuery('#section-field-templates-selected-field option:selected').remove();
        if ((jQuery('#section-field-templates-selected-field').get(0).selectedIndex = (jQuery('#section-field-templates-selected-field').get(0).selectedIndex + 1)) && 
            jQuery('#section-field-templates-selected-field option').size() > 0) {            

        }
        
        if (jQuery('#section-field-templates-selected-field option').size() <= 0 ) {
            //jQuery('#div-section-field-templates-selected-type').hide(); 
            jQuery('#section-field-templates-selected-field').attr({
                'size': '15', 
                'disabled':true
            });
            
        }
        
    });
    
    jQuery('table.admin-table select').each(function(){
        if (this.id != 'select-template-type' && this.id != 'select-template-type-goods') {
            this.disabled = true;
            jQuery('#'+this.id+' option').remove();
                
        }
    });
    jQuery('table.admin-table td select#select-template-type-goods').hide();
    
    
   
    
    var loadSelectedSections = function () {
        jQuery.ajax({
            'url':'/ajax/loadselectedsections',              
            'type':'POST',
            'dataType':'JSON',
            'success': function(data) {
                jQuery('#section-field-templates option').remove();
                jQuery('#section-field-templates').attr('disabled', false).append('<option value="#">Укажите шаблон</option>');
                
                for (var i = 0; i < data.length; i++) {
                    var obj = data[i];
                    jQuery('#section-field-templates').append('<option value="'+obj.artikul+'">'+obj.name+'</option>');
                }
            }
        });            
        
    }
    
    var loadSelectedTemplate = function () {
        jQuery('#section-field-templates').attr('disabled', false).parent('option').remove();
        jQuery('#section-field-templates option').remove();
            
        jQuery('#section-field-templates').append('<option value="#">Выберите группу</option>')
        .append('<option value="use_tempalte_goods_length">Размер товара</option>')
        .append('<option value="use_tempalte_pack_length">Размер в упаковке</option>')
        .append('<option value="use_tempalte_body">Внешний вид</option>')
        .append('<option value="use_tempalte_use">Применение</option>')
        .append('<option value="use_tempalte_weight">Вес</option>');
        
    }
    
    
    jQuery('#section-field-templates').change(function(){
        
        var isUseTemplate = 'no';
        if (jQuery(this).val().indexOf('use_tempalte_') !== -1) {
            isUseTemplate = 'yes';
        }
        
        jQuery.ajax({
            'url':'/ajax/showcatsections', 
            'data': {
                'artikul':jQuery(this).val(),
                'activesection':activeSection,
                'isLoadTemplate':isUseTemplate
            }, 
            'type':'POST',
            'dataType':'JSON',
            'success': function(data) {
                jQuery('#section-field-templates-list').attr('disabled', false);
                jQuery('#section-field-templates-list option').remove();
                
                if (isUseTemplate == 'yes') {
                    for (dataName in data) {                        
                        jQuery('#section-field-templates-list').append('<option value="'+dataName+':'+data[dataName]+'">'+data[dataName]+'</option>');
                    }
                    jQuery('#section-field-templates-list option:first').attr('selected', 'selected');
                } else {
                    for (var i = 0; i < data.length; i++) {
                        var obj = data[i];
                        jQuery('#section-field-templates-list').append('<option value="'+obj.title+'">'+obj.title+'</option>');
                    }
                    jQuery('#section-field-templates-list option:first').attr('selected', 'selected');
                }
                
                
            }
        });    
    });
    
    
   
    
    jQuery ('#select-template-type').change(function(){
        jQuery('table.admin-table td select#select-template-type-goods').hide();
        if ((value = jQuery(this).val()) == '#') {
            jQuery('table.admin-table select').each(function(){
                if (this.id != 'select-template-type' && this.id != 'select-template-type-goods') {
                    this.disabled = true;
                    jQuery('#'+this.id+' option').remove();
                
                }
            });
            jQuery('table.admin-table td select#select-template-type-goods').hide();
        } else {
            if (value == '1') {   
                loadSelectedTemplate();            
            } else if (value == '2') {       
               
                loadSelectedSections();
            }   
        }
    });
    
});