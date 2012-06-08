<!-- BDP: order_form -->
<script type="text/javascript">
    jQuery(function(){
        jQuery('#form2').ksValidate(
            {'noTestFieldsId':[
                        'email',
                        'city_phone',
                        'dop_info'
                    ]
                }
        );
    });

</script>
<form action="/order/add" method="POST" id="form2" class="basket_form">
    <span class="f_description"><span class="required">*</span> - поля обязательные для заполнения</span>

    <span class="f_name error">Фамилия <span class="required">*</span></span>
    <input type="text" name="surname" id="surname" value="{ORDER_FORM_SURNAME}" class="f_text error" />
    <span class="f_error">Это поле необходимо заполнить!</span>

    <span class="f_name">Имя <span class="required">*</span></span>
    <input type="text" name="name" id="name" value="{ORDER_FORM_NAME}" class="f_text" />

    <span class="f_name">Отчество <span class="required">*</span></span>
    <input type="text" name="patronymic" id="patronymic" value="{ORDER_FORM_PATRONYMIC}" class="f_text" />

    <span class="f_name">e-mail <span class="required">*</span></span>
    <input type="text" name="email" id="email" value="{ORDER_FORM_EMAIL}" class="f_text" />

    <span class="f_name">Мобильный телефон <span class="required">*</span></span>
    <input type="text" name="mobile_phone" id="mobile_phone" value="{ORDER_FORM_MOBILE_PHONE}" class="f_text" />

    <span class="f_name">Городской телефон (с кодом города)</span>
    <input type="text" name="city_phone" id="city_phone" value="{ORDER_FORM_CITY_PHONE}" class="f_text" />

    <div class="wrap_select"><span class="f_name select">Доставка</span>
    <select name="delivery_service" id="delivery_service" class="f_select checkout">
        <option value="">Выберите способ доставки</option>
        <option value="Самовывоз" {ORDER_FORM_DELIVERY_SERVICE_SELECTED1}>Самовывоз</option>
        <option value='К дому, офису' {ORDER_FORM_DELIVERY_SERVICE_SELECTED2}>К дому, офису</option>
        <!-- BDP: delivery_service_list -->
        <option value="{ORDER_FORM_DELIVERY_SERVICE_TEXT}" {ORDER_FORM_DELIVERY_SERVICE_SELECTED}  >{ORDER_FORM_DELIVERY_SERVICE_TEXT}</option>
        <!-- EDP: delivery_service_list -->
    </select>
    </div>

    <span class="f_name">Город <span class="required">*</span></span>
    <input type="text" id="city" name="city" value="{ORDER_FORM_CITY}" class="f_text" />

    <span class="f_name">Улица <span class="required">*</span></span>
    <input type="text" id="street" name="street" value="{ORDER_FORM_STREET}" class="f_text" />

    <span class="f_name">Номер дома <span class="required">*</span></span>
    <input type="text" value="{ORDER_FORM_HOUSE_NUM}" name="house_num" id="house_num" class="f_text" />

    <span class="f_name">Номер кв./офиса <span class="required">*</span></span>
    <input type="text" id="ap_num" name="ap_num" value="{ORDER_FORM_AP_NUM}" class="f_text" />

    <span class="f_name">Дополнительная информация</span>
    <textarea class="f_textarea" id="dop_info" name="dop_info">{ORDER_FORM_DOP_INFO}</textarea>
    <br clear="all" />
    <div class="button">
        <input type="submit" value="Отправить заявку" name="submit" />
        <span></span>
    </div>











    <div class="oform_block">
        <p><span>Все поля обязательны для заполнения</span></p>
        <p>Фамилия: * <br><input type="text" name="surname" id="surname" value="{ORDER_FORM_SURNAME}"><span class="error-message"></span></p>
        <p>Имя: *<br><input type="text" name="name" id="name" value="{ORDER_FORM_NAME}"><span class="error-message"></span></p>
        <p>Отчество: *<br><input type="text"  name="patronymic" id="patronymic" value="{ORDER_FORM_PATRONYMIC}"> <span  class="error-message"></span></p>
        <p>e-mail:<br><input type="text"name="email" id="email"  value="{ORDER_FORM_EMAIL}" ><span  class="error-message"></span></p>
        <p>Городской телефон (с кодом города):<br><input type="text"  name="city_phone" id="city_phone" value="{ORDER_FORM_CITY_PHONE}"><span class="error-message"></span></p>
        <p>Мобильный телефон: *<br><input type="text" name="mobile_phone" id="mobile_phone" value="{ORDER_FORM_MOBILE_PHONE}"><span class="error-message" ></span></p>
        <p>Доставка: *<br>
            <select name="delivery_service" id="delivery_service">
                <option value="">Выберите способ доставки</option>
                <option value="Самовывоз" {ORDER_FORM_DELIVERY_SERVICE_SELECTED1}>Самовывоз</option>
                <option value='К дому, офису' {ORDER_FORM_DELIVERY_SERVICE_SELECTED2}>К дому, офису</option>
                <!-- BDP: delivery_service_list -->
                <option value="{ORDER_FORM_DELIVERY_SERVICE_TEXT}" {ORDER_FORM_DELIVERY_SERVICE_SELECTED}  >{ORDER_FORM_DELIVERY_SERVICE_TEXT}</option>
                    <!-- EDP: delivery_service_list -->
            </select><span class="error-message"></span>
        </p>

        <div class="delivery_fields1">
            <p>Номер склада: *<br><input type="text" name="warehouse_number" id="warehouse_number" value="{ORDER_FORM_WAREHOUSE_NUMBER}"><span class="error-message"></span></p>
            <p>Страхование: *<br>
                <select name="insurance">
                    <option value="на полную стоимость">на полную стоимость</option>
                    <option value="на минимальную стоимость">на минимальную стоимость</option>
                </select>
            </p>
            <p>Способ оплаты: *<br>
                <select name="payment_method">
                    <option value="наличным платежом">наличным платежом</option>
                    <option value="предоплата">предоплата</option>
                </select>
            </p>
        </div>
        <div class="deliver_fields">

            <p>Город: *<br><input type="text" id="city" name="city" value="{ORDER_FORM_CITY}"><span class="error-message"></span></p>
            <p>Улица: *<br><input type="text" id="street" name="street" value="{ORDER_FORM_STREET}"><span class="error-message"></span></p>

            <p>

            <span id="number">Номер дома *<span>Номер кв./ офиса *</span></span><br>
            <input type="text" value="{ORDER_FORM_HOUSE_NUM}" name="house_num" id="house_num" class="small unfocus">
             <input type="text" class="small" id="ap_num" name="ap_num" value="{ORDER_FORM_AP_NUM}">
             <span class="error-message"></span>
        </p>

            <p>Дополнительно: <br><textarea id="dop_info" name="dop_info">{ORDER_FORM_DOP_INFO}</textarea><span class="error-message"></span></p>
        </div>
        <!-- BDP: order_passport_data -->

        <p><strong>Для совершения сделки купли-продажи<br>выбранного Вами товара требуется договор.</strong></p>
            <p><strong>Заполните пожалуйста ниже форму.</strong></p>
            <p><span>Все поля обязательны для заполнения</span></p>
            <p>Серия паспорта:<br><input type="text" name="passport" id="passport" value="{ORDER_FORM_PASSPORT}"><span class="error-message"></span></p>
            <p>Номер паспорта:<br><input type="text" name="passport_number" id="passport_number" value="{ORDER_FORM_PASSPORT_NUMBER}"><span class="error-message"></span></p>
            <p>Когда выдан паспорт:<br><input type="text" name="when_the_passport_is_given" id="when_the_passport_is_given" value="{ORDER_FORM_WHEN_THE_PASSPORT_IS_GIVEN}"><span class="error-message"></span></p>
            <p>Кем выдан паспорт:<br><input type="text" name="issued_passport" id="issued_passport" value="{ORDER_FORM_ISSUED_PASSPORT}"><span class="error-message"></span></p>
            <p>Адрес прописки:<br><input type="text" name="registration_address" id="registration_address" value="{ORDER_FORM_REGISTRATION_ADDRESS}"><span class="error-message"></span></p>
        <p>ИНН:<br><input type="text" name="inn" id="inn" value="{ORDER_FORM_INN}"><spanclass="error-message"></span></p>


        <!-- EDP: order_passport_data -->

        <p><input type="submit" class="button" value="Отправить заявку"></p>

    </div>


</form>
<!-- EDP: order_form -->

<!-- BDP: order_empty -->
Заказов не найдено
<!-- EDP: order_empty -->

<!-- BDP: order_success -->
<h2>Заказ выполнен</h2>
<!-- EDP: order_success -->