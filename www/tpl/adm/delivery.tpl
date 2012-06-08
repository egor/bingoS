<!-- BDP: delivery -->

<div style="clear: both; margin: 15px 0;">
    <a href="/admin/adddelivery" title="Добавить службу перевозки" style="margin: 0 5px;"><img src="/img/admin_icons/add_page.png" alt="Добавить службу перевозки" title="Добавить службу перевозки" width="32px" height="32px" /></a>
</div>

<div class="text">

	<table class="text_table" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
            <th scope="col">Название службы</th>
            <th colspan="2" scope="col">Статус</th>
	</tr>

	<tr>

	</tr>


	<!-- BDP: delivery_item -->
		<tr>
		<td><b>{NAME}</b></td>
		<td>{VISIBLE}</td>
		<td align="right">
                    <a href="/admin/editdelivery/{ID}"><img src="/img/admin_icons/admin-edit.png"></a>
		    <a onclick="return confirm('Вы уверены что хотите удалить?');" href="/admin/deletedelivery/{ID}"><img src="/img/admin_icons/admin-delete.png"></a>

		</td>

		</tr>
	<!-- EDP: delivery_item -->



	</table>

</div>

<!-- EDP: delivery -->