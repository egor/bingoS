<!-- BDP: order_form -->
<div class="wrap_text">
        
        <form class="basket_form" action="" method="POST">
        <span class="f_description"><span class="required">*</span> - поля обязательные для заполнения</span>

        <span class="f_name {ORDER_FORM_SURNAME_ERROR_CLASS_NAME}" id="f-surname"> Фамилия <span class="required">*</span></span>
        <input type="text" class="f_text {ORDER_FORM_SURNAME_ERROR_CLASS_NAME}" value="{ORDER_FORM_SURNAME}" name="surname">
        <span class="f_error" id="surname">{ORDER_FORM_SURNAME_ERROR_MESSAGE}</span>

        <span class="f_name {ORDER_FORM_NAME_ERROR_CLASS_NAME}" id="f-name">Имя <span class="required">*</span></span>
        <input type="text" class="f_text {ORDER_FORM_NAME_ERROR_CLASS_NAME}" value="{ORDER_FORM_NAME}" name="name">
        <span class="f_error" id="name">{ORDER_FORM_NAME_ERROR_MESSAGE}</span>

        <span class="f_name {ORDER_FORM_PATRONYMIC_ERROR_CLASS_NAME}" id="f-patronymic">Отчество <span class="required">*</span></span>
        <input type="text" class="f_text {ORDER_FORM_PATRONYMIC_ERROR_CLASS_NAME}" value="{ORDER_FORM_PATRONYMIC}" name="patronymic">
        <span class="f_error" id="patronymic">{ORDER_FORM_PATRONYMIC_ERROR_MESSAGE}</span>

        <span class="f_name {ORDER_FORM_EMAIL_ERROR_CLASS_NAME}" id="f-email">e-mail <span class="required">*</span></span>
        <input type="text" class="f_text {ORDER_FORM_EMAIL_ERROR_CLASS_NAME}" value="{ORDER_FORM_EMAIL}" name="email">
        <span class="f_error" id="email">{ORDER_FORM_EMAIL_ERROR_MESSAGE}</span>

        <span class="f_name {ORDER_FORM_MOBILE_PHONE_CLASS_NAME}" id="f-mobile_phone">Мобильный телефон <span class="required">*</span></span>
        <input type="text" class="f_text {ORDER_FORM_MOBILE_PHONE_CLASS_NAME}" value="{ORDER_FORM_MOBILE_PHONE}" name="mobile_phone">
        <span class="f_error" id="mobile_phone">{ORDER_FORM_MOBILE_PHONE_ERROR_MESSAGE}</span>

        <span class="f_name">Городской телефон (с кодом города)</span>
        <input type="text" class="f_text" value="" name="city_phone">

        <div class="wrap_select"><span class="f_name {ORDER_FORM_DELIVERY_SERVICE_ERROR_CLASS_NAME} select">Доставка <span class="required">*</span></span>
        <select class="f_select checkout {ORDER_FORM_DELIVERY_SERVICE_ERROR_CLASS_NAME} " name="delivery_service">
            <option value="">Выберите способ доставки</option>
            <option value="Самовывоз" {ORDER_FORM_DELIVERY_SERVICE_SELECTED1}>Самовывоз</option>
            <option value='К дому, офису' {ORDER_FORM_DELIVERY_SERVICE_SELECTED2}>К дому, офису</option>
            <!-- BDP: delivery_service_list -->
                <option value="{ORDER_FORM_DELIVERY_SERVICE_TEXT}" {ORDER_FORM_DELIVERY_SERVICE_SELECTED}  >{ORDER_FORM_DELIVERY_SERVICE_TEXT}</option>
            <!-- EDP: delivery_service_list -->
        </select>
            <span class="f_error_select" id="mobile_phone">{ORDER_FORM_DELIVERY_SERVICE_ERROR_MESSAGE}</span>

        </div>
            
       <div id="hidden-block" style="display:{ORDER_DISPLAY_BLOCK}"> 
            <span class="f_name {ORDER_FORM_WAREHOUSE_NUMBER_ERROR_CLASS_NAME}" id="f-surname">Нмер склада <span class="required">*</span></span>
            <input type="text" class="f_text {ORDER_FORM_WAREHOUSE_NUMBER_ERROR_CLASS_NAME}" value="{ORDER_FORM_WAREHOUSE_NUMBER}" name="warehouse_number">
            <span class="f_error" id="surname">{ORDER_FORM_WAREHOUSE_NUMBER_ERROR_MESSAGE}</span>
            
            <div class="wrap_select"><span class="f_name select">Страхование</span>
            <select class="f_select insurance" name="insurance">
               <option value="на полную стоимость" {ORDER_FORM_INSURANCE_SELECTED_INDEX_1}>на полную стоимость</option>
               <option value="на минимальную стоимость" {ORDER_FORM_INSURANCE_SELECTED_INDEX_2}> на минимальную стоимость</option>
            </select>
            </div>    
            <div class="wrap_select"><span class="f_name select">Способ оплаты</span>
            <select class="f_select payment_method" name="payment_method">
                <option value="наличным платежом" {ORDER_FORM_PAYMENT_METHOD_SELECTED_INDEX_1}>наличным платежом</option>
                <option value="предоплата" {ORDER_FORM_PAYMENT_METHOD_SELECTED_INDEX_2}>предоплата</option>
            </select>
            </div>    
       </div>
            
            
        <div id="hidden-block1" style="display:{ORDER_DISPLAY_BLOCK1}">   
            <span class="f_name {ORDER_FORM_CITY_ERROR_CLASS_NAME}" id="f-city">Город <span class="required">*</span></span>
            <input type="text" class="f_text {ORDER_FORM_CITY_ERROR_CLASS_NAME}" value="{ORDER_FORM_CITY}" name="city">
            <span class="f_error" id="city">{ORDER_FORM_CITY_ERROR_MESSAGE}</span>

            <span class="f_name {ORDER_FORM_STREET_ERROR_CLASS_NAME}" id="f-street">Улица <span class="required">*</span></span>
            <input type="text" class="f_text {ORDER_FORM_STREET_ERROR_CLASS_NAME}" value="{ORDER_FORM_STREET}" name="street">
            <span class="f_error" id="street">{ORDER_FORM_STREET_ERROR_MESSAGE}</span>

            <span class="f_name {ORDER_FORM_HOUSE_ERROR_CLASS_NAME}" id="f-house">Номер дома <span class="required">*</span></span>
            <input type="text" class="f_text {ORDER_FORM_HOUSE_ERROR_CLASS_NAME}" value="{ORDER_FORM_HOUSE_NUM}" name="house">
            <span class="f_error" id="house">{ORDER_FORM_HOUSE_ERROR_MESSAGE}</span>

            <span class="f_name {ORDER_FORM_AP_NUM_ERROR_CLASS_NAME}" id="f-ap_num">Номер кв./офиса <span class="required">*</span></span>
            <input type="text" class="f_text {ORDER_FORM_AP_NUM_ERROR_CLASS_NAME}" value="{ORDER_FORM_AP_NUM}" name="ap_num">
            <span class="f_error" id="ap_num">{ORDER_FORM_AP_NUM_ERROR_MESSAGE}</span>

            <span class="f_name">Дополнительная информация</span>
        </div>
        <textarea name="dop_info" class="f_textarea">{ORDER_FORM_DOP_INFO}</textarea>

       
        <div style="clear:both;">
        <div class="button">
            <input type="submit" name="submit" value="Отправить заявку">
            <span></span>
        </div>
    </form>
</div>
<!-- EDP: order_form -->

<!-- BDP: order_empty -->
Заказов не найдено
<!-- EDP: order_empty -->

<!-- BDP: order_success -->
<h2>Заказ выполнен</h2>
<!-- EDP: order_success -->