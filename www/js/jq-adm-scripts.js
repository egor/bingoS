/* 
 * Разные скрипты для админки 
 */

var objDump = function(obj) {
    var ret = '';
    for (o in obj) {
        ret +='Key: '+o+' Value: '+obj[o]+"\n";
      
    }
    return ret;
}

var datePickerOptions = {
    dateFormat: 'dd-mm-yy',
    monthNames: ['Январь', 'Февраль', 'Март', 
    'Апрель', 'Май', 'Июнь', 
    'Июль' , 'Август', 'Сентябрь',
    'Октябрь', 'Ноябрь', 'Декабрь'],
				
    monthNamesShort: ['Янв', 'Фев', 'Мрт', 
    'Апр', 'Май', 'Ин', 
    'Ил' , 'Авг', 'Сент',
    'Окт', 'Нбр', 'Дкб'],
    dayNames: ['Воскресенье','Понедельник','Вторник',
    'Среда','Четверг','Пятница','Суббота'],
							   
    dayNamesMin: ['Вос','Пон','Втр',
    'Срд','Чтв','Птн','Суб'],
    onSelect: function(){}
        
};

var dateRange = function(goToUrl) {
    
   
    
    jQuery(function(){
            
        var date1 = 0;
        var date2 = 0;
        var myId = jQuery(this).attr('id');
        jQuery('#date-range-win').dialog({
            width: 536,
            height: 377,
            resizable: false,
            buttons: {
                'Ok': function() {
                    
                    var d1 = jQuery('#datepicker-text').text();
                    var d2 = jQuery('#datepicker-text1').text();
                    //var reg = ;
                    var d1Index = d1.search(/\d{2}\-\d{2}\-\d{4}/);
                    var d2Index = d2.search(/\d{2}\-\d{2}\-\d{4}/);
                    
                    date1 = d1Index != -1 ? d1.substr(d1Index, d1.length) : 0;
                    date2 = d2Index != -1 ? d2.substr(d2Index, d2.length) : 0;
                   
                    if (date1 == 0 && date2 == 0) {
                        alert('Укажите дату');
                    } else {
                        if (date1 != 0) {
                            date1 = date1.split("-");					
                            date1 = date1[2]+'-'+date1[1]+'-'+date1[0];								
                        }
							
                        if (date2 != 0) {
                            date2 = date2.split('-');
                            date2 = date2[2]+'-'+date2[1]+'-'+date2[0];
                        }
                            
                        if (date1 != 0 && date2 != 0) {
                            goToUrl+="?date1="+date1+"&date2="+date2;
                        }
                            
                        if (date1 != 0 && date2 == 0) {
                            goToUrl+="?date1="+date1;
                        }
				
                        if (date1 == 0 && date2 != 0) {
                            goToUrl+="?date2="+date2;
                        }
                        document.location.href = goToUrl;
                    }
                },
                'Отмена' : function() {
                    jQuery('#date-range-win').dialog('close');
                }			
            },		
            open:function(){	
                    
                datePickerOptions.onSelect = function(text) {
                    date1 = text;                        
                    jQuery('#datepicker-text').text('От: '+text);
                }
                
                jQuery("#datepicker").datepicker(datePickerOptions);
                datePickerOptions.onSelect = function(text) {
                    date2 = text;
                    jQuery('#datepicker-text1').text('До: '+text);
                }	
                jQuery("#datepicker1").datepicker(datePickerOptions);

                jQuery('#datepicker-text').text('От: ');	
                jQuery('#datepicker-text1').text('До: ');
            }
        });
    });
    return false;
}





var showDataPicker = function() { 
    jQuery(function() {
        datePickerOptions.altFormat = 'dd.mm.yy';
        datePickerOptions.dateFormat = 'dd.mm.yy';
        jQuery( "#date_i" ).datepicker(datePickerOptions);              
        jQuery( "#date_i" ).datepicker("show");  
		
    });
    
}

