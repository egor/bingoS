jQuery(function() {


    jQuery(".top_banner_list li:last").addClass("last");
    jQuery(".partners li:last").addClass("last");
    jQuery(".goods_list li:nth-child(3n)").addClass("last");
    jQuery(".akces_block li:nth-child(5n)").addClass("last");
    jQuery(".list_3d li:nth-child(6n)").addClass("last");
    jQuery(".har_block tr:even").addClass("bg");
    jQuery(".har_block tr td:even").addClass("width");
    jQuery(".basket_block tr td:nth-child(4n), .basket_block tr td:nth-child(6n), .basket_block tr td:nth-child(7n), .basket_block tr th:nth-child(3n), .basket_block tr th:nth-child(5n), .basket_block tr th:nth-child(6n)").addClass("right");
    jQuery(".basket_block tr td:nth-child(5n), .basket_block tr th:nth-child(4n)").addClass("center");
    jQuery(".basket_block tr td:nth-child(3n)").addClass("nowrap");
    jQuery('ul.sf-menu li:first').addClass("first");





    jQuery('p.error input').focus(function(){
        jQuery(this).parent('p').removeClass('error');
        jQuery('p span[id='+jQuery(this).attr('id')+']').empty();


    }).blur(function(){
        var val = jQuery(this).val();
        val = val.replace(' ', '');
        if (val == '') {
            jQuery(this).parent('p').addClass('error');
            jQuery('p span[id='+jQuery(this).attr('id')+']').text('Это поле не должно быть пустым');
        }
    });

    jQuery('p.error select').change(function(){
        if (jQuery(this).val() == '#') {
            jQuery(this).parent('p').addClass('error');
            jQuery('p span[id='+jQuery(this).attr('id')+']').text('Вы не выбрали способ доставки');
        } else {
            jQuery(this).parent('p').removeClass('error');
            jQuery('p span[id='+jQuery(this).attr('id')+']').empty();
        }
    });



    var myEmail = jQuery('#email');
    var myCity = jQuery('#city');
    var myStreet = jQuery('#street');
    var myHouse = jQuery('#house_num');
    var myAp = jQuery('#ap_num');
    var myInfo = jQuery('#dop_info');
    //	myEmail.focus(function() { if (jQuery(this).val() == 'Поиск по каталогу') {jQuery(this).val('');} });
    //	myEmail.blur(function() { if (jQuery(this).val() == '') {jQuery(this).val('Поиск по каталогу');} });
    //	myCity.focus(function() { if (jQuery(this).val() == 'Город') {jQuery(this).val('');} });
    //	myCity.blur(function() { if (jQuery(this).val() == '') {jQuery(this).val('Город').addClass('unfocus').removeClass('focus');} });
    //	myStreet.focus(function() { if (jQuery(this).val() == 'Улица') {jQuery(this).val('');} });
    //	myStreet.blur(function() { if (jQuery(this).val() == '') {jQuery(this).val('Улица').addClass('unfocus').removeClass('focus');} });
    //	myHouse.focus(function() { if (jQuery(this).val() == 'Номер дома') {jQuery(this).val('');} });
    //	myHouse.blur(function() { if (jQuery(this).val() == '') {jQuery(this).val('Номер дома').addClass('unfocus').removeClass('focus');} });
    //	myAp.focus(function() { if (jQuery(this).val() == 'Номер кв. / офиса') {jQuery(this).val('');} });
    //	myAp.blur(function() { if (jQuery(this).val() == '') {jQuery(this).val('Номер кв. / офиса').addClass('unfocus').removeClass('focus');} });
    //	myInfo.focus(function() { if (jQuery(this).val() == 'Дополнительная информация') {jQuery(this).val('');} });
    //	myInfo.blur(function() { if (jQuery(this).val() == '') {jQuery(this).val('Дополнительная информация').addClass('unfocus').removeClass('focus');} });

    jQuery(".right_column .type_elem .oform_block .deliver_fields input, .right_column .type_elem .oform_block .deliver_fields textarea").focus(function() {
        jQuery(this).addClass('focus').removeClass('unfocus');
    }).click(function() {
        jQuery(this).addClass('focus').removeClass('unfocus');
    });
    jQuery(".right_column .type_elem input.button").hover(function() {
        jQuery(this).addClass('button-hover');
    },function() {
        jQuery(this).removeClass('button-hover');
    });
    jQuery(".right_column .type_elem input.button").focus(function() {
        jQuery(this).addClass('button-focus');
    }).click(function() {
        jQuery(this).addClass('button-focus');
    });
    jQuery(".right_column .type_elem input.button").blur(function() {
        jQuery(this).removeClass('button-focus');
    });


    if (jQuery.browser.mozilla && jQuery.browser.version == 5) {
        jQuery('.left_column .search_block input').css({
            'height':'21px',
            'padding-top':'0',
            'padding-bottom':'0'
        });
        jQuery('.right_column .type_elem input').css({
            'height':'27px',
            'padding-top':'0',
            'padding-bottom':'0'
        });
        jQuery('.right_column .type_elem .oform_block .capcha p input').css({
            'height':'19px'
        });
        jQuery('.right_column .type_elem input.button').css({
            'height':'36px',
            'padding-top':'0',
            'padding-bottom':'6px'
        });
        jQuery('.right_column .type_elem .goods_list ul li .buy input, .right_column .type_elem .basket_block table td input, .right_column .type_elem .product_block .desc .buy input').css({
            'height':'20px',
            'padding-top':'0',
            'padding-bottom':'0'
        });
    }
    if (jQuery.browser.mozilla && jQuery.browser.version == 6) {
        jQuery('.left_column .search_block input').css({
            'height':'21px',
            'padding-top':'0',
            'padding-bottom':'0'
        });
        jQuery('.right_column .type_elem input').css({
            'height':'27px',
            'padding-top':'0',
            'padding-bottom':'0'
        });
        jQuery('.right_column .type_elem .oform_block .capcha p input').css({
            'height':'19px'
        });
        jQuery('.right_column .type_elem input.button').css({
            'height':'36px',
            'padding-top':'0',
            'padding-bottom':'6px'
        });
        jQuery('.right_column .type_elem .goods_list ul li .buy input, .right_column .type_elem .basket_block table td input, .right_column .type_elem .product_block .desc .buy input').css({
            'height':'20px',
            'padding-top':'0',
            'padding-bottom':'0'
        });
    }
    if (jQuery.browser.msie && jQuery.browser.version == 8) {
        jQuery('.right_column .type_elem .product_block .desc .buy a').css({
            'padding-top':'8px'
        });
    }
});



