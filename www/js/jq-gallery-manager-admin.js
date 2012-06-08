var GMSaveImageTitleAlt = function (obj) {
    jQuery(function(){        
        var id = jQuery(obj).attr('id');       
        var liObj = new String(id).replace('save-', '');
        
        var title = jQuery('#input-title-'+liObj).val();
        var alt = jQuery('#input-alt-'+liObj).val();
        
        jQuery.post(jQuery(obj).attr('href'), {'alt':alt, 'title':title, 'id':liObj}, function(data){
            alert(data);
        });        
     });
}

var GMDeleteImage = function (obj) {
     jQuery(function(){
        if (!confirm("Удалить изображение ?")) return;
        var id = jQuery(obj).attr('id');       
        var liObj = new String(id).replace('delete-', '');
        jQuery('#li-'+liObj).remove();          
        jQuery.post(jQuery(obj).attr('href'), function(data){
          
        });        
     });
 }

 jQuery(function(){  
    
 });
 
