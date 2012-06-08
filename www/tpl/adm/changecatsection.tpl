<!-- BDP: changecatsection -->
<style>
    table.admin-table {width: 665px;}
    table.admin-table td#left {width: 300px; vertical-align: top;} 
    table.admin-table td#left select{width: 299px; height: 100%; vertical-align: top;} 
    table.admin-table td select#select-template-type-goods{display: none;} 
    
    table.admin-table td#center {width: 15px;}     
    table.admin-table td select {width: 100%;}
    
    
</style>
<table >
   <tr>
      <td width="50%"><b>Характеристики</b></td>
      <td align="right">
         <input type="text" id="search-tree" value=""  style="width: 100px;" />
         <input type="button" id="bt-search-tree" value=" найти " style="width: 55px;" />        
      </td>
   </tr>

   <tr>
      <td colspan='2'>
          
          
         <script type="text/javascript" src="/js/jq-section-fields-templates-viewer.js"> </script>
          
         <script type="text/javascript">
            var sectionArtikul = '{SECTION_ARTIKUL}';
            var activeSection = '';
         </script>
         

         <script type="text/javascript" src="/js/jq-tree-view.js"></script>

         <div id="options-tree" style="overflow:auto; width: 740px;">



         </div>
         <div id="dialog-tree">

         
               {ADM_SECTION_FIELDS_IS_PARAMS}  
         

            <table class="admin-table" border="0">             
               <tr>
                  <td colspan="2" valign="top">
                     <select id="section-field-templates">
                        <option value="#">Укажите шаблон</option>
                     </select>
                  </td>
                  <td> 
                      <select id="select-template-type">
                          <option value="#">Укажите шаблон</option>
                          <option value="1">Выбрать поля из шаблона</option>
                          <option value="2">Выбрать поля из созданного раздела</option>                          
                      </select>
                      <select id="select-template-type-goods">
                                               
                      </select>
                  </td>
               </tr>
               <tr>
                  <td id="left">
                     <div style="float:left">
                        <select id="section-field-templates-list" size="15">
                        </select>
                     </div>
                  </td>
                  <td align="center" id="center">
                     <div style="float:left;">
                        &nbsp; <input style="width: 35px; margin-right: 5px;" type="button" value=">" class="arrow-button" id="send-one" /> <br /><br />
                        &nbsp; <input style="width: 35px; margin-right: 5px;" type="button" value=">>" class="arrow-button"  id="send-all" /> <br /><br />
                        &nbsp; <input style="width: 35px; margin-right: 5px;" type="button" value="<" class="arrow-button"  id="back-one" /> <br /><br />
                        &nbsp; <input style="width: 35px; margin-right: 5px;" type="button" value="<<" class="arrow-button"  id="back-all" /> <br /><br />
                     </div>
                  </td>
                  <td valign="top">

                     <select id="section-field-templates-selected-field" name="section_field_templates_selected_field"  size="15">

                     </select><br />
                     <div id="div-section-field-templates-selected-type">
                        
                        
                        <div>
                           <div id="hidden-cont"></div>                           
                           </td>
                           </tr>
                           </table>
                        </div>


                  </td>
               </tr>

            </table>


            <!-- EDP: changecatsection -->