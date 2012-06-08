<!-- BDP: otzivi -->
<script type="text/javascript">
    jQuery(function(){
        
        jQuery('.button').unbind('click');
        
        jQuery('#form2').ksValidate(
            {'noTestFieldsId':[
                        'email',                      
                        'dop_info'
                    ]
                }
            );
    });
    
</script>
<div class="product_block">
    
 <p><a href="#" id="a-oform-block">Оставить свой отзыв</a></p>    
    <div class="oform_block" id="otzivi">
        <form method="post" action="" id="form1" onsubmit="otzivAdd(); return false;">
            <input type='hidden' value="{OTZIVI_GOODS_ARTIKUL}" name="artikul" />
            
            <p><span>Все поля обязательны для заполнения</span></p>
            <p>Имя и фамилия:<br><input type="text"  name ="fio" value ="{OTZIVI_FIO}"> <span class="error-message"></span></p>
            <p>Укажите группу товаров: <br> <select name="section" onchange="getGoodsList(this);">
                    <option value=""></option>
                    {OTZIVI_SECTION_LIST}
                </select><span class="error-message"></span></p>
             <p>Укажите товар: <br> <select name="goods_list">
                     <option value=""></option>
                </select><span class="error-message"></span></p>
            <p>e-mail:<br><input type="text"name="email" id="email"  value="{OTZIVI_EMAIL}" ><span  class="error-message"></span></p>
            <p>Город: *<br><input type="text" id="city" name="city" value="{OTZIVI_CITY}"><span class="error-message"></span></p>
            <p>Текст сообщения:<br><textarea name="conclusion">{OTZIVI_CONCLUSION}</textarea><span class="error-message"></span></p>
            
            <div class="clear"><span class="error-message"></span></div>

            <!-- BDP: otzivi_captcha -->
            <p><br><span class="simple">Введите текст показанный на картинке:</span></p>            
            <div class="capcha">
                <p><span class="error-message"></span></p>
                <p >

                    <span><a href="#" class="a-captcha"><img width="130" height="60" alt="" src="/img/captcha/{CAPTCHA_ID}.png"></a></span>
                     <input type="hidden" name="captcha_id" id="captcha_id"  value="{CAPTCHA_ID}" /> 
                    <span class="captcha"> <input type="text" name="captcha_input" id="captcha_input"> </span> <span class="error-message"></span>
                </p>
            </div>
            <div class="clear"></div>
            <p><span class="simple"><a href="#" class="a-captcha">Обновить картинку</a></span> <a href="#" class="a-captcha"><img width="21" height="21" alt="" src="/img/reload.gif"></a></p>
            <!-- EDP: otzivi_captcha -->

            <p><input type="submit" class="button otzivi" id="otzivi-button" value="Отправить отзыв"></p>
        </form>
    </div>
</div>


<!-- EDP: otzivi -->

<!-- BDP: otzivi_list -->
    <div class="clear"></div>    
    {PAGES_TOP}
    <div class="otziv_block news_list">
        <ul>
            <!-- BDP: otzivi_items -->
            <li {OTZIVI_LI_ID}>                
                <div class="desc {OTZIVI_LIST_DESC_CLASS_NAME}">                    
                    <p><span>{OTZIVI_LIST_DATE}/ <span>{OTZIVI_LIST_FIO}</span></span></p>
                    <p><strong>Товар:</strong><br>{OTZIVI_GOODS_NAME}</p>
                    <p><strong>e-mail:</strong><br>{OTZIVI_EMAIL}</p>
                    <p><strong>Город:</strong><br>{OTZIVI_CITY}</p>
                    <p><strong>Текст сообщения:</strong><br>{OTZIVI_BODY}</p>                    
                </div>                
                {ADMIN_BUTTONS}
                
            </li>
            <!-- EDP: otzivi_items -->
        </ul>
        {PAGES_BOTTOM}
    </div>    
    <!-- EDP: otzivi_list -->
