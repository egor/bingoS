<!-- BDP: adm_js -->
<link  rel="stylesheet" type="text/css" href="/css/uploadify.css" />
<script type="text/javascript" src="/js/uploadify/swfobject.js"></script>
<script type="text/javascript" src="/js/uploadify/jquery.uploadify.v2.1.0.min.js"></script>

<script type="text/javascript" src="/js/jq-form-manager.js"></script>

<!-- EDP: adm_js -->

<!-- BDP: adm_mce -->
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
<!-- EDP: adm_mce -->

<!-- BDP: adm_start -->
<style>
    table.admin-table tr td input, table.admin-table tr td select {width: 320px; background-color: #fff;}
    .dell-button-hide {display: none;}
</style>
<form enctype="multipart/form-data" method="post" action="" id="from1">
    <table Border=0 CellSpacing=0 CellPadding=0 Width="100%" Align="" vAlign="" class="admin-table">
        <!-- EDP: adm_start -->

        <!-- BDP: adm_slider -->
        <tr>
            <th colspan="2">{ADM_SLIDER_TITLE}</th>      
        </tr>


        <!-- EDP: adm_slider -->

        <!-- BDP: adm_varchar -->   
        <tr>
            <td>{ADM_FIELD_TITLE}</td>
            <td><input type="text" name="form_manager[{ADM_FIELD_NAME}]" value="{ADM_FIELD_VALUE}" /></td>
        </tr>
        <!-- EDP: adm_varchar -->

        <!-- BDP: adm_href -->   
        <tr>
            <td>{ADM_FIELD_TITLE}</td>
            <td><input type="text" name="form_manager[{ADM_FIELD_NAME}]" value="{ADM_FIELD_VALUE}" /></td>
        </tr>
        <!-- EDP: adm_href -->

        <!-- BDP: adm_mce -->   
        <tr>
            <td colspan=2><b>{ADM_FIELD_TITLE}</b> &nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="form_manager[{ADM_FIELD_NAME}]">{ADM_FIELD_VALUE}</textarea></td>
        </tr>
        <!-- EDP: adm_mce -->

        <!-- BDP: adm_yes_no -->
        <tr>
            <td>{ADM_FIELD_TITLE}</td>
            <td>
                <select name="form_manager[{ADM_FIELD_NAME}]">
                    {ADM_YES_NO_TYPE_OPTION}
                </select>    
            </td>
        </tr>
        <!-- EDP: adm_yes_no -->


        <!-- BDP: adm_image -->
        <tr >
            <td colspan="2">
               <a class="adm-href" id="photo" href="#"><img src="/img/admin_icons/construct.png">{ADM_FIELD_TITLE}</a>
                
            </td>
        </tr>

        <tr class="photo">
            
            <td>
                <!-- BDP: adm_image_uploader -->
                

                <script type="text/javascript">                    
                    jQuery(function(){
                
                        initFileUploader({
                            'id':'pic_{ADM_FIELD_NAME}',
                            'upload':{
                                'uploader'  : '/js/uploadify/uploadify.swf',                            
                               // 'script' :'/js/uploadify/uploadify.php',
                                'script'    : '/formmanageractions/uploadimage/{ADM_FIELD_ID}',
                                'cancelImg' : '/js/uploadify/cancel.png',
                                //'folder'    : '/img/uploads',
                                'buttonText':'Ok',
                                'auto'      : true,
                                'method': 'post',
                                'fileDataName':'{ADM_FIELD_NAME}',
                                'multy':false,
                                'scriptData':{
                                    'file_data_name':'{ADM_FIELD_NAME}',
                                    'file_uploader_sid':jQuery.cookie('{SESSION_NAME}')
                                },
                                
                              
                                
                                'onComplete': function(event,queueID,fileObj,response,data) {
                                 
                                    var rspObj = {
                                        'response':response,
                                        'fieldId':'{ADM_FIELD_NAME}'
                                    };
                                  setResponse(rspObj);                                    
                                },
                                
                                'onAllComplete': function(event,data) {
                                    
                                   //s alert(objDump(data));
                                 //  alert(data);
                                },
                                'onError'  : function (event,ID,fileObj,errorObj) {
                                   
                                 //   alert(objDump(ID));
                                    alert(errorObj.type + ' Ошибка: ' + fileObj.info);

                                }   
                            }
                        });
                        
                        
                        jQuery('#{ADM_FIELD_NAME}-dell-button a').click(function(){
                            if (!confirm('Удалить изображение ?')) return false;
                            var path = jQuery(this).attr('href');
                            jQuery.post(path, function(data) {    
                                
                                jQuery('#img-{ADM_FIELD_NAME}').attr('src', data);
                                jQuery('#{ADM_FIELD_NAME}-dell-button').hide();
                            });
                            return false;                            
                        });
                        
                        
                    });
                </script>
                <!-- EDP: adm_image_uploader -->

                <img src="{ADM_FIELD_IMG_SRC}"  id="img-{ADM_FIELD_NAME}" />
               
                <div class="{ADM_IMAGE_DELL_BUTTON_CSS}" id="{ADM_FIELD_NAME}-dell-button">
                    <a href="/formmanageractions/deleteimage/{ADM_FIELD_ID}">
                        <img height="12" width="12" alt="Удалить" src="/img/admin_icons/delete.png" ></a>
                    <a href="/formmanageractions/deleteimage/{ADM_FIELD_ID}">Удалить картинку</a>

                </div>
               
            </td>
            <td>
                <p>Выберите файл</p>
                <input type="file" name="form_manager[pic_{ADM_FIELD_NAME}]" id="pic_{ADM_FIELD_NAME}" />

            </td>      
        </tr>

        <!-- EDP: adm_image -->

        
           <!-- BDP: adm_gallery -->
       

        <!-- EDP: adm_gallery -->

        
 {ADM_OTHER_FIELD_FORM}       

<!-- BDP: adm_end -->


<tr>
     <td colspan=2>&nbsp;dddddd <input type="hidden" name="form_manager[HTTP_REFERER]" value="{REFERER}"></td>
</tr>
<tr>
     <td colspan=2><br><br><center><input class="button" value="Сохранить" type="submit" id="submit-button" /> </center></td>
</tr>
</table>
</form>
<!-- EDP: adm_end -->
