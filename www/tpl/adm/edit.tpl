<!-- BDP: start -->

<form enctype="multipart/form-data" method="post" action="" id="from1">
<table Border=0 CellSpacing=0 CellPadding=0 Width="100%" Align="" vAlign="" class="admin-table">
<!-- EDP: start -->

<!-- BDP: js -->

<!-- EDP: js -->


<!-- BDP: mce -->
<script type="text/javascript" src="/js/tiny_mce2/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
    tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "mceEditor",
        language : "ru",
        mode : "textareas",
        theme : "advanced",
        plugins : "imagemanager,filemanager,safari,layer,table,advhr,advimage,advlink,preview,media,contextmenu,paste,directionality,noneditable,visualchars, nonbreaking",
        theme_advanced_buttons2_add : "separator,preview",
        theme_advanced_buttons2_add_before: "cut,copy,pastetext,pasteword,separator",
        theme_advanced_buttons3_add_before : "tablecontrols,separator",
        theme_advanced_buttons3_add : "media,advhr,separator,visualchars,nonbreaking",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_path_location : "bottom",
        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",
        extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color],span[class|align|style]",
        file_browser_callback : "mcImageManager.filebrowserCallBack",
        theme_advanced_resize_horizontal : true,
        theme_advanced_resizing : true,
        apply_source_formatting : false,
        relative_urls : false,
        add_unload_trigger : true,
        strict_loading_mode : true
    });
</script>
<!-- EDP: mce -->






<!-- BDP: settings -->
 <!-- BDP: settings_item -->
<tr>
  <td width="220" valign="middle">{NAME}: &nbsp;&nbsp;&nbsp;</td><td><textarea rows="1" style="width: 100%;" name="settings_{KEY}" >{VALUE}</textarea><br></td>
</tr>
 <!-- EDP: settings_item -->
<!-- EDP: settings -->

<!-- BDP: lookups -->
    <!-- BDP: lookups_item -->
    <tr>
        <td width="220" valign="middle">{LOOK_NAME}: &nbsp;&nbsp;&nbsp;</td><td><textarea rows="4" style="width: 100%;" name="{LOOK_KEY}">{LOOK_VALUE}</textarea><br></td>
    </tr>
    <!-- EDP: lookups_item -->
<!-- EDP: lookups -->

<!-- BDP: adress -->
<tr>
     <td>Адрес в URL <small><font color="red">[</font>A-Z<font color="red">]</font><font color="red">[</font>a-z<font color="red">]</font><font color="red">[</font>0-9<font color="red">]</font><font color="red">[</font>-<font color="red">]</font><font color="red">[</font>_<font color="red">]</font></small>:</td><td><input type=text size=50 name="adm_href" value='{ADM_HREF}' class='admin-input-text'  id='admin-input-url'><br></td>
</tr>
<!-- EDP: adress -->



<!-- BDP: s_adress -->
<tr>
     <td>Адрес в URL <small><font color="red">[</font>A-Z<font color="red">]</font><font color="red">[</font>a-z<font color="red">]</font><font color="red">[</font>0-9<font color="red">]</font><font color="red">[</font>-<font color="red">]</font><font color="red">[</font>_<font color="red">]</font></small>:</td><td><input type=text size=50 name="adm_href" value='{ADM_HREF}' class='admin-input-text'  id='admin-input-url'><br></td>
</tr>
<!-- EDP: s_adress -->

<!-- BDP: visible -->
<tr>
    <td>Отображать</td>
    <td>
  	<select name="visible">{VISIBLE_S}
  	</select>
    </td>
</tr>
<!-- EDP: visible -->

<!-- BDP: s_visible -->
<tr>
    <td>Отображать</td>
    <td>
  	<select name="visible">{VISIBLE_S}
  	</select>
    </td>
</tr>
<!-- EDP: s_visible -->

<!-- BDP: top -->
<tr>
    <td>Выводить на главную</td>
    <td>
  	<select name="top">{TOP_S}
  	</select>
    </td>
</tr>
<!-- EDP: top -->

<!-- BDP: header -->
<tr>
     <td>Header: &nbsp;&nbsp;&nbsp;</td><td><input type="text" name="header" value='{ADM_HEADER}' class='admin-input-text'  id='admin-input-header'><br></td>
</tr>
<!-- EDP: header -->

<!-- BDP: meta -->
<tr>
   <td colspan="2"><a href="#" id="seo-tags" class='adm-href'><img src="/img/admin_icons/seo.png">&nbsp;&nbsp;SEO поля</a></td>
</tr>
<tr>
     <td>Header: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="header" value='{ADM_HEADER}' class='admin-input-text' id='admin-input-header'><br></td>
</tr>
<tr>
     <td>Title: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="title" value='{ADM_TITLE}' class='admin-input-text' id='admin-input-title'> <br></td>
