<?php
class CatalogFormOptions {
    public static $imgPath = array(
                'small'=>'/img/catalog/small',
                'big'=>'/img/catalog/big',
                'real'=>'/img/catalog/real');
    
    public static $imgSize = array(
                'small'=>array('width'=>'133', 'height'=>'133'),
                'big'=>array('width'=>'420', 'height'=>'420'),
                'real'=>array('width'=>'600', 'height'=>'600'));
    
    public static $defaultFieldsGroups = array(
                'Основные поля'=>array(
                    'name',
                    'href'=>array('title'=>'Ссылка', 'type'=>'href'),
                    'header'        

                ),
                'SEO'=>array(
                    'title',
                    'keywords',
                    'description'
                ),
                'Стоимость'=>array(
                    'cost_old',
                    'cost'
                ),
                'Изображение'=>array(
                   'pic',
                   'pic_alt',
                   'pic_title',
                   'catalog_gallery'=>array('title'=>'Галерея', 'type'=>'gallery'),       
                ),

                'Описание'=>array(
                    'used_complete',
                    'featured_products',
                    'preview',
                    'body',
                ),
                'Дополнительно'=>array(
                   'brand',                                      
                   'equipment',
                   'scope',
                   'guarantee',
                   'status',
                   'availability'
                ),

            );
     public static $isUseFlashUploader = true;
}


class CatalogGalleryOptions {   

     public static $imgPath = array(
            'small'=>'/img/catalog/gallery/small',
            //'big'=>'/img/catalog/big',
            'real'=>'/img/catalog/gallery/real'
            );
    public static $imgSize = array(
            'small'=>array('width'=>'133', 'height'=>'133'),            
            'real'=>array('width'=>'600', 'height'=>'600'),
    );    
   
    public static $isUseFlashUploader = true;
}


class CatalogDetailFieldsLayout {
    public static $right = array(
        //'brand','equipment', 'guarantee'//,'preview'
        'preview'
    );
    
    public static $bottom = array(
        'body'
    );
}