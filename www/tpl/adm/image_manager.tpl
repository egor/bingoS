<!-- BDP: image_manager -->
<!-- BDP: im_form -->
<script type="text/javascript">

   jQuery(function(){
      var idPrefix = '{ADM_IM_NAME}';
      var Filedata = idPrefix + 'im_file';          
      var isMulti = {ADM_IM_MULTI}; 
      var multiType = (isMulti ? 'multi' : 'simple');
      jQuery('#'+idPrefix+'im-dell-img').css('display', 'none');
      var multiCounter = 0;
      var multiItems = '';
          
    
       
       
      var imDellInit = function(linkObj, isDis) { 
   
         if (linkObj == null) {            
            linkObj = jQuery('.'+idPrefix+'im-dell-img-button');
            //       alert(linkObj.attr('id'));
         }
    
         
         var isLeftImage = (linkObj.parent('div').parent('td').parent('tr').attr('class') != undefined);
            
         if (!confirm('Удалить фото ?')) return false;
            
         var id = linkObj.attr('id');
         
            
         jQuery.ajax({
            type: 'POST',
            url: '/library/ImageManager.php',
            data: {'sid':'{ADM_IM_SID}', 
               'smallPath':'{ADM_IM_SMALL_PATH}',
               'bigPath':'{ADM_IM_BIG_PATH}',
               'realPath':'{ADM_IM_REAL_PATH}',
               'Path':'{ADM_IM_PATH}',
               'action':'deleteImage',
               'tName':'{ADM_IM_DB_TABLE_NAME}',
               'fPic':'{ADM_IM_DB_PIC_FIELD_NAME}',                            
               'parentArticul': '{ADM_IM_PARENT_ARTICUL}',                           
               'PAFieldName': '{ADM_IM_PARENT_ARTICUL_FIELD_NAME}',
               'galleryType': '{ADM_IM_PARENT_GALLERY_TYPE}',
               'name' : '{ADM_IM_NAME}',
               'isMulti' : multiType,
               'id':id


            },
            success: function (response) {
              
               var mess = response.split('#~#');                

               if (mess[0] == 'err') {
                  alert('Ошибка: '+mess[1]);
               } else if (mess[0] == 'path') { 
                  
                  if (!isMulti) {
                      
                     jQuery('#'+idPrefix+'im_img').attr('src', mess[1].replace('//', '/'));
                  } else {
                  
                     if (isDis) return id;
                     if (isLeftImage) {
                       
                        if (jQuery('#'+idPrefix+'td-multi div img:first').attr('id') == undefined) { 
                            
                           jQuery('#'+idPrefix+'im_img').attr('src', mess[1]);
                           jQuery('#'+idPrefix+'im-dell-img').css('display', 'none');
                        } else {
                       
                           if ((obj = jQuery('#'+idPrefix+'td-multi div img:first')) != undefined) {
                              jQuery('#'+idPrefix+'im_img').attr('src', obj.attr('src')); 
                              obj.parent('div').remove();
                              obj.remove();
                           }
                        
                        }
                        
                     } else {
                        jQuery('#'+idPrefix+'div-multi-'+id).remove();
                     }
                     
                     
                     /*                     
                     if ((obj = jQuery('#'+idPrefix+'td-multi div img:first')) != undefined) {
                        if (isLeftImage) {
                           jQuery('#'+idPrefix+'im_img').attr('src', obj.attr('src')); 
                        }

                        
                                                   
                     } else {
                        jQuery('#'+idPrefix+'im_img').attr('src', '/img/nophoto_s.jpg');
                        jQuery('#'+idPrefix+'im-dell-img').css('display', 'none');

                     }*/
                  }

               } else {
                  jQuery('#'+idPrefix+'div-upload-button').html(response); 
                  jQuery('#'+idPrefix+'im-dell-img').css('display', 'none');
               }



            }


         });
         return id;
      }
          
      
      
      jQuery('.'+idPrefix+'im-dell-img-button').click(function(){
         imDellInit(jQuery(this), false);
         return false;
      });
      
      // };
      
      
    
          
      jQuery('#'+idPrefix+'im_file').uploadify({
         'uploader'  : '/modules/uploadify/uploadify.swf',
         'script'    : '/library/ImageManager.php',
         'cancelImg' : '/modules/uploadify/cancel.png',
         'removeCompleted':true,
         'action': 'upload',
         'scriptData': {'sid':'{ADM_IM_SID}', 
            'smallWidth':'{ADM_IM_SMALL_WIDTH}',
            'smallHeight':'{ADM_IM_SMALL_HEIGHT}',
            'smallPath':'{ADM_IM_SMALL_PATH}',
            'bigWidth':'{ADM_IM_BIG_WIDTH}',
            'bigHeight':'{ADM_IM_BIG_HEIGHT}',
            'bigPath':'{ADM_IM_BIG_PATH}',
            'realWidth':'{ADM_IM_REAL_WIDTH}',
            'realHeight':'{ADM_IM_REAL_HEIGHT}',
            'realPath':'{ADM_IM_REAL_PATH}',
            'tName':'{ADM_IM_DB_TABLE_NAME}',
            'fPic':'{ADM_IM_DB_PIC_FIELD_NAME}',
            'parentArticul': '{ADM_IM_PARENT_ARTICUL}',                           
            'PAFieldName': '{ADM_IM_PARENT_ARTICUL_FIELD_NAME}',
            'galleryType': '{ADM_IM_PARENT_GALLERY_TYPE}',
            'id':'{ADM_IM_ID}',     
            'action':'upload',
            'name' : '{ADM_IM_NAME}',
            'Path':'{ADM_IM_PATH}',
            'isMulti' : multiType
                        
                           
                           
         },
         'fileDataName': Filedata,
         'buttonText':'Ok',   
         'auto' : true,
         'multi': isMulti,
         'removeCompleted': true,
         'onComplete'   : function(event,queueID,fileObj,response,data) {
            var mess = response.split('#~#');                
               
            if (mess[0] == 'err') {
               alert('Ошибка: '+mess[1]);
            } else if (mess[0] == 'path') {                   
               //  jQuery('#'+idPrefix+'im_img').attr('src', mess[1].replace('//', '/'));
               var id = (mess[2] != undefined ? mess[2] : '');
               var alt = (mess[3] != undefined ? mess[3] : '');
               var title = (mess[4] != undefined ? mess[4] : '');   
                 
               if (multiCounter == 0 && jQuery('#'+idPrefix+'im_img').attr('src') == '/img/nophoto_s.jpg') {
                  jQuery('#'+idPrefix+'im_img').attr('src', mess[1].replace('//', '/'));
                  jQuery('#'+idPrefix+'im-dell-img a').attr('id', id);
                 
               } else {
                  
                      
                  multiItems = "<div style='float: left; margin-left: 10px;'  class='div-multi' id='"+idPrefix+"div-multi-"+id+"'>";                       
                  multiItems += "<img src='"+mess[1]+"' alt='"+alt+"' title='"+title+"' id='"+id+"' />";                                               
                  multiItems += '<div class="im-div-dell-image" ><a href="#" id="'+id+'" onclick="imDellInit(jQuery(this)); return false;" class="'+idPrefix+'im-dell-img-button im-href"> <img src="/img/admin_icons/image_delete_16x16.gif" /> &nbsp; Удалить фото</a></div>';
                  multiItems += "</div>";
                  
                  jQuery('#'+idPrefix+'td-multi').append(multiItems);
                  //jQuery('#'+idPrefix+'td-multi').addClass(idPrefix+"div-multi"+id);
                  
          
                    
                  //alert('#'+idPrefix+'td-multi');
               }
               if (isMulti) {
                  multiCounter++;                 
               } 
                   
               jQuery('#'+idPrefix+'im-dell-img').show();
            } else {
               jQuery('#'+idPrefix+'div-upload-button').html(response); 
            }
                

               
         },
    
         'onAllComplete' : function() {
            multiItems = '';
             
                 if (isMulti) {                      
                    jQuery('.'+idPrefix+'im-dell-img-button').click( function(){
                        id = imDellInit(jQuery(this), true);
                        alert(id);
                        var linkObj = jQuery('.'+idPrefix+'im-dell-img-button');
                     
                        //var linkObj = jQuery(this);
                        var isLeftImage = false;// (linkObj.parent('div').parent('td').parent('tr').attr('class') != undefined);
                        if (isLeftImage) {
                       
                        if (jQuery('#'+idPrefix+'td-multi div img:first').attr('id') == undefined) { 
                            
                           jQuery('#'+idPrefix+'im_img').attr('src', '/img/nophoto_s.jpg');
                           jQuery('#'+idPrefix+'im-dell-img').css('display', 'none');
                        } else {
                       
                           if ((obj = jQuery('#'+idPrefix+'td-multi div img:first')) != undefined) {                              
                              jQuery('#'+idPrefix+'im_img').attr('src', obj.attr('src')); 
                              obj.parent('div').remove();
                              obj.remove();
                           }
                        
                        }
                        
                     } else {
                        alert('#'+idPrefix+'div-multi-'+id);
                        jQuery('#'+idPrefix+'div-multi-'+id).remove();
                     }
                     return false;
                     });
                     
                }
               
               
               
         },

         'onError'  : function (event,ID,fileObj,errorObj) {
            jQuery('#'+idPrefix+'div-upload-button').html(errorObj.type + ' Ошибка: ' + fileObj.info);

         }   

      });
     
     
    
     
     
      jQuery('#'+idPrefix+'simple-upload').click(function(){
      
      });
     
      if (jQuery('#'+idPrefix+'im_img').attr('src') != '/img/nophoto_s.jpg') {
         jQuery('#'+idPrefix+'im-dell-img').show();
      }
     
      var isSumpleUpload = false;
      jQuery('#'+idPrefix+'im_pic').val('').css('display', 'none');
      jQuery('#'+idPrefix+'simple-upload').click(function(){
        
         if (!isSumpleUpload) {        
            jQuery('#'+idPrefix+'im_file').hide();
            jQuery('#'+idPrefix+'im_fileUploader').hide();
            jQuery('#'+idPrefix+'im_pic').show();
            jQuery('#'+idPrefix+'p-simple-upload span').html('Воспользуйтесь');              
            jQuery('#'+idPrefix+'simple-upload span').text('Flash загрузкой');
            //jQuery('#simple-upload span').html('Flash');
         } else {
            //jQuery('#im_file').show();
            jQuery('#'+idPrefix+'im_fileUploader').show();
            jQuery('#'+idPrefix+'im_pic').val('').css('display', 'none');
            jQuery('#'+idPrefix+'p-simple-upload span').html('Не получается загрузить? Воспользуйтесь');           
            jQuery('#'+idPrefix+'simple-upload span').text('обычной загрузкой');
            //jQuery(this).text('обычной загрузкой');
         }
        
         isSumpleUpload = !isSumpleUpload;
        
         return false;
      });
     
      //imDellInit ();
     
   });
      