</tr>
<tr>
     <td>Keywords: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="keywords" value='{ADM_KEYWORDS}' class='admin-input-text' id='admin-input-keyword'><br></td>
</tr>
<tr>
     <td>Description: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="description" value='{ADM_DESCRIPTION}' class='admin-input-text' id='admin-input-description'><br></td>
</tr>
<!-- EDP: meta -->


<!-- BDP: s_meta -->

<tr>
     <td>Header: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="header" value='{ADM_HEADER}' class='admin-input-text' id='admin-input-header'><br></td>
</tr>
<tr>
     <td>Title: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="title" value='{ADM_TITLE}' class='admin-input-text' id='admin-input-title'> <br></td>
</tr>
<tr>
     <td>Keywords: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="keywords" value='{ADM_KEYWORDS}' class='admin-input-text' id='admin-input-keyword'><br></td>
</tr>
<tr>
     <td>Description: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="description" value='{ADM_DESCRIPTION}' class='admin-input-text' id='admin-input-description'><br></td>
</tr>
<!-- EDP: s_meta -->

<!-- BDP: news -->
<tr>
    <td>Топ новость</td>
    <td>
  	<select name="topnews">{ADM_TOPNEWS}
  	</select>
    </td>
</tr>


<tr>
     <td>Дата: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size="10" name="date" value="{ADM_DATE}" id="date_i" readonly="1" />
    <a href="#" onclick="showDataPicker(); return false;"><img src="/img/calendar.gif" id="trigger" title="Date selector"/></a></td>
</tr>


<tr>
     <td colspan=2><b>Анонс:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan=2><textarea class="mceEditor" rows=10 style="width: 100%;" name="preview">{ADM_PREVIEW}</textarea></td>
</tr>
<!-- EDP: news -->

<!-- BDP: name -->
<tr>
    <td>Название:</td><td><input type="text" size="50" name="name" value="{ADM_NAME}" class='admin-input-text'  id='admin-input-name'></td>
</tr>
<!-- EDP: name -->

<!-- BDP: artikul -->
<tr>
    <td>Артикул:</td><td><input type="text" size="50" name="artikul" value="{ADM_ARTIKUL}" ></td>
</tr>
<!-- EDP: artikul -->

<!-- BDP: proizvoditel -->
<tr>
    <td>Производитель:</td><td><input type="text" size="50" name="proizvoditel" value="{ADM_PROISVODITEL}" class='admin-input-text' ></td>
</tr>
<!-- EDP: proizvoditel -->

<!-- BDP: cost -->
<tr>
    <td>Цена:</td><td><input type="text" size="50" name="cost" value="{ADM_COST}" ></td>
</tr>
<!-- EDP: cost -->

<!-- BDP: cost_old -->
<tr>
   <td colspan="2"><a href="#" id="info-fields" class='adm-href'><img src="/img/admin_icons/info.png">&nbsp;&nbsp;Информация о товаре</a></td>
</tr>
<tr>
<tr>
    <td>Цена в магазинах:</td><td><input type="text" size="50" name="cost_old" value="{ADM_COST_OLD}" ></td>
</tr>
<!-- EDP: cost_old -->


<!-- BDP: pos -->
<tr>
    <td>Позиция:</td><td><input type="text" size="50" name="position" value="{ADM_POSITION}"></td>
</tr>
<!-- EDP: pos -->

<!-- BDP: s_pos -->
<tr >
    <td>Позиция:</td><td><input type="text" size="50" name="position" value="{ADM_POSITION}"></td>
</tr>
<!-- EDP: s_pos -->

<!-- BDP: preview -->
<tr>
     <td colspan=2><b>Краткое описание:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="preview">{ADM_PREVIEW}</textarea></td>
</tr>
<!-- EDP: preview -->

<!-- BDP: s1_preview -->
<tr>
     <td colspan=2><b>Краткое описание:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="preview">{ADM_PREVIEW}</textarea></td>
</tr>
<!-- EDP: s1_preview -->

<!-- BDP: s_preview -->
<tr>
     <td colspan=2><b>Краткое описание:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="preview">{ADM_PREVIEW}</textarea></td>
</tr>
<!-- EDP: s_preview -->


<!-- BDP: body -->
<tr>
     <td colspan=2><b>Текст:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="body">{ADM_BODY}</textarea></td>
</tr>
<!-- EDP: body -->

<!-- BDP: s_body -->
<tr>
     <td colspan=2><b>Текст:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="body">{ADM_BODY}</textarea></td>
</tr>
<!-- EDP: s_body -->

<!-- BDP: pic -->
<tr style="width:500px;">
   <td colspan="2"><a href="#" id="photo" class='adm-href'><img src="/img/admin_icons/img.png">&nbsp;&nbsp;Изображение товара / Галерея</a></td>
