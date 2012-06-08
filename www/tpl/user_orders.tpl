<!-- BDP: orders -->
<div class="basket_block1">
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
    <table>
        <tr >
            <td><strong>№</strong></td>

            <td><strong>Сумма</strong></td>

            <td><strong>Дата </strong> <a href="#" onclick="dateRange('/user/orders'); return false;"><img src="/img/calendar.png" width="16" height="16" alt="" /></a></th>
            <td><strong>Статус</strong></td>
            <td align="right"><strong>Удалить</strong></td>
        </tr>
        <!-- BDP: orders_list -->
        <tr>
            <td><a href="/user/order_detail/{ADM_ORDER_ID}">{ADM_ORDER_ID1}</a></td>
            <td>{ADM_ORDER_SUMM} грн.</td>


            <td>{ADM_ORDER_DATE}</td>
            <td class="{ADM_ORDER_CLASS}">{ADM_ORDER_STATUS}</td>
            <td align="right"><a title="Удалить заказ" href="/user/orderdelete/{ADM_ORDER_ID}"><img width="12" height="12" alt="Удалить заказ" src="/img/admin_icons/admin-delete.png"></a></td>
        </tr>
        <!-- EDP: orders_list -->

    </table>
</div>
<!-- EDP: orders -->

<!-- BDP: orders_empty -->
<h2>Заказов не найдено</h2>
<!-- EDP: orders_empty -->

<!-- BDP: order_detail -->
<div class="basket_block">
    <div class="w_del">
        <br>
        <p><strong>№ заказа: {ORDER_DETAIL_ID} </strong></p>
        <table>
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
                <td id="basket-goods-name-41"><a title="{ORDER_DETAIL_LIST_NAME}" href="{ORDER_DETAIL_LIST_URL}"> {ORDER_DETAIL_LIST_NAME} </a> </td>
                <td class="nowrap">{ORDER_DETAIL_LIST_ARTIKUL} </td>
                <td class="right">{ORDER_DETAIL_LIST_COST} грн.</td>
                <td class="center">{ORDER_DETAIL_LIST_COUNT} шт.</td>
                <td class="right nowrap"><span >{ORDER_DETAIL_LIST_SUMM}</span> грн.</td>

            </tr>


            <!-- EDP: order_detail_list -->
        </table>


        <div class="total_summ">Всего: {ORDER_DETAIL_TOTAL_COUNT} шт.<span>Сумма: <span>{ORDER_DETAIL_TOTAL_SUMM} грн.</span></span></div>
        <!-- BDP: order_detail_list_empty -->           
        <p>Заказов не найдено</p>      
        <!-- EDP: order_detail_list_empty -->
    </div>
</div>
<!-- EDP: order_detail -->
