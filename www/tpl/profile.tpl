<!-- BDP: profile -->
<div class="oform_block1">
    <form id="form1" method="post">   
        <p><strong>Изменить пароль</strong></p>
        <p>Старый пароль:<br><input type="password" value="" name="old_password"><span class="error-message"></span></p>      
        <p>Новый пароль:<br><input type="password" value="" name="password"><span class="error-message"></span></p>   
        <p>Подтвердите пароль:<br><input type="password" value="" name="password_confirm"><span class="error-message"></span></p>   
        <p><input type="submit" class="button" value="Сохранить"></p>

    </form>
</div>
<div class="oform_block1">
    <form id="form1" method="post">
        <p><strong>Личные данные</strong></p>
        <p>e-mail: *<br><input type="text" value="{USER_PROFILE_EMAIL}" name="email"><span class="error-message"></span></p>
        <p>Имя: *<br><input type="text" value="{USER_PROFILE_NAME}" name="name"><span class="error-message"></span></p>
        <p>Фамилия:<br><input type="text" value="{USER_PROFILE_SURNAME}" name="surname"><span class="error-message"></span></p>
        <p>Отчество:<br><input type="text"  name="patronymic" id="patronymic" value="{USER_PROFILE_PATRONYMIC}"> <span  class="error-message"></span></p>
        <p>Городской телефон (с кодом города):<br><input type="text"  name="city_phone" id="city_phone" value="{USER_PROFILE_CITY_PHONE}"><span class="error-message"></span></p>
        <p>Мобильный телефон: <br><input type="text" name="mobile_phone" id="mobile_phone" value="{USER_PROFILE_MOBILE_PHONE}"><span class="error-message" ></span></p>
        <p><input type="submit" class="button" value="Сохранить"></p>
    </form>
</div>

<div class="oform_block1">
    <form id="form1" method="post">
    <p><strong>Для совершения сделки купли-продажи<br>выбранного Вами товара требуется договор.</strong></p>          

    <p>Серия паспорта:<br><input type="text" name="passport" id="passport" value="{USER_PROFILE_PASSPORT}"><span class="error-message"></span></p>
    <p>Номер паспорта:<br><input type="text" name="passport_number" id="passport_number" value="{USER_PROFILE_PASSPORT_NUMBER}"><span class="error-message"></span></p>
    <p>Когда выдан паспорт:<br><input type="text" name="when_the_passport_is_given" id="when_the_passport_is_given" value="{USER_PROFILE_WHEN_THE_PASSPORT_IS_GIVEN}"><span class="error-message"></span></p>
    <p>Кем выдан паспорт:<br><input type="text" name="issued_passport" id="issued_passport" value="{USER_PROFILE_ISSUED_PASSPORT}"><span class="error-message"></span></p>
    <p>Адрес прописки:<br><input type="text" name="registration_address" id="registration_address" value="{USER_PROFILE_REGISTRATION_ADDRESS}"><span class="error-message"></span></p>
    <p>ИНН:<br><input type="text" name="inn" id="inn" value="{USER_PROFILE_INN}"><spanclass="error-message"></span></p>

        <p><input type="submit" class="button" value="Сохранить"></p>
    </form>
</div>
</div>
<!-- EDP: profile -->