</tr>
<tr>

    <td align="left">
    <!-- BDP: show_pic -->
    	<br />
	  <img src="{ADM_IMG_SRC}" alt="{ADM_IMG_ALT}" title="{ADM_IMG_TITLE}" />
            <!-- BDP: show_pic_dell -->
            <div class="clear"></div>
                <a onclick="return confirm('Вы уверены что хотите удалить?'); return false;" title="Удалить" href="/admin/{ADM_DELL_PHOT_METHOD}/{ADM_DELL_PHOTO_ID}"><img width="12" height="12" alt="Удалить" src="/img/admin_icons/delete.png"> Удалить фото</a>
                <!-- EDP: show_pic_dell -->
	 <br />

	<!-- EDP: show_pic -->
    </td><td valign="bottom">
	 Выберите Фото <br />
        <input type="file" name="pic" id="pic" /><br />

    </td>
</tr>
<!-- EDP: pic -->


<!-- BDP: s_pic -->

<tr>

    <td align="left">
    <!-- BDP: s_show_pic -->
    	<br />
	  	<img src="{ADM_IMG_SRC}" alt="{ADM_IMG_ALT}" title="{ADM_IMG_TITLE}" />
                <!-- BDP: s_show_pic_dell -->
                <a onclick="return confirm('Вы уверены что хотите удалить?'); return false;" title="Удалить" href="/admin/{ADM_DELL_PHOT_METHOD}/{ADM_DELL_PHOTO_ID}"><img width="12" height="12" alt="Удалить" src="/img/admin_icons/delete.png"></a>
                <!-- EDP: s_show_pic_dell -->
	 <br />

	<!-- EDP: s_show_pic -->
    </td><td valign="bottom">
	 Выберите Фото <br />
        <input type="file" name="pic" id="pic" /><br />

    </td>
</tr>
<!-- EDP: s_pic -->



<!-- BDP: goods_pic -->
<tr>

    <td align="left">Картинка:
    <!-- BDP: show_goods_pic -->
    	<br />
	  	<img src="{ADM_IMG_SRC}" alt="{ADM_IMG_ALT}" title="{ADM_IMG_TITLE}" />
	  	<br />
	  	<input name="dell_pic" type="checkbox" style="width: 5px;" onclick="if (this.checked) {this.checked = confirm('Вы уверены что хотите удалить изображение?'); }"> Удалить изображение
	<!-- EDP: show_goods_pic -->
    </td><td valign="bottom">
		<input type="file" name="pic">

    </td>
</tr>
<!-- EDP: goods_pic -->

<!-- BDP: catalog_upload_pic -->
<tr>
    <td colspan='2'>
   Добавить изображения к товарам каталога <br>
   Имена файлов картинок должны быть указаны в файле импорта.

    </td>
</tr>

<tr>
    <td colspan="2">

    <table class="adm-gallery">
</tr>
		<tr>
    		<td>
    			<table>
                            <tr><td>Картинка 1:</td><td> <input type="file"  name="catalog_upload_pic1"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="catalog_upload_title1"   > </td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="catalog_upload_alt1" ></td></tr>

    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 2:</td><td> <input type="file" name="catalog_upload_pic2"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="catalog_upload_title2" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="catalog_upload_alt2" ></td></tr>

    			</table>
    		</td>
		</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 3:</td><td> <input type="file" name="catalog_upload_pic3"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="catalog_upload_title3" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="catalog_upload_alt3" ></td></tr>
    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 4:</td><td> <input type="file" name="catalog_upload_pic4"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="catalog_upload_title4" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="catalog_upload_alt4" ></td></tr>
    			</table>
    		</td>

		</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 5:</td><td> <input type="file" name="catalog_upload_pic5"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="catalog_upload_title5" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="catalog_upload_alt5" ></td></tr>
    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 6:</td><td> <input type="file" name="catalog_upload_pic6"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="catalog_upload_title6" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="catalog_upload_alt6" ></td></tr>
    			</table>
    		</td>

		</tr>
    </table>



    </td>
</tr>

<!-- EDP: catalog_upload_pic -->


<!-- BDP: gallery_pic -->
<tr>
    <td colspan='2'>
   Добавить изображения в минигалерею
    </td>
</tr>

<tr >
    <td colspan="2">
	<div class="goods_full">
	 <div class="details_list">
	 	<ul>
			<!-- BDP: gallery_pic_list -->
    		<li style="font-size: 12px"><a href="#"><img src="/img/catalog/gallery/small/{G_SRC}" width="100" height="100" alt="{G_ALT}" title="{G_TITLE}" /></a>
    		<br />
	  			<input name="dell_gallery_pic[{G_ID}]" type="checkbox" style="width: 5px;" onclick="if (this.checked) {this.checked = confirm('Вы уверены что хотите удалить изображение?'); }"> Удалить
    		</li>
    		<!-- EDP: gallery_pic_list -->
    	</ul>
    </div>
    </div>
    <table class="adm-gallery">
