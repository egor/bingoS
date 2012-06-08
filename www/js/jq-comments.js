// Отзыв полезен/не полезен
var commentVote = function(id, type, obj) {
    if (type == 'yes' || type == 'no') {        
       
        jQuery.post(
            '/ajax/commentary', 
            {
                'action':'comment_vote',
                'type':type,
                'id':id
            },
            function (data) {
                var yesCount = parseInt(jQuery('.yes_count[id='+id+']').text());    
                var noCount = parseInt(jQuery('.no_count[id='+id+']').text());    
       
                if (type == 'yes') {
                    yesCount++;
                }
        
                if (type == 'no') {
                    noCount++;
                } 
                jQuery(obj).parent('.comment_vote').html('  Отзыв полезен? <span class="yes-no"> Да </span> <span class="yes_count">'+yesCount+'</span> / <span class="yes-no"> Нет </span> <span class="no_count">'+noCount+'</span>');
      
            });
    } 
    return false;
}

jQuery(function(){
    $(".wrap_comments .rate_stars .rate").stars({
        captionEl: $('.wrap_comments .rate_stars .caption')
    });
        
    jQuery('input.error, .f_textarea.error').focus(function(){
        jQuery(this)
        .removeClass('error')
        .prev('span')
        .removeClass('error');
        jQuery(this).next('span.f_error')
        .remove();
        if (jQuery(this).attr('name') == 'code') {
            jQuery('.f_protect_error').remove();
        }
    });     
    
    jQuery('.toggle').click(function() {
        //  jQuery('.f_text, .f_textarea').val('');
        jQuery('.wrap_comments_form').slideDown();
        hideMessage();
    });
    
    var hideMessage = function() {
        
        jQuery('#message').slideUp(2000);
    }
    
    if (jQuery('#message').is(':visible')) {
        setTimeout(hideMessage, 3000);
    }
    
});

var captchaReload = function () {
  
    jQuery.get('/ajax/capcha',  function(data){
        jQuery('input#captcha_id').val(data);        
        jQuery('.protect img').attr('src', '/img/captcha/'+data+'.png');
    });

}

var addAdminMessage = function (id) { 
    jQuery('.basket_form').attr('action', '/commentsactions/add');
    if (jQuery('.comments .wrap_item .wrap_quote_item#form').is(':visible')) {
        jQuery('.comments .wrap_item .wrap_quote_item#form').hide();
        jQuery('.basket_form').attr('action', '/commentsactions/adminmessage/'+id);
     
    } else {
        jQuery('.comments .wrap_item .wrap_quote_item#form').show().css('display','inline-block');
    }
    
}



