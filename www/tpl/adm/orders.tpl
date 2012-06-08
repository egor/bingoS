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

<!-- <div class="order_history"> -->
    <table class="text_table admin_orders" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <th scope="col">№</th>
            <th scope="col">Ф.И.О.</th>
            <th scope="col">Сумма</th>
            <th scope="col">Город</th>
            <th scope="col">
                Дата  <a href="#" onclick="dateRange('/admin/orders'); return false;"><img src="/img/calendar.png" width="16" height="16" alt="" />
            </th>
            <th scope="col">Статус</th>
            <th scope="col">&nbsp;</th>
        </tr>
        <!-- BDP: orders_list -->
        <tr>
            <td><a href="/admin/order_detail/{ADM_ORDER_ID}">{ADM_ORDER_ID1}</a></td>
            <td><a href="/admin/order_detail/{ADM_ORDER_ID}">{ADM_ORDER_FIO}</a></td>
            <td>{ADM_ORDER_SUMM} грн.</td>
            <td>{ADM_ORDER_CITY}</td>

            <td>{ADM_ORDER_DATE}</td>
            <td>{ADM_ORDER_STATUS}</td>
            <td align="right"><a title="Удалить заказ" href="/admin/orderdelete/{ADM_ORDER_ID}"><img width="12" height="12" alt="Удалить заказ" src="/img/admin_icons/admin-delete.png"></a></td>
        </tr>
        <!-- EDP: orders_list -->

    </table>
<!-- </div> -->
<!-- EDP: orders -->

<!-- BDP: orders_empty -->
<h2>Заказов не найдено</h2>
<!-- EDP: orders_empty -->
<!-- BDP: order_detail -->