</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 1:</td><td> <input type="file" name="gallery_pic1"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_title1"  ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_alt1" ></td></tr>

    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 2:</td><td> <input type="file" name="gallery_pic2"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_title2" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_alt2" ></td></tr>

    			</table>
    		</td>
		</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 3:</td><td> <input type="file" name="gallery_pic3"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_title3" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_alt3" ></td></tr>
    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 4:</td><td> <input type="file" name="gallery_pic4"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_title4" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_alt4" ></td></tr>
    			</table>
    		</td>

		</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 5:</td><td> <input type="file" name="gallery_pic5"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_title5" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_alt5" ></td></tr>
    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 6:</td><td> <input type="file" name="gallery_pic6"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_title6" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_alt6" ></td></tr>
    			</table>
    		</td>

		</tr>
    </table>



    </td>
</tr>

<!-- EDP: gallery_pic -->

<!-- BDP: banners_html -->
<tr>
	<td>Имя (используется только для удобства в администрировании)</td><td><input type="text" name="name"  class='admin-input-text' value="{ADM_BANNER_NAME}"></td>
<tr>
<tr>
	<td>Расположение на сайте</td><td>
	<select name="layout">
		<option value="top" {ADM_BANNER_LAYOUT_TOP}>Вверху (не более двух)</option>
		<option value="left" {ADM_BANNER_LAYOUT_LEFT}>Слева под меню</option>
		<option value="bottom" {ADM_BANNER_LAYOUT_BOTTOM}>Внизу (Наши партнеры)</option>
	</select>
	</td>
</tr>
<tr>
	<td>Порядок вывода</td><td><input type="text" name="position" value="{ADM_BANNER_POSITION}"></td>
</tr>
<tr>
	<td>Выводить</td><td>
	<select name="show_as">
		<option value="all" {ADM_BANNER_SHOW_AS_ALL}>На всех страницах сайта</option>
		<option value="index" {ADM_BANNER_SHOW_AS_INDEX}>Только на главной</option>
		<option value="hide" {ADM_BANNER_SHOW_AS_HIDE}>Скрыть</option>

	</select>
	</td>
</tr>
<tr>
	<td>Порядок вывода</td><td><input type="text" name="position" value="{ADM_BANNER_POSITION}"></td>
</tr>
<tr>
     <td colspan=2><b>HTML код</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="body">{ADM_BODY}</textarea></td>
</tr>

<!-- EDP: banners_html -->


<!-- BDP: banners_pic -->
<tr>
	<td>Имя (используется только для удобства в администрировании)</td><td><input type="text" name="name"  class='admin-input-text' value="{ADM_BANNER_NAME}"></td>
<tr>
<tr>
	<td>Укажите файл</td><td><input type="file" name="pic" ></td>
</tr>
<tr>
	<td>Ссылка (можно не указывать для FLASH файлов <i>.swf</i>)</td><td><input type="text" name="href" value="{ADM_BANNER_HREF}"  class='admin-input-text'></td>
</tr>
<tr>
	<td>Альтернативный текст (тег Alt)</td><td><input type="text" name="alt" value="{ADM_BANNER_ALT}"  class='admin-input-text'></td>
</tr>
<tr>
	<td>Текст для всплывающей подсказки (тег Title)</td><td><input type="text" name="title" value="{ADM_BANNER_TITLE}"  class='admin-input-text'></td>
</tr>
<tr>
	<td>Расположение на сайте</td><td>
	<select name="layout">
		<option value="top" {ADM_BANNER_LAYOUT_TOP}>Вверху (не более двух)</option>
		<option value="left" {ADM_BANNER_LAYOUT_LEFT}>Слева под меню</option>
		<option value="bottom" {ADM_BANNER_LAYOUT_BOTTOM}>Внизу (Наши партнеры)</option>
	</select>
	</td>
</tr>
<tr>
	<td>Порядок вывода</td><td><input type="text" name="position" value="{ADM_BANNER_POSITION}"></td>
</tr>
<tr>
	<td>Выводить</td><td>
	<select name="show_as">
		<option value="all" {ADM_BANNER_SHOW_AS_ALL}>На всех страницах сайта</option>
		<option value="index" {ADM_BANNER_SHOW_AS_INDEX}>Только на главной</option>
		<option value="hide" {ADM_BANNER_SHOW_AS_HIDE}>Скрыть</option>

	</select>
	</td>
</tr>
<!-- EDP: banners_pic -->


<!-- BDP: gallery_foreshortening_pic -->
<tr>
    <td colspan="2">Добавить изображения товара в другом ракурсе (Не меньше: 420px X 420px)</td>
