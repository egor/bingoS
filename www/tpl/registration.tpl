<!-- BDP: registration -->
<div class="oform_block1">
    <form id="form1" method="post">
   <p><span>Все поля обязательны для заполнения</span></p>
   <p>Имя:<br><input type="text" value="{REGISTRATION_USER_NAME}" name="name"><span class="error-message"></span></p>
   <p>e-mail:<br><input type="text" value="{REGISTRATION_E_MAIL}" name="email"><span class="error-message"></span></p>
   <p>Пароль:<br><input type="password" value="" name="password"><span class="error-message"></span></p>   
   <p>Подтвердите пароль:<br><input type="password" value="" name="password_confirm"><span class="error-message"></span></p>   
   
   <div class="oform_block">
   
   <p ><br><span class="simple">Введите текст показанный на картинке:</span> </p>

        <div class="capcha">
         
            <p >
                
                 <span><a href="#" class="a-captcha"><img width="130" height="60" alt="" src="/img/captcha/{CAPTCHA_ID}.png"></a></span>
                <span class="captcha"> <input type="text" name="captcha_input" id="captcha_input"> </span>
                <span class="error-message"></span>
            </p>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="captcha_id" id="captcha_id"  value="{CAPTCHA_ID}" /> 
        <p><span class="simple"><a href="#" class="a-captcha">Обновить картинку</a></span> <a href="#" class="a-captcha"><img width="21" height="21" alt="" src="/img/reload.gif"></a></p>
        <p><input type="submit" class="button" value="Отправить"></p>

    </div>
    </form>
    
    
    
</div>

<!-- EDP: registration -->
