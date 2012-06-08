<!-- BDP: catalog -->

<!-- BDP: catalog_items_body -->




<div class="goods_list">
    {TOP_NAV_BAR}
    <!-- BDP: catalog_items -->
    {CATALOG_ITEM}
    <!-- EDP: catalog_items -->
    {BOTT_NAV_BAR}
    <div class="clear"></div>
</div>
<!-- EDP: catalog_items_body -->


<!-- BDP: catalog_items_empty -->
{GOODS_EMPTY}
<!-- EDP: catalog_items_empty -->

<!-- EDP: catalog -->




<!-- BDP: catalog_section_list_body -->




{TOP_NAV_BAR}
<ul class="news_list">
    <!-- BDP: catalog_section_list -->
    <li>{CATALOG_SECTION_LIST_PIC}
        <strong> <a href="{CATALOG_SECTION_LIST_HREF}">{CATALOG_SECTION_LIST_NAME}</a> </strong>
        <p>{CATALOG_SECTION_LIST_PREVIEW}</p>
        <p class="{ADMIM_CLASS_NAME}"><a href="{CATALOG_SECTION_LIST_HREF}" title="{CATALOG_SECTION_LIST_NAME}">Подробнее</a> {ADMIN_BUTTON_PANEL}</p>
    </li>
    <!-- EDP: catalog_section_list -->
</ul>
{BOTT_NAV_BAR}
<!-- BDP: catalog_section_list_empty -->
{GOODS_EMPTY}
<!-- EDP: catalog_section_list_empty -->


<!-- EDP: catalog_section_list_body -->


<!-- BDP: catalog_detail_body -->
<div class="category"><span>Группа:</span> {CATALOG_DETAIL_SECTION_TEXT}</div>
<div class="big_image">
    {CATALOG_DETAIL_ING_TYPE}      
    {CATALOG_DETAIL_ING_LOOP}

    <a href="{CATALOG_DETAIL_IMG_REAL_PATH}{CATALOG_DETAIL_IMG}" title="{CATALOG_DETAIL_TITLE}" rel="lightbox[photo-item]"  id="href-detial" class="link_big">
        <img src="{CATALOG_DETAIL_IMG_BIG_PATH}{CATALOG_DETAIL_IMG}" class="img"  id="img-detial" alt="{CATALOG_DETAIL_ALT}" title="{CATALOG_DETAIL_TITLE}" />
    </a>
    
    {3D_OBJECTS}
</div>

<div class="attributes">
   <div class="rate1">{CATALOG_DETAIL_RATING}</div>
        <!-- BDP: store -->
        <table cellspacing="0" cellpadding="0" border="0">
            <tbody>
                 <!-- BDP: catalog_detail_plashka -->
                <tr class="old_price">
            <td class="n">Розничная цена:
            </td><td class="v">{CATALOG_DETAIL_COST_IN_SHOP} грн.
            </td></tr>
            <tr class="economy">
            <td class="n">Экономия:</td>
            <td class="v">{CATALOG_DETAIL_COST_ECONOM} грн.</td>
            </tr>
             <!-- EDP: catalog_detail_plashka -->
             
            
            <tr class="new_price">
            <td class="n">Наша цена:</td>
            <td class="v">{CATALOG_DETAIL_COST}  грн.</td>
            
            
            </tr>
            
            <!-- BDP: pbutton -->
               

                <tr class="to_tobusket">
                    <td class="n"><span class="count"><input type="text" value="1" name="count" class="goods-input" id="{CATALOG_DETAIL_ID}">шт.</span></td>
                    <td class="v"><a href="detail"  id="{CATALOG_DETAIL_ID}" class="add buy-button" >Купить</a></td>
                 </tr>
            
            <!-- EDP: pbutton -->
        
            <!-- BDP: npbutton -->
            
             <tr class="to_tobusket">
                <td class="n"><span class="count"><input style="display:none;" type="text" value="1" name="count" class="goods-input" id="{CATALOG_DETAIL_ID}"></span></td>
                <td class="v"><a href="/basket" class="link_basket">Оформить заказ</a></td>
            </tr>
            
            <!-- EDP: npbutton -->
            
             <!-- BDP: availability_button -->
             <tr class="to_tobusket">
                <td class="v" colspan="2"><span class="add_disabled">{EXPECTED_TEXT}</span></td>
             </tr>                
            <!-- EDP: availability_button -->

            <!-- BDP: availability_button1 -->
             <tr class="to_tobusket">
                <td class="v" colspan="2"><span class="add_disabled">{EXPECTED_TEXT2}</span></td>
             </tr>
               
            <!-- EDP: availability_button1 -->
            
            <!-- <tr class="to_tobusket">
            <td class="v" colspan="2"><span class="add_disabled">Ожидается на складе</span></td>
            </tr> -->
        </tbody></table>
        <!-- EDP: store -->
        
        <div class="zakaz_phone">Заказать по телефону: (056) 778-65-84</div>
        <div class="product_about">
          <table>
            <tbody>


                {RIGHT_FIELDS}

            </tbody></table>
        </div>
        <div class="bookmarks">
            {SOC_BUTTONS}        
         </div>   
        <div class="bookmarks product_item">
           
          
           
        </div>
    </div>
            
  
            
            
    <!-- BDP: catalog_foreshortening_list -->
        <div class="thumbs">      
            <!-- BDP: catalog_foreshortening_items -->
            <a class="small_image" href="/img/catalog/gallery/real/foreshortening/{F_SRC}" title="{G_TITLE}" rel="lbox[photo-item1]"  id="{F_ID}">
                <img src="/img/catalog/gallery/small_1/foreshortening/{F_SRC}" alt="{F_ALT}" title="{F_TITLE}" id="img-{F_ID}" >
            </a>
             <!-- EDP: catalog_foreshortening_items -->  
        </div>  
 
    <!-- EDP: catalog_foreshortening_list -->

  <!-- BDP: catalog_gallery_list -->
        <div class="thumbs">      
            <!-- BDP: catalog_gallery_items -->
            <a class="small_image" href="/img/catalog/gallery/real/gallery/{G_SRC}" title="{G_TITLE}" rel="lbox[photo-item1]"  id="{F_ID}">
                <img src="/img/catalog/gallery/small_1/gallery/{G_SRC}" alt="{G_ALT}" title="{G_TITLE}" id="img-{G_ID}" >
            </a>
             <!-- EDP: catalog_gallery_items -->  
        </div>  
 
    <!-- EDP: catalog_gallery_list -->        
     <div class="product_description">
          {BOTTOM_FIELDS}
  </div>  
    <div class="specifications">
         {CATALOG_DETAIL_USER_SECTION_FIELDS}
    </div>  
    
    
     <!-- BDP: complate_block -->
    <div class="clear"></div>
   <div class="wrap_other_products">
        {USED_COMPLATE_TITLE}
        <div class="products_list">{USED_COMPLATE_CATALOG_ITEM}</div>
        
    </div>
    <!-- EDP: complate_block -->

    <!-- BDP: features_block -->
    <div class="clear"></div>
   <div class="wrap_other_products">
        {FEATURED_TITLE}
       <div class="products_list"> {FEATURED_CATALOG_ITEM} </div>
    </div>
    <!-- EDP: features_block -->
    
    
    {COMMENTS}
    
<!-- EDP: catalog_detail_body -->