</tr>
<tr>
    <td colspan="2">
    <div class="goods_full">
	 <div class="details_list">
	 	<ul>
			<!-- BDP: gallery_foreshortening_pic_list -->
    		<li style="font-size: 12px"><a href="#"><img src="/img/catalog/foreshortening/small/{F_SRC}" width="100" height="100" alt="{F_ALT}" title="{F_TITLE}" /></a>
    		<br />
	  			<input name="dell_foreshortening_pic[{F_ID}]" type="checkbox" style="width: 5px;" onclick="if (this.checked) {this.checked = confirm('Вы уверены что хотите удалить изображение?'); }"> Удалить
    		</li>
    		<!-- EDP: gallery_foreshortening_pic_list -->
    	</ul>
    </div>
    </div>
    <table class="adm-gallery">
</tr>
<tr>

    <td colspan='2'>
   Добавить картинки
    </td>
</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 1:</td><td> <input type="file" name="gallery_foreshortening_pic1"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_foreshortening_text1" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_foreshortening_alt1"  ></td></tr>

    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 2:</td><td> <input type="file" name="gallery_foreshortening_pic2"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_foreshortening_text2"  ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_foreshortening_alt2" ></td></tr>

    			</table>
    		</td>
		</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 3:</td><td> <input type="file" name="gallery_foreshortening_pic3"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_foreshortening_text3" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_foreshortening_alt3" ></td></tr>
    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 4:</td><td> <input type="file" name="gallery_foreshortening_pic4"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_foreshortening_text4" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_foreshortening_alt4" ></td></tr>
    			</table>
    		</td>

		</tr>
		<tr>
    		<td>
    			<table>
    			<tr><td>Картинка 5:</td><td> <input type="file" name="gallery_foreshortening_pic5"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_works_text5" ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_foreshortening_alt5" ></td></tr>
    			</table>
    		</td>

    		<td>
    			<table>
    			<tr><td>Картинка 6:</td><td> <input type="file" name="gallery_foreshortening_pic6"></td></tr>
    			<tr><td>Подпись (title):</td><td> <input type="text" name="gallery_foreshortening_text6"  ></td></tr>
    			<tr><td>Альтерн. текст  (alt):</td><td> <input type="text" name="gallery_foreshortening_alt6"  ></td></tr>
    			</table>
    		</td>

		</tr>
    </table>



    </td>
</tr>

<!-- EDP: gallery_foreshortening_pic -->

<!-- BDP: pic_alt_title_info -->
<tr>
    <td colspan="2"> <i>Если не заполнять поля title и alt для картинки, добавится текст с названия</i></td>
</tr>
<!-- EDP: pic_alt_title_info -->

<!-- BDP: pic_title -->
<tr>
    <td>Подпись (title):</td><td><input type="text" name="pic_title" class='admin-input-text' value="{ADM_PIC_TITLE}" /></td>
</tr>
<!-- EDP: pic_title -->

<!-- BDP: pic_alt -->
<tr>
    <td>Альтерн. текст  (alt):</td><td><input type="text" name="pic_alt" class='admin-input-text' value="{ADM_PIC_ALT}" /></td>
</tr>
<!-- EDP: pic_alt -->




<!-- BDP: featured_products -->

<tr>
     <td colspan=2><b>Рекомендованные товары: </b> <i>(Укажите артикулы товаров через запятую)</i>&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea rows=5 style="width: 100%;" name="featured_products">{ADM_FEATURED_PRODUCTS}</textarea></td>
</tr>
<!-- EDP: featured_products -->


<!-- BDP: used_complete -->
<tr>
   <td colspan="2"><a href="#" id="description"><img src="/img/admin_icons/insert-text.png">&nbsp;&nbsp;Описания / Комплект</a></td>
</tr>
<tr class="additional description">
     <td colspan=2><b>Аксессуары: </b> <i>(Укажите артикулы товаров через запятую)</i>&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr class="additional description">
     <td colspan="2"><textarea rows=5 style="width: 100%;" name="used_complete">{ADM_USED_COMPLETE}</textarea></td>
</tr>
<!-- EDP: used_complete -->


<!-- BDP: import -->
<tr>
    <td><a href="/admin/export">Создать резервную копию</a></td><td></td>
</tr>
<tr>
    <td>Файл импорта (.xls):</td><td><input type="file" name="file"></td>
</tr>
<!-- BDP: import_section_list_title -->
<tr>
   <td colspan='2'><b>Укажите разделы, которые НЕ НУЖНО обновлять</b></td>
</tr>
<!-- EDP: import_section_list_title -->

<!-- BDP: import_section_list -->
<tr>
   <td>{ADM_IMPORT_SECTION_NAME}</td>
   <td><input type="checkbox" name="import_section_name[]" value="{ADM_IMPORT_SECTION_ARTIKUL}" /></td>
</tr>

<!-- EDP: import_section_list -->
<tr>
   <td colspan="2">{FIELD_EXISTS}</td>