jQuery(function(){
   
  
   
    jQuery('#section-group').change(function(){
        if (jQuery(this).val() == 'new') {
            jQuery(this).hide();//css('display', 'none');
            jQuery('#new-section-group').show();
            jQuery('#section-fields-edit-group').hide(); 
        } else if (jQuery(this).val() != 'A1') {
            jQuery('#section-fields-edit-group').show();          
        } else {
            jQuery('#section-fields-edit-group').hide(); 
        }
    });
   
    jQuery('#section-sub-group').change(function(){
        if (jQuery(this).val() == 'new') {
            jQuery(this).hide();//css('display', 'none');
            jQuery('#new-section-sub-group').show();
            jQuery('#section-fields-edit-sub-group').hide(); 
        } else if (jQuery(this).val() != 'A1') {
            jQuery('#section-fields-edit-sub-group').show();          
        } else {
            jQuery('#section-fields-edit-sub-group').hide(); 
        }
    });
   
    jQuery('#new-section-group a').click(function(){
        jQuery('#new-section-group').css('display', 'none');
        jQuery('#section-group option:first').attr('selected', 'selected');
        jQuery('#section-group').show();
        return false;
    });
   
    jQuery('#new-section-sub-group a').click(function(){
        jQuery('#new-section-sub-group').css('display', 'none');
        jQuery('#section-sub-group option:first').attr('selected', 'selected');
        jQuery('#section-sub-group').show();
        return false;
    });
   
  
   
    var fields = {
        'basic-fields':true,
        'seo-tags':false,
        'info-fields':false,
        'photo':false,
        'description':false,
        'features':false
    };
   
  
   
   
    for (field in fields) { 
        if (fields[field]) {
            jQuery("."+field).show();   
            jQuery("."+field+' td').css('background-color', '#F8F8F8'); 
        } else {
            jQuery("."+field).css('display', 'none');
        }      
        fields[field] = !fields[field];
    }
   
   
    var adminLinkClick = function() {
   
        jQuery('.admin-table a').click(function(){

            if (jQuery(this).attr('id') != 'upload-button') { 
                if (jQuery(this).attr('class').indexOf('adm-href') != -1) { 
                    for (field in fields) {          
                        if (jQuery(this).attr('id') == field) {

                            jQuery("."+field+' td').css('background-color', '#F8F8F8');            
                            //jQuery("."+field).show();
                            jQuery("."+field).slideDown();
                        } else {
                            //jQuery("."+field).hide();
                            jQuery("."+field).slideUp();
                        }


                    }
                    return false;
                } 
            }
         
         
        });
     
    }();
   
   
   
    jQuery('select[name=is_use_unique_goods_names]').change(function(){ 
        if (jQuery(this).val() == '1') {         
            jQuery('#tr-con-fields').show();       
        } else {
            jQuery('#tr-con-fields').hide();
        }
    });
   
    jQuery('select[name=is_show_hits]').change(function(){ 
        if (jQuery(this).val() == '1') {         
            jQuery('#con-fields-is-show-hits').show();       
        } else {
            jQuery('#con-fields-is-show-hits').hide();
        }
    });
   
    jQuery('select[name=is_show_new]').change(function(){ 
        if (jQuery(this).val() == '1') {         
            jQuery('#con-fields-is-show-new').show();       
        } else {
            jQuery('#con-fields-is-show-new').hide();
        }
    });
 
 
    jQuery('select[name=is_show_actions]').change(function(){ 
        if (jQuery(this).val() == '1') {         
            jQuery('#con-fields-is-show-actions').show();       
        } else {
            jQuery('#con-fields-is-show-actions').hide();
        }
    });
   
    jQuery('.show-fields').click(function() {      
        var id = jQuery(this).attr('id');
        // alert(id);
        jQuery('.fields-exists').hide();
        jQuery('.field-exists-'+id).show();  
        return false;
      
    });
   

    if ((jQuery('#section-field-templates').attr('id')) != undefined ) {
        var sectionFieldsTemplates1 = null;
        //        
        jQuery('#show-field').click(function(){
            return false;
        });
      
        var getFieldParams = function(tpl, field) { 
          
            for (var tplTmp in tpl) {             
                var tplTmpVal = tpl[tplTmp];               
                for (var i = 0; i < tplTmpVal.fields.length; ++i) {                 
                    var sectionFieldsTemplatesFieldTmp1 = tplTmpVal.fields[i];
                    if (sectionFieldsTemplatesFieldTmp1.name == field) {
                        return sectionFieldsTemplatesFieldTmp1;
                    }
                }
            }
            return false;
        }
        
  
    }
   
    //jQuery('.admin-table input[type=text]').parent('td').append("<a href='#'><img src='/img/admin_icons/clear_left.png'></a>");
  
    jQuery("a[href*='deleteotziv']").unbind('click').click(function(){
        var id = jQuery(this).attr('href').split('/');     
        if (id[1] != undefined) {
            id = id[1];
            jQuery.post('/ajax/otzivi', {
                'id':id, 
                'action':'dell'
            }, function(data) {
                jQuery('li[id='+id+'] ').remove();  
                alert(data);
                return false;
            });
            
        }
        return false;
    });
   
  
    jQuery("a[href*='editotziv']").unbind('click').click(function(){
        var id = jQuery(this).attr('href').split('/');
        if (id[1] != undefined) {
            id = id[1];
            jQuery.post('/ajax/otzivi', {
                'id':id, 
                'action':'get'
            }, function(data) {
                if (data.otziv.id !== undefined) {
                    jQuery('select[name=section]').parent('p').hide();
                    jQuery('select[name=goods_list]').parent('p').hide();
                    jQuery('input[name=fio]').val(data.otziv.fio);
                    jQuery('input[name=email]').val(data.otziv.email);
                    jQuery('input[name=city]').val(data.otziv.city);                  
                    jQuery('textarea[name=conclusion]').val(data.otziv.body);                        
                    jQuery('.oform_block').show();                                    
                    jQuery('#otzivi-button').unbind('click').click(function(){                        
                        jQuery.post('/ajax/otzivi', 
                        {
                            'id':data.otziv.id, 
                            'action' : 'update',
                            'fio':jQuery('input[name=fio]').val(),
                            'email':jQuery('input[name=email]').val(),
                            'city':jQuery('input[name=city]').val(),
                            'body':jQuery('textarea[name=conclusion]').val()
                        }, function(data) {                            
                            jQuery('input[name=fio]').val('');
                            jQuery('input[name=email]').val('');
                            jQuery('input[name=city]').val('');
                            jQuery('textarea[name=conclusion]').val('');  
                            jQuery('li[id='+id+'] div.desc').html(data); 
                            jQuery('.oform_block').toggle(); 
                            return false;
                        });
                        return false;
                    });
                }
            }, 'json');

        }
        return false;
    });
    
    jQuery('.table-comment input[type=checkbox]').click(function(){
        
        if (jQuery(this).attr('id') == 'check-all') {
            jQuery('.table-comment input[type=checkbox]').attr('checked', jQuery(this).attr('checked'));
        }
        var size = jQuery('.table-comment input[type=checkbox]:checked').size();
        if (size > 0) {
            jQuery('#checked-count').html('('+size+' шт.)');
            jQuery('#p-comment-list').show();
        } else {
            jQuery('#p-comment-list').hide();
        }
        
        
    });
    
    jQuery('#comment-show-all').click(function() {
        
    
        if ((jQuery('.table-comment input[type=checkbox]').size() - 1) == jQuery('.comment-info-block:visible').size()) {
            jQuery(this).text('Развернуть все');
            jQuery('.comment-info-block').slideUp();
        } else {
            jQuery('.comment-info-block').slideDown();
            jQuery(this).text('Свернуть все');
            
        }
        return false;
    });

});