var objDump = function(obj, ret) {
    var ret = '';
    for(o in obj) {
        ret += 'Key: '+o+' Val: '+obj[o]+ret;
    }
    return ret;
}

jQuery(function(){
    if (!navigator.cookieEnabled){
        jQuery('#no-cookies-text').html('<h2>Внимание! Для корректной работы сайта необходима поддержка Cookie </h2>').show();
        jQuery('.type_elem').css('display', 'none');
    }
});

jQuery(function(){
    jQuery('.goods-length').click(function(){
        var val = jQuery(this).val();
        if (isNaN(val) || val <= 1) {
            jQuery(this).val('');
        }
    }).blur(function(){
        var val = jQuery(this).val();
        if (isNaN(val) || val <= 1) {
            jQuery(this).val('1');
        }
    });

    jQuery('.radio').click(function(){
        if (jQuery(this).val() == 'delivery_service') {
            jQuery('.delivery').show();
            jQuery('#delivery_service').show();
        } else {
            jQuery('.delivery').hide();
            jQuery('#delivery_service').hide();
        }
    });

    jQuery('.select-filter').change(function(){
        jQuery('#filter-form').submit();
    });

    jQuery( "#dialog" ).dialog(
    {
        dialogClass: 'auth-form',
        modal: true,
        width: 410,
        height: 273,
        resizable: false,
        autoOpen: false,
        closeOnEscape: true
    }
    );

    jQuery('#enter').click(function(){
        jQuery( "#dialog" ).dialog("open");
        return false;
    });

    jQuery('p#colse a').click(function(){
        jQuery( "#dialog" ).dialog("close");
        return false;
    });

    jQuery('.commen-helpful-yes a').click(function(){
        var artikul = jQuery(this).parent('span').attr('id');
        var status = 'yes';
        var id = jQuery(this).attr('id');

        jQuery.post('/ajaxAddGoodsCommentaryRating.php',
        {
            'artikul':artikul,
            'status':status,
            'commentIndex':id
        },
        function(data){
            var text = jQuery('p#'+id).text();

            jQuery('span#commen-helpful-yes-'+id).text(data);
            if (text == null || text == '') {
                text = jQuery('p#'+id+' span#'+artikul).text();
                jQuery('p#'+id+' span#'+artikul).text(text);
            } else {

                jQuery('p#'+id).text(text);
            }

        });
        return false;
    });

    var deliverySelected = jQuery('select[name=delivery_service] option:selected').val();



    if (deliverySelected !== undefined) {

        

        jQuery('select[name=delivery_service]').change(function(){
            var value = jQuery(this).val();

            if (value == '' || value == 'Самовывоз') {
                jQuery('#hidden-block1').hide(100);
                jQuery('.delivery_fields1').hide(100);
            }

            if ( value == 'К дому, офису') {
                jQuery('#hidden-block1').show(100);
                jQuery('#hidden-block').hide(100);
            } else if (value != '' &&  value != 'Самовывоз') {
                jQuery('#hidden-block1').show(100);
                jQuery('#hidden-block').show(100);
            } else {
                jQuery('#hidden-block1').hide(100);
                jQuery('#hidden-block').hide(100);
            }

        });
    }

    //jQuery('#form1').ksValidate();

    jQuery('.reload_protect_image').click(function () {
        jQuery.get('/ajax/capcha',  function(data){
            jQuery('input#captcha_id').val(data);            
            jQuery('.protect img').attr('src', '/img/captcha/'+data+'.png');
        });

        return false;
    });


    jQuery('#a-oform-block').toggle(function(){
        jQuery('select[name=section]').parent('p').show();
        jQuery('select[name=goods_list]').parent('p').show();
        jQuery('#otzivi-button').unbind('click').click(function(){

        jQuery.post('/ajax/otzivi', {
            'action':'add',
            'fio':jQuery('input[name=fio]').val(),
            'goods_artikul':jQuery('select[name=goods_list]').val(),
            'goods_name': jQuery('select[name=goods_list] :selected').text(),
            'email':jQuery('input[name=email]').val(),
            'city':jQuery('input[name=city]').val(),
            'conclusion':jQuery('textarea[name=conclusion]').val()
        },
        function(data) {
            alert('Ваше сообщение добавлено');
            jQuery('input[name=fio]').val('');
            jQuery('select[name=section] option:selected').attr('selected', false);
            jQuery('select[name=section] option:first').attr('selected', true);
            jQuery('select[name=goods_list] option').remove().append('<option selected value=""></option>');
            jQuery('input[name=email]').val('');
            jQuery('input[name=city]').val('');
            jQuery('textarea[name=conclusion]').val('');
            jQuery('.otziv_block ul').prepend(data);
            jQuery('.oform_block').toggle();
            return false;
        }
        );
        return false;
    });
        jQuery('#otzivi').show();

    }, function(){

        jQuery('#otzivi').hide();
    });



    jQuery('.h1-add-button').mouseenter(function(){
        jQuery('div.h1-add').show();
    });

    jQuery('div.h1-add ').mouseleave(function(){
        jQuery('div.h1-add').hide();
    });


    jQuery('.basket_form .f_text.error, .basket_form .f_textarea.error, #captcha_input').focus(function(){
        
        var obId = jQuery(this);
        
        if (obId.attr('id') == 'captcha_input') {
            
            obId.removeClass('error');
            jQuery('.f_protect_error').remove();
        } else {
            obId
                .removeClass('error')
                .prev('span')
                .removeClass('error');
                jQuery('span#'+obId.attr('name')).remove();
        }
    });


});

