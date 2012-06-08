<?php
// Добавляешь новый массив с параметрами? Добавь в getCatalogOptions класса Abstract
$defaultFields = array(
    //'artikul' => 'Артикул',
       'name' => 'Название',
       'position' => array('title' => 'Позиция в списке', 'default' => '9999'),
       //'href'=>'Ссылка',
       'header' => 'Заголовок H1',
       'title' => 'Заголовок Title',
       'keywords' => 'Мета тэг Keywords',
       'description' => 'Мета тэг Description',
       'preview' => 'Краткое описание',
       'body' => 'Подробное описание',
       'pic' => 'Изображение',
       'pic_alt' => 'Альтернативный текст для изображения',
       'pic_title' => 'Всплывающая подсказка для изображения',
       // 'visibility' => array('title' => 'Отображать', 'value' => array('Нет', 'Да'), 'default' => 'Да'),
       'brand' => 'Бренд',
       'brand_rus' => 'Бренд рус',
       'proizvoditel' => 'Страна производитель',     
       'guarantee' => 'Гарантия',
       'cost_old' => 'Цена розничная',
       'cost' => 'Цена на сайте',
       'used_complete' => 'Комплект артикулы',
       'featured_products' => 'Рекомендуемые товары артикулы',
       'status' => array('title' => 'Статус', 'value' => array('hit' => 'Хит', 'new' => 'Новинка', 'action' => 'Акция')),
       'availability' => array('title' => 'Наличие на складе', 'value' => array( 'Нет', 'Есть','Снят с производства'), 'type'=>'yes-no'),
       
   );





?>
