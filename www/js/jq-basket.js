
function reportError(request)
{
    alert('Sorry. There was an error.');
}

jQuery(function(){    
    var setBasketValue = function(data, id) {
        if (data === null || data.error === undefined) {
            if (data !== null &&  data.totalCount !== undefined) {
                        
                var basketTotalSumm = 'товар';            
                if (data.totalCount >=2 && data.totalCount < 5) {
                    basketTotalSumm = 'товара';
                }
            
                if (data.totalCount >= 5) {
                    basketTotalSumm = 'товаров';
                }
                
                if (data.totalCount > 0) {      
               
                    jQuery('.wrap_basket_info')
                        .addClass('active')
                        .html('<a href="/basket" class="go_to_basket">Оформить заказ</a><div class="wrap_basket"><div class="line"><span class="count">'+data.totalCount+' шт.</span><strong>В корзине</strong> товаров:</div>   <div class="line"><span class="summ">'+(new String( data.totalSumm) .numberFormat(0, ' '))+' грн.</span>на сумму:</div></div>');
                   
                    
                } else {
                 
                    jQuery('.wrap_basket_info')
                    .removeClass('active')
                    .html('<span class="go_to_basket">Ваша корзина пуста</span><div class="wrap_basket"><div class="line"><span class="count">0 шт.</span><strong>В корзине</strong> товаров:</div> <div class="line"><span class="summ">0 грн.</span>на сумму:</div></div>');
                                       
                }
            } else {
                jQuery('.wrap_basket_info')
                        .addClass('active')
                        .html('<a href="/basket" class="go_to_basket">Оформить заказ</a><div class="wrap_basket"><div class="line"><span class="count">'+data.totalCount+' шт.</span><strong>В корзине</strong> товаров:</div>   <div class="line"><span class="summ">'+(new String( data.totalSumm) .numberFormat(0, ' '))+' грн.</span>на сумму:</div></div>');
                            
            }
        } else {
            alert('Ошибка!!! '+data.error);
        }
                
    }
    
    jQuery('.buy-button').click(function() {
        var id = jQuery(this).attr('id');
        
        var count = jQuery('input[id='+id+']').val();
        if (count === undefined) {
            count = 1;
        }
        if (isNaN(count)) {
            jQuery('input[id='+id+']').val(1);
            count = 1;
        }
        var obj = jQuery(this);
        if (count <= 0) {
            count = -1;
            jQuery('input[id='+id+']').val(1);
            count = 1;
        }
        
        jQuery.ajax({
            type: 'POST',
            url: '/basket/add',
            data: {
                'item_id':id,
                'item_count':count
            },
            success: function(data) {
                setBasketValue(data, id);
                
                if (obj.attr('href') == 'akces_block') {  
                    obj.parent('li.p_item').html('<a href="/basket" style="" class="p_link_basket">Оформить заказ</a>')
                                        
                } 
                
                if (obj.attr('href') == 'detail') {
                   jQuery('tr.to_tobusket td.n span.count').remove();
                    obj.parent('tr.to_tobusket td.v').html('<a href="/basket" class="link_basket" >Оформить заказ</a>');                                        
                }
                

               
            },
            dataType: 'json'
        });
        
        return false;
    });
    
   
      
      
    var setBasketGoodsStatus = function (id, status) {
       
        jQuery.ajax({
            type: 'POST',            
            url: '/basket/'+status+'/'+id,  
            
            success: function(data) {
                setBasketValue(data, id);   
               
                           
                var totalSumm = 0;
                var totalCount = 0;
                if (data.totalSumm !== undefined && !isNaN(data.totalSumm)) {
                    totalSumm = data.totalSumm;
                    totalCount = data.totalCount;
                }
                
                if (data[id].totalSumm !== undefined) {
                    jQuery('td#summ-'+id).text((new String(data[id].totalSumm) .numberFormat(0, ' ')) + ' грн.');
                }
                               
               
                jQuery('.all_summ').text((new String(totalSumm) .numberFormat(0, ' '))   + ' грн.');
                jQuery('.all_count').text(totalCount  + ' шт.');
             
            },
            dataType: 'json'
        });
    }   
       
    jQuery('.basket-delete-button').click(function(){            
    
       var id = jQuery(this).attr('id');   
       
        var status = 'delete';

        if (jQuery('tr#tr-'+id).attr('class') != 'disabled') {
            status = 'delete';
      
             jQuery(this)
                .removeClass('delete')
                .addClass('return')
                .text('Вернуть');
                jQuery('tr#tr-'+id).addClass('disabled').
                find('input').attr('disabled', true);
        }  else {
             status = 'back';
            jQuery(this)
            .removeClass('return')
                .addClass('delete')
                .text('Удалить');
            
            jQuery('tr#tr-'+id).removeClass('disabled').
                find('input').attr('disabled', false);
             
        } 
        
//        $('.basket_table tr').removeClass('disabled');
//	$('.basket_table td input').attr('disabled','');
        
        
        setBasketGoodsStatus(id, status);               
        return false;
    });   
   
   
    var onChangeCount = function (id, oldVal, newVal) {
       
        var url = '/basket';
        
     
        url += '/add/'+id;
       
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: {
                'item_id':id,
                'item_count':newVal
            },
            success: function(data) {
                 
                setBasketValue(data, id);                
                if (data[id] !== undefined && data.totalSumm !== undefined) { 
                    jQuery('#summ-'+id).text(data[id].totalSumm  + ' грн.');
                    jQuery('.all_count').text(data.totalCount +' шт.');
                    
                    jQuery('.all_summ').text((new String(data.totalSumm) .numberFormat(0, ' '))   + ' грн.');
                }
               
            },
            dataType: 'json'
        });
    }

    var goodsCount = false;
    
    jQuery('.goods-input').click(function(){
        goodsCount = jQuery(this).val();
        jQuery(this).val('');
        
    }).keyup(function(){              
      
        if (jQuery(this).val() == '') {
            jQuery(this).val(1);
        } else {
            var val = jQuery(this).val();
            if (isNaN(val)) {                
                jQuery(this).val(new String(val).replace(/\D+/, ''));
                alert('Ошибка! Недопустимое значение');
            }
            var val = parseInt(val);
            
            if (val <= 0) {
                //jQuery(this).val(1);
                alert('Ошибка! Недопустимое значение');
            }
            
            if (val >= 1000000) {
                alert('Не более 1000000 шт.')
                //jQuery(this).val(1);
            }
        }
        
        
        
    }).focusout(function(){        
        if (jQuery(this).val() == '') {
            
            if (!isNaN(goodsCount) && goodsCount > 0) {
                jQuery(this).val(goodsCount);            
            } else {
                jQuery(this).val(1);            
            }            
        }
        
    });    
    
    jQuery('.goods-input-basket').keyup (function(){
        onChangeCount(jQuery(this).attr('id'), goodsCount,jQuery(this).val() );
    });
    
    jQuery('.clear_basket').click(function(){
        if (confirm('Вы действительно хотите очистить корзину ?')) {
        
            jQuery.post('/basket/clear', function(){
                var emptyBesketTopBlock = '<span class="go_to_basket">Ваша корзина пуста</span><div class="wrap_basket"><div class="line"><span class="count">0 шт.</span><strong>В корзине</strong> товаров:</div><div class="line"><span class="summ">0 грн.</span>на сумму:</div></div>';
                jQuery('#basket_block').html(emptyBesketTopBlock).removeClass('active');
                jQuery('.wrap_text').html('<h1 class="title">Корзина</h1><h2>Заказов не найдено</h2>');
            });
            
            
            
        }
        
        return false;
    });
    
    jQuery('input[id=order]').click(function(){
    
        document.location.href = '/order';
    });
    
});