</script>

<tr>
   <td colspan="2"><a href="#" id="photo" class='adm-href'><img src="/img/admin_icons/img.png">&nbsp;&nbsp;Изображение товара / Галерея</a></td>
</tr>

<tr class="image-manager-form photo">
   <td valign="top"><img src="{ADM_IMG_FILE_NAME}" width="{ADM_IM_WIDTH}" height="{ADM_IM_HEIGHT}" id="{ADM_IM_NAME}im_img" /> 
      <div id="{ADM_IM_NAME}im-dell-img"><a href="#" id="{ADM_IM_ID}" class="{ADM_IM_NAME}im-dell-img-button im-href"> <img src="/img/admin_icons/image_delete_16x16.gif" /> &nbsp; Удалить фото</a></div>
   </td>
   <td>

      <font size="5">Виберите файл ...</font> &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
      <input type="file" id="{ADM_IM_NAME}im_file" name="{ADM_IM_NAME}im_file" />
      <input type="file" id="{ADM_IM_NAME}im_pic" name="{ADM_IM_NAME}im_pic" /> 
      <div id="{ADM_IM_NAME}div-upload-button"> </div>

      <br />
      <p id="{ADM_IM_NAME}p-simple-upload"><span>Не получается загрузить? Воспользуйтесь</span> <a href="#" id="{ADM_IM_NAME}simple-upload" class="im-href"> <span>обычной загрузкой</span> </a> </p>

   </td>
</tr>
<!-- BDP: im_alt_title -->
<tr class="photo" id="{ADM_IM_NAME}im-img-alt">

   <td>Альтернативный текст (тег Alt).</td>
   <td> 
      <div id="test"></div>
      <input type="text" class="admin-input-text" name="im_alt" value="{ADM_IM_ALT}"/></td>

</tr>


<tr class="photo" id="{ADM_IM_NAME}im-img-title">
   <td>Подпись (тег Title). </td>
   <td><input type="text" class="admin-input-text" name="im_title" value="{ADM_IM_TITLE}"/></td>
</tr>
<!-- EDP: im_alt_title -->

<!-- BDP: im_multi -->
<tr class="photo" >
   <td colspan="2" id="{ADM_IM_NAME}td-multi">
      {ADN_IM_MULTY_ITEMS} 
   </td>
</tr>
<!-- EDP: im_multi -->


<!-- EDP: im_form -->
<!-- EDP: image_manager -->