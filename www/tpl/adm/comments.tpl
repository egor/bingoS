<!-- BDP: comments -->
<div class="goods_full">
   <div class="comment_block">
    <form method="post" action="">
        <table >
            <tr>
                <td>Видимость</td>
                <td><select name="visible">
                        <option value="1" {COMMENT_FIO_VISIBLE}>Видимый</option>
                        <option value="0"  {COMMENT_FIO_HIDDEN}>Скрытый</option>
                    </select></td>
             </tr>
            <tr>
                <td>Имя и фамилия</td>
                <td><input type="text" name ="fio" value ="{COMMENT_FIO}"/></td>
             </tr>
             <tr>
                <td>Период эксплуатации</td>
                <td><input class="small" type="text" name="period_of_operation" value="{COMMENT_PERIOD_OF_OPERATION}" /></td>
             </tr>
             <tr>
                <td>Достоинства</td>
                <td><textarea name="dignity">{COMMENT_DIGNITY}</textarea></td>
             </tr>
             <tr>
                <td>Недостатки</td>
                <td><textarea name="shortcomings">{COMMENT_SHORTCOMMINGS}</textarea></td>
             </tr>
             <tr>
                <td>Рекомендации</td>
                <td><textarea name="recommendations">{COMMENT_RECOMENDATIONS}</textarea></td>
             </tr>
             <tr>
                 <td>Вывод</td>
                 <td><textarea name="conclusion">{COMMENT_CONCLUSION}</textarea></td>
             </tr>
             <tr>
                  <td></td>
                    <td>
                        <p><input type="submit"  value="Отправить" /></p>
                    </td>
                </tr>
            </table>
          </form>
     </div>
</div>
<!-- EDP: comments -->

<!-- BDP: comments_list -->
<div class="text">
    <table class="text_table table-comment" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <th scope="col" width="4"><input type='checkbox' id='check-all'></th>
            <th scope="col">Ф.И.О. (<a href="#" id="comment-show-all">Развернуть все</a>) </th>
            <th scope="col">Дата</th>
            <th scope="col">Артикул</th>
            <th scope="col">Статус</th>
            <th scope="col">&nbsp;</th>
        </tr>
        <!-- BDP: comments_list_items -->

        <tr id="tr1-{COMMENT_ITEM_ID}">
            <td width="4"><input type='checkbox' name='id[]' value='{COMMENT_ITEM_ID}' id='ch-{COMMENT_ITEM_ID}'></td>
            <td> <a href="#" onclick="showCommentInfoBlock({COMMENT_ITEM_ID}); return false;">{COMMENT_ITEM_FIO}</a></td>
            <td>{COMMENT_ITEM_DATE}</td>
            <td>{COMMENT_ITEM_ARTIKUL}</td>
            <td>{COMMENT_ITEM_STATUS}</td>

            <td>
                <a href="/admin/editcomments/{COMMENT_ITEM_ID}"><img src="/img/admin_icons/admin-edit.png"></a>
                <a href="#" onclick="commentDelete({COMMENT_ITEM_ID}); return false;"><img src="/img/admin_icons/admin-delete.png"></a>
            </td>
        </tr>
        <tr class="comment-info-block" id="tr-{COMMENT_ITEM_ID}">
            <td colspan="3">
                <p><b>Период эксплуатации:</b> </p>
                <p>{COMMENT_ITEM_PERIOD_OF_OPERATION} </p>
                <p> <b>Достоинства:</b> </p>
            <p> {COMMENT_ITEM_DIGNITY} </p>
            <p>  <b>Недостатки:</b> </p>
            <p> {COMMENT_ITEM_SHORTCOMINGS} </p>
            <p>  <b>Рекоммендации:</b> </p>
            <p> {COMMENT_ITEM_RECOMMENDATIONS} </p>
                <p>  <b>Вывод:</b> </p>
            <p> {COMMENT_ITEM_CONCLUSION} </p>




            </td>
            <td colspan="3" align="center" valign="middle">
                <a href="{COMMENT_ITEM_HREF}" target="_blank"> <img width="200" height="180" src="{COMMENT_ITEM_CATALOG_PIC}"> </a> <br>
                {COMMENT_ITEM_CATALOG_NAME}
            </td>
        </tr>

        <!-- EDP: comments_list_items -->


    </table>
</div>
        <p id="p-comment-list"> <a href="#"  onclick="commentActiveAll(1); return false;">Активировать </a> / <a href="#" onclick="commentActiveAll(0); return false;">Скрыть </a> <span style="padding:0 10px 0 10px;"> <a href="#" onclick="commentDelete(null); return false;">Удалить</a> </span><span id="checked-count"></span></p>
        {PAGINATOR}
<!-- EDP: comments_list -->