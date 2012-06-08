
<!-- BDP: gallery_manager_admin -->
<link  rel="stylesheet" type="text/css" href="/css/uploadify.css" />
<script type="text/javascript" src="/js/uploadify/swfobject.js"></script>
<script type="text/javascript" src="/js/uploadify/jquery.uploadify.v2.1.0.min.js"></script>

<style>
    .right_column div.gallery-images-list-body {height: 520px; overflow: auto;}
    .right_column ul.gallery-images-list  {list-style: none;}
    .right_column ul.gallery-images-list li {list-style: none; background: none;  float: left;}
    .right_column ul.gallery-images-list li input {width: 180px;}
    .right_column ul.gallery-images-list li img {border: 1px solid;}
    .right_column ul.gallery-images-list li table {width: 100%; }
    .right_column ul.gallery-images-list li table td p.button-panel {text-align: justify;}

</style>       



<tr><td colspan="2">
        <script type="text/javascript">
            
            var initFileUploader = function(params) {
            jQuery(function(){   

        if (params.upload === undefined) {
            params.upload = {
                'uploader'  : '/js/uploadify/uploadify.swf',
                'script'    : '/js/uploadify/uploadify.php',
                'cancelImg' : '/js/uploadify/cancel.png',
                'folder'    : '/upload',
                // 'buttonText':'Ok',
                'auto'      : true
            };
        } else {
            
        }
    
        
        jQuery('#'+params.id).uploadify(params.upload);       
    });

}
            
                    jQuery(function(){
                        initFileUploader({
                            'id':'pic_{ADM_FIELD_NAME}',
                            'upload':{
                                'uploader'  : '/js/uploadify/uploadify.swf',                                                           
                                'script'    : '{ADM_FIELD_SCRIPT_URL}',
                                'cancelImg' : '/js/uploadify/cancel.png',   
                                //'buttonImg' : '/img/button-test.jpg',
                                //'width' : '204',
                                'buttonText':'Ok',
                                'auto'      : true,
                                'method': 'post',
                                'fileDataName':'{ADM_FIELD_NAME}',
                                'multi':true,
                                'scriptData':{
                                    'file_data_name':'{ADM_FIELD_NAME}'   ,
                                     'file_uploader_sid':'{SESSION_NAME}'
                                },
                                'onComplete': function(event,queueID,fileObj,response,data) {  
                                    
                                     jQuery('.button').hide();
                                       
                                       path = response;
                                       fileName = path.substr(path.lastIndexOf('/')+1, path.length);
                                            
                                       
                                    
                                    
                                    
                                     
                                    if (path && fileName) {
                                        jQuery('.gallery-images-list-body').show();
                                        jQuery('ul.gallery-images-list').prepend('<li><table><tr><td colspan="2" class="ui-state-default">Файл: '+fileName+'</td></tr><tr><td><img src="'+path+'" /></td></tr></table></li>');
                                        
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
                       
                    });
        </script>

    </td></tr>
<tr>
    <td colspan="2">
        <b>        {ADM_FIELD_TITLE}</b>
    </td>
</tr>

<tr>

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
<tr>
    <td colspan="2">
        <div class="gallery-images-list-body">

            <ul class="gallery-images-list">
               {ADM_FIELD_LOADED}
            </ul>
        </div>
    </td>

</tr>

<!-- EDP: gallery_manager_admin -->
