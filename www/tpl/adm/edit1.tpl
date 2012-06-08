
<!-- BDP: mce -->
<script language="javascript" type="text/javascript" src="/js/tiny_mce/filemanager/jscripts/mcfilemanager.js"></script>
<script language="javascript" type="text/javascript" src="/js/tiny_mce/imagemanager/jscripts/mcimagemanager.js"></script>
<script type="text/javascript" src="/js/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
   
tinyMCE_GZ.init({
    plugins : "style,layer,table,save,advhr,advimage,advlink,preview,zoom,media,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking",
    themes : 'advanced',
    languages : 'ru',
    disk_cache : true,
    debug : false
});
</script>
<script language="javascript" type="text/javascript">
    tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "mceEditor",
        language : "ru",
        mode : "textareas",
        theme : "advanced",
        plugins : "layer,table,save,advhr,advimage,advlink,preview,zoom,media,contextmenu,paste,directionality,noneditable,visualchars, nonbreaking",
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
        file_browser_callback : "mcFileManager.filebrowserCallBack",
        theme_advanced_resize_horizontal : true,
        theme_advanced_resizing : true,
        apply_source_formatting : false,
        relative_urls : false,
        add_unload_trigger : true,
        strict_loading_mode : true
    });
</script>
<!-- EDP: mce -->

<!-- BDP: start -->

<form enctype="multipart/form-data" method="post" action="" id="from1">
<table Border=1 CellSpacing=0 CellPadding=0 Width="100%" Align="" vAlign="" class="admin-table">
<!-- EDP: start -->
   
   <!-- BDP: adm_slider -->
   <tr>
      <th colspan="2">{ADM_SLIDER_TITLE}</th>      
   </tr>
   
   
   <!-- EDP: adm_slider -->
   
   <!-- BDP: adm_varchar -->
   
   <tr>
      <td>{ADM_FIELD_TITLE}</td>
      <td><input type="text" name="{ADM_FIELD_NAME}" value="{ADM_FIELD_VALUE}" /></td>
   </tr>
   <!-- EDP: adm_varchar -->
   
   
<!-- BDP: end -->
</table>
</form>
<!-- EDP: end -->