<div class="text">

    {KONTAKTI_BODY}

   

    <h4 class="contact_title">Вы можете отправить нам сообщение<br />с помощью этой формы</h4>
    <form class="basket_form" action="" method="post">
        <input type="hidden" name="captcha_id" id="captcha_id" value="{CAPTCHA_ID}" />
        <span class="f_name {FIO_ERROR_CLASS_NAME}">Контактное лицо (ФИО)</span>
        <input type="text" class="f_text {FIO_ERROR_CLASS_NAME}" value="{FIO}" name="fio">
        <span class="f_error" id="fio">{FIO_ERROR_MESSAGE}</span>

        <span class="f_name {EMAIL_ERROR_CLASS_NAME}">e-mail</span>
        <input type="text" class="f_text {EMAIL_ERROR_CLASS_NAME}" value="{EMAIL}" name="email">
        <span class="f_error" id="email">{EMAIL_ERROR_MESSAGE}</span>

        <span class="f_name {BODY_ERROR_CLASS_NAME}">Дополнительная информация</span>
        <textarea name="body" class="f_textarea {BODY_ERROR_CLASS_NAME}"> {BODY} </textarea>
        <span class="f_error" id="body">{BODY_ERROR_MESSAGE}</span>
        <div style="clear:both;"></div>
        <span class="protect_title"><span class="required">Внимание!</span> Все поля обязательные для заполнения</span>
        <span class="protect">
            <img src="/img/captcha/{CAPTCHA_ID}.png">
            <span class="wrap_input">
                <input type="text" class="{CAPTCHA_ERROR_CLASS_NAME}" value="" name="captcha_input" id="captcha_input">
                </span>
        </span>
        <span class="f_error f_protect_error">{CAPTCHA_ERROR_MESAGE}</span>
        <br clear="all">
        <a class="reload_protect_image a-captcha" href="#">Обновить картинку</a>
        <br clear="all">
        <div class="button">
            <input type="submit" name="submit" value="Отправить сообщение">
            <span></span>
        </div>
    </form>

</div>