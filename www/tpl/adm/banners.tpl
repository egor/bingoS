<!-- BDP: banners -->

 <div class="order_history">
   <table>
   <tr>
   <td colspan="3">
    <a href="/admin/addbannerimg/"><img src="/img/admin_icons/admin-add.png"> Добавить графический баннер</a>
   </td>
   <td align="right" colspan="2">
  <a href="/admin/addbannerhtml/" ><img src="/img/admin_icons/admin-add.png"> Добавить НТML код</a>
   </td>
   </tr>
    <tr class="hnone">     
     <th>Название</th>    
     <th>Расположение на сайте</th>
     <th>Тип</th>
     <th>Статус</th>
     <th></th>
     
    </tr>
    <!-- BDP: banners_list -->
    <tr>
     <td>{BANNER_NAME}</td>
     <td>{BANNER_LAYOUT}</td>   
     <td>{BANNER_TYPE}</td> 
     <td>{BANNER_SHOW_AS}</td>
     
     <td width="45" align="left">
          <a href="/admin/editbanner{ADM_TYPE}/{BANNER_ID}"><img src="/img/admin_icons/admin-edit.png"></a>
	   <a onclick="return confirm('Вы уверены что хотите удалить?');" href="/admin/deletebanner/{BANNER_ID}"><img src="/img/admin_icons/admin-delete.png"></a>
     </td>
       
    </tr>
    <!-- EDP: banners_list -->
    
   </table>
  </div>
  
  <!-- BDP: banners_empty -->
  Нет данных
  <!-- EDP: banners_empty -->
  
<!-- EDP: banners -->