</tr>
<!-- EDP: import -->

<!-- BDP: upload_catalog_pics -->
<table Border=0 CellSpacing=0 CellPadding=0 Width="100%" Align="" vAlign="">
<tr>
    <td colspan="2" class="p10p">
    Файлы изображений не должны быть меньше 420px X 420px
<div id="fileQueue"></div>
<input type="file" name="uploadify" id="uploadify" />
<p><a href="javascript://" onClick="$('response').innerHTML='';jQuery('#uploadify').uploadifyUpload();">Начать загрузку</a> | <a href="javascript:jQuery('#uploadify').uploadifyClearQueue()">Отменить все загрузки</a></p>

   <div id="response"></div>

    </td>
</tr>
</table>
<!-- EDP: upload_catalog_pics -->

<!-- BDP: delete_spare_catalog_pics -->



<!-- BDP: availability -->
<tr>
    <td>Наличие на складе</td><td>
    <select name="availability">
		{AVAILABILITY_S}
	</select>

    </td>
</tr>
<!-- EDP: availability -->


<!-- BDP: status -->
<tr>
    <td>Статус</td><td>
    <select name="status">
		{STATUS_S}
	</select>

    </td>
</tr>
<!-- EDP: status -->


<!-- BDP: link_3d -->

<tr >
    <td>
        3D Объект
    </td>
    <td>
        Id: <input type="text" name="id_3d" value="{ADM_ID_3D}" style="width: 85px;" />
        Title: <input type="text" name="title_3d" value="{ADM_TITLE_3D}"  style="width: 175px;" />
    </td>
</tr>
<!-- EDP: link_3d -->


<!-- BDP: is_new -->
<tr>
    <td>Новинка</td><td>
    <select name="is_new">
		{IS_NEW_S}
	</select>

    </td>
</tr>
<!-- EDP: is_new -->

<!-- BDP: hit -->
<tr>
    <td>Хит</td><td>
    <select name="hit">
		{HIT_S}
	</select>

    </td>
</tr>
<!-- EDP: hit -->


<!-- BDP: action -->
<tr>
    <td>Акция</td><td>
    <select name="hit">
		{HIT_S}
	</select>

    </td>
</tr>
<!-- EDP: action -->

<!-- BDP: section_s -->
<tr>
    <td>Разделы</td><td>
    <select name="sections">
		{SECTIONS_S}
	</select>

    </td>
</tr>
<!-- EDP: section_s -->

<!-- BDP: cat_setting -->

<tr>
    <td>Использовать подразделы</td>
    <td>
    <select name="is_use_sub_section">
		{ADM_IS_USE_SUB_SECTION}
	</select>

    </td>
</tr>
<tr>
   <td> Использовать одинаковые названия товаров в одном {ADM_SECTION_TYPE}  </td>
   <td>
    <select name="is_use_unique_goods_names">
		{ADM_IS_UNIQUE_GOODS_NAMES_S}
	</select>

    </td>
</tr>
<tr class="con-fields{ADM_UNIQUE_STYLE_PREFIX}" id="tr-con-fields">
   <td>Прибавить к URL товара поля</td>
   <td>{ADM_UNIQUE_URL_FIELDS}</td>
</tr>


<!-- EDP: cat_setting -->

<!-- BDP: cat_global_setting -->

<tr>
    <td>Отображать товары без картинок</td><td>
    <select name="is_show_empty_pic">
		{ADM_IS_SHOW_EMPTY_PIC_S}
	</select>

    </td>
</tr>

<tr>
    <td>Отображать товары без цены или с нулевой ценой</td><td>
    <select name="is_show_empty_price">
		{ADM_IS_SHOW_EMPTY_PRICE_S}
	</select>

    </td>
</tr>

<tr>
    <td>Отображать хиты</td><td>
    <select name="is_show_hits">
		{ADM_IS_SHOW_EMPTY_HITS_S}
	</select>
    </td>
</tr>
<tr class="con-fields-is-show-hits{ADM_IS_SHOW_HITS_STYLE_PREFIX}"  id="con-fields-is-show-hits">
    <td>Количество хитов на главной</td><td>
     	<input type="text" name="hits_index_length" value ="{HITS_INDEX_LENGTH}" />
    </td>
</tr>

<tr>
    <td>Отображать новинки</td><td>
    <select name="is_show_new">
		{ADM_IS_SHOW_EMPTY_NEW_S}
	</select>
    </td>
</tr>
<tr class="con-fields-is-show-new{ADM_IS_SHOW_NEW_STYLE_PREFIX}" id="con-fields-is-show-new">
    <td>Количество новинок на главной</td><td>
     	<input type="text" name="new_index_length" value ="{NEW_INDEX_LENGTH}" />
    </td>
</tr>

