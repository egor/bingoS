    
var dellButtonClick = function(obg) {
    
    alert(obj.attr('id'));
    return false;
    
}    
    
var setErrorMessage = function (text) {
    alert(text);
}

var showTmpFile = function(fieldId, src) {  
    jQuery('#img-'+fieldId).attr('src', src); 
    jQuery('#'+fieldId+'-dell-button')
    .show()
    .children('a')
    .click(function(){                                       
        return false;
    });
}
    
var setResponse = function (respObj) {    
    var ret = new String(respObj.response);   
   
    if (ret.indexOf('#~#@') != -1) {
        var tmpArr = ret.split('#~#@'); 
       
        if (tmpArr[0] !== undefined && tmpArr[1] !== undefined) {
           
            switch (tmpArr[0]) {
                case ('err') : {
                    setErrorMessage(tmpArr[1]);     
                    break;    
                }
                case ('mess-copy-tmp'): {                         
                    showTmpFile(respObj.fieldId, tmpArr[1]);
                    break;     
                }
            }
        }
    }
    return true;
}    
   
   
   
var initFileUploader = function(params) {
    jQuery(function(){   

        if (params.upload === undefined) {
            params.upload = {
                'uploader'  : '/js/uploadify/uploadify.swf',
                'script'    : '/js/uploadify/uploadify.php',
                'cancelImg' : '/js/uploadify/cancel.png',
                'folder'    : '/js/uploads',
                // 'buttonText':'Ok',
                'auto'      : true
            };
        } else {
            
        }
    
        
        jQuery('#'+params.id).uploadify(params.upload);       
    });

}




