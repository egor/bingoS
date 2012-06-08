<?php

$config = array(
    'database' => array(
        'adapter'   => 'PDO_MYSQL',
        'params'    => array(
            'host'              => 'localhost',
            //'username'          => 'vpohodinua',
            //'password'          => 'J25aoIrG',
           // 'username'          => 'temp-cross',
            //'password'          => 'AtqYNGWe',
            'username'          => 'root',
            'password'          => '',
            'dbname'            => 'tempcross',
            'driver_options'    => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
            'profiler'          => true
        )
    ),
    'language' => array(
        'defaultLanguage'   => 'ru',
        'allowLanguage'     => array(
            'ru' => 'ru',
            //'en' => 'en',
            //'de' => 'de',
            //'fr' => 'fr',
            //'it' => 'it',
        ),
        'useDefLangPath'    => false
    ),
    // Настройка каталога
    'catalog'=>array (
    	'isStore' => true, 		  	// Использовать каталог как интернет магазин. Будут выводиться элементы: цена, зачркнутая цена если есть, корзина, оформление заказа, база заказов в админке
    	// Настройка для подробного описания товара
    	'isForeshortening'=>true, 	// Использовать галерею "Другие ракурсы товара".
    	'isMiniGallery'=>true, 	  	// Использовать минигалерею.
    	'isFeatured'=>true, 	  	// Использовать рекомендуемые товары.
    	'isComments'=>true, 	  	  	// Использовать отзывы.
        'isUsedComplete'=>true 	  	// Использовать комплект.

    )
);