<tr>
    <td>Отображать акции</td><td>
    <select name="is_show_actions">
		{ADM_IS_SHOW_EMPTY_ACTIONS_S}
	</select>
    </td>
</tr>
<tr class="con-fields-is-show-actions{ADM_IS_SHOW_ACTIONS_STYLE_PREFIX}" id="con-fields-is-show-actions">
    <td>Количество акционных товаров на главной</td><td>
     	<input type="text" name="action_index_length" value ="{ACTION_INDEX_LENGTH}" />
    </td>
</tr>


<!-- EDP: cat_global_setting -->


<!-- BDP: export -->
<tr>

    <td> Укажите имя файла экспорта </td>
    <td>
        <input type="text" name="export_name" value="{ADM_EXPORT_FILE_NAME}" />
    </td>
</tr>

<tr>

    <td> Выберите формат файла </td>
    <td>
        <select name="export_format">
            <option value="xls">MS Office 2003-2005</option>
            <option value="xlsx">MS Office 2007</option>
            <option value="ods">Open Office Calc</option>

        </select>
    </td>
</tr>
<!-- EDP: export -->

<!-- BDP: section_fields_copy -->

{ADM_SECTION_FIELDS_IS_PARAMS}


<tr >
   <td colspan="2">
      <div class="section-fields-menu">
         <a href="/admin/changecatsection/{ADM_SECTION_ID}" class="im-href"><img src="/img/admin_icons/stock_insert-fields-subject.png"   />&nbsp; Редактировать поле</a>
      </div>
       <div class="section-fields-menu">
          {ADM_SECTION_FIELDS_LINKS1}

      </div>
      <div class="section-fields-menu">
         {ADM_SECTION_FIELDS_LINKS2}

      </div>
   </td>
</tr>
<tr>
    <td>Группа</td><td> <select name="group" id="section-group"  style="{ADM_SECTION_FIELD_GROUP_STYLE}" >{ADM_SECTION_FIELD_GROUP}</select>
       <span id="new-section-group" style="{ADM_NEW_GROUP_STYLE}">  <input type="text" name="new_group" class="admin-input-text" value="{ADM_NEW_GROUP}" /><br /><a href="#">Вернуться к списку</a></span> </span> </td>
</tr>
<tr>
    <td>Подгруппа</td><td> <select name="sub_group"  id="section-sub-group"  style="{ADM_SECTION_FIELD_SUB_GROUP_STYLE}">{ADM_SECTION_FIELD_SUB_GROUP}</select>
     <span id="new-section-sub-group" style="{ADM_NEW_SUB_GROUP_STYLE}"> <input type="text" class="admin-input-text"  name="new_sub_group" value="{ADM_NEW_SUB_GROUP}"  />
     <br /><a href="#">Вернуться к списку</a>
     </span>
    </td>
</tr>
<tr>
   <td>Шаблон<br />
      <select id="section-field-templates">
         <option value="#">Укажите шаблон</option>
      </select>
   </td>
   <td>
      Поля

   </td>
</tr>
<tr>
   <td>
      <div style="float:left">
         <select id="section-field-templates-list" size="15">

         </select>
     </div>
      <div>
        &nbsp; <input type="button" value="  >  " class="arrow-button" id="send-one" /> <br /><br />
        &nbsp; <input type="button" value=" >>" class="arrow-button"  id="send-all" /> <br /><br />
        &nbsp; <input type="button" value=" <  " class="arrow-button"  id="back-one" /> <br /><br />
        &nbsp; <input type="button" value=" << " class="arrow-button"  id="back-all" /> <br /><br />
      </div>
   </td>
   <td valign="top">

       <select id="section-field-templates-selected-field" name="section_field_templates_selected_field"  size="15">

      </select><br />
      <div id="div-section-field-templates-selected-type">
      Тип <br />
      <select id="section-field-templates-selected-type">
         {ADM_SECTION_FIELD_TYPE}
      </select>
      <div>
         <div id="hidden-cont"></div>
   </td>
</tr>

<!-- EDP: section_fields_copy -->




<!-- BDP: section_fields_form -->
<tr >
   <td colspan="2">
      <div class="section-fields-menu">
         <img src="/img/admin_icons/stock_insert-fields-subject.png" />&nbsp; Редактировать поле
      </div>
       <div class="section-fields-menu">
         <a href="/admin/copysectionoptions/{ADM_SECTION_ID}" class="im-href"><img src="/img/admin_icons/stock_insert-fields.png" />&nbsp; Копировать поля из другого раздела</a>
      </div>
      <div class="section-fields-menu">
         <a href="/admin/copytemplatesoptions/{ADM_SECTION_ID}" class="im-href"><img src="/img/admin_icons/template.png" />&nbsp;Копировать поля из шаблонов</a>
      </div>
   </td>
