<!-- BDP: data_import -->
<form enctype="multipart/form-data" method="post" action=""> 
  <div class="order_history">
  {ADM_FIELDS}
  <h3>Эти товары редактировались в админ. панели. Выберите данные, которые вы хотите заменить.</h3>
   <table border="0">
    <tr class="hnone">
     <th ><input type="checkbox" style="width:5px;" class="check-all"> Все</th>
     <th>Товар</th>
    
     <th>Артикул</th>
     <th>Дата изменения</th>     
    </tr>
    <!-- BDP: data_import_list -->
    <tr>
     <td><input type="checkbox" style="width:5px;" name="changed[{ADM_ART}]" class="check-import-export" /></td>
     <td>{ADM_GOODS_NAME}</td>    
     <td>{ADM_ART}</td>
     <td>{ADM_DATE}</td>     
    </tr>
    <!-- EDP: data_import_list -->
    
   </table>
  </div>
  <table Border=0 CellSpacing=0 CellPadding=0 Width="100%" Align="" vAlign="" class="admin-table">
  <tr>
     <td colspan=2>&nbsp; <input type="hidden" name="HTTP_REFERER" value="{REFERER}"></td>
</tr>
<tr>
     
<td colspan=2><br><br><center><input name="h_chenged" type="hidden"><input class="frm_bsubm" type="submit"></center></td>
</tr>
</table>
  </form>
  
<!-- EDP: data_import -->