function add2Fav (x, text){
    if (document.all  && !window.opera) {
        if (typeof window.external == "object") {
            window.external.AddFavorite (document.location, document.title);
            return true;
        }
        else return false;

    } else{
        x.href=text;

        x.rel = "sidebar";
        return true;
    }
}

function getBrowserInfo() {
  var t,v = undefined;

  if (window.chrome) t = 'Chrome';
  else if (window.opera) t = 'Opera';
  else if (document.all) {
  t = 'IE';
  var nv = navigator.appVersion;
  var s = nv.indexOf('MSIE')+5;
  v = nv.substring(s,s+1);
  }
  else if (navigator.appName) t = 'Netscape';

  return {type:t,version:v};
 }

 function bookmark(a){
  var url = window.document.location;
  var title = window.document.title;
  var b = getBrowserInfo();

  if (b.type == 'IE' && 8 >= b.version && b.version >= 4) window.external.AddFavorite(url,title);
  else if (b.type == 'Opera') {
  a.href = url;
  a.rel = "sidebar";
  a.title = url+','+title;
  return true;
  }
  else if (b.type == "Netscape") window.sidebar.addPanel(title,url,"");
  else alert("Нажмите CTRL-D, чтобы добавить страницу в закладки.");
  return false;
 }

function getGoodsList(obj) {
    jQuery(function(){
        if ((val = jQuery(obj).val()) != '') {
            jQuery.post('/ajax/otzivi', {
                'id':val,
                'action':'get_goods_list'
            },  function(data){
                jQuery('select[name=goods_list] option').remove();
                jQuery('select[name=goods_list] optgroup').remove();
                jQuery('select[name=goods_list] ').append('<option value=""></option>').append(data);
            });
        } else {
            jQuery('select[name=goods_list] option').remove();
            jQuery('select[name=goods_list] optgroup').remove();
        }
    });
}



