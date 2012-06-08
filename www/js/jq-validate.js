(function(jQuery){
    jQuery.fn.ksValidate = function(optionsArgs) {
        
        var options = {
            'fields':'all',
            'errorText':'Это поле не должно быть пустым',
            'selectErrorText':'Выберите значение из списка',
            'emailErrorText':'Это не e-mail',
            'emailPasswordError':'Пользователь с таким email-ом или паролем уже существует',
            'emailErrorConfirmPassword':'Пароли не совпадают',
            'noTestFieldsId':[]
            
        };
        
        options = jQuery.extend(options, optionsArgs);
        
       
        
        var isTestedFields = function(field) {      
            for (key in options.noTestFieldsId) {       
              
                if (field == options.noTestFieldsId[key]) {                     
                    return false;
                }
            }
            return true;
        }
        
        var testEmail = function(obj) {
            var jqObject = jQuery(obj);
            if (jqObject.attr('name') == 'email') {
                reg = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;                   
                return reg.test( jqObject.val());
            }
            return true;
        }
        
        var testPassword = function() {     
            var ret = true;            
            if (jQuery('.right_column input').is('[name=email]') && jQuery('.right_column input').is('[name=password]')) {
                
                jQuery.ajax({
                    'url':'/ajax/registratetest',
                    'async': false,
                    'type':'POST',
                    'data':{
                        'email':jQuery('[name=email]').val(),
                        'password':jQuery('[name=password]').val()
                    }, 
                    'success': function(data) {
                        
                        if (data != 'true') {
                            jQuery('[name=password]').parent('p').
                            addClass('error').
                            children('span.error-message').text(options.emailPasswordError);  
                            ret = false;
                        }
                    }
                });
              
            }          
            
            return ret;
        }
        
        var testConfirmPassword = function() { 
            if (jQuery('input').is('[name=password]') && jQuery('input').is('[name=password_confirm]')) {
              
                if (jQuery('input[name=password]').val() != jQuery('input[name=password_confirm]').val()) {
                    jQuery('[name=password_confirm]').parent('p').
                    addClass('error').
                    children('span.error-message').text(options.emailErrorConfirmPassword);  
                    return false;
                }
            }
            return true;
        }
        
        
        var testCaptcha = function() {
            var ret = true;
            if (jQuery('input').is('[name=captcha_input]') && jQuery('input').is('[name=captcha_id]')) {   
              
                jQuery.ajax({
                    'url':'/ajax/testcaptcha',
                    'async': false,
                    'type':'POST',
                    'data':{                 
                        'captcha_input':jQuery('input[name=captcha_input]').val(),
                        'captcha_id':jQuery('input[name=captcha_id]').val()                
                    }, 
                    'success': function(data) { 
                        if (data != 'true') {                            
                            ret = false;
                            jQuery('span.captcha'). addClass('error'); 
                            jQuery('div.capcha span.error-message').text(data); 
                            jQuery('input[name=captcha_input]').attr('disabled', true);
                            
                            
                            jQuery.get('/ajax/capcha', {}, function(data){                                  
                                jQuery('input#captcha_id').val(data);
                                jQuery('.capcha img').attr('src', '/img/captcha/'+data+'.png');
                                jQuery('input[name=captcha_input]').attr('disabled', false);                               
                                
                            
                            });
                        } else {
                            jQuery('span.captcha'). removeClass('error'); 
                            jQuery('div.capcha span.error-message').text(''); 
                        } 
                
                    }
                });
              
            }
            
            return ret;
        }
        
        return this.each(function (){
            var isRun = true;
            var objId = '';
            for(var i = 0; i < this.elements.length; i++) {
                var obj = this.elements[i];
                
                if (isTestedFields(jQuery(obj).attr('id'))) {
               
                    if (obj.type == 'select-one' ) {
                        
                        jQuery(obj).change(function(){
                            
                       
                            if (jQuery(this).val() == '' && isTestedFields(jQuery(obj).attr('id'))) {
                                if ((jQuery(obj).is(":visible"))) {
                                jQuery(this).parent('p').
                                addClass('error').
                                children('span.error-message').text(options.errorText);                              
                                isRun = false;
                                }
                            } else {
                                jQuery(this).parent('p').
                                removeClass('error')
                                .children('span.error-message').text('');
                                isRun = true;
                            }
                        });
                    } else if ( (obj.type == 'text' || obj.type == 'textarea' || obj.type == 'password' )) {                    
                    
                        jQuery(obj).focus(function(){                                            
                            jQuery(this).parent('p').
                            removeClass('error')
                            .children('span.error-message').text('');                        
                            isRun = true;
                        
                            if (jQuery('input').is('[name=captcha_input]') && jQuery('input').is('[name=captcha_id]')) {          
                                jQuery('span.captcha'). removeClass('error'); 
                                jQuery('div.capcha span.error-message').text(''); 
                            }
                        
                        });
                   
                        jQuery(obj).blur(function(){                                            
                            var testValue = new String(jQuery(obj).val().replace(' ', ''));
                       
                            if ((jQuery(obj).is(":visible")) && jQuery(this).val() == '' || new String(jQuery(obj).val().replace(' ', '')) == '' ) {                       
                                isRun = false;
                                objId = jQuery(obj).attr('name'); 
                                jQuery(this).parent('p').
                                addClass('error').
                                children('span.error-message').text(options.errorText);                                                    
                                
                        
                            } 
                            
                            
                            if ((jQuery(obj).is(":visible")) && !testEmail(obj)) {
                                    jQuery(this).parent('p').
                                    addClass('error').
                                    children('span.error-message').text(options.emailErrorText);  
                                }
                        });                       
                
                    } 
                }
            }
            
            jQuery(this).submit(function() {                
                for(var i = 0; i < this.elements.length; i++) {
                    var obj = this.elements[i];
                    
                    var valTmp = jQuery(obj).val().replace(' ', '');
                    
                    if ((jQuery(obj).is(":visible")) && (jQuery(obj).val() == '' || valTmp == '')) {
                        var errorMessage = options.errorText;
                        if ((obj.type == 'text' || obj.type == 'textarea' || obj.type == 'select-one')  && isTestedFields(jQuery(obj).attr('id'))) {
                            
                            if (obj.type == 'select-one') {
                                errorMessage = options.selectErrorText;
                            }
                            jQuery(obj).parent('p').
                            addClass('error').
                            children('span.error-message').text(errorMessage);                            
                            
                            
                            objId = jQuery(obj).attr('name'); 
                        
                            if (objId != 'captcha_input') {                               
                                isRun = false;                            
                            } 
                            if (!testEmail(obj)) {                         
                            jQuery(obj).parent('p').
                            addClass('error').
                            children('span.error-message').text(options.emailErrorText);  
                            isRun = false;
                        }
                            
                        }
                    } 
                }
                                
              
                                
                if (isRun) {
                    isRun = testCaptcha();
                   
                }                
                                
                    
                if (isRun) {                    
                    
                    if ((isRun = testConfirmPassword())) {                   
                        isRun = testPassword(obj);                        
                    }
                }    
                
                
               
                return isRun;
            });
        });
        
    }
})(jQuery);

