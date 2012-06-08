/* 
 * Файл используется в админ панели при редактировании
 * полей каталога. Вызывается в section_fields_templates шаблона админки
 */
var sectionFieldsTemplates = {
   'template1': {
      'title':'Размеры',
      'fields':[
         {
            'title': 'Длина',
            'name': 'length', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         },
         {
            'title': 'Высота',
            'name': 'height', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         },
         {
            'title': 'Ширина',
            'name': 'width', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         }       
         
      ]
   },
   'additionalinformation': {
      'title':'Дополнительная информация',
      'fields':[
         {
            'title': 'Особенности',
            'name': 'features', 
            'type': 'have-not-have',
            'isSerch': false,
            'isFilter':false
         },
         {
            'title': 'Достоинства',
            'name': 'accomplishments', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         },
         {
            'title': 'Преимущества',
            'name': 'benefits', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         }       
         
      ]   
   },
   'SportsNavigation': {
      'title':'Спортивные навигаторы',
      'fields':[
         {
            'title': 'Функция энергосбережения, функция ANT™ + Sport',
            'name': 'energy-saving-function-the-function-of-ant-sport', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         },
         {
            'title': 'Температура, отображение на экране и запись значений во время движения',
            'name': 'the-temperature-display-on-the-screen-and-record-values-​​during-motion', 
            'type': 'have-not-have',
            'isSerch': false,
            'isFilter':false
         },
         {
            'title': 'Барометрический высотомер',
            'name': 'barometric-altimeter', 
            'type': 'yes-no',
            'isSerch': false,
            'isFilter':false
         }
         ,
         {
            'title': 'Скорость, м/с',
            'name': 'speed', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         }
          ,
         {
            'title': 'Местоположение',
            'name': 'location', 
            'type': 'varchar',
            'isSerch': false,
            'isFilter':false
         }
         //
         
      ]
   }
   

   
   
};

