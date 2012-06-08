jQuery(function () {

    var params = {};

    jQuery('#bt-search-tree').click(function(){
        var searchText = jQuery('#search-tree').val();
        if (searchText != '') {
            jQuery("#options-tree").jstree("search", searchText);
        }
    });



    var hideNode = function(nodeObj, node, type) {
        var id = node.attr('id').replace('node_', '');
        
        jQuery.post('/ajax/node', {
            'id':id,
            'type':type,
            'operation':'hide_node'
        },function(data) {            
            var aTextObj = node.children('a');
            var nodeText = nodeObj.get_text(node).replace(' (Скрытое поле)', '');            
            nodeObj.set_text(node, nodeText);            
            if (data == 'hidden') {
                nodeObj.set_text(node, nodeText+' (Скрытое поле)');
            }
        });
                    
    }
    
    var openTemplate = function (options ) {
        var value = '';
        jQuery('#dialog-tree').dialog({
            'width': 680,
            'height':430,
            'modal':true,
            'zindex':'1',
            'title':options.title,
            'buttons' : [
                {
                    'text':'Ок',              
                    'click':function(){
                        var val = '';
                        var count = 0;
                        jQuery('#section-field-templates-selected-field option').each(function(){  
                        
                            jQuery.ajax({
                                'url':'/ajax/node',
                                'type' : 'POST',
                                'async': false,
                                'data':{                        
                                    'section_artikul':sectionArtikul,
                                    'activesection':activeSection,
                                    'id':options.id,
                                    'layout': 'sub_group',
                                    'operation': 'create_node',
                                    'title':this.value,
                                    'type': 'fields'
                                } ,
                                'success' : function(data) {
                                    value += data+"\n";
                                    options.jsTree.refresh(options.node);                            
                        
                                }
                            });
                        
                        });
                    
                        jQuery(this).dialog("close");
                        alert(value);
                    }
                },
                {
                    'text':'Отмена',
                    'click':function(){
                        jQuery(this).dialog("close");
                    }
                },
            ],
            'open':function() {   
                jQuery ('#select-template-type :selected').each(function(){
                    this.selected = false;
                });
                jQuery ('#select-template-type option[value=#]').attr('selected', 'selected');
                
                jQuery('table.admin-table select').each(function(){
                    if (this.id != 'select-template-type' && this.id != 'select-template-type-goods') {
                        this.disabled = true;
                        jQuery('#'+this.id+' option').remove();
                
                    }
                });
                jQuery('table.admin-table td select#select-template-type-goods').hide();            
            }
                                   
        }
    );
    }
    
    var createNode = function(obj) {
        
    }
    
    var contextMenu = {};
    var jTreeObj = null;

    jQuery("#options-tree")
    .bind("before.jstree", function (e, data) {
          
        if (data.func == 'select_node') {
            var obj = data.inst.get_container();              
              
        }
           
        if (data.func == 'show_contextmenu') {
            // var obj = data.getNode();
             
              

        }
         
           
        if (data.func != 'hover_node' && data.func != 'dehover_node' &&
            data.func != 'enable_hotkeys' && data.func != 'init' && data.func != 'set_focus' &&
            data.func != 'is_focused' && data.func != 'set_theme' &&  data.func != 'hide_dots' 
            &&  data.func != 'show_icons' &&  data.func != 'load_node'  &&  data.func != 'load_node'
            &&  data.func != 'load_node_json' &&  data.func != 'clean_node' &&  data.func != 'loaded' &&  data.func != 'hide_dots'
            &&  data.func != 'show_icons'&&  data.func != 'reload_nodes'&&  data.func != 'open_node'&&  data.func != 'reopen'
            &&  data.func != 'reset' &&  data.func != 'select_node' &&  data.func != 'reset' &&  data.func != 'reselect'
    ) {
            // alert(data.func);
        }
           
        
           
    })
    .jstree({  
           
        "themes" : {
            "theme" : "default",
            "dots" : false
        },
            
        // List of active plugins
        "plugins" : [ 
            "themes","json_data","ui","crrm",/*"cookies",*/"dnd","search","types","hotkeys","contextmenu" 
        ],
        
        'contextmenu' : {
            'items' : function (node) {
                // alert(node.attr('rel'));
                activeSection = node.attr('rel');
                var activeId = node.attr('id').replace('node_', '');
                var activeText = node.find('a').text();
                var hiddenFieldTitle = 'Скрыть';
                
                if (activeText.indexOf('(Скрытое поле)') != -1) {
                    hiddenFieldTitle = 'Показать';
                }
                //  alert(node.attr('rel'));
                //base group sub_group
                switch (node.attr('rel')) {
                    case ('base') : {
                            return {
                                'create' : {
                                    'label': 'Создать группу полей',
                                    'action': function (obj) {
                                    
                                        this.create(obj, 'last', {
                                            'data':'Название группы'
                                        }, function(){});   
                                    }                                
                                }
                            }
                        }
                    
                    case ('group') : {
                            return {
                                'create' : {
                                    'label': 'Создать подгруппу полей',
                                    'action': function (obj) {
                                    
                                        this.create(obj, 'last', {
                                            'data':'Новая подгруппа'
                                        }, function(){
                                        
                                        });   
                                    }                                
                                }, 
                                'rename': {
                                    'label' :'Переименовать',
                                    'action' : function (obj) {                                   
                                        this.rename(obj);
                                    }
                                }, 
                                'remove': {
                                    'label':'Удалить',
                                    'action' : function(obj) {
                                        if (!confirm('Удалить группу полей?')) {
                                            return false;
                                        }
                                        this.remove(obj);
                                    }
                                }
                            }
                        }
                    
                    case ('sub_group') : {
                            return {
                                'create' : {
                                    'label': 'Создать поле',
                                    'action': function (obj) {
                                        this.create(obj, 'last', {
                                            'data':'Новое поле'
                                        }, function(){});   
                                    }                                
                                },  
                                'create_ftpl' : {
                                    'label': 'Добавить из шаблона',
                                    'action': function (obj) {
                                        var options = {
                                            'jsTree': this,
                                            'node':node,
                                            'id':activeId
                                        };
                                        openTemplate(options);
                                    }
                                },
                                'rename': {
                                    'label' :'Переименовать',
                                    'action' : function (obj) {                                   
                                        this.rename(obj);
                                    }
                                },  
                            
                                'remove' : {
                                    'label' : 'Удалить', 
                                    'action': function (obj) {
                                        if (!confirm('Удалить подгруппу полей?')) {
                                            return false;
                                        }
                                        this.remove(obj);
                                    }
                                }
                            
                            
                            }
                        }
                    
                    case ('right_group') : {
                            return {
                                'create' : {
                                    'label': 'Создать поле',
                                    'action': function (obj) {
                                        this.create(obj, 'last', {
                                            'data':'Новое поле'
                                        }, function(){});   
                                    }                                
                                }
                            }
                        }
                    case ('bottom_group') : {
                            return {                            
                                'create' : {
                                    'label': 'Создать поле',
                                    'action': function (obj) {
                                        this.create(obj, 'last', {
                                            'data':'Новое поле'
                                        }, function(){});   
                                    }                                
                                }
                            }
                        }
                    
                    case ('fields'): {
                            return {
                                'remove' : {
                                    'label': 'Удалить',
                                    'action': function(obj) {
                                        this.remove(obj);
                                    }
                                } ,
                                'rename': {
                                    'label' :'Переименовать',
                                    'action' : function (obj) {
                                        this.set_text(obj, this.get_text(obj).replace('(Скрытое поле)', ''));
                                        this.rename(obj);
                                    }
                                }, 
                                'hide' : {
                                    'label' : hiddenFieldTitle,
                                    'action': function(obj) {                                                                        
                                        //alert(objDump(this.data.crrm));                                    
                                        hideNode(this, node, 'field');
                                    }
                                
                                }
                            }      
                        }
                }
                return false;            
            }          
        },
      
    
        /* "contextmenu":{                                                       
            "items": {
                      
                "create": {
                    "label":"Создать ..." ,                                      
                    "submenu": {
                          
                        "group": {
                            "label":"Элемент"
                        },
                        "section": {
                            "label":"Поля другого раздела",
                            //"_disabled": true,
                            "action":function(obj) {                      
                                jQuery('#dialog-tree').dialog({
                                    'width': 680,
                                    'height':450,
                                    'modal':true,
                                    'zindex':'1',
                                    'title':'Поля из других разделов',
                                    'buttons' : [
                                    {
                                        'text':'Ок',
                                        'click':function(){
                                            jQuery(this).dialog("close");
                                        }
                                    },
                                    {
                                        'text':'Отмена',
                                        'click':function(){
                                            jQuery(this).dialog("close");
                                        }
                                    },
                                    ],
                                    'open':function() {                                                                        
                                     
                                    }
                                   
                                }
                                );
                          
                                 
                                return false;          
                            }
                        },
                        "templates": {
                            "label":"Выбрать из шаблона",
                            "action":function(obj){  
                         
                            }
                        }
                           
                    }
                },
                "hide": {
                    "label":"Скрыть/Показать",
                 
                    'action':function(obj) {
                           var id = this._get_node(obj).attr('id').replace('node_', '');                        
                           var actionTmp = 'hidden';
                           var text = this.get_text(obj);
                           if (text.indexOf('(Скрытое поле)') != -1) {
                               actionTmp = 'show';
                           }
                           var obj1 = this;
                            jQuery.post("/ajaxSectionFields.php",
                                        {"operation" : "hide_node", 
                                "id" : id,
                                "type" :this._get_node(obj).attr('rel'),
                                "text": text,
                                'artikul':sectionArtikul,
                                'act':actionTmp
                                },
                                function (data) {
                                    if (data == '1') {
                                        obj1.set_text(obj, text+' (Скрытое поле) ');
                                    } else {
                                        obj1.set_text(obj, text.replace(' (Скрытое поле) ', ''));
                                    }
                                }
                            );
                    }
                },
           
                "remove":{
                    "label":"Удалить"                      
                },
                "rename":{
                    "label":"Переименовать"
                }
            }
        },*/
                
        // I usually configure the plugin that handles the data first
        // This example uses JSON as it is most common
        "json_data" : { 
            // This tree is ajax enabled - as this is most common, and maybe a bit more complex
            // All the options are almost the same as jQuery's AJAX (read the docs)
            "ajax" : {
                // the URL to fetch the data
                "url" : "/ajax/node",
                "type":"POST",
                // the `data` function is executed in the instance's scope
                // the parameter is the node being loaded 
                // (may be -1, 0, or undefined when loading the root nodes)
                "success": function(data) {
                                  
                },
                                
                "data" : function (n) {                                    
                    // the result is fed to the AJAX request `data` option                                     
                    //var options = n.attr ? n.attr("id").split("_val_") : '1';
                    var id = n.attr ? n.attr("id").replace("node_","") : '-1';
                    //var id = options[0] !== undefined ? options[0].replace("node_", "") : "1";
                                        
                    return { 
                        "operation" : "get_children",                                                 
                        "artikul":sectionArtikul,   
                        "rel":n.attr ? n.attr("rel") : 'group' ,                                                
                        "id" : id,
                        "params":params
                    }; 
                }
            }
        },
        // Configuring the search plugin
        "search" : {
            // As this has been a common question - async search
            // Same as above - the `ajax` config option is actually jQuery's AJAX object
            "ajax" : {
                "url" : "/ajaxSectionFields.php",
                // You get the search string as a parameter
                "data" : function (str) {
                    return { 
                        "operation" : "search", 
                        "search_str" : str 
                    }; 
                }
            }
        },
        // Using types - most of the time this is an overkill
        // read the docs carefully to decide whether you need types
        "types" : {
            // I set both options to -2, as I do not need depth and children count checking
            // Those two checks may slow jstree a lot, so use only when needed
            "max_depth" : -2,
            "max_children" : -2,
            // I want only `drive` nodes to be root nodes 
            // This will prevent moving or creating any other type as a root node
            "valid_children" : [ "group" ],
            "types" : {
                // The default type
                                
                "right_group" : {
                    "valid_children" :  [ "fields" ],					
                    "icon" : {
                        "image" : "/js/jquery-plugins/jq-tree/themes/default/folder.png"                                                
                    }
                },
                                
                "bottom_group" : {
                    "valid_children" :  [ "fields" ],					
                    "icon" : {
                        "image" : "/js/jquery-plugins/jq-tree/themes/default/folder.png"                                                
                    }
                },
                                
                "base" : {
                    "valid_children" :  [ "group" ],					
                    "icon" : {
                        "image" : "/js/jquery-plugins/jq-tree/themes/default/folder.png"                                                
                    }
                },
                                
                                
                "group" : {
                    "valid_children" :  [ "sub_group" ],					
                    "icon" : {
                        "image" : "/js/jquery-plugins/jq-tree/themes/default/folder.png"                                                
                    }
                },
                                
                                
                "sub_group" : {
					
                    "valid_children" :  [ "fields" ],					
                    "icon" : {
                        "image" : "/js/jquery-plugins/jq-tree/themes/default/folder.png"                                                
                    }
                },
                                
                "fields" : {
                    "valid_children" : "none",					
                    "icon" : {
                        "image" : "/js/jquery-plugins/jq-tree/themes/default/file.png"		                                            
                    }
                }
                                
				
				
            }
        }//,
        /*
		// UI & core - the nodes to initially select and open will be overwritten by the cookie plugin

		// the UI plugin - it handles selecting/deselecting/hovering nodes
		"ui" : {
			// this makes the node with ID node_4 selected onload
			"initially_select" : [ "node_4" ]
		},
		// the core plugin - not many options here
		"core" : { 
			// just open those two nodes up
			// as this is an AJAX enabled tree, both will be downloaded from the server
			"initially_open" : [ "node_2" , "node_3" ] 
		}*/
    }).bind("create.jstree", function (e, data) { 
        
        jQuery.post(
        "/ajax/node", 
        { 
            "operation" : "create_node", 
            "id" : data.rslt.parent.attr("id").replace("node_",""), 
            "position" : data.rslt.position,
            "title" : data.rslt.name,
            "type" : data.rslt.obj.attr("rel"),
            "layout": data.rslt.parent.attr("rel"),
            'section_artikul': sectionArtikul
                    
        }, 
        function (r) {      
            data.inst.refresh(data.rslt.parent);
            alert(r);
            //  data.inst.create(data.rslt.obj, 'last', {'state': 'open', 'data': 'ddddddddd'}); 
        }
            
    );
    })
    .bind("remove.jstree", function (e, data) {
        data.rslt.obj.each(function () {
            jQuery.ajax({
                async : false,
                type: 'POST',
                url: "/ajax/node",
                data : { 
                    "operation" : "remove_node", 
                    "id" : this.id.replace("node_",""),                    
                    "type" : data.rslt.obj.attr("rel")                    
                }, 
                success : function (r) {
                     data.obj.refresh(-1);  
                   
                }
            });
        });
    })
    .bind("rename.jstree", function (e, data) {       
        
     
        jQuery.post(
        "/ajax/node", 
        { 
            "operation" : "rename_node", 
            "id" : data.rslt.obj.attr("id").replace("node_",""),
            "title" : data.rslt.new_name,
            "type" : data.rslt.obj.attr("rel")
              
        }, 
        function (ajaxData) {
            data.inst.set_text(data.rslt.obj, ajaxData); 
                
        }
    );
    })    
    .bind("move_node.jstree", function (e, data) {                        
             
         
             
        data.rslt.o.each(function (i) {
      
            jQuery.ajax({
                async : false,
                type: 'POST',
                url: "/ajax/node",
                data : { 
                    "operation" : "move_node", 
                    "id" : jQuery(this).attr("id").replace("node_",""), 
                    "ref" : data.rslt.cr === -1 ? 1 : data.rslt.np.attr("id").replace("node_",""), 
                    "position" : (data.rslt.cp + i)+1,
                    "title" : data.rslt.name,
                    "artikul":sectionArtikul,   
                    "type":jQuery(this).attr("rel"),
                    "value":data.rslt.np.children('a').text(),
                    "new_parent":data.rslt.np.attr('rel'),
                    "copy" : data.rslt.cy ? 1 : 0
                },
                success : function (r) {
                    //                    if(!r.status) {
                    //                        jQuery.jstree.rollback(data.rlbk);
                    //                    }
                    //                    else {
                    //                        jQuery(data.rslt.oc).attr("id", "node_" + r.id);
                    //                        if(data.rslt.cy && jQuery(data.rslt.oc).children("UL").length) {
                    //                            data.inst.refresh(data.inst._get_parent(data.rslt.oc));
                    //                        }
                    //                    }
                    //                    jQuery("#analyze").click();
                }
            });
        });
    });

});


/*
 
 */