</tr>
<tr class="section-field-form">
    <td>Название:</td><td><input type="text" size="50" name="sectionfields_name" value="{ADM_SECTION_FIELD_NAME}" class='admin-input-text'  id='admin-input-name'></td>
</tr>
<tr class="section-field-form">
    <td>Группа</td><td> <select name="group" id="section-group"  style="{ADM_SECTION_FIELD_GROUP_STYLE}" >{ADM_SECTION_FIELD_GROUP}</select>
       <span id="section-fields-edit-group">&nbsp&nbsp
           <a title='Редактировать' href='/admin/catalogoptions/editgroup/SELECTED_ID/{ADM_SECTION_ID}'><img width='12' height='12' alt='Редактировать' src='/img/admin_icons/edit.png'></a>
           &nbsp&nbsp
           <a onclick="return confirm('Вы уверены что хотите удалить?'); return false;" title="Удалить" href="href='/admin/catalogoptions/dellgroup/SELECTED_ID/{ADM_SECTION_ID}'"><img width="12" height="12" alt="Удалить" src="/img/admin_icons/delete.png"></a>

       </span>
       <span id="new-section-group" style="{ADM_NEW_GROUP_STYLE}">  <input type="text" name="new_group" class="admin-input-text" value="{ADM_NEW_GROUP}" /><br /><a href="#">Вернуться к списку</a></span> </span> </td>
</tr>
<tr class="section-field-form">
    <td>Подгруппа</td><td> <select name="sub_group"  id="section-sub-group"  style="{ADM_SECTION_FIELD_SUB_GROUP_STYLE}">{ADM_SECTION_FIELD_SUB_GROUP}</select>
       <span id="section-fields-edit-sub-group">&nbsp&nbsp
           <a title='Редактировать' href='/admin/catalogoptions/editgsubroup/SELECTED_ID/{ADM_SECTION_ID}'><img width='12' height='12' alt='Редактировать' src='/img/admin_icons/edit.png'></a>
           &nbsp&nbsp
           <a onclick="return confirm('Вы уверены что хотите удалить?'); return false;" title="Удалить" href="href='/admin/catalogoptions/dellsubgroup/SELECTED_ID/{ADM_SECTION_ID}'"><img width="12" height="12" alt="Удалить" src="/img/admin_icons/delete.png"></a>

       </span>
     <span id="new-section-sub-group" style="{ADM_NEW_SUB_GROUP_STYLE}"> <input type="text" class="admin-input-text"  name="new_sub_group" value="{ADM_NEW_SUB_GROUP}"  />
     <br /><a href="#">Вернуться к списку</a>
     </span>
    </td>
</tr>
<tr class="section-field-form">
    <td>Тип</td><td> <select name="type">{ADM_SECTION_FIELD_TYPE}</select></td>
</tr>

<tr>
    <td>Порядок вывода</td><td> <select name="position" >{ADM_SECTION_FIELD_POSITION}</select></td>
</tr>

<tr class="section-field-form">
    <td> <a href="/catalog/{ADM_SECTION_HREF}">Вернуться в раздел </a></td><td> <input type="hidden" name="sectionfields_catalog_href" value="{ADM_CATALOG_HREF}" /></td>
</tr>
<tr class="section-field-form">
    <td colspan="2">Результат</td>
</tr>
<tr class="section-field-form">
    <td colspan="2">{ADM_SECTION_FIELD_RESULT}</td>
</tr>
<!-- EDP: section_fields_form -->


<!-- BDP: sectionfields_section_name -->
<tr>
    <td>Раздел каталога</td><td> <select name="section_name" >{ADM_SECTION_FIELD_SECTION_NAME}</select></td>
</tr>
<!-- EDP: sectionfields_section_name -->





<!-- BDP: sectionfields_form -->
<tr>
   <td colspan="2"><a href="#" id="features" class='adm-href'><img src="/img/admin_icons/construct.png">&nbsp;&nbsp;Характеристики</a></td>
</tr>
<tr>
    <td colspan="2">{ADM_SECTION_FIELD_FORM}</td>
</tr>

<!-- EDP: sectionfields_form -->

<!-- BDP: end -->
<tr>
     <td colspan=2>&nbsp; <input type="hidden" name="HTTP_REFERER" value="{REFERER}"></td>
</tr>
<tr>
     <td colspan=2><br><br><center><input class="button" type="submit" value="Сохранить" id="submit-button" /> </center></td>
</tr>
</table>
</form>
<!-- EDP: end -->

<!-- BDP: end_ajax -->
<tr>
     <td colspan=2>&nbsp; <input type="hidden" name="HTTP_REFERER" value="{REFERER}"></td>
</tr>
<tr>
     <td colspan=2><br><br><center><input class="button" id="ajax-submit-button" type="submit"></center></td>
</tr>
</table>
</form>
<div id="jq-form-result"></div>
<!-- EDP: end_ajax -->
