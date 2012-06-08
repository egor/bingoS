<!-- BDP: orders -->

<div style="display: none; " id="date-range-win" title="Сортировка по дате">
    <div  style="float: left; margin-right: 15px;">
        <div id="datepicker"></div>
        <br>
        <div style="padding: 4px;" id="datepicker-text" class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all"></div>
    </div>
    <div  style="float: left; ">
        <div id="datepicker1"></div>
        <br>
        <div style="padding: 4px;" id="datepicker-text1" class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all"></div>
    </div>
</div>

<div class="order_history">
    <table>
        <tr class="hnone">
            <th>№</th>
            <th>Ф.И.О.</th>

            <th>Сумма</th>
            <th>Город</th>

            <th>Дата  <a href="#" onclick="dateRange('/admin/orders'); return false;"><img src="/img/calendar.png" width="16" height="16" alt="" /></th>
            <th>Статус</th>
            <th>Удалить</th>
        </tr>
        <!-- BDP: orders_list -->
        <tr>
            <td><a href="/admin/order_detail/{ADM_ORDER_ID}">{ADM_ORDER_ID1}</a></td>
            <td><a href="/admin/order_detail/{ADM_ORDER_ID}">{ADM_ORDER_FIO}</a></td>
            <td>{ADM_ORDER_SUMM} грн.</td>
            <td>{ADM_ORDER_CITY}</td>

            <td>{ADM_ORDER_DATE}</td>
            <td class="{ADM_ORDER_CLASS}">{ADM_ORDER_STATUS}</td>
            <td align="right"><a title="Удалить заказ" href="/admin/orderdelete/{ADM_ORDER_ID}"><img width="12" height="12" alt="Удалить заказ" src="/img/admin_icons/admin-delete.png"></a></td>
        </tr>
        <!-- EDP: orders_list -->

    </table>
</div>
<!-- EDP: orders -->

<!-- BDP: orders_empty -->
<h2>Заказов не найдено</h2>
<!-- EDP: orders_empty -->
<!-- BDP: order_detail -->

<div class="order_review">
    <div style="padding-top: 10px; padding-bottom: 10px;">{ORDER_TEXT} </div>
    <div class="w_del">
   
        <table>
            <tr>
                <td class="width" colspan="3">№ заказа:</td>
                <td colspan="3">{ORDER_DETAIL_ID}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Фамилия:</td>
                <td colspan="3">{ORDER_DETAIL_SURNAME}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Имя:</td>
                <td colspan="3">{ORDER_DETAIL_NAME}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Отчество:</td>
                <td colspan="3">{ORDER_DETAIL_PATRONYMIC}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">e-mai:</td>
                <td colspan="3">{ORDER_DETAIL_EMAIL}</td>
            </tr>

            <tr>
                <td class="width" colspan="3">Городской телефон (с кодом города):</td>
                <td colspan="3">{ORDER_DETAIL_CITY_PHONE}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Мобильный телефон:</td>
                <td colspan="3">{ORDER_DETAIL_MOBILE_PHONE}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Способ доставки:</td>
                <td colspan="3">{ORDER_DETAIL_DELIVERY_SERVICE}</td>
            </tr>
            <!-- BDP: deliver_service_fields -->
            <tr>
                <td class="width" colspan="3">Номер склада:</td>
                <td colspan="3">{ORDER_DETAIL_WAREHOUSE_NUMBER}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Стрaхование:</td>
                <td colspan="3">{ORDER_DETAIL_INSURANCE}</td>
            </tr>

            <tr>
                <td class="width" colspan="3">Способ оплаты:</td>
                <td colspan="3">{ORDER_DETAIL_PAYMENT_METHOD}</td>
            </tr>
            <!-- EDP: deliver_service_fields -->

            <!-- BDP: deliver_service -->

            <tr>
                <td class="width" colspan="3">Город:</td>
                <td colspan="3">{ORDER_DETAIL_CITY}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Улица:</td>
                <td colspan="3">{ORDER_DETAIL_STREET}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Номер дома:</td>
                <td colspan="3">{ORDER_DETAIL_HOUSE_NUM}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Номер кв. / офиса:</td>
                <td colspan="3">{ORDER_DETAIL_AP_NUM}</td>
            </tr>

            <tr>
                <td class="width" colspan="3">Дополнительная информация:</td>
                <td colspan="3">{ORDER_DETAIL_DOP_INFO}</td>
            </tr>
            <!-- EDP: deliver_service -->

            <!-- BDP: passport_data -->
            <tr>
                <td class="width" colspan="3">Серия паспорта:</td>
                <td colspan="3">{ORDER_DETAIL_PASSPORT}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Номер паспорта:</td>
                <td colspan="3">{ORDER_DETAIL_PASSPORT_NUMBER}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Когда выдан паспорт:</td>
                <td colspan="3">{ORDER_DETAIL_WHEN_THE_PASSPORT_IS_GIVEN}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">Кем выдан паспорт:</td>
                <td colspan="3">{ORDER_DETAIL_ISSUED_PASSPORT}</td>
            </tr>

            <tr>
                <td class="width" colspan="3">Адрес прописки:</td>
                <td colspan="3">{ORDER_DETAIL_REGISTRATION_ADDRESS}</td>
            </tr>
            <tr>
                <td class="width" colspan="3">ИНН:</td>
                <td colspan="3">{ORDER_DETAIL_INN}</td>
            </tr>

            <!-- EDP: passport_data -->

            <tr>
                <th colspan="2">Наименование</th>
                <th>Модель</th>
                <th class="right">Цена</th>
                <th class="center">Кол-во</th>
                <th class="right">Сумма</th>
            </tr>

            <!-- BDP: order_detail_list -->

            <tr id="41">
                <td>{ORDER_DETAIL_LIST_COUNTER}</td>
                <td id="basket-goods-name-41"><a title="{ORDER_DETAIL_LIST_NAME}" href="{ORDER_DETAIL_LIST_URL}">{ORDER_DETAIL_LIST_NAME}</a></td>
                <td class="nowrap"><nobr>{ORDER_DETAIL_LIST_ARTIKUL}</nobr></td>
                <td class="right">{ORDER_DETAIL_LIST_COST} грн.</td>
                <td class="center">{ORDER_DETAIL_LIST_COUNT} шт.</td>
                <td class="right nowrap"><nobr><span>{ORDER_DETAIL_LIST_SUMM}</span> грн.</nobr></td>

            </tr>

            <!-- EDP: order_detail_list -->

            <tr>
                <td class="width" colspan="4">Всего:</td>
                <td colspan="2">{ORDER_DETAIL_TOTAL_COUNT} шт.</td>
            </tr>


            <!-- BDP: order_detail_list_empty -->

            <tr>
                <td colspan="6" align="center">Заказов не найдено</td>
            </tr>
            <!-- EDP: order_detail_list_empty -->

            <tr>
                <td class="width" colspan="5"><b>Общая сумма</b></td>
                <td><nobr>{ORDER_DETAIL_TOTAL_SUMM} грн.</nobr></td>
            </tr>
            
            
            {ORDER_DETAIL_DOP_INFO}
            
        </table>


    </div>


</div>
<!-- EDP: order_detail -->