var otzivToggle = function (obj) {    
    jQuery(function(){
        jQuery.post('/ajax/otzivi', {
            'id':jQuery(obj).attr('id'), 
            'action':'toggle'
        }, function(data) {
            jQuery(obj).text(data);           
        }); 
    });        
}

var showCommentInfoBlock = function (id) {
    var trObject = jQuery('#tr-'+id);
    if (!trObject.is(':visible')) {
        trObject.slideDown().css('background-color', 'EBEBEB');
        
    } else {
        trObject.slideUp().css('background-color', 'FFFFFF');
      
        
    }
}

var commentaryActive = function(id, obj) {
    var isHide = (jQuery(obj).text() == 'Скрыть');

  
    jQuery.post('/ajax/commentary', {
        'id':id, 
        'action':(!isHide ? 'active' : 'hide')
    }, function(data){
        jQuery(obj).text(data);
        jQuery('#span-comment-ststus-'+id).text((!isHide ? 'Актывный' : 'Скрытый'));
    });
}


var commentActiveAll = function(actionIndex) {
    
    var url = 'action='+(actionIndex == 1 ? 'active' : 'hide');
    var counter = 0;
    if (jQuery('.table-comment input[type=checkbox]:checked').size() > 0) {
        jQuery('.table-comment input[type=checkbox]:checked').each(function(){
            var id = jQuery(this).val();
            url += '&id['+counter+']='+ id;
            var txt1 = jQuery('#span-comment-ststus-'+id).text();
            if (actionIndex == 1) {
                jQuery('#span-comment-ststus-'+id).text('Актывный');
                jQuery('#span-comment-ststus-'+id).next('a').text('Скрыть');
            } else {
                jQuery('#span-comment-ststus-'+id).text('Скрытый');
                jQuery('#span-comment-ststus-'+id).next('a').text('Активировать');
            }
            counter++;           
        });
    }
  
    jQuery.ajax({
        'url':'/ajax/commentary',
        'type':'POST',
        'data':url,
        'success':function(data){
        // alert(data);
        }
    });
    
    
}

var commentDelete = function(id) {
    var url = 'action=delete';  
    var hideScript = '';
    var hideScript1 = '';
    if (id === null) {        
        if (!confirm('Удалить комментарии?')) return;
        var counter = 0;
        if (jQuery('.table-comment input[type=checkbox]:checked').size() > 0) {
            jQuery('.table-comment input[type=checkbox]:checked').each(function(){ 
                var tmpId = jQuery(this).val();
                url += '&id['+counter+']='+ tmpId;  
                hideScript += (counter > 0 ? ',' : '')+'#tr-'+tmpId;
                hideScript1 += (counter > 0 ? ',' : '')+'#tr1-'+tmpId;
                counter++;           
            });
        }
    } else {
        if (!confirm('Удалить комментарий?')) return;
        url += '&id='+id;
        hideScript = '#tr-'+id;
        hideScript1 = '#tr1-'+id;
    }
    
    
    jQuery.ajax({
        'url':'/ajax/commentary',
        'type':'POST',
        'data':url,
        'success':function(data){    
            
            jQuery(hideScript).remove();
            jQuery(hideScript1).remove();
            
            var size = (jQuery('.table-comment input[type=checkbox]').size()-1);
            if (size > 0) {
                jQuery('#checked-count').text('('+size+' шт.)');
            } else {
               jQuery('#p-comment-list').hide();
            }
           
        }
    });
    
    
}