String.prototype.numberFormat=function(length, strRepl){
    if (!isNaN(this)) {
        var ret = this;
        var retStr = '';
		
        retStr = '';
        var retArr = ret.split('.');
        ret = retArr[0]+strRepl;
			
        var retStr = '';
        var slider = ' ';
			
        for (var i = 0, f = 1; i < retArr[0].length; i++, f++) {
				
            retStr += retArr[0].charAt(i);
				
				
            if (retArr[0].length == 4 && i == 0) {					
                retStr += slider;
				
            }
            if (retArr[0].length == 5 && i == 1) {
                retStr += slider;				
            }
            if (retArr[0].length == 6 && i == 2) {
                retStr += slider;				
            }
            if (retArr[0].length == 7 && (i == 0 || i == 3)) {
                retStr += slider;				
            }
            if (retArr[0].length == 8 && (i == 1 || i == 4)) {
                retStr += slider;				
            }
            if (retArr[0].length == 9 && (i == 2 || i == 5)) {
                retStr += slider;				
            }
            if (retArr[0].length == 10 && (i == 3 || i == 4)) {
                retStr += slider;				
            }
        }
			
        ret = retStr;
			
        var retStr = '';			
		    
        if (retArr.length == 1) {
            for (var i = 0; i < length; i++) {
                retStr += '0';
            }
        } else if (retArr.length == 2) {
            for (var i = 0; i < length; i++) {				
                if (retArr[1][i] != undefined) {
                    retStr += retArr[1][i];
                }	else {
                    retStr += '0';
						
                }
            }
        }
			
        ret += strRepl+retStr;			
    }
	
    return ret;
};

