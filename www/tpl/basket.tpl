<!-- BDP: basket -->
<form id="basket_filter" action="/order/add" method="post">

<table border="0" cellspacing="0" cellpadding="0" class="basket_table">
    <tr>
        <th scope="col" class="action">Действие</th>
        <th scope="col" class="name">Наименование</th>
        <th scope="col" class="code">Артикул</th>
        <th scope="col" class="price">Цена</th>
        <th scope="col" class="count">Кол-во</th>
        <th scope="col" class="sum">Сумма</th>
    </tr>
    <!-- BDP: basket_items -->
    <tr id="tr-{BASKET_ITEM_ID}" {BASKET_ITEM_CLASS}>
        <td class="action"><a class="basket-delete-button {BASKET_ITEM_ACTION}" href="/basket/{BASKET_ITEM_ACTION}/{BASKET_ITEM_ID}" id="{BASKET_ITEM_ID}">{BASKET_ITEM_ACTION_TEXT}</a></td>
        <td class="name">{BASKET_ITEM_NAME}</td>
        <td class="code">{BASKET_ITEM_ARTIKUL}</td>
        <td class="price">{BASKET_ITEM_COST} грн.</td>
        <td class="count"><input type="text" class="goods-input goods-input-basket {BASKET_ITEM_INPUT_DISABLED}" id="{BASKET_ITEM_ID}" name="count" value="{BASKET_ITEM_COUNT}" {BASKET_ITEM_INPUT_DISABLED} />шт.</td>
        <td class="sum" id="summ-{BASKET_ITEM_ID}">{BASKET_ITEM_SUMM} грн.</td>
    </tr>
    <!-- EDP: basket_items -->
</table>

<div class="basket_table_all">
    <span class="all_summ">{BASKET_ITEM_ALL_SUMM} грн.</span>
    <b class="all_count">{BASKET_ITEM_ALL_COUNT} шт.</b>
    <span class="all_count_title">Итого заказано:</span>
    <a href="#" class="clear_basket">Очистить корзину</a>
</div>
<div class="button basket">
    <input type="submit" value="Далее" name="submit" />
    <span></span>
</div>


<!--
<div class="basket_block">

    <div class="basket_block">

        <table>
            <tbody>
                <tr>
                    <th>&nbsp;</th>
                    <th>Наименование</th>
                    <th>Модель</th>
                    <th class="right">Цена</th>
                    <th class="center">Кол-во</th>
                    <th class="right">Сумма</th>
                    <th class="right">Удалить</th>
                </tr>



                <tr id="{BASKET_ITEM_ID}" {BASKET_ITEM_CLASS}>
                    <td class="basket-counter">{BASKET_ITEM_COUNTER}</td>
                    <td id="basket-goods-name-{BASKET_ITEM_ID}">{BASKET_ITEM_NAME} </td>
                    <td class="nowrap">{BASKET_ITEM_ARTIKUL}  </td>
                    <td class="right">{BASKET_ITEM_COST} грн.</td>
                    <td class="center"><input type="text" class="goods-input goods-input-basket" id="{BASKET_ITEM_ID}" style="{BASKET_ITEM_INPUT_DISABLED_STYLE}" value="{BASKET_ITEM_COUNT}" {BASKET_ITEM_INPUT_DISABLED}></td>
                    <td class="right nowrap"><span id="summ-{BASKET_ITEM_ID}">{BASKET_ITEM_SUMM}</span> грн.</td>
                    <td class="right"><a href="/basket/delete/{BASKET_ITEM_ID}" id="{BASKET_ITEM_ID}" class="basket-delete-button">{BASKET_ITEM_BUTTON_NAME}</a></td>
                </tr>



            </tbody></table>
        <div class="total_summ"><a href="#" id="clear-basket" class="clear-basket">Очистить корзину</a><span>Сумма: <span>{BASKET_ITEM_ALL_SUMM} грн.</span></span></div>
        <div class="make_an_order"><input type="submit" class="button"  value="Оформить заказ" style="height: 36px; padding-top: 0pt; padding-bottom: 6px;"></div>

    </div>
</div>

</form>
-->
<!-- EDP: basket -->


<!-- BDP: basket_empty -->
<h2>Заказов не найдено</h2>
<!-- EDP: basket_empty -->