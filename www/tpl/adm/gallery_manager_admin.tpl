<!-- BDP: gallery_manager_admin_js -->
<script type="text/javascript" src="/js/jq-gallery-manager-admin.js" ></script>
<style>
     div.gallery-images-list-body {height: 520px; overflow: auto;}
     .type_elem ul.gallery-images-list  {list-style: none;}
     .type_elem ul.gallery-images-list li {list-style: none; background: none;  float: left; padding: 3px;}
     .type_elem ul.gallery-images-list li input {width: 140px; padding: 2px;}
     .type_elem ul.gallery-images-list li img {border: 1px solid;}
     .type_elem ul.gallery-images-list li table {width: 100%; }
     .type_elem ul.gallery-images-list li table td {padding: 3px 5px;}
     .type_elem ul.gallery-images-list li table td p {padding: 2px;}
     .type_elem ul.gallery-images-list li table td p.button-panel {text-align: justify;}

</style>       
<!-- EDP: gallery_manager_admin_js -->

<!-- BDP: gallery_manager_admin -->

<tr  class="photo"><td colspan="2">       
        <script type="text/javascript">
                    jQuery(function(){
                        initFileUploader({
                            'id':'pic_{ADM_FIELD_NAME}',
                            'upload':{
                                'uploader'  : '/js/uploadify/uploadify.swf',                            
                               // 'script' :'/js/uploadify/uploadify.php',
                                'script'    : '/formmanagergalleryactions/uploadimage/{ADM_FIELD_ID}',
                                'cancelImg' : '/js/uploadify/cancel.png',
                                //'folder'    : '/img/uploads',
                                'buttonText':'Ok',
                                'auto'      : true,
                                'method': 'post',
                                'fileDataName':'{ADM_FIELD_NAME}',
                                'multi':true,
                                'scriptData':{
                                    'gallery_type' :'{ADM_GALLERY_TYPE}',
                                    'file_data_name':'{ADM_FIELD_NAME}' , 
                                    'file_uploader_sid':'{ADM_SID}'
                                     
                                },
                                'onComplete': function(event,queueID,fileObj,response,data) {  
                                
                                    var tmpArr = response.split(';');
                                    var path = false;
                                    var id = false;
                                    var fileName = false;
                                    if (tmpArr[0] !== undefined) {
                                        var tmpArr1 = new String(tmpArr[0]).split(':');
                                        if (tmpArr1[1] !== undefined) {
                                            path = tmpArr1[1];
                                            fileName = path.substr(path.lastIndexOf('/')+1, path.length);
                                            
                                        }
                                    }
                                    
                                    if (tmpArr[1] !== undefined) {
                                        var tmpArr1 = new String(tmpArr[1]).split(':');
                                        if (tmpArr1[1] !== undefined) {
                                            id = tmpArr1[1];
                                        }
                                    }
                                    
                                    if (path && id && fileName) {
                                        jQuery(' ul.gallery-images-list').prepend('<li id="li-'+id+'"><table><tr><td colspan="2" class="ui-state-default">Файл: '+fileName+'</td></tr><tr><td><img src="'+path+'" /></td><td><p>Всплывающая подсказка </p><p><input type="text" id="input-title-'+id+'" value="{ADM_DEFAUULT_TITLE_PICK}" /></p><p>Альтернативный текст </p><p><input type="text" id="input-alt-'+id+'" value="{ADM_DEFAUULT_ALT_PICK}" /></p><p class="button-panel"><a href="/formmanagergalleryactions/save/'+id+'" id="save-'+id+'" onclick="GMSaveImageTitleAlt(this); return false;">Сохранить</a> <a href="/formmanagergalleryactions/deleteimage/'+id+'" id="delete-'+id+'" onclick="GMDeleteImage(this); return false;">Удалить</a></p></td></tr></table></li>');
                                        jQuery('.gallery-images-list-body').show();
                                        
                                    }
                                    
                                },
                                
                                'onAllComplete': function(event,data) {
                                   // alert(objDump(data));
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
                        
                    
                    
                    jQuery(".gallery-images-list" ).
        sortable(
        {
            'helper':'ui-state-default',
             'update': function(event, ui) { 
                 jQuery.post('/formmanagergalleryactions/sort/{ADM_FIELD_ID}', {'vals': jQuery(this).sortable('toArray').toString()}, function(data){
                 
                     });
                 }
                 
             
        });
                    
                    });
        </script>

    </td></tr>



<tr class="photo">
    <td colspan="2">
        <b>        {ADM_FIELD_TITLE}</b>
    </td>
</tr>

<tr class="photo">

    <td>
        <img src="/img/admin_icons/gallery.png" width="128" height="128" />
        <p>Загрузить сразу несколько файлов</p>
        <input type="file" name="pic_{ADM_FIELD_NAME}" id="pic_{ADM_FIELD_NAME}" />

    </td>
    <td>
        <p>
            Не получается загрузить? Воспользуйтесь обычной загрузкой.
        <p><input type="file" name="pic_{ADM_FIELD_NAME}[]" /> </p>
        <p><input type="file" name="pic_{ADM_FIELD_NAME}[]" /> </p>
        <p><input type="file" name="pic_{ADM_FIELD_NAME}[]" /> </p>
        <p><input type="file" name="pic_{ADM_FIELD_NAME}[]" /> </p>
        <p><input type="file" name="pic_{ADM_FIELD_NAME}[]" /> </p>
    </p>
</td>              
</tr>
<tr class="photo">
    <td colspan="2">
        <div class="gallery-images-list-body{ADM_GALLERY_IMAGE_ID}">

            <ul class="gallery-images-list">
                <!-- BDP: gallery_images_list_admin -->
                <li id="li-{ADM_GALLERY_IMAGE_ID}">
                    <table>
                        <tr>
                            <td colspan="2" class="ui-state-default">Файл: {ADM_GALLERY_IMG_NAME}</td>
                        </tr>
                        <tr>
                            <td><img src="{ADM_GALLERY_IMG_SRC}" /></td>
                            <td>                                    
                                <p>Всплывающая подсказка </p>
                                <p><input type="text" id="input-title-{ADM_GALLERY_IMAGE_ID}" value="{ADM_GALLERY_IMG_TITLE}" /></p>
                                <p>Альтернативный текст </p>
                                <p><input type="text" id="input-alt-{ADM_GALLERY_IMAGE_ID}" value="{ADM_GALLERY_IMG_ALT}" /></p>
                                <p class="button-panel"><a href="/formmanagergalleryactions/save/{ADM_GALLERY_IMAGE_ID}" id="save-{ADM_GALLERY_IMAGE_ID}" onclick="GMSaveImageTitleAlt(this); return false;">Сохранить</a> <a href="/formmanagergalleryactions/deleteimage/{ADM_GALLERY_IMAGE_ID}" id="delete-{ADM_GALLERY_IMAGE_ID}" onclick="GMDeleteImage(this); return false;">Удалить</a> </p>
                            </td>
                        </tr>
                    </table>
                </li>

                <!-- EDP: gallery_images_list_admin -->
            </ul>
        </div>
    </td>

</tr>

<!-- EDP: gallery_manager_admin -->