<div class="text">
    <!--<div class="w_del">-->

        <table class="text_table admin_orders" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td class="width">№ заказа:</td>
                <td> {ORDER_DETAIL_ID} </td>
            </tr>
            <tr>
                <td class="width">Фамилия:</td>
                <td> {ORDER_DETAIL_SURNAME} </td>
            </tr>
            <tr>
                <td class="width">Имя:</td>

                <td> {ORDER_DETAIL_NAME} </td>
            </tr>
            <tr>
                <td class="width">Отчество:</td>
                <td> {ORDER_DETAIL_PATRONYMIC} </td>
            </tr>
            <tr>
                <td class="width">e-mai:</td>
                <td> {ORDER_DETAIL_EMAIL} </td>
            </tr>

            <tr>
                <td class="width">Городской телефон (с кодом города):</td>
                <td> {ORDER_DETAIL_CITY_PHONE} </td>
            </tr>
            <tr>
                <td class="width">Мобильный телефон:</td>
                <td> {ORDER_DETAIL_MOBILE_PHONE} </td>
            </tr>
            <tr>
                <td class="width">Способ доставки:</td>

                <td> {ORDER_DETAIL_DELIVERY_SERVICE} </td>
            </tr>
            <!-- BDP: deliver_service_fields -->
            <tr>
                <td class="width">Номер склада:</td>
                <td> {ORDER_DETAIL_WAREHOUSE_NUMBER} </td>
            </tr>
            <tr>
                <td class="width">Стрaхование:</td>
                <td> {ORDER_DETAIL_INSURANCE} </td>
            </tr>

            <tr>
                <td class="width">Способ оплаты:</td>
                <td> {ORDER_DETAIL_PAYMENT_METHOD} </td>
            </tr>
            <!-- EDP: deliver_service_fields -->

            <!-- BDP: deliver_service -->

            <tr>
                <td class="width">Город:</td>
                <td> {ORDER_DETAIL_CITY} </td>
            </tr>
            <tr>
                <td class="width">Улица:</td>

                <td> {ORDER_DETAIL_STREET} </td>
            </tr>
            <tr>
                <td class="width">Номер дома:</td>
                <td> {ORDER_DETAIL_HOUSE_NUM} </td>
            </tr>
            <tr>
                <td class="width">Номер кв. / офиса:</td>
                <td> {ORDER_DETAIL_AP_NUM} </td>
            </tr>

            <tr>
                <td class="width">Дополнительная информация:</td>
                <td> {ORDER_DETAIL_DOP_INFO} </td>
            </tr>
            <!-- EDP: deliver_service -->

            <!-- BDP: passport_data -->
            <tr>
                <td class="width">Серия паспорта:</td>
                <td> {ORDER_DETAIL_PASSPORT} </td>
            </tr>
            <tr>
                <td class="width">Номер паспорта:</td>

                <td> {ORDER_DETAIL_PASSPORT_NUMBER} </td>
            </tr>
            <tr>
                <td class="width">Когда выдан паспорт:</td>
                <td> {ORDER_DETAIL_WHEN_THE_PASSPORT_IS_GIVEN} </td>
            </tr>
            <tr>
                <td class="width">Кем выдан паспорт:</td>
                <td> {ORDER_DETAIL_ISSUED_PASSPORT} </td>
            </tr>

            <tr>
                <td class="width">Адрес прописки:</td>
                <td> {ORDER_DETAIL_REGISTRATION_ADDRESS} </td>
            </tr>
            <tr>
                <td class="width">ИНН:</td>
                <td> {ORDER_DETAIL_INN} </td>
            </tr>
            <!-- EDP: passport_data -->

            <tr>
                <td colspan="2">
                    <table class="text_table admin_orders" cellspacing="0" cellpadding="0" border="0" width="100%">

                        <tr>
                            <th scope="col">№</th>
                            <th scope="col">Наименование</th>
                            <th scope="col">Модель</th>
                            <th scope="col" align="right">Цена</th>
                            <th scope="col" align="center">Кол-во</th>
                            <th scope="col" align="right">Сумма</th>
                        </tr>

                        <!-- BDP: order_detail_list -->





                        <tr id="41">
                            <td>{ORDER_DETAIL_LIST_COUNTER}</td>
                            <td id="basket-goods-name-41"><a title="{ORDER_DETAIL_LIST_NAME}" href="{ORDER_DETAIL_LIST_URL}"> {ORDER_DETAIL_LIST_NAME} </a> </td>
                            <td>{ORDER_DETAIL_LIST_ARTIKUL} </td>
                            <td align="right">{ORDER_DETAIL_LIST_COST} грн.</td>
                            <td align="center">{ORDER_DETAIL_LIST_COUNT} шт.</td>
                            <td align="right"><span >{ORDER_DETAIL_LIST_SUMM}</span> грн.</td>

                        </tr>


                        <!-- EDP: order_detail_list -->
                    </table>
                </td>
            </tr>
            <tr>
                <td class="width">Всего:</td>
                <td align="right">{ORDER_DETAIL_TOTAL_COUNT} шт. </td>
            </tr>


            <!-- BDP: order_detail_list_empty -->
            </tbody></table>
        </td>
        </tr>
        <tr>
            <td colspan="2" align="center">Заказов не найдено</td>
        </tr>
        <!-- EDP: order_detail_list_empty -->

        <tr>
            <td class="width"><b>Общая сумма</b></td>
            <td align="right">{ORDER_DETAIL_TOTAL_SUMM} грн.</td>
        </tr>
        </table>

        <form name="order_detail_status" method="POST" action="/admin/orderupdatestatus/{ORDER_DETAIL_ID}" >
            <table class="text_table admin_orders" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td class="width">Статус</td>
                    <td><select name="status" class="select">
                            <option value="no-paid" {ADM_STATUS_NO_PAID_SELECTED}>Не оплачен</option>
                            <option value="paid" {ADM_STATUS_PAID_SELECTED}>Оплачен</option>
                            <option value="completed" {ADM_STATUS_COMPLETED_SELECTED} >Выполнен</option>
                            <option value="booked" {ADM_STATUS_BOOKED_SELECTED}>Бронь</option>
                            <option value="advance" {ADM_STATUS_ADVANCED_SELECTED}>Аванс</option>
                        </select>

                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <div class="button">
                            <input type="submit" value="Применить" />
                            <span></span>
                        </div>

                        <div class="button">
                            <input type="submit" value="Отмена" onclick="document.order_detail_status.action='/admin/orders/';" />
                            <span></span>
                        </div>
                    </td>
                </tr>
            </table>
        </form>

    <!--</div>-->


</div>
<!-- EDP: order_detail -->
