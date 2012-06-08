<?php

class admin extends page{
	
	private $db = null;
	
	private $tpl = null;
	
	public $cfg = null;
	
	private $userData=array();
	
	public $metatitle = '';
	
	public $metakeywords = '';
	
	public $metadescription = '';
	
	public $way = '';
	
	public $header = '';
	
	public $_err = null;
	
	public $w = null;
	
	public $lang = 'ru';
	
	public $langUrl = '';
	
	public function __construct($db, $tpl, $cfg, $userData, $w, $lang, $langUrl){
		$this->db = $db;
		$this->tpl = $tpl;
		$this->cfg = $cfg;
		$this->userData = $userData;
		$this->w = $w;
		$this->lang = $lang;
		$this->langUrl = $langUrl;
		
		if(Auth::getPrivilege() != 'admin' && Auth::getPrivilege() != 'master') {
			er_404();
		}
		
		$this->tpl->define_dynamic('edit', 'adm/edit.tpl');
		
		
	}
	
	/* News Section */
	public function addnews() {
		$meta = 'Добавление новости';
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		//$idm = gp($this->w, 2);
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('top', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('optimization_text', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('news', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
		$this->tpl->define_dynamic('help', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$pic = '';
		
		$adress_str = ru2Lat(gp($_POST, 'adress_str', ''));
		$name = gpm($_POST, 'name', '');
		$date = gpm($_POST, 'date', date('d-m-Y'));
		$preview = gpm($_POST, 'preview', '');
		$body = gpm($_POST, 'body', '');
		$header = htmlspecialchars(gp($_POST, 'header', ''));
		$title = gp($_POST, 'title', '');
		$keywords = gp($_POST, 'keywords', '');
		$description = gp($_POST, 'description', '');
		$optimizationText = gp($_POST, 'description', '');
		$top = gp($_POST, 'top', '');
		
		$visible = gpm($_POST, 'visible', '1');
        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
            
        }
        
        $top_s = '';
        if ($top == 1) {
            $top_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $top_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
            
        }
				
		if(!empty($_POST)){
			$referer = gp($_POST, 'HTTP_REFERER', '');
			
			if (empty($adress_str)) $this->_err .= 'Не заполнен адрес!<br>';
			else{
				$numRow = $this->db->getResult('SELECT count(`id`) FROM `news` WHERE `link` = "'.$adress_str.'" AND `lang` = "'.$this->lang.'"');
				
				if ($numRow > 0) {
					$this->_err .= 'Элемент с таким адресом уже существует!<br>';
				}
			}
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
		}
		
		if(!empty($_POST) && !$this->_err){
            $date = explode('-', $date);
			
			$date = mktime(date('H'), date('i'), date('s'), $date[1], $date[0], $date[2]);
			
			$this->db->setQuery('INSERT INTO `news`
			( `name`, `link`, `preview`, `description`, `date`, `visibility`, `metatitle`, `metah1`, `metakeywords`, `metadescription`, `lang`, `pic`, `top`, `optimization_text`)
			VALUES
			("'.$name.'", "'.$adress_str.'", "'.addslashes($preview).'", "'.addslashes($body).'", "'.$date.'", "'.$visible.'", "'.$title.'", "'.$header.'", "'.$keywords.'", "'.$description.'", "'.$this->lang.'", "'.$pic.'", "'.$top.'", "'.addslashes($optimizationText).'")');
			
			$id = $this->db->getLastInsertId();
			
			if(!empty($_FILES) && $_FILES['pic']['size'] > 0){
				
				if (
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/news/'.$id.'-'.$_FILES['pic']['name'], 132, 132) &&
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/news/big/'.$id.'-'.$_FILES['pic']['name'], 188, 230) ) {				    	
			    	@chmod(BASE_PATH.'img/catalog/big/'.$id.'-'.$_FILES['pic']['name'], 0666);
			        $this->db->setQuery('UPDATE `news` SET `pic` = "'.$id.'-'.$_FILES['pic']['name'].'" WHERE `id` = '.$id);
			    }
				
			}
			
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){
				
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);
				}
				
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    	
			    	@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
			        $this->db->setQuery('UPDATE `news` SET `collage` = "'.$id.'-'.$_FILES['collage']['name'].'" WHERE `id` = '.$id);
			    }
				
			}
			
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/';
			$content = "Элемент добавлен. <meta http-equiv='refresh' content='1;URL=$referer'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		if ($this->_err) {
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if (empty($_POST) || $this->_err) {
			$this->tpl->assign(
				array(
					'ADRESS_STR' => $adress_str,
					
					'DATE' => $date,
					'PREVIEW' => $preview,
					'TEXT' => $body,
					'VISIBLE_S' => $visible_s,
					'HEADER' => $header,
					'TITLE' => $title,
					'KEYWORDS' => $keywords,
					'DESCRIPTION' => $description,
					'REFERER' => $referer,
					'OPTIMIZATION_TEXT' => stripslashes($optimizationText),
					'NAME' => $name,
					'DELETE_COLLAGE'=>"",
					'TOP_S' => $top_s
				)
			);
			
			$help = array();
			$help[] = array('title' => 'Адрес в URL', 'body' => 'Ссылка в адресной строке.<br /><br />Например: <img src="/img/help/adres.gif">', 'type' => 'link');
			$help[] = array('title' => 'Название', 'body' => 'В соответствующем поле прописывается название страницы, которое будет отображаться в меню и списках страниц.', 'type' => 'name');
			$help[] = array('title' => 'Изображение к новости', 'body' => 'Вставив нужную Вам картинку с помощью кнопки "Обзор", справа от нее отобразится текст.', 'type' => 'pic');
			$help[] = array('title' => 'Дата', 'body' => 'В этом поле указывается дата создания/редактирования новости.', 'type' => 'date');
			$help[] = array('title' => 'Анонс', 'body' => 'В этом поле прописывается краткое описание страницы/раздела/новости.', 'type' => 'preview');
			$help[] = array('title' => 'Header', 'body' => 'В соответствующем поле прописывается заголок страницы.<br /><br />Например: <img src="/img/help/header.gif">', 'type' => 'header');
			$help[] = array('title' => 'Title', 'body' => 'Заголовок окна броузера.<br /><br />Например: <img src="/img/help/title.gif">', 'type' => 'title');
			$help[] = array('title' => 'Keywords', 'body' => 'Ключевые слова для сайта/страницы сайта', 'type' => 'keywords');
			$help[] = array('title' => 'Description', 'body' => 'Самые важные фразы, характеризующие тематику сайта/страницы.', 'type' => 'description');
			$help[] = array('title' => 'Видимость', 'body' => 'Если в выпадающем поле выбрать "Да", то страница будет видна всем пользователям.</br>Если "Нет" - только администратору сайта.', 'type' => 'visibility');
			$help[] = array('title' => 'Текст', 'body' => 'Полный текст (страницы, новости и т.п.).', 'type' => 'body');
			
			
			
			$help[] = array('title' => 'Выпадающее', 'body' => 'Если выбрать "Да", то будут отображаться подпункты горизонтального/вертикального меню.<br />Если "Нет" - подпункты горизонтального/вертикального меню отображаться не будут.', 'type' => 'body');
			
			$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'body');
			
			$help[] = array('title' => 'Артикул', 'body' => 'Буквенное -и(или) цифровое обозначение.', 'type' => 'body');
						
			for ($i=0; $i<11; $i++) {
			    $this->tpl->assign(
                    array(
                        'HELP_TITLE' => $help[$i]['title'],
                        'HELP_TEXT' => $help[$i]['body'],
                        'HELP_TYPE' => $help[$i]['type']
                    )
			    );
			    
			    $this->tpl->parse('CONTENT', '.help');
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.top');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.news');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.optimization_text');
			$this->tpl->parse('CONTENT', '.visible');
			$this->tpl->parse('CONTENT', '.body');
			$this->tpl->parse('CONTENT', '.end');
		}
		return true;
	}
	
	public function editnews() {
		$meta = 'Редактирование новости';
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('top', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('optimization_text', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('news', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
		$this->tpl->define_dynamic('help', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		if(!$id = gp($this->w, 2)) return false;
				
		$q = $this->db->getRow('SELECT * FROM `news` WHERE `id` = "'.$id.'" LIMIT 1');
		
		//if($this->db->getNumRows() != 1){
		if (!$q) {
		    return false;
		}
		
		$pic = '';
		
		$adress_str = gp($q, 'link', '');
		
		$date = convertDate(gpm($q, 'date', mktime()), "d-m-Y");
		$preview = gpm($q, 'preview', '');
		$body = gpm($q, 'description', '');
		$visible = gpm($q, 'visibility');
		$header = gp($q, 'metah1', '');
		$title = gp($q, 'metatitle', '');
		$keywords = gp($q, 'metakeywords', '');
		$description = gp($q, 'metadescription', '');
		$top = gp($q, 'top', '');
		$name = gp($q, 'name', '');
		$optimizationText = gp($q, 'optimization_text', '');
		
		if (!empty($_POST)) {
			$referer = gp($_POST, 'HTTP_REFERER', '');
			
			$adress_str = ru2Lat(gp($_POST, 'adress_str', ''));
			
			if (empty($adress_str)) $this->_err .= 'Не заполнен адрес!<br>';
			else{
				$numRow = $this->db->getResult('SELECT count(`id`) FROM `news` WHERE `link` = "'.$adress_str.'" AND `id` <> "'.$id.'" AND `lang` = "'.$this->lang.'"');
				
				if ($numRow > 0) {
					$this->_err .= 'Элемент с таким адресом уже существует!<br>';
				}
			}
		
			$date = gpm($_POST, 'date', date('d-m-Y'));
			$preview = gpm($_POST, 'preview', '');
			$body = gpm($_POST, 'body', '');
			$visible = gpm($_POST, 'visible', '1');
			$header = htmlspecialchars(gp($_POST, 'header', ''));
			$title = gp($_POST, 'title', '');
			$keywords = gp($_POST, 'keywords', '');
			$description = gp($_POST, 'description', '');
			$top = gp($_POST, 'top', '');
			$name = gpm($_POST, 'name', '');
			$optimizationText = gpm($_POST, 'optimization_text', '');
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
		}
		
		$visible_s = '';
		if ($visible == 1){
			$visible_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$visible_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		
		$top_s = '';
		if ($top == 1){
			$top_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$top_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		
		if (!empty($_POST) && !$this->_err) {
			
			if(!empty($_FILES) && $_FILES['pic']['size'] > 0){
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/news/'.$q['pic'])){
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/news/'.$q['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/news/'.$q['pic']);
				}
				
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/news/big/'.$q['pic'])){
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/news/big/'.$q['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/news/big/'.$q['pic']);
				}
				
				if (
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/news/small/'.$id.'-'.$_FILES['pic']['name'], 132, 132) &&
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/news/'.$id.'-'.$_FILES['pic']['name'], 188, 230) ) {				    	
			    	@chmod(BASE_PATH.'img/catalog/big/'.$id.'-'.$_FILES['pic']['name'], 0666);
			        $this->db->setQuery('UPDATE `news` SET `pic` = "'.$id.'-'.$_FILES['pic']['name'].'" WHERE `id` = '.$id);
			    }
			   
			   
				
				if(!empty($pic['err'])){
					$pic = '';
					$this->err .= $pic['err'];
				}
				else{
					$pic = ", `pic` = '".$pic['pic']."'";
				}
			}
			
			
			$collageFileName = '';
			    
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){				
			    	
			   	if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);
				}
			    	
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    				    	
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
			       	$collageFileName = $id.'-'.$_FILES['collage']['name'];
			       	
			   	}
				
			}
			
			$date = explode('-', $date);
			
			$date = mktime(date('H'), date('i'), date('s'), $date[1], $date[0], $date[2]);
			
			$this->db->setQuery('UPDATE `news` SET `link` = "'.$adress_str.'", `name` = "'.$name.'", `preview` = "'.addslashes($preview).'", `optimization_text`="'.$optimizationText.'", `collage`="'.$collageFileName.'", `description` = "'.addslashes($body).'", `visibility` = "'.$visible.'", `metah1` = "'.$header.'", `metatitle` = "'.$title.'", `metadescription` = "'.$description.'", `metakeywords` = "'.$keywords.'", `top` = "'.$top.'", `date` = "'.$date.'"'.$pic.' WHERE `id` = '.$id);
			
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/';
			//$content = "Данные изменены";
			$content = "Данные изменены <meta http-equiv='refresh' content='1;URL=$referer'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		if ($this->_err) {
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if (empty($_POST) || $this->_err) {
			$this->tpl->assign(
				array(            
					'ADRESS_STR' => $adress_str,
					
					'DATE' => $date,
					'PREVIEW' => $preview,
					'TEXT' => $body,
					'VISIBLE_S' => $visible_s,
					'HEADER' => $header,
					'TITLE' => $title,
					'KEYWORDS' => $keywords,
					'DESCRIPTION' => $description,
					'REFERER' => $referer,
					'TOP_S' => $top_s,
					'DELETE_COLLAGE'=>(!empty($q['collage']) ? " Удалить коллаж <a onclick=\"return confirm('Вы уверены что хотите удалить?'); return false;\" title=\"Удалить\" href=\"/admin/deletenewscollage/$id\"><img height=\"12\" width=\"12\" src=\"/img/admin_icons/delete.png\"></a>" : ''),
					'OPTIMIZATION_TEXT' => stripslashes($optimizationText),
					'NAME' => $name,
					'REFERER'=>$referer,
				
				)
			);
			
			$help = array();
			$help[] = array('title' => 'Адрес в URL', 'body' => 'Ссылка в адресной строке.<br /><br />Например: <img src="/img/help/adres.gif">', 'type' => 'link');
			$help[] = array('title' => 'Название', 'body' => 'В соответствующем поле прописывается название страницы, которое будет отображаться в меню и списках страниц.', 'type' => 'name');
			$help[] = array('title' => 'Изображение к новости', 'body' => 'Вставив нужную Вам картинку с помощью кнопки "Обзор", справа от нее отобразится текст.', 'type' => 'pic');
			$help[] = array('title' => 'Дата', 'body' => 'В этом поле указывается дата создания/редактирования новости.', 'type' => 'date');
			$help[] = array('title' => 'Анонс', 'body' => 'В этом поле прописывается краткое описание страницы/раздела/новости.', 'type' => 'preview');
			$help[] = array('title' => 'Header', 'body' => 'В соответствующем поле прописывается заголок страницы.<br /><br />Например: <img src="/img/help/header.gif">', 'type' => 'header');
			$help[] = array('title' => 'Title', 'body' => 'Заголовок окна броузера.<br /><br />Например: <img src="/img/help/title.gif">', 'type' => 'title');
			$help[] = array('title' => 'Keywords', 'body' => 'Ключевые слова для сайта/страницы сайта', 'type' => 'keywords');
			$help[] = array('title' => 'Description', 'body' => 'Самые важные фразы, характеризующие тематику сайта/страницы.', 'type' => 'description');
			$help[] = array('title' => 'Видимость', 'body' => 'Если в выпадающем поле выбрать "Да", то страница будет видна всем пользователям.</br>Если "Нет" - только администратору сайта.', 'type' => 'visibility');
			$help[] = array('title' => 'Текст', 'body' => 'Полный текст (страницы, новости и т.п.).', 'type' => 'body');
			
			
			
			$help[] = array('title' => 'Выпадающее', 'body' => 'Если выбрать "Да", то будут отображаться подпункты горизонтального/вертикального меню.<br />Если "Нет" - подпункты горизонтального/вертикального меню отображаться не будут.', 'type' => 'body');
			
			$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'body');
			
			$help[] = array('title' => 'Артикул', 'body' => 'Буквенное -и(или) цифровое обозначение.', 'type' => 'body');
						
			for ($i=0; $i<11; $i++) {
			    $this->tpl->assign(
                    array(
                        'HELP_TITLE' => $help[$i]['title'],
                        'HELP_TEXT' => $help[$i]['body'],
                        'HELP_TYPE' => $help[$i]['type']
                    )
			    );
			    
			    $this->tpl->parse('CONTENT', '.help');
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.top');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.news');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.optimization_text');
			$this->tpl->parse('CONTENT', '.visible');
			$this->tpl->parse('CONTENT', '.body');
			$this->tpl->parse('CONTENT', '.end');
		}
		return true;
	}

	public function deletenews() {
		if(!$id = gp($this->w, 2)) return false;
		
		$query = 'SELECT * FROM `news` WHERE `id` = '.$id;
		$this->db->setQuery($query);
		
		$new = $this->db->getRow('SELECT * FROM `news` WHERE `id` = '.$id);
		
		if($this->db->getNumRows() != 1){
			$this->_err .= 'Элемента с таким id не существует!<br>';
		}
		else{
			if(file_exists('./img/news/'.$new['pic'])) {
				@unlink('./img/news/'.$new['pic']);
			}
			
			if(file_exists('./img/news/big/'.$new['pic'])) {
				@unlink('./img/news/big/'.$new['pic']);
			}
			
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/pic/'.$new['collage'])) {
				@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$new['collage'], 0666);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$new['collage']);
			}
			
			$this->db->setQuery('DELETE FROM `news` WHERE `id` = '.$id);
		}
		
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
			$content = "Элемент удалён.<meta http-equiv='refresh' content='1;URL=$referer'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		return true;
	}
	/* End News Section */
	
	/* Menu & Page Section */
	public function menu() {
		$location = (isset($this->w[2])) ? $this->w[2]:'horisontal';
		$this->viewMenuItems($location);
		
		return true;
	}
	
	private function viewMenuItems($location='horisontal') {
		$this->tpl->define_dynamic('_section', 'section.tpl');
		$this->tpl->define_dynamic('section', '_section');
		$this->tpl->define_dynamic('section_row', 'section');
		
		$pages = $this->db->getAllRecords('SELECT * FROM `page` WHERE `menu` = "'.$location.'" AND `level` = 0 AND `lang` = "'.$this->lang.'" ORDER BY `type`, `position`, `name`');
		
		$pages_size = $this->db->getNumRows();
		
		$admin = getAdminCreate('all', $location, $this->langUrl);
				
		$this->tpl->assign(array('CONTENT' => '<div id="body">'.$admin.'</div><div class="c_sep_h"></div>'));
		
		$meta = (($location == 'horizontal')?('Горизонтальное'):('Вертикальное')).' меню';
		
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		//$this->way = ' <span>&rarr;</span> <a href="'.$this->langUrl.'/admin/menu/'.$location.'">'.(($location == 'horizontal')?'Горизонтальное':'Вертикальное').' меню</a>';
		
		for($i=0; $i<$pages_size; ++$i){
			
			$types = getAdminTypes($pages[$i]['type'], $pages[$i]['id'], 'pages', true, $this->langUrl);
			
			$link = ($pages[$i]['type'] == 'link') ? $pages[$i]['link'] : $this->langUrl."/".$pages[$i]['link'].(($pages[$i]['type'] == 'section') ? '/' : '');
			
			$this->tpl->assign(
				array(
					'PAGE_ID' => $pages[$i]['id'],
					'PAGE_ADRESS' => $link,
					'PAGE_TYPE' => $types,
					'PAGE_HEADER' => $pages[$i]['name'],
					'PAGE_PREVIEW' => ($pages[$i]['preview'] != '') ? $pages[$i]['preview'] : '',
					'SEPARATOR' => '',
				)
			);
			$this->tpl->parse('SECTION_ROW', '.section_row');
		}
		
		$this->tpl->assign(array('PAGES_TOP' => '', 'PAGES_BOTTOM' => '', 'PIC' => ''));
		
		if($pages_size > 0){
			$this->tpl->parse('CONTENT', '.section');
			return true;
		}
		else{
			$this->tpl->parse('SECTION_ROW', 'null');
			$this->tpl->parse('CONTENT', '.section');
		}
		
		return true;
	}
	
	private function addpages($type='page') {
		$id = end($this->w);
		
		if(!ctype_digit($id)){
			if($id == 'horizontal' || $id == 'vertical'){
				switch($type){
					case 'page' : $dop = 'страница'; break;
					case 'section' : $dop = 'раздел'; break;
					case 'link' : $dop = 'ссылка'; break;
					default : er_404(); break;
				}
				
				//$this->SetMeta('Добавление пунктов меню ('.$dop.')');
				$meta = 'Добавление пунктов меню ('.$dop.')';
			}
			else{
				er_404();
			}
		}
		else{
			switch($type){
				case 'page' : $meta = 'Добавление страницы'; break;
				case 'section' : $meta = 'Добавление раздела'; break;
				case 'link' : $meta = 'Добавление ссылки'; break;
				default : er_404(); break;
			}
		}
		
		//$meta = 'Добавление '.(($type == 'page')?('товара'):('раздела'));
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		//if($type != 'link') $this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('pos', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('slide', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('optimization_text', 'edit');
		$this->tpl->define_dynamic('short_desc', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
		$this->tpl->define_dynamic('tube', 'edit');
		$this->tpl->define_dynamic('help', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		$this->tpl->define_dynamic('date_block', 'edit');
		
		$adress_str = ru2Lat(gp($_POST, 'adress_str', ''), $type);
		$name = gpm($_POST, 'name', '');
		$position = gpm($_POST, 'position', '9999');
		$header = gpm($_POST, 'header', '');
		$title = gpm($_POST, 'title', '');
		$keywords = gpm($_POST, 'keywords', '');
		$description = gpm($_POST, 'description', '');
		$preview = gpm($_POST, 'preview', '');
		$body = gpm($_POST, 'body', '');
		$tube = gpm($_POST, 'tube', '');
		$date = gpm($_POST, 'date', date('d-m-Y'));
		
		$visible = gpm($_POST, 'visible', '1');
		$optimizationText = gpm($_POST, 'optimization_text', '');
		$visible_s = '';
		if ($visible == 1) {
			$visible_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$visible_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		$slide = gpm($_POST, 'slide', (($type == 'section')?('1'):('0')));
		$slide_s = '';
		if($slide == 1){
			$slide_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$slide_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		
		if(!ctype_digit(end($this->w))){
			
			$this->way = ' <span>&rarr;</span> <a href="'.$this->langUrl.'/admin/menu/'.end($this->w).'">'.((end($this->w) == 'horizontal')?'Горизонтальное':'Вертикальное').' меню</a>';
			$location = end($this->w);
			$level = 0;
		}
		elseif(ctype_digit(end($this->w))){
			$location = 'none';
			$level = end($this->w);
		}
		
		if(!empty($_POST)){
			$referer = gp($_POST, 'HTTP_REFERER', '');
			
			if (empty($adress_str)) $this->_err .= 'Не заполнен адрес!<br>';
			else{
				if($type != 'link'){
					$numRow = $this->db->getResult('SELECT count(`id`) FROM `page` WHERE `link` = "'.$adress_str.'" AND `lang` = "'.$this->lang.'"');
					
					if ($numRow > 0) {
						$this->_err .= 'Элемент с таким адресом уже существует!<br>';
					}
				}
			}
			if (!$name) $this->_err .= 'Не указано название!<br>';
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
		}
		
		if(!empty($_POST) && !$this->_err){
		    $date = explode('-', $date);
			
			$date = mktime(date('H'), date('i'), date('s'), $date[1], $date[0], $date[2]);
		    
			$content = '';
			
			$this->db->setQuery('INSERT INTO `page`
			(`name`, `link`, `type`, `menu`, `slide`, `position`, `preview`, `description`, `level`, `visibility`, `metah1`, `metatitle`, `metakeywords`, `metadescription`, `lang`, `tube`, `date`, `optimization_text`) VALUES
			("'.$name.'", "'.$adress_str.'", "'.$type.'", "'.$location.'", "'.$slide.'", "'.$position.'", "'.addslashes($preview).'", "'.addslashes($body).'", "'.$level.'", "'.$visible.'", "'.$header.'", "'.$title.'", "'.$keywords.'", "'.$description.'", "'.$this->lang.'", "'.addslashes($tube).'", "'.$date.'", "'.addslashes($optimizationText).'")');
			
			$id = $this->db->getLastInsertId();
			
			if(isset($_FILES['pic']) && $_FILES['pic']['size'] > 0){
				$pic = $this->uploadSinglePic('/img/pages'.$this->langUrl.'/', $id.'-'.$_FILES['pic']['name']);
				if(!empty($pic['err'])){
					$this->err .= $pic['err'];
					
					$pic = '';
				}
				else{
					$this->setQuery('UPDATE `page` SET `pic` = "'.$pic['pic'].'" WHERE `id` = '.$id);
				}
			}
			
			
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){
				
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);
				}
				
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    	
			    	@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
			        $this->db->setQuery('UPDATE `news` SET `page` = "'.$id.'-'.$_FILES['collage']['name'].'" WHERE `id` = '.$id);
			    }
				
			}
			
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/';
						
			if($content != '') $content = "Данные изменены.<br>При загрузке коллажа возникли ошибки. Новый коллаж не был загружен.<br><br><div style='color: red'>".$content."</div><meta http-equiv='refresh' content='15;URL=".$referer."'>";
			else $content = "Данные изменены <meta http-equiv='refresh' content='1;URL=".$referer."'>";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if(empty($_POST) || $this->_err){
			$this->tpl->assign(
				array(
					'ADRESS_STR' => $adress_str,
					'NAME' => $name,
					'POSITION' => $position,
					'HEADER' => $header,
					'TITLE' => $title,
					'KEYWORDS' => $keywords,
					'DESCRIPTION' => $description,
					'PREVIEW' => stripslashes($preview),
					'TEXT' => stripslashes($body),
					'TUBE' => $tube,
					'VISIBLE_S' => $visible_s,
					'SLIDE_S' => $slide_s,
					'REFERER' => $referer,
					'OPTIMIZATION_TEXT' =>stripcslashes($optimizationText),
					'DELETE_COLLAGE'=>'',
					'DATE' => $date
				)
			);
			
			$help = array();
			$help[] = array('title' => 'Адрес в URL', 'body' => 'Ссылка в адресной строке.<br /><br />Например: <img src="/img/help/adres.gif">', 'type' => 'link');
			$help[] = array('title' => 'Название', 'body' => 'В соответствующем поле прописывается название страницы, которое будет отображаться в меню и списках страниц.', 'type' => 'name');
			//$help[] = array('title' => 'Изображение к новости', 'body' => 'Вставив нужную Вам картинку с помощью кнопки "Обзор", справа от нее отобразится текст.', 'type' => 'pic');
			$help[] = array('title' => 'Дата', 'body' => 'В этом поле указывается дата создания/редактирования новости.', 'type' => 'date');
			$help[] = array('title' => 'Краткое описание', 'body' => 'В этом поле прописывается краткое описание страницы/раздела/новости.', 'type' => 'preview');
			$help[] = array('title' => 'Header', 'body' => 'В соответствующем поле прописывается заголок страницы.<br /><br />Например: <img src="/img/help/header.gif">', 'type' => 'header');
			$help[] = array('title' => 'Title', 'body' => 'Заголовок окна броузера.<br /><br />Например: <img src="/img/help/title.gif">', 'type' => 'title');
			$help[] = array('title' => 'Keywords', 'body' => 'Ключевые слова для сайта/страницы сайта', 'type' => 'keywords');
			$help[] = array('title' => 'Description', 'body' => 'Самые важные фразы, характеризующие тематику сайта/страницы.', 'type' => 'description');
			$help[] = array('title' => 'Видимость', 'body' => 'Если в выпадающем поле выбрать "Да", то страница будет видна всем пользователям.</br>Если "Нет" - только администратору сайта.', 'type' => 'visibility');
			$help[] = array('title' => 'Текст', 'body' => 'Полный текст (страницы, новости и т.п.).', 'type' => 'body');
			$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'position');
			$help[] = array('title' => 'Выпадающее', 'body' => 'Если выбрать "Да", то будут отображаться подпункты горизонтального/вертикального меню.<br />Если "Нет" - подпункты горизонтального/вертикального меню отображаться не будут.', 'type' => 'slide');
			
			//$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'position');
			
			//$help[] = array('title' => 'Артикул', 'body' => 'Буквенное -и(или) цифровое обозначение.', 'type' => 'artikul');
						
			for ($i=0; $i<11; $i++) {
			    $this->tpl->assign(
                    array(
                        'HELP_TITLE' => $help[$i]['title'],
                        'HELP_TEXT' => $help[$i]['body'],
                        'HELP_TYPE' => $help[$i]['type']
                    )
			    );
			    
			    $this->tpl->parse('CONTENT', '.help');
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
			//if($type != 'link') $this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.pos');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.date_block');
			if ($type != 'link') $this->tpl->parse('CONTENT', '.visible');
			if ($type != 'link' && $type != 'page' && $location != 'none') $this->tpl->parse('CONTENT', '.slide');
			if ($type != 'link') $this->tpl->parse('CONTENT', '.meta');
			if ($type != 'link') $this->tpl->parse('CONTENT', '.optimization_text');
			$this->tpl->parse('CONTENT', '.short_desc');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.body');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.tube');
			$this->tpl->parse('CONTENT', '.end');
		}
		return true;
	}

	public function addlink() {
		$this->addpages('link');
		return true;
	}
	
	public function addpage() {
		$this->addpages('page');
		return true;
	}
	
	public function addsection() {
		$this->addpages('section');
		return true;
	}
	
	public function editpages() {
		$id = end($this->w);
		
		if(!ctype_digit($id)){
			er_404();
			exit;
		}
		
		$q = $this->db->getRow('SELECT * FROM `page` WHERE `id` = '.$id);
		
		//if($this->db->getNumRows() != 1){
		if (!$q) {
		    er_404();
			exit();
		}
		
		if($id == 1) $meta = 'Редактирование главной страницы';
		elseif($q['level'] == 0) $meta = 'Редактирование пунктов меню';
		else $meta = 'Редактирование разделов сайта';
		
		//$meta = 'Добавление '.(($type == 'page')?('товара'):('раздела'));
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		
		$pic = '';
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('optimization_text', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
		$this->tpl->define_dynamic('tube', 'edit');
		$this->tpl->define_dynamic('help', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		$this->tpl->define_dynamic('date_block', 'edit');
		
		if($id != 1){
			$this->tpl->define_dynamic('adress', 'edit');
			//if($q['type'] != 'link') $this->tpl->define_dynamic('pic', 'edit');
			//if($q['type'] != 'link') $this->tpl->define_dynamic('hpic', 'edit');
			$this->tpl->define_dynamic('pos', 'edit');
			$this->tpl->define_dynamic('visible', 'edit');
			$this->tpl->define_dynamic('slide', 'edit');
			$this->tpl->define_dynamic('short_desc', 'edit');
			
			$adress_str = gpm($q, 'link', '');
			$menu = gpm($q, 'menu', 'none');
			$position = gpm($q, 'position', '9999');
			$preview = gpm($q, 'preview', '');
			$slide = (gpm($q, 'slide') == 1) ? 1:0;
			$visible = gpm($q, 'visibility');
			$date = convertDate(gpm($q, 'date', date('d-m-Y')), 'd-m-Y');
			$date = convertDate(gpm($q, 'date', mktime()), "d-m-Y");
		}
		else{
			$adress_str = 'mainpage';
			$position = 9999;
			$preview = '';
			$slide = 0;
			$visible = 1;
		}
		
		$type = (gpm($q, 'type') != '') ? gpm($q, 'type'):'page';
		$name = gpm($q, 'name', '');
		$header = gpm($q, 'metah1', '');
		$title = gpm($q, 'metatitle', '');
		$keywords = gpm($q, 'metakeywords', '');
		$description = gpm($q, 'metadescription', '');
		$body = gpm($q, 'description', '');
		$tube = gpm($q, 'tube', '');
		$optimizationText = gpm($q, 'optimization_text', '');
		$date = convertDate(gpm($q, 'date', mktime()), "d-m-Y");
		
		if(!empty($_POST)){
			$referer = gp($_POST, 'HTTP_REFERER', '');
			
			if($id != 1){
				$adress_str = ru2Lat(gp($_POST, 'adress_str', ''), $type);
				$position = gpm($_POST, 'position', '');
				$preview = gpm($_POST, 'preview', '');
				$slide = gpm($_POST, 'slide', '0');
				$visible = gpm($_POST, 'visible', '1');
			}
			else{
				$adress_str = 'mainpage';
				$position = 9999;
				$preview = '';
				$slide = 0;
				$visible = 1;
			}
			
			$name = gpm($_POST, 'name', '');
			$header = gpm($_POST, 'header', '');
			$title = gpm($_POST, 'title', '');
			$keywords = gpm($_POST, 'keywords', '');
			$description = gpm($_POST, 'description', '');
			$body = gpm($_POST, 'body', '');
			$tube = gpm($_POST, 'tube', '');
			$alt = gpm($_POST, 'alt');
			$optimizationText = gpm($_POST, 'optimization_text');
			$date = gpm($_POST, 'date', date('d-m-Y'));		
			
			if($id != 1){
				if (empty($adress_str)) $this->_err .= 'Не заполнен адрес!<br>';
				else{
					if($type != 'link'){
						$numRow = $this->db->getResult('SELECT count(`id`) FROM `page` WHERE `link` = "'.$adress_str.'" AND `lang` = "'.$this->lang.'" AND `id` <> '.$id);
						
						if ($numRow > 0) {
							$this->_err .= 'Элемент с таким адресом уже существует!<br>';
						}
					}
				}
			}
			if (!$name) $this->_err .= 'Не указано название!<br>';
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
		}
		
		$visible_s = '';
		if($visible == 1){
			$visible_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$visible_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		
		$slide_s = '';
		if($slide == 1){
			$slide_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$slide_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		$_slide = ($q['type'] != 'link' && $q['type'] != 'page' && $slide == 1) ? '`slide` = "1", ':'`slide` = "0", ';
		$collageFileName = '';
		if(!empty($_POST) && !$this->_err){
						
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){
				
				$collageFileName = '';
				
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);
				}
				
				
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    	
			    	@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
			        $collageFileName = $id.'-'.$_FILES['collage']['name'];
			    }
				
			}
			
		    $date = explode('-', $date);
			
			$date = mktime(date('H'), date('i'), date('s'), $date[1], $date[0], $date[2]);
		  
			$this->db->setQuery('
			UPDATE `page` SET '.$_slide.'`name` = "'.$name.'", `link` = "'.$adress_str.'", `type` = "'.$type.'", `position` = "'.$position.'", `optimization_text` = "'.addslashes($optimizationText).'", `collage`="'.$collageFileName.'", `preview` = "'.addslashes($preview).'", `description` = "'.addslashes($body).'", `visibility` = "'.$visible.'", `metah1` = "'.$header.'", `metatitle` = "'.$title.'", `metadescription` = "'.$description.'", `metakeywords` = "'.$keywords.'", `tube` = "'.addslashes($tube).'", `date`="'.$date.'" WHERE `id` = '.$id);
			
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/';
			
			if($id == 1) $referer = '/';
			
			$content = "Данные изменены <meta http-equiv='refresh' content='1;URL=".$referer."'>";
			//$content = "Данные изменены";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
			
			
		
		}        
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if(empty($_POST) || $this->_err){
			if(!empty($referer) && $referer != '{REFERER}'){
				$this->way = ' <span>&rarr;</span> <a href="'.$referer.'">'.$q['name'].'</a>';
			}
			if (empty($date)) {
				$date = date('d-m-Y');
			}
			$this->tpl->assign(
				array(
					'ADRESS_STR' => $adress_str,
					'NAME' => $name,
					'POSITION' => $position,
					'HEADER' => $header,
					'TITLE' => $title,
					'KEYWORDS' => $keywords,
					'DESCRIPTION' => $description,
					'PREVIEW' => stripslashes($preview),
					'TEXT' => stripslashes($body),
					'TUBE' => $tube,
					'VISIBLE_S' => $visible_s,
					'SLIDE_S' => $slide_s,
					'REFERER' => $referer,
					'OPTIMIZATION_TEXT' => stripslashes($optimizationText),
					'DELETE_COLLAGE'=>(!empty($q['collage']) ? " Удалить коллаж <a onclick=\"return confirm('Вы уверены что хотите удалить?'); return false;\" title=\"Удалить\" href=\"/admin/deletepagecollage/$id\"><img height=\"12\" width=\"12\" src=\"/img/admin_icons/delete.png\"></a>" : ''),
					'DATE' => $date
				)
			);
			
			$help = array();
			$help[] = array('title' => 'Адрес в URL', 'body' => 'Ссылка в адресной строке.<br /><br />Например: <img src="/img/help/adres.gif">', 'type' => 'link');
			$help[] = array('title' => 'Название', 'body' => 'В соответствующем поле прописывается название страницы, которое будет отображаться в меню и списках страниц.', 'type' => 'name');
			//$help[] = array('title' => 'Изображение к новости', 'body' => 'Вставив нужную Вам картинку с помощью кнопки "Обзор", справа от нее отобразится текст.', 'type' => 'pic');
			//$help[] = array('title' => 'Дата', 'body' => 'В этом поле указывается дата создания/редактирования новости.', 'type' => 'date');
			$help[] = array('title' => 'Краткое описание', 'body' => 'В этом поле прописывается краткое описание страницы/раздела/новости.', 'type' => 'preview');
			$help[] = array('title' => 'Header', 'body' => 'В соответствующем поле прописывается заголок страницы.<br /><br />Например: <img src="/img/help/header.gif">', 'type' => 'header');
			$help[] = array('title' => 'Title', 'body' => 'Заголовок окна броузера.<br /><br />Например: <img src="/img/help/title.gif">', 'type' => 'title');
			$help[] = array('title' => 'Keywords', 'body' => 'Ключевые слова для сайта/страницы сайта', 'type' => 'keywords');
			$help[] = array('title' => 'Description', 'body' => 'Самые важные фразы, характеризующие тематику сайта/страницы.', 'type' => 'description');
			$help[] = array('title' => 'Видимость', 'body' => 'Если в выпадающем поле выбрать "Да", то страница будет видна всем пользователям.</br>Если "Нет" - только администратору сайта.', 'type' => 'visibility');
			$help[] = array('title' => 'Текст', 'body' => 'Полный текст (страницы, новости и т.п.).', 'type' => 'body');
			$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'position');
			$help[] = array('title' => 'Выпадающее', 'body' => 'Если выбрать "Да", то будут отображаться подпункты горизонтального/вертикального меню.<br />Если "Нет" - подпункты горизонтального/вертикального меню отображаться не будут.', 'type' => 'slide');
			
			//$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'position');
			
			//$help[] = array('title' => 'Артикул', 'body' => 'Буквенное -и(или) цифровое обозначение.', 'type' => 'artikul');
						
			for ($i=0; $i<11; $i++) {
			    $this->tpl->assign(
                    array(
                        'HELP_TITLE' => $help[$i]['title'],
                        'HELP_TEXT' => $help[$i]['body'],
                        'HELP_TYPE' => $help[$i]['type']
                    )
			    );
			    
			    $this->tpl->parse('CONTENT', '.help');
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			if($id != 1) $this->tpl->parse('CONTENT', '.adress');
			//if($id != 1) if($q['type'] != 'link') $this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.name');
			if($id != 1) $this->tpl->parse('CONTENT', '.pos');
			if($type == 'page') $this->tpl->parse('CONTENT', '.date_block');
			if($id != 1) $this->tpl->parse('CONTENT', '.visible');
			if($id != 1) if($type == 'section' && $menu != 'none') $this->tpl->parse('CONTENT', '.slide');
			if ($type != 'link') $this->tpl->parse('CONTENT', '.meta');
			if ($type != 'link') $this->tpl->parse('CONTENT', '.optimization_text');
			if($id != 1 && $type != 'link') $this->tpl->parse('CONTENT', '.short_desc');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.body');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.tube');
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}

	public function deletepages() {
		$id = end($this->w);
		
		if(!ctype_digit($id)){
			er_404();
			exit;
		}
		
		$page = $this->db->getRow('SELECT * FROM `page` WHERE `id` = '.$id);
		
		if($this->db->getNumRows() != 1){
		    er_404();
			exit();
		}
		
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$page['collage'])) {
			@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$page['collage'], 0666);
			@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$page['collage']);
		}
		
		switch($page['type']) {
			case 'page':
				if(file_exists('./img/pages'.$this->langUrl.'/'.$page['pic'])) {
					@unlink('./img/pages'.$this->langUrl.'/'.$page['pic']);
				}
				
				$this->db->setQuery('DELETE FROM `page` WHERE `id` = '.$id);
				break;
		
			case 'link':
				$this->db->setQuery('DELETE FROM `page` WHERE `id` = '.$id);
				break;
		
			case 'section':
				$sub = $this->db->getAllRecords('SELECT * FROM `page` WHERE `level` = '.$id);
				
				if(sizeof($sub) > 0){
					if(!gpm($_GET, 'confirm', false)){
						
						$confirm = '
						<script>
						<!--
						if(confirm("Вы действительно желаете удалить раздел содержащий вложенные элементы ?")){
							location.href = "?confirm=1";
						}
						-->
						</script>
						';
						$this->tpl->assign(array('CONTENT' => $confirm));
						return true;
					}
					else{
						if(file_exists('./img/pages'.$this->langUrl.'/'.$page['pic'])) {
							@unlink('./img/pages'.$this->langUrl.'/'.$page['pic']);
						}
						
						$this->db->setQuery('DELETE FROM `page` WHERE `id` = '.$id);
						
						$this->deleteSubPages($id);
					}
				}
				else{
					if(file_exists('./img/pages'.$this->langUrl.'/'.$page['pic'])) {
						@unlink('./img/pages'.$this->langUrl.'/'.$page['pic']);
					}
					
					$this->db->setQuery('DELETE FROM `page` WHERE `id` = '.$id);
				}
		
				
				break;
		
				default:
					er_404();
					break;
		}

		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
			$content = "Элемент удалён.<meta http-equiv='refresh' content='5;URL=$referer'>";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		return true;
	}
	
	private function deleteSubPages($id = 0){
		if($id != 0){
			$q = $this->db->getAllRecords('SELECT * FROM `page` WHERE `level` = '.$id);
			
			$size = sizeof($q);
			
			if($size > 0){
				for($i=0; $i<$size; $i++){
					if($q[$i]['type'] == 'section'){
						$this->deleteSubPages($q[$i]['id']);
					}
					
					if(file_exists('./img/pages'.$this->langUrl.'/'.$q[$i]['pic'])) {
						@unlink('./img/pages'.$this->langUrl.'/'.$q[$i]['pic']);
					}
					
					$this->db->setQuery('DELETE FROM `page` WHERE `id` = '.$q[$i]['id']);
				}
			}
		}
	}
	/* End Menu & Page Section */
	
	/*Службы доставки
	
	public function delivery() {
		$this->setMeta('Службы доставки', null, null, 'Службы доставки', ' <span>&rarr;</span> Службы доставки');
		
		$this->tpl->define_dynamic('_delivery', 'adm/delivery.tpl');		
		$this->tpl->define_dynamic('delivery', '_delivery');
		$this->tpl->define_dynamic('delivery_item', 'delivery');		
		
		$delivery = $this->db->getAllRecords('SELECT * FROM `delivery`');
		
		$size = sizeof($delivery);
		
		
		if ($size <= 0) {
			$this->tpl->parse('DELIVERY_ITEM', 'null');
			//$this->tpl->parse('ORDERS_ITEM', 'null');
		}
		else {
			for ($i=0; $i<$size; $i++) {
				$this->tpl->assign(
					array(
						'ID'   =>  $delivery[$i]['id'],
						'VISIBLE' =>($delivery[$i]['visibility'] == '1' ? 'Видимый' : 'Скрытый'),
						'NAME' => $delivery[$i]['name']					
					)
				);
				
				$this->tpl->parse('DELIVERY_ITEM', '.delivery_item');
			}
			
			//$this->tpl->parse('ORDERS_EMPTY', 'null');
		}
		
		
		$this->tpl->parse('CONTENT', '.delivery');
		
		
		return true;
	}
	
	public function adddelivery() {
		
		$meta = 'Добавление службы доставки';
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		//$idm = gp($this->w, 2);
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('name', 'edit');		
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('end', 'edit');		
		
		$name = gpm($_POST, 'name', '');		
		
		$visible = gpm($_POST, 'visible', '1');
        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
            
        }        
				
		if(!empty($_POST)){
			$referer = gp($_POST, 'HTTP_REFERER', '');		
			$numRow = $this->db->getResult('SELECT count(`id`) FROM `delivery` WHERE `name` = "'.$name.'"');
			if ($numRow > 0) {
				$this->_err .= 'Элемент с таким именем уже существует!<br>';
			}			
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/delivery');
		}
		
		if(!empty($_POST) && !$this->_err){           
			
			$this->db->setQuery('INSERT INTO `delivery` ( `name`, `visibility`)	VALUES ("'.$name.'", "'.$visible.'" )');						
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/admin/delivery';			
			$content = "Элемент добавлен. <meta http-equiv='refresh' content='1;URL=$referer'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		if ($this->_err) {
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if (empty($_POST) || $this->_err) {
			$this->tpl->assign(
				array(
					'VISIBLE_S' => $visible_s,
					'NAME' => $name,				
				)
			);
			
			$help = array();			
			//$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'body');
			//$help[] = array('title' => 'Артикул', 'body' => 'Буквенное -и(или) цифровое обозначение.', 'type' => 'body');
						
			for ($i=0; $i<count($help); $i++) {
			    $this->tpl->assign(
                    array(
                        'HELP_TITLE' => $help[$i]['title'],
                        'HELP_TEXT' => $help[$i]['body'],
                        'HELP_TYPE' => $help[$i]['type']
                    )
			    );
			    
			    $this->tpl->parse('CONTENT', '.help');
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.visible');			
			$this->tpl->parse('CONTENT', '.end');
		}
		return true;
	}
	
	public function editdelivery() {
		$id = end($this->w);
		
		if(!ctype_digit($id)){
			er_404();
			exit;
		}
		$meta = 'Добавление службы доставвки';
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		//$idm = gp($this->w, 2);
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('name', 'edit');		
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('end', 'edit');		
		
		$name = gpm($_POST, 'name', '');		
		
		$visible = gpm($_POST, 'visible', '1');
        $visible_s = '';
            
				
		
		$referer = gp($_POST, 'HTTP_REFERER', '');		
					
		$deliveryValues = $this->db->getAllRecords('SELECT * FROM `delivery` WHERE `id` = "'.$id.'"');
		if (count($deliveryValues) <= 0) {
			$this->_err .= 'Элемента с таким id не существует!<br>';
		}
		
		if(!empty($_POST) && !$this->_err){           
			
			$this->db->setQuery('UPDATE `delivery` SET  `name` = "'.$name.'", `visibility` =  "'.$visible.'" WHERE `id` = "'.$id.'"');						
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/admin/delivery';			
			$content = "Данные изменены . <meta http-equiv='refresh' content='1;URL=$referer'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		if ($this->_err) {
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if ((empty($_POST) || $this->_err) && isset($deliveryValues[0])) {
		
			if ($deliveryValues[0]['visibility'] == '1') {
            	$visible_s .= "<option value='1' selected>Да</option>
            		<option value='0'>Нет</option>";
        	} else {
            	$visible_s .= "<option value='1'>Да</option>
            	<option value='0' selected>Нет</option>";
            
        	}    
			$this->tpl->assign(
				array(
					'VISIBLE_S' => $visible_s,
					'NAME' => $deliveryValues[0]['name'],				
				)
			);
			
			$help = array();			
			//$help[] = array('title' => 'Позиция', 'body' => 'Порядковый номер страницы/раздела, используемый для сортировки.', 'type' => 'body');
			//$help[] = array('title' => 'Артикул', 'body' => 'Буквенное -и(или) цифровое обозначение.', 'type' => 'body');
						
			for ($i=0; $i<count($help); $i++) {
			    $this->tpl->assign(
                    array(
                        'HELP_TITLE' => $help[$i]['title'],
                        'HELP_TEXT' => $help[$i]['body'],
                        'HELP_TYPE' => $help[$i]['type']
                    )
			    );
			    
			    $this->tpl->parse('CONTENT', '.help');
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.visible');			
			$this->tpl->parse('CONTENT', '.end');
		}
		return true;
	}

	public function deletedelivery() {
		if(!$id = gp($this->w, 2)) return false;
		$length = $this->db->getRow('SELECT * FROM `delivery` WHERE `id` = '.$id);
		
		if(count($length) <= 0){
			$this->_err .= 'Элемента с таким id не существует!<br>';
		}		
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}else{
			$query = 'DELETE FROM `delivery` WHERE `id` = '.$id;		
			$this->db->setQuery($query);
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/admin/delivery');
			$content = "Элемент удалён.<meta http-equiv='refresh' content='1;URL=$referer'>";			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		return true;
	}
	*/
	/* Catalog Section */
	
	
	private function addCat($type) {
	    $id = end($this->w);
		
		if (!ctype_digit($id) || $id < 0) {
			er_404();
			exit;
		}
		
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('pos', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		
		if ($type == 'page') $this->tpl->define_dynamic('artikul', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('proizvoditel', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('cost', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('hit', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('new', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('used_complete', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('recommended_products', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('gallery_pic', 'edit');		
		if ($type == 'page') $this->tpl->define_dynamic('body', 'edit');		
		
		if ($type == 'page') $this->tpl->define_dynamic('packing_size', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('power', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('consumption', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('brightness', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('cylinder_type', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('weight', 'edit');
		
		//if ($q['type'] == 'section' && $q['level'] == 0) $this->tpl->define_dynamic('slide', 'edit');
		$this->tpl->define_dynamic('meta', 'edit'); 
		$this->tpl->define_dynamic('optimization_text', 'edit'); 
		$this->tpl->define_dynamic('short_desc', 'edit');
		if ($type == 'page') $this->tpl->define_dynamic('specifications', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$referer = gp($_POST, 'HTTP_REFERER', '');
			
		$adress_str = ru2Lat(gp($_POST, 'adress_str', ''), $type);
		$position = gpm($_POST, 'position', '9999');
		$preview = gpm($_POST, 'preview', '');
		$artikul = gpm($_POST, 'artikul', '');
		$proizvoditel = gpm($_POST, 'proizvoditel', '');
		$cost = gpm($_POST, 'cost', '0');
		//$slide = gpm($_POST, 'slide', '0');
		$visible = gpm($_POST, 'visible', '1');
		$name = gpm($_POST, 'name', '');
		$header = gpm($_POST, 'header', '');
		$title = gpm($_POST, 'title', '');
		$keywords = gpm($_POST, 'keywords', '');
		$description = gpm($_POST, 'description', '');
		$optimizationText = gpm($_POST, 'optimization_text', '');
		
		$body = gpm($_POST, 'text', '');
		$hit = gpm($_POST, 'hit', 0);
		$new = gpm($_POST, 'new', 0);
		$alt = gpm($_POST, 'alt');
		$body = gpm($_POST, 'body', '');
	
		$packing_size = gpm($_POST, 'packing_size', '');
		$consumption = gpm($_POST, 'consumption', '');
		$power = gpm($_POST, 'power', '');
		$brightness = gpm($_POST, 'brightness', '');
		$cylinder_type = gpm($_POST, 'cylinder_type', '');
		$weight = gpm($_POST, 'weight', '');
		
		$used_complete = gpm($_POST, 'used_complete', '');
		$recommended_products = gpm($_POST, 'recommended_products', '');
		
		
		$visible_s = '';
		if ($visible == 1) {
			$visible_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$visible_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		
		
		
		$hit_s = '';
		if ($hit == 0) {
		    $hit_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		else {
		    $hit_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		
		$new_s = '';
		if ($new == 0) {
		    $new_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		else {
		    $new_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		
		$meta = 'Добавление '.(($type == 'page')?('товара'):('раздела'));
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		
		if(!empty($_POST)){
			$referer = gp($_POST, 'HTTP_REFERER', '');
			
			if (empty($adress_str)) $this->_err .= 'Не заполнен адрес!<br>';
			
			$numRow = $this->db->getResult('SELECT count(`id`) FROM `catalog` WHERE `link` = "'.$adress_str.'"');
					
			if ($numRow > 0) {
				$this->_err .= 'Элемент с таким адресом уже существует!<br>';
			}
			
			if (!$name) $this->_err .= 'Не указано название!<br>';
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', '/');
		}
		
		if(!empty($_POST) && !$this->_err){
			
			
			$this->db->setQuery('INSERT INTO `catalog`
			(`name`, `link`, `type`, `recommended_products`,  `used_completed`, `packing_size`, `consumption`, `power`, `brightness`, `cylinder_type`, `weight`, `position`, `preview`, `description`, `level`, `visibility`, `metah1`, `metatitle`, `metakeywords`, `metadescription`,  `artikul`, `proizvoditel`, `cost`, `hit`, `new`, `optimization_text`) VALUES
			("'.$name.'", "'.$adress_str.'", "'.$type.'", "'.$recommended_products.'", "'.$used_complete.'", "'.$packing_size.'", "'.$consumption.'", "'.$power.'",  "'.$brightness.'", "'.$cylinder_type.'", "'.$weight.'", "'.$position.'", "'.addslashes($preview).'", "'.addslashes($body).'", "'.$id.'", "'.$visible.'", "'.$header.'", "'.$title.'", "'.$keywords.'", "'.$description.'", "'.$artikul.'", "'.$proizvoditel.'", "'.$cost.'", "'.$hit.'", "'.$new.'", "'.addslashes($optimizationText).'")
			');
			
			$id = $this->db->getLastInsertId();
			
			
			if(isset($_FILES['gallery_pic']) && count($_FILES['gallery_pic']) > 0 ){				
				$catGallery = $this->db->getAllRecords('SELECT `id`, `pic` FROM `catalog_gallery` WHERE `catalog_id` = '.$id);
				
				$picsArr = array();							
				for ($i = 0; $i < count($_FILES['gallery_pic']); $i++) {				
					if ($_FILES['gallery_pic']['size'][$i] > 0) {
					
						if ( $this->uploadCatPic($_FILES['gallery_pic']['tmp_name'][$i], './img/catalog/gallery/small/'.$id.'-'.$_FILES['gallery_pic']['name'][$i], 91, 91) &&
				   			@copy($_FILES['gallery_pic']['tmp_name'][$i], './img/catalog/gallery/big/'.$id.'-'.$_FILES['gallery_pic']['name'][$i]) ) {				    			
			     			$this->db->setQuery('INSERT INTO `catalog_gallery` (`catalog_artikul`, `pic`, `title`) VALUES ("'.$artikul.'", "'.$id.'-'.$_FILES['gallery_pic']['name'][$i].'", "'.(isset($_POST['gallery_text'][$i]) ? $_POST['gallery_text'][$i] : '').'")');
				 		}	
			    	}	
				}
			}
			
			
			
			
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){
				
				$collageFileName = '';
				
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);
				}
				
				
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    	
			        $this->db->setQuery('UPDATE `catalog` SET `collage` = "'.$id.'-'.$_FILES['collage']['name'].'" WHERE `id` = '.$id);
			        @chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
			    }
				
			}
			
			
			
			
			if(!empty($_FILES) && $_FILES['pic']['size'] > 0){ 
				
				
				
				if (
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/catalog/small/'.$id.'-'.$_FILES['pic']['name'], 186, 186) &&
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/catalog/big/'.$id.'-'.$_FILES['pic']['name'], 400, 367) && 
				    @copy($_FILES['pic']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$id.'-'.$_FILES['pic']['name'])) {
				    	
			    	@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$id.'-'.$_FILES['pic']['name'], 0666);
			        $this->db->setQuery('UPDATE `catalog` SET `pic` = "'.$id.'-'.$_FILES['pic']['name'].'" WHERE `id` = '.$id);
			    }
			}
			
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/';
						
			$content = "Данные изменены <meta http-equiv='refresh' content='1;URL=".$referer."'>";
			//$content = "Данные изменены";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if(empty($_POST) || $this->_err){
			$this->tpl->assign(
				$this->tpl->assign(
				array(
					'ADRESS_STR' => $adress_str,
					'NAME' => stripslashes($name),
					'POSITION' => $position,
					'HEADER' => stripslashes($header),
					'ARTIKUL' => $artikul,
					'PROIZVODITEL' => stripslashes($proizvoditel),
					'OPTIOMOZATION_TEXT' => stripslashes($optimizationText),
					'COST' => $cost,
					'TITLE' => stripslashes($title),
					'KEYWORDS' => stripslashes($keywords),
					'DESCRIPTION' => stripslashes($description),
					'PREVIEW' => stripslashes($preview),
					
					'WEIGHT' => $weight,
					'CYLINDER_TYPE' => $cylinder_type,
					'BRIGHTNESS' => $brightness,
					'CONSUMPTION' => $consumption,
					'PACKING_SIZE' => $packing_size,
					'POWER' => $power,
					
					'TEXT' => stripslashes($body),
					'VISIBLE_S' => $visible_s,
					//'SLIDE_S' => $slide_s,
					'HIT_S' => $hit_s,
					'NEW_S' => $new_s,
					'RECOMENDED_PRODUCTS'=>'',
					'USED_COMPLATE'=>'',
					'DELETE_COLLAGE'=>'',
					'REFERER' => $referer
				)
			));
			
			//packing_size consumption brightness cylinder_type weight
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.name');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.artikul');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.proizvoditel');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.cost');
			
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.packing_size');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.consumption');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.power');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.brightness');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.cylinder_type');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.weight');
			
		//	if ($type == 'page') $this->tpl->parse('CONTENT', '.hit');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.new');
			$this->tpl->parse('CONTENT', '.pos');
			$this->tpl->parse('CONTENT', '.visible');
			//fif ($type == 'section' && $q['level'] == 0) $this->tpl->parse('CONTENT', '.slide');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.optimization_text');
			$this->tpl->parse('CONTENT', '.short_desc');			
		
			if ($type == 'page') $this->tpl->parse('CONTENT', '.used_complete');			
			if ($type == 'page') $this->tpl->parse('CONTENT', '.recommended_products');			
			
			if ($type == 'page') $this->tpl->parse('CONTENT', '.gallery_pic');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.body');
			
			$this->tpl->parse('CONTENT', '.end');
		}
	}
	
	public function addcatpage() {
	    $this->addCat('page');
		return true;
	}
	
	public function addcatsection() {
	    $this->addCat('section');
		return true;
	}
	
	public function editcatalog() {
	    $id = end($this->w);
		
		if(!ctype_digit($id)){
			er_404();
			exit;
		}
		
		$q = $this->db->getRow('SELECT * FROM `catalog` WHERE `id` = '.$id);
		
		if($this->db->getNumRows() != 1){
		    er_404();
			exit();
		}
		
		if($q['type'] == 'section') $meta = 'Редактирование раздела';
		else $meta = 'Редактирование товара';
		
		$this->setMeta($meta, null, null, $meta, '<span>&rarr;</span> '.$meta);
		$pic = '';
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('pos', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		
		if ($q['type'] == 'page') $this->tpl->define_dynamic('artikul', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('proizvoditel', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('cost', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('hit', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('new', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('used_complete', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('recommended_products', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('body', 'edit');		
		if ($q['type'] == 'page') $this->tpl->define_dynamic('gallery_pic', 'edit');
		
		if ($q['type'] == 'page') $this->tpl->define_dynamic('packing_size', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('power', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('consumption', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('brightness', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('cylinder_type', 'edit');
		if ($q['type']== 'page') $this->tpl->define_dynamic('weight', 'edit');
		
		//if ($q['type'] == 'section' && $q['level'] == 0) $this->tpl->define_dynamic('slide', 'edit');
		$this->tpl->define_dynamic('meta', 'edit'); 
		$this->tpl->define_dynamic('optimization_text', 'edit'); 
		$this->tpl->define_dynamic('short_desc', 'edit');
		if ($q['type'] == 'page') $this->tpl->define_dynamic('specifications', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$adress_str = gpm($q, 'link', '');
		$menu = gpm($q, 'menu', 'none');
		$position = gpm($q, 'position', '9999');
		$preview = gpm($q, 'preview', '');
		//$slide = (gpm($q, 'slide') == 1) ? 1:0;
		$visible = gpm($q, 'visibility');
		$artikul = gpm($q, 'artikul', '');
		$proizvoditel = gpm($q, 'proizvoditel', '');
		$cost = gpm($q, 'cost', '0');
		$type = gpm($q, 'type');
		$name = gpm($q, 'name', '');
		$header = gpm($q, 'metah1', '');
		$title = gpm($q, 'metatitle', '');
		$keywords = gpm($q, 'metakeywords', '');
		$description = gpm($q, 'metadescription', '');
		$all_text = gpm($q, 'all_text', '');
		$body = gpm($q, 'description', ''); // Сохраняет в поле description
		$hit = gpm($q, 'hit', 0);
		$new = gpm($q, 'new', 0);
		$used_complete = gpm($q, 'used_completed', '');
		$recommended_products = gpm($q, 'recommended_products', '');
		
		$packing_size = gpm($q, 'packing_size', '');
		$consumption = gpm($q, 'consumption', '');
		$power = gpm($q, 'cpower', '');
		$brightness = gpm($q, 'brightness', '');
		$cylinder_type = gpm($q, 'cylinder_type', '');
		$weight = gpm($q, 'weight', '');
		$optimizationText = gpm($q, 'optimization_text', '');
		
		if(!empty($_POST)){
			$referer = gp($_POST, 'HTTP_REFERER', '');
			
			$adress_str = ru2Lat(gp($_POST, 'adress_str', ''), $type);
			$position = gpm($_POST, 'position', '9999');
			$preview = gpm($_POST, 'preview', '');
			$artikul = gpm($_POST, 'artikul', '');
			$proizvoditel = gpm($_POST, 'proizvoditel', '');
			$cost = gpm($_POST, 'cost', '0');
			//$slide = gpm($_POST, 'slide', '0');
			$visible = gpm($_POST, 'visible', '1');
			$name = gpm($_POST, 'name', '');
			$header = gpm($_POST, 'header', '');
			$title = gpm($_POST, 'title', '');
			$keywords = gpm($_POST, 'keywords', '');
			$description = gpm($_POST, 'description', '');
			$all_text = gpm($_POST, 'all_text', '');
			$body = gpm($_POST, 'text', '');
			$hit = gpm($_POST, 'hit', 0);
			$new = gpm($_POST, 'new', 0);
			$alt = gpm($_POST, 'alt');
			$body = gpm($_POST, 'body', '');
			$used_complete = gpm($_POST, 'used_complete', '');
			$recommended_products = gpm($_POST, 'recommended_products', '');
			
			$packing_size = gpm($_POST, 'packing_size', '');
			$consumption = gpm($_POST, 'consumption', '');
			$power = gpm($_POST, 'power', '');
			$brightness = gpm($_POST, 'brightness', '');
			$cylinder_type = gpm($_POST, 'cylinder_type', '');
			$weight = gpm($_POST, 'weight', '');
			$optimizationText = gpm($_POST, 'optimization_text', '');
		
			if (empty($adress_str)) $this->_err .= 'Не заполнен адрес!<br>';
			else{
				if($type != 'link'){
					$numRow = $this->db->getResult('SELECT count(`id`) FROM `catalog` WHERE `link` = "'.$adress_str.'" AND `id` <> '.$id);
					
					if ($numRow > 0) {
						$this->_err .= 'Элемент с таким адресом уже существует!<br>';
					}
				}
			}
			
			if (!$name) $this->_err .= 'Не указано название!<br>';
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
		}
		
		$visible_s = '';
		if($visible == 1){
			$visible_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		else{
			$visible_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		
		
		$hit_s = '';
		if ($hit == 0) {
		    $hit_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		else {
		    $hit_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		
		$new_s = '';
		if ($new == 0) {
		    $new_s .= "<option value='1'>Да</option>
			<option value='0' selected>Нет</option>";
		}
		else {
		    $new_s .= "<option value='1' selected>Да</option>
			<option value='0'>Нет</option>";
		}
		
		
		
		
		if(!empty($_POST) && !$this->_err){
			
			
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){
				
				$collageFileName = '';
				
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);					
				}
				
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    	
			    	@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);			    	
			        $this->db->setQuery('UPDATE `catalog` SET `collage` = "'.$id.'-'.$_FILES['collage']['name'].'" WHERE `id` = '.$id);
			    }
				
			}
			
			
		    $pics = '';
		    if(!empty($_FILES) && $_FILES['pic']['size'] > 0){
				if(is_file($_SERVER['DOCUMENT_ROOT'].'/img/catalog/small/'.$q['pic'])){
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/small/'.$q['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/small/'.$q['pic']);
				}
				
				if(is_file($_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$q['pic'])){
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$q['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$q['pic']);
				}
				
				if(is_file($_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$q['pic'])){
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$q['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$q['pic']);
				}
				
				if(!empty($_FILES) && $_FILES['pic']['size'] > 0){
    				if (
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/catalog/small/'.$id.'-'.$_FILES['pic']['name'], 186, 186) &&
				    $this->uploadCatPic($_FILES['pic']['tmp_name'], './img/catalog/big/'.$id.'-'.$_FILES['pic']['name'], 400, 367) && 
				    @copy($_FILES['pic']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$id.'-'.$_FILES['pic']['name'])) {
    			        $this->db->setQuery('UPDATE `catalog` SET `pic` = "'.$id.'-'.$_FILES['pic']['name'].'" WHERE `id` = '.$id);
    			        $pics = ", `pic` = '".$id.'-'.$_FILES['pic']['name']."'";
    			    }  			  
    			}		
			}
		    
			$recommended_products = str_replace(' ', '', trim($recommended_products));
			$used_complete = str_replace(' ', '', trim($used_complete));
		    //`packing_size` = "'.$packing_size.'", `consumption` = "'.$consumption.'" `brightness` = "'.$brightness.'" `cylinder_type` = "'.$cylinder_type.'",  `weight` = "'.$weight.'",
		    //packing_size consumption brightness cylinder_type weight
			$this->db->setQuery('
			UPDATE `catalog` SET `name` = "'.addslashes($name).'",  `optimization_text` = "'.addslashes($optimizationText).'", `link` = "'.$adress_str.'", `packing_size` = "'.$packing_size.'", `power` = "'.$power.'", `consumption` = "'.$consumption.'", `brightness` = "'.$brightness.'", `cylinder_type` = "'.$cylinder_type.'",  `weight` = "'.$weight.'", `type` = "'.$type.'", `position` = "'.$position.'", `preview` = "'.addslashes($preview).'", `description` = "'.addslashes($body).'", `visibility` = "'.$visible.'", `metah1` = "'.addslashes($header).'", `metatitle` = "'.addslashes($title).'", `metadescription` = "'.addslashes($description).'", `metakeywords` = "'.addslashes($keywords).'", `artikul` = "'.$artikul.'", `proizvoditel` = "'.addslashes($proizvoditel).'", `recommended_products`="'.$recommended_products.'", `used_completed`="'.$used_complete.'", `hit` = "'.$hit.'", `new` = "'.$new.'", `cost` = "'.$cost.'"'.$pics.' WHERE `id` = '.$id);
			
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/';
			
			if($id == 1) $referer = '/';
			
			$content = "Данные изменены <meta http-equiv='refresh' content='1;URL=".$referer."'>";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}        
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		
		
		if(isset($_FILES['gallery_pic']) && count($_FILES['gallery_pic']) > 0 ){				
			$catGallery = $this->db->getAllRecords('SELECT `id`, `pic` FROM `catalog_gallery` WHERE `catalog_artikul` = "'.$artikul.'"');
				
			$picsArr = array();							
			for ($i = 0; $i < count($_FILES['gallery_pic']); $i++) {				
				if ($_FILES['gallery_pic']['size'][$i] > 0) {
					
					if ( $this->uploadCatPic($_FILES['gallery_pic']['tmp_name'][$i], './img/catalog/gallery/small/'.$id.'-'.$_FILES['gallery_pic']['name'][$i], 91, 91) &&
				   		@copy($_FILES['gallery_pic']['tmp_name'][$i], './img/catalog/gallery/big/'.$id.'-'.$_FILES['gallery_pic']['name'][$i]) ) {				    			
			     		$this->db->setQuery('INSERT INTO `catalog_gallery` (`catalog_artikul`, `pic`, `title`) VALUES ("'.$artikul.'", "'.$id.'-'.$_FILES['gallery_pic']['name'][$i].'", "'.(isset($_POST['gallery_text'][$i]) ? $_POST['gallery_text'][$i] : '').'")');
				 	}	
			    }	
			}
		}
		
		  
		if(empty($_POST) || $this->_err){
			if(!empty($referer) && $referer != '{REFERER}'){
				$this->way = ' <span>&rarr;</span> <a href="'.$referer.'">'.$q['name'].'</a>';
			}
			//echo stripslashes($name);
			$this->tpl->assign(
				array(
					'ADRESS_STR' => $adress_str,
					'NAME' => stripslashes($name),
					'POSITION' => $position,
					'HEADER' => stripslashes($header),
					'ARTIKUL' => $artikul,
					'PROIZVODITEL' => stripslashes($proizvoditel),
					'OPTIMIZATION_TEXT' => stripslashes($optimizationText),
					'COST' => $cost,
					'TITLE' => stripslashes($title),
					'KEYWORDS' => stripslashes($keywords),
					'DESCRIPTION' => stripslashes($description),
					'PREVIEW' => stripslashes($preview),
					'ALL_TEXT' =>$all_text,
					'TEXT' => stripslashes($body),
					'VISIBLE_S' => $visible_s,
					//'SLIDE_S' => $slide_s,
					'HIT_S' => $hit_s,
					'NEW_S' => $new_s,
					'RECOMENDED_PRODUCTS'=>$recommended_products,
					'USED_COMPLATE'=>$used_complete,
						
					'WEIGHT' => $weight,
					'CYLINDER_TYPE' => $cylinder_type,
					'POWER' => $power,
					'BRIGHTNESS' => $brightness,
					'CONSUMPTION' => $consumption,
					'PACKING_SIZE' => $packing_size,
						'DELETE_COLLAGE'=>(!empty($q['collage']) ? " Удалить коллаж <a onclick=\"return confirm('Вы уверены что хотите удалить?'); return false;\" title=\"Удалить\" href=\"/admin/deletecatalogcollage/$id\"><img height=\"12\" width=\"12\" src=\"/img/admin_icons/delete.png\"></a>" : ''),	
					'REFERER' => $referer
				)
			);
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.name');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.artikul');
	//		if ($type == 'page') $this->tpl->parse('CONTENT', '.proizvoditel');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.cost');
		//	if ($type == 'page') $this->tpl->parse('CONTENT', '.hit');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.new');
			$this->tpl->parse('CONTENT', '.pos');
			$this->tpl->parse('CONTENT', '.visible');
			//fif ($type == 'section' && $q['level'] == 0) $this->tpl->parse('CONTENT', '.slide');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.optimization_text');
			
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.packing_size');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.consumption');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.power');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.brightness');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.cylinder_type');
			//if ($type == 'page') $this->tpl->parse('CONTENT', '.weight');
			
			$this->tpl->parse('CONTENT', '.short_desc');			
		
			if ($type == 'page') $this->tpl->parse('CONTENT', '.used_complete');			
			if ($type == 'page') $this->tpl->parse('CONTENT', '.recommended_products');			
			
		
			
			if ($type == 'page') $this->tpl->parse('CONTENT', '.gallery_pic');
			if ($type == 'page') $this->tpl->parse('CONTENT', '.body');
			
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}
	
	
	public function deletecatalogcollage() {
		return $this->deletecollage();
	}
	
	public function deletecmetacollage() {
		return $this->deletecollage('meta_tags');
	}
	
	public function deletenewscollage() {
		return $this->deletecollage('news');
	}
	
	public function deletepagecollage() {
		return $this->deletecollage('page');
	}
	
	protected function deletecollage($tableName = 'catalog') {
		$id = end($this->w);
		
		if(!ctype_digit($id)){
			return false;
		}
		
		$page = $this->db->getRow('SELECT `collage` FROM `'.$tableName.'` WHERE `id` = '.$id);
		
		if($this->db->getNumRows() != 1){
		    er_404();
			return false;
		}
		$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');				
		
		if ($page) { 
			$this->db->setQuery('UPDATE `'.$tableName.'` SET `collage` = "" WHERE `id` = '.$id);			
			if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$page['collage'])) {
				
				@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$page['collage'], 0666);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$page['collage']);
			
			}
			$content = "Коллаж удалён.<meta http-equiv='refresh' content='1;URL=$referer'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		return true;
		
	}
	
	
	public function deletecatalog(){
	    $id = end($this->w);
		
		if(!ctype_digit($id)){
			er_404();
			exit;
		}
		
		$page = $this->db->getRow('SELECT * FROM `catalog` WHERE `id` = '.$id);
		
		if($this->db->getNumRows() != 1){
		    er_404();
			exit();
		}
		
		$this->deleteCatalogGallery($id);
		
		switch($page['type']) {
			case 'page':
				if(file_exists('./img/catalog/'.$page['pic'])) {
					@unlink('./img/catalog/'.$page['pic']);
				}
				
				if(file_exists('./img/catalog/big/'.$page['pic'])) {
					@unlink('./img/catalog/big/'.$page['pic']);
				}
				
				$this->db->setQuery('DELETE FROM `catalog` WHERE `id` = '.$id);
				break;
		
			case 'section':
				$sub = $this->db->getAllRecords('SELECT * FROM `catalog` WHERE `level` = '.$id);
				
				if(sizeof($sub) > 0){
					if(!gpm($_GET, 'confirm', false)){
						
						$confirm = '
						<script>
						<!--
						if(confirm("Вы действительно желаете удалить раздел содержащий вложенные элементы ?")){
							location.href = "?confirm=1";
						}
						-->
						</script>
						';
						$this->tpl->assign(array('CONTENT' => $confirm));
						return true;
					}
					else{
						if(file_exists('./img/catalog/'.$page['pic'])) {
        					@unlink('./img/catalog/'.$page['pic']);
        				}
        				
        				if(file_exists('./img/catalog/big/'.$page['pic'])) {
        					@unlink('./img/catalog/big/'.$page['pic']);
        				}
						
						$this->db->setQuery('DELETE FROM `catalog` WHERE `id` = '.$id);
						
						$this->deleteSubCat($id);
					}
				}
				else{
					if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/catalog/'.$page['pic'])) {
    					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/small/'.$page['pic'], 0666);
    					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/small/'.$page['pic']);
    				}
    				
    				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$page['pic'])) {
    					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$page['pic'], 0666);
    					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$page['pic']);
    				}
					
    				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$page['pic'])) {
    					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$page['pic'], 0666);
    					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$page['pic']);
    				}
    				
					$this->db->setQuery('DELETE FROM `catalog` WHERE `id` = '.$id);
				}
		
				break;
		
				default:
					er_404();
					break;
		}

		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
			$content = "Элемент удалён.<meta http-equiv='refresh' content='1;URL=$referer'>";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		return true;
	}
	
	private function deleteCatalogGallery($id) {
		$items = $this->db->getAllRecords("SELECT * FROM `catalog_gallery` WHERE `catalog_id` = '$id'");
		if ($items) {
			foreach ($items as $item) {
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$item['pic'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$item['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$item['pic']);
				}
				
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$item['pic'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$item['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$item['pic']);
				}
			}
			$this->db->setQuery("DELETE FROM `catalog_gallery` WHERE `catalog_id` = '$id'");
		}
	}
	
	private function deleteSubCat($id = 0){
		if($id != 0){
			$q = $this->db->getAllRecords('SELECT * FROM `catalog` WHERE `level` = '.$id);
			
			$size = sizeof($q);
			
			if($size > 0){
				for($i=0; $i<$size; $i++){
					if($q[$i]['type'] == 'section'){
						$this->deleteSubCat($q[$i]['id']);
					}
					
					if(file_exists('./img/catalog/'.$q[$i]['pic'])) {
					@unlink('./img/catalog/'.$q[$i]['pic']);
				}
				
				if(file_exists('./img/catalog/big/'.$q[$i]['pic'])) {
					@unlink('./img/catalog/big/'.$q[$i]['pic']);
				}
					
					$this->db->setQuery('DELETE FROM `catalog` WHERE `id` = '.$q[$i]['id']);
				}
			}
		}
	}
	
	public function editgallerypic() {
		$meta = 'Редактирование картинки галереи';
		$this->setMeta($meta, null, null, $meta, ' <span>&rarr;</span> '.$meta);
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('gallery_pic1', 'edit');
		
		$this->tpl->define_dynamic('end', 'edit');
		
		if(!$id = gp($this->w, 2)) return false;
				
		$q = $this->db->getRow('SELECT * FROM `catalog_gallery` WHERE `id` = '.$id);
		
		if($this->db->getNumRows() != 1){
		    return false;
		}
		
		$gallery_text = gp($q, 'title', '');
		$gallery_position = gp($q, 'position', '0');
		$pic = gp($q, 'pic', '');
		$pics = '';
		if (!empty($_POST)) {
			$referer = gp($_POST, 'HTTP_REFERER', '');			  
			$gallery_text = gp($_POST, 'gallery_text1', '');
			$gallery_position = gp($_POST, 'gallery_position1', '0');
		}
		else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
		}
				
		
		if (!empty($_POST) && !$this->_err) {			
			
			if(isset($_FILES['gallery_pic1']) && $_FILES['gallery_pic1']['size'] > 0){
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$q['pic'])){
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$q['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$q['pic']);
				}
				
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$q['pic'])){
					@chmod($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$q['pic'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$q['pic']);
				}
							
				if ($this->uploadCatPic($_FILES['gallery_pic1']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$id.'-'.$_FILES['gallery_pic1']['name'], 91, 91) &&
				   @copy($_FILES['gallery_pic1']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$id.'-'.$_FILES['gallery_pic1']['name'])) {
				    	$pic = $id.'-'.$_FILES['gallery_pic1']['name'];
			        
			        
			    }
				
				
			}			
			
			$this->db->setQuery('UPDATE `catalog_gallery` SET `title`="'.$gallery_text.'", `position`="'.$gallery_position.'" , `pic`="'.$pic.'" WHERE `id` = '.$id);	
			$referer = (!empty($referer) && $referer != '{REFERER}') ? $referer:$this->langUrl.'/';					
			$content = "Данные изменены <meta http-equiv='refresh' content='1;URL=$referer'>";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		if ($this->_err) {
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if (empty($_POST) || $this->_err) {
			$this->tpl->assign(
				array(            
					'GALLERY_IMG' => (!empty($pic) && is_file($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$pic) ? '/img/catalog/gallery/small/'.$pic : '/img/no_pic_s.jpg'),
					'GALLERY_IMG_TEXT'=>$gallery_text,
					'GALLERY_IMG_POS'=>$gallery_position,
					'REFERER' =>$_SERVER['HTTP_REFERER']
				
				)
			);
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.gallery_pic1');
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}
	
	public function deletegallerypic() {
		if(!$id = gp($this->w, 2)) return false;
		
		$query = 'SELECT * FROM `catalog_gallery` WHERE `id` = '.$id;
		$galleryPic = $this->db->getRow($query);
		
		
		if($this->db->getNumRows() != 1){
			$this->_err .= 'Элемента с таким id не существует!<br>';
		}
		else{
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$galleryPic['pic'])) {
				@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/big/'.$galleryPic['pic']);
			}
			
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$galleryPic['pic'])) {
				@unlink($_SERVER['DOCUMENT_ROOT'].'/img/catalog/gallery/small/'.$galleryPic['pic']);
			}
			
			$this->db->setQuery('DELETE FROM `catalog_gallery` WHERE `id` = '.$id);
		}
		
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}else{
			$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
			$content = "Элемент удалён.<meta http-equiv='refresh' content='1;URL=$referer'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		
		return true;
	}
	
	/* End Catalog Section */
	
	/* Orders section 
	
	public function orders() {
		$this->tpl->define_dynamic('_orders', 'adm/orders.tpl');
		$this->tpl->define_dynamic('orders', '_orders');
		$this->tpl->define_dynamic('orders_item', 'orders');
		
		$this->setMeta('База заказов', null, null, 'База заказов', ' <span>&rarr;</span> База заказов');
		
		$orders = $this->db->getAllRecords('SELECT `o`.`id`, `o`.`fio`, `o`.`email`, `o`.`date`, SUM(`g`.`cost`*`g`.`count`) as `summ` FROM `orders` as `o`, `orders_goods` as `g` WHERE `o`.`id` = `g`.`order_id` GROUP BY `o`.`id` ORDER BY `date`');
		
		$size = sizeof($orders);
		
		if ($size <= 0) {
			$this->tpl->parse('ORDERS_EMPTY', '.orders_empty');
			$this->tpl->parse('ORDERS_ITEM', 'null');
		}
		else {
			for ($i=0; $i<$size; $i++) {
				$this->tpl->assign(
					array(
						'FIO' => $orders[$i]['fio'],
						'EMAIL' => $orders[$i]['email'],
						'DATE' => convertDate($orders[$i]['date'], 'd.m.Y H:i'),
						'SUM' => $orders[$i]['summ'],
						'ID' => $orders[$i]['id']
					)
				);
				
				$this->tpl->parse('ORDERS_ITEM', '.orders_item');
			}
			
			$this->tpl->parse('ORDERS_EMPTY', 'null');
		}
		
		$this->tpl->parse('CONTENT', '.orders');
		
		return true;
	}
	
	public function vieworders() {
		//$this->setMeta('Удаление заказа', null, null, 'Удаление заказа', ' <span>&rarr;</span> Удаление заказа');
		
		$id = end($this->w);
		
		if (!ctype_digit($id)) {
			return false;
		}
		
		$order = $this->db->getRow('SELECT * FROM `orders` WHERE `id` = "'.$id.'"');
		
		if (!$order) {
			return false;
		}
		
		$this->setMeta(convertDate($order['date'], 'd.m.Y H:i').' | '.$order['email'], null, null, convertDate($order['date'], 'd.m.Y H:i').' | '.$order['email'], ' <span>&rarr;</span> <a href="/admin/orders/">База заказов</a> <span>&rarr;</span> '.convertDate($order['date'], 'd.m.Y H:i').' | '.$order['email']);
		
		$this->tpl->define_dynamic('_order', 'adm/order_detail.tpl');
		$this->tpl->define_dynamic('order', '_order');
		$this->tpl->define_dynamic('order_item', 'order');
		$this->tpl->define_dynamic('order_delivery', 'order');
		$this->tpl->define_dynamic('order_delivery_departament', 'order');
		$this->tpl->define_dynamic('order_delivery_addres', 'order');
		
		$goods = $this->db->getAllRecords('SELECT `o`.`count`, `o`.`cost`, `c`.`name`, `c`.`proizvoditel`, `c`.`artikul` FROM `orders_goods` as `o`, `catalog` as `c` WHERE `o`.`order_id` = "'.$id.'" AND `o`.`goods_id` = `c`.`id` ORDER BY `o`.`id`');
		
		$size = sizeof($goods);
		
		$summ = 0;
		
		for ($i=0; $i<$size; $i++) {
			$this->tpl->assign(
				array(
					'PRO' => stripslashes($goods[$i]['proizvoditel']),
					'ARTIKUL' => $goods[$i]['artikul'],
					'NAME' => stripslashes($goods[$i]['name']),
					'COUNT' => $goods[$i]['count'],
					'COST' => number_format($goods[$i]['cost'], 2, ',', "'"),
					'SUM' => number_format($goods[$i]['cost']*$goods[$i]['count'], 2, ',', "'"),
					'SEPARATOR' => ($i<$size-1) ? '' : '&nbsp;'
				)
			);
			
			$summ += $goods[$i]['cost']*$goods[$i]['count'];
			
			$this->tpl->parse('ORDER_ITEM', '.order_item');
		}
		
		$this->tpl->assign(
				array(
					'SUMM' => number_format($summ, 2, ',', "'"),
					'FIO' => $order['fio'],
					'EMAIL' => $order['email'],
					'CITY' => $order['city'],
					'DELIVERY'=>$order['delivery'],
					'DELIVERY_DEPARTAMENT'=>$order['delivery_departament'],
					'DELIVERY_ADDR'=>$order['delivery_addres'],
					
					'TEL' => $order['tel'],
					'MTEL' => $order['mtel'],
					'BODY' => $order['body'],
				)
			);
		if ($order['delivery_office'] == '1') {
				if ($order['delivery'] == 'home') {
				
					$this->tpl->assign(
					array(	
						'DELIVERY' => 'Доставка к дому / офису'
						)
					);					
					$this->tpl->parse('ORDER_DELIVERY_DEPARTAMENT', 'null');
				} else {
					$this->tpl->assign(
					array(	
						'DELIVERY' => $order['delivery'],
						'DELIVERY_DEPARTAMENT' => $order['delivery_departament'],
						)
					);
					$this->tpl->parse('ORDER_DELIVERY_DEPARTAMENT', '.order_delivery_departament');
				}	
				$this->tpl->assign(
					array(	
						'DELIVERY_ADDR' => $order['delivery_addres']
						)
					);
				
				$this->tpl->parse('ORDER_DELIVERY', '.order_delivery');
				
				$this->tpl->parse('ORDER_DELIVERY_ADDRES', '.order_delivery_addres');
				
			} else {
				$this->tpl->parse('ORDER_DELIVERY', 'null');
				$this->tpl->parse('ORDER_DELIVERY_ADDRES', 'null');
				$this->tpl->parse('ORDER_DELIVERY_DEPARTAMENT', 'null');
			}
		$this->tpl->parse('CONTENT', '.order');
		
		return true;
	}
	
	public function delorders() {
		$this->setMeta('Удаление заказа', null, null, 'Удаление заказа', ' <span>&rarr;</span> Удаление заказа');
		
		$id = end($this->w);
		
		if (!ctype_digit($id)) {
			return false;
		}
		
		$this->db->setQuery('DELETE FROM `orders` WHERE `id` = "'.$id.'"');
		$this->db->setQuery('DELETE FROM `orders_goods` WHERE `order_id` = "'.$id.'"');
		
		$content = "<div id='body'>Заказ удален!</div><meta http-equiv='refresh' content='1;URL=/admin/orders/'>";
		$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		
		return true;
	}
	
	 End Orders section */
	
	// Обновление цены на товары по артикулу. Данные загружауюся из файла exel
	/*public function updateprice() {
	    $this->setMeta('Обновление цены на товары', null, null, 'Обновление цены на товары', ' <span>&rarr;</span> Импорт товаров');
	    
	    $this->tpl->define_dynamic('_import', 'adm/import.tpl');
	    $this->tpl->define_dynamic('import', '_import');
	    $this->tpl->define_dynamic('import_err_row', 'import');
	    $this->tpl->define_dynamic('import_success', 'import');
	    
	    $this->tpl->parse('IMPORT_ERR_ROW', 'null');
	    $this->tpl->parse('IMPORT_SUCCESS', 'null');
	    
	    if (!empty($_FILES)) {
            $file = ($_FILES['file']['size'] > 0) ? $_FILES['file'] : null;
            if($file['type'] != "application/vnd.ms-excel" && $file['type'] != "application/octet-stream" && $file['type'] != "application/x-msexcel") {
                $this->_err .= "<br>Неправильный формат файла каталога";
            }
        }
		
        $arr_err = array();
        
        if (!empty($_FILES) && !$this->_err) {
            ini_set('max_execution_time', 120);
            require_once('class/xlsparser.php');
            $sheets = parse_excel($file['tmp_name']);
            
            $arr = $sheets[2];
            //array_shift($arr);
     		 
            if (!empty($arr)) {              
                
                foreach ($arr as $row) {                 
                
                    if ($row) {                                            
                        $artikul = gp($row, 0, '');                        
                        $cost = gp($row, 1, '');                                   	
               
                         if ( !empty($cost)) {
                         	$cost = preg_replace('/\s/', '', $cost);                         	
                         	$sql = "UPDATE `catalog` SET `cost`='$cost' WHERE `artikul` LIKE '".trim($row[0])."'";                          	                         	
                         	
                         	$this->db->setQuery($sql);
                         }                         
                      }                   
                }
                
                $this->db->setQuery('OPTIMIZE TABLE `catalog`');
              
            }
            
        }
        
        if ($this->_err) {
            $this->tpl->assign(array('CONTENT' => page::ViewErr()));
        }
        
        if (!empty($arr_err)) {
            $size = sizeof($arr_err);
            
            for ($i=0; $i<$size; $i++) {
                $this->tpl->assign(array('ERROR_IMPORT' => $arr_err[$i]));
            }
            
            $this->tpl->parse('IMPORT_ERR_ROW', '.import_err_row');
        }
        else {
            if (!empty($_FILES) && !$this->_err) {
               // $this->generatesitemap();
              //  $this->generateYandexMarket();
                $this->tpl->parse('IMPORT_SUCCESS', '.import_success');
            }
        }
        
        $this->tpl->parse('CONTENT', '.import');      
     
        
        return true;
	}*/
	
	public function import() {
		
		$fields = $this->db->getAllRecords("DESC `catalog`");
		for ($i = 0; $i < count($fields); $i++) {
		//	print ($i > 0 ? ', ' : '')."'\$".$fields[$i]['Field']."'";
		}
	//die;
	    $this->setMeta('Импорт товаров', null, null, 'Импорт товаров', ' <span>&rarr;</span> Импорт товаров');
	    
	    $this->tpl->define_dynamic('_import', 'adm/import.tpl');
	    $this->tpl->define_dynamic('import', '_import');
	    $this->tpl->define_dynamic('import_err_row', 'import');
	    $this->tpl->define_dynamic('import_success', 'import');
	    
	    $this->tpl->parse('IMPORT_ERR_ROW', 'null');
	    $this->tpl->parse('IMPORT_SUCCESS', 'null');
	    
	    if (!empty($_FILES)) {
            $file = ($_FILES['file']['size'] > 0) ? $_FILES['file'] : null;
            if($file['type'] != "application/vnd.ms-excel" && $file['type'] != "application/octet-stream" && $file['type'] != "application/x-msexcel") {
                $this->_err .= "<br>Неправильный формат файла каталога";
            }
        }
		
        $arr_err = array();
        
        if (!empty($_FILES) && !$this->_err) {
            ini_set('max_execution_time', 120);
            //require_once('class/xlsparser.php');
            require_once('class/PHPExcel.php');
            require_once('class/PHPExcel/IOFactory.php');
            
			$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file']['tmp_name']);
			
			
			$allSheets = $objPHPExcel->getAllSheets();
			$sheetsLength = count($allSheets);
			//var_dump(get_class_methods('PHPExcel_Worksheet'));
			//var_dump(get_class_methods('PHPExcel_Worksheet_Row')); 
			//var_dump(get_class_methods('PHPExcel_Worksheet_CellIterator'));
			
			$importType = '1';
			
			if (isset($_POST['import_type'])) {
				$importType = $_POST['import_type'];
			}
			
			if (count($sheetsLength) > 0) {
				if ($importType == '3') {
					$this->db->setQuery("TRUNCATE TABLE `catalog`");
				}	
				
				$rowCounter = 0;	// Активнй лист	
				foreach ($allSheets as $sheet) {
					$title = iconv('utf-8', 'cp1251', $sheet->getTitle());
				
					$counter = 1;		

					
            		$pagesArr = array();
            		$sectionArr = array();
            		$subSectionArr = array();
					//var_dump($sheet->getHighestRow());				
					$i1 = 0;
					foreach($sheet->getRowIterator() as $row) { 
					 
					 	$item = array();
					 	$collIndex = 0;
					
					 	$i1++;
					 	if ($counter >= 5) {
					 		
						 	$cellIterator = $row->getCellIterator();
						 	
						 	$dataArray = array();
						 	
						 	$dataArray['section'] =  iconv('utf-8', 'cp1251', $sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	
						 	$sectionPosition = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());;
						 	if (empty($sectionPosition)) {
						 		$sectionPosition = '9999';
						 	}
						 	
						 	
						 	$collIndex++;	
						 	$dataArray['sub_section'] = '';		
						 	$subSectionPosition = '9999';
						 	
						 	if ($rowCounter == 1 || $rowCounter == 7  || $rowCounter == 2 || $rowCounter == 3) {						 			 	
						 		$dataArray['sub_section'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 		$collIndex++;
						 		
						 		$subSectionPosition = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());;
						 		$collIndex++;	
						 	}	
						 	
						 	if (empty($subSectionPosition)) {
						 		$subSectionPosition = '9999';
						 	}
						 	
						 	$dataArray['name'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());						 	
						 	$collIndex++;
						 	
						 	$dataArray['position'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());

						 	if (empty($dataArray['position'])) {
						 		$dataArray['position'] = '9999';
						 	}
						 						 	
						 	$collIndex++;
						 	
						 	
						 	
						 	$dataArray['model'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	
						 	$dataArray['artikul'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 
						 	$dataArray['proizvoditel'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 
						 	$dataArray['used_completed'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	
						 	$dataArray['recommended_products'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	
						 	
						 	$dataArray['country'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	
						 	// Информация об упаковке
						 	$dataArray['length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	
						 	$dataArray['width'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	
						 	$dataArray['height'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
						 	$collIndex++;
						 	// Вес
						 	$dataArray['weight'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 	$collIndex++;
						 	
						 	// Описание
						 	
						 	$dataArray['description'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());  
						 	$collIndex++;
						 	
						 	$dataArray['model_features'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Особенности модели
						 	$collIndex++;
						 	
						 	//Сфера применения	
						 	if ($rowCounter != 2) {
						 		$dataArray['tourism']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 		$collIndex++;
						 	
						 		$dataArray['fishing']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 		$collIndex++;
						 	
						 		$dataArray['hunting']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 		$collIndex++;
						 	
						 		$dataArray['mountaineering']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); //Альпинизм
						 		$collIndex++;
						 	
						 		$dataArray['picnic']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 		$collIndex++;
						 	}
						 	//Гарантия
						 	
						 	$dataArray['warranty']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 	$collIndex++;
						 	
						 	//Преимущества
						 	
						 	$dataArray['benefits']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 	$collIndex++;
						 	
						 	//Достоинства
						 	
						 	$dataArray['dignity']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 	$collIndex++;
						 	
						 	//Особенности применения
						 	
						 	$dataArray['featuresOf']   = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 	$collIndex++;
						 	
						 	
					 	 	

						 	
						 	if ($rowCounter == 0 || $rowCounter == 3 || $rowCounter == 4 || $rowCounter == 5) { // Для листа с резаками, газовыми лампами, грилями
						 		
						 		$dataArray['fuel'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Топливо						 	
						 		$collIndex++;						 		
						 		
						 		$dataArray['material'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Материал						 	
						 		$collIndex++;
						 		
						 		$dataArray['cylinder_type'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); //Тип баллона						 	
						 		$collIndex++;						 		
						 		
						 		$dataArray['completion'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());	// Комплектация					 	
						 		$collIndex++;
						 		
						 		if ($rowCounter == 0 ) { // Только для резаков
						 		
						 			$dataArray['thickness_of_the_material_being_cut']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Толщина разрезаемого материала						 	
							 		$collIndex++;
							 	
							 		$dataArray['time_of_continuous_operation']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Время непрерывной работы						 	
						 			$collIndex++;
						 		
						 			$dataArray['piezo']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Пьезоподжиг
						 			$collIndex++;
						 		
						 			$dataArray['heating_temperature'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Температура нагрева
						 			$collIndex++;
						 		
						 			$dataArray['power'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());  // Мощность
						 			$collIndex++;
						 		
						 			$dataArray['consumption'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Расход газа
						 			$collIndex++;
						 		}	
						 	}
						 	

                            if ($rowCounter == 1) { // Для листа с посудой

                                                                                // Параметры для кастрюль
                                 // шт.
                                $dataArray['casserole_length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
                                
                                // Объем
                                $dataArray['casserole_volume'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());                                
                                $dataArray['casserole_volume'] = addslashes($dataArray['casserole_volume']); 
                                $collIndex++;
                               
                                 // Материал
                                $dataArray['casserole_material'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
                                                                                // Параметры для сковородки
                                $dataArray['pan_length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['pan_diameter'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['pan_material'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
                                                                                // Параметры для пластиковых чашек
                                $dataArray['plastic_cup_length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['plastic_cup_volume'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['plastic_cup_material'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
                                                                                // Параметры для тарелок
                                $dataArray['plate_length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['plate_volume'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['plate_material'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
 
                                // Половник
                                $dataArray['soup_ladle'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                // Лопатка
                                $dataArray['scapula'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                // Дуршлаг
                                $dataArray['colander'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                // Параметры для чайника
                                $dataArray['kettle_length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['kettle_volume'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['kettle_material'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                // В некоторых комплектах посуды входит горелка
                                //gas-jet
                                $dataArray['gas_jet_name'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;

                                $dataArray['gas_jet_fuel'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
                                
                                $dataArray['gas_jet_weight'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
                                
                                $dataArray['gas_jet_power'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue());
                                $collIndex++;
                                                                
                                $dataArray['gas_jet_maximum_flow_rate'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Расход газа
                                $collIndex++;
                                
                                $dataArray['gas_jet_cylinder_type'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
                                $collIndex++;
                                
                                // Оптимально для количества человек

                                $dataArray['optimally_for_the_number_of_people'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
                                $collIndex++;
                                //

                            }
                            
                        	if ($rowCounter == 2) { // Для листа с акцессуармаи
	                           	 $dataArray['compatibility'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
                            }
                            
                            if ($rowCounter == 3) { // Для листа Газовые лампы
                            	// Добавляем длину, ширину, высоту, световая мощность, максимальный расход, яркость, пьезо, регулятор яркости, время работы, время непрерывной работы
                            	
	                           	 $dataArray['goods_length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
	                           	 $collIndex++;
	                           	 
	                           	 $dataArray['goods_width'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
	                           	 $collIndex++;
	                           	 
	                           	 $dataArray['goods_height'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
	                           	 $collIndex++;
	                           	 
	                           	 $dataArray['power'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // мощность
	                           	 $collIndex++;
	                           	 
	                           	 
	                           	 
						 		 $dataArray['consumption'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Расход газа
						 		 $collIndex++;
						 		
						 		 $dataArray['brightness'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Яркость
						 		
						 		 
						 		 $collIndex++;
	                           	 
	                           	 
						 		$dataArray['piezo']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Пьезоподжиг
						 		$collIndex++;
						 		 
						 		$dataArray['brightnes_control']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Регулятор яркости
						 		$collIndex++;
						 		
						 		$dataArray['time_operation']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Время  работы						 	
						 		$collIndex++;
						 		
						 		$dataArray['time_of_continuous_operation']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Время непрерывной работы						 	
						 		$collIndex++;
						 								 		
						 		
                            }
                            
                            
                             if ($rowCounter == 4) { // Для листа грили
                            	
                            	 
	                           	 $dataArray['power'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // мощность
	                           	 $collIndex++;
	                           	 
	                           	 $dataArray['consumption'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Расход газа
						 		 $collIndex++;
						 		 
						 		 $dataArray['brightnes_control']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Регулятор яркости
						 		 $collIndex++;
						 		 
						 		 $dataArray['piezo']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Пьезоподжиг
						 		 $collIndex++;
	                           	 
						 		 $dataArray['area_preparation']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Площадь приготовления 
						 		 $collIndex++;
						 		 						 		 
						 		 $dataArray['time_of_continuous_operation']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Время непрерывной работы						 	
						 		 $collIndex++;
						 		
                            }
                            
                            
                            if ($rowCounter == 5 || $rowCounter == 6) { // Для листа обогреватели, плиты
                            	
                            	                            	 
	                           	 $dataArray['power'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // мощность
	                           	 $collIndex++;
	                           	 
	                           	 $dataArray['consumption'] = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Расход газа
						 		 $collIndex++;
						 		 
						 		 
						 		 $dataArray['time_of_continuous_operation']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Время непрерывной работы						 	
						 		 $collIndex++;
						 		 
						 		 $dataArray['operating_instructions']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Инструкция по эксплуатации						 	
						 		 $collIndex++;
						 		 
						 		  
						 		 $dataArray['piezo']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Пьезоподжиг
						 		 $collIndex++;
						 		 // 
						 		 
						 		 if ($rowCounter == 5) {
						 		 	$dataArray['brightnes_control']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Регулятор температуры
						 		 	$collIndex++;
						 		 	
						 		 	$dataArray['heating_in_the_room']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Обогрев в помещении 
						 		 	$collIndex++;
						 		 }
						 		 
						 		 if ($rowCounter == 6 || $rowCounter == 7 || $rowCounter == 8) {
						 		 	
						 		 	$dataArray['windscreens']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); // Ветрозащита
						 		 	$collIndex++;

						 		 	//Диаметр устанавливаемой емкости
						 		 
						 		 	$dataArray['diameter_of_the_installed_capacity']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 		 	$collIndex++;
						 		 	
						 		 	
						 		 	
						 		 }
	                            						 		 
						 			
						 		 //Клапан избыточного давления
						 		 
						 		 $dataArray['overpressure_valve']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 		 $collIndex++;
						 		 
						 		 if ($rowCounter == 6 || $rowCounter == 7 || $rowCounter == 8) {
						 		 	// Время закипания 1л воды 
						 		 	
						 		 	$dataArray['time_of_boiling_liter_of_water']  = iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
						 		 	$collIndex++;
						 		 	
						 		 	// Оптимально для количества человек

                                	$dataArray['optimally_for_the_number_of_people'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
                                	$collIndex++;
						 		 }
						 		 
						 		 
	                           	 $dataArray['goods_length'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
	                           	 $collIndex++;
	                           	 
	                           	 $dataArray['goods_width'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
	                           	 $collIndex++;
	                           	 
	                           	 $dataArray['goods_height'] =  iconv('utf-8', 'cp1251',$sheet->getCellByColumnAndRow($collIndex, $counter)->getCalculatedValue()); 
	                           	 $collIndex++;
						 		
                            }
                            
						 	
						 	
						 	$dataArray['preview'] = '';
						 
						 	
						 	
						 
						 	$dataArray['metadescription'] = $dataArray['description'];
						 	$dataArray['type'] = 'section';						 	
						 	$dataArray['level'] = '0';
						 	
						 	$dataArray['slide'] = '1';						 	
						 	$dataArray['new'] = '0';
						 	
						 	$section = '';						 	
						 	
							if (isset($dataArray['section'])) {
								$section = $dataArray['section'];
							}
		
							if (!empty($section)) {
							
								$subSection = '';
								$sectionHref = ru2lat(trim($section)); 						 		
            					$sectionHref = preg_replace('/\W+/i', '-', $sectionHref);
            					$dataArray['link'] = $sectionHref;
            					$subSectionHref = '';
            					
            					if (isset($dataArray['sub_section']) && !empty($dataArray['sub_section'])) { 
            						$subSection = $dataArray['sub_section'];
            					}
            					
            					// Загрузка разделов
            				
            					if (!isset($sectionArr[$sectionHref])) {      
            						
            					//	if ($importType == '3') {      			            					
            							//$sql = "INSERT INTO `catalog` (`name`, `level`, `type`, `link`, `metah1`, `metatitle`, `metakeywords`) VALUES ('$section', '0', 'section', '$sectionHref', '$section', '$section', '$section')";
            					/*	} else {
            							$row = $this->db->getRow("SELECT `id` FROM `catalog` WHERE `artikul` = '".$dataArray['artikul']."'");
            							if (!$row) {
            								$sql = "INSERT INTO `catalog` (`name`, `level`, `type`, `link`, `metah1`, `metatitle`, `metakeywords`) VALUES ('$section', '0', 'section', '$sectionHref', '$section', '$section', '$section')";
            							} else {
            								$sql = "UPDATE `catalog` SET `name` = '$section' WHERE `artikul` = '".$dataArray['artikul']."'";
            							}
            						} */
            						$row = $this->db->getRow("SELECT `id` FROM `catalog` WHERE `link` = '$sectionHref'");
            					
            						if (!$row) {
            							$sql = "INSERT INTO `catalog` (`name`, `level`, `type`, `link`, `metah1`, `metatitle`, `metakeywords`, `position`) VALUES ('$section', '0', 'section', '$sectionHref', '$section', '$section', '$section', '$sectionPosition')";
            						} else {
            							$sql = "UPDATE `catalog` SET `name` = '$section' ".(!empty($sectionPosition) ? ", `position` = '$sectionPosition'" : '')." WHERE `link` = '$sectionHref'";
            						}
            						
            						$this->db->setQuery($sql);
            						$sectionArr[$sectionHref] = $this->db->getLastInsertId();            				
            					}		
            			
            					// Загрузка подразделов
            					if (!empty($subSection) && isset($sectionArr[$sectionHref])) {            			
	        						$subSectionHref = ru2lat(trim($subSection));
    	        					$subSectionHref = preg_replace('/\W+/i', '-', $subSectionHref);            				            				            				
            						$dataArray['link'] = $subSectionHref;
            						 
        	    					if (!isset($subSectionArr[$sectionHref][$subSectionHref])) {    
        	    						
        	    					//	if ($importType == '3') {      			            					
            								//$sql = "INSERT INTO `catalog` (`name`, `level`, `type`, `link`, `metah1`, `metatitle`, `metakeywords`) VALUES ('$subSection', '$sectionArr[$sectionHref]', 'section', '$subSectionHref', '$subSection', '$subSection', '$subSection')";
            						/*	} else {
            							
            								$row = $this->db->getRow("SELECT `id` FROM `catalog` WHERE `artikul` = '".$dataArray['artikul']."'");
            								if (!$row) {
            									$sql = "INSERT INTO `catalog` (`name`, `level`, `type`, `link`, `metah1`, `metatitle`, `metakeywords`) VALUES ('$subSection', '$sectionArr[$sectionHref]', 'section', '$subSectionHref', '$subSection', '$subSection', '$subSection')";
            								} else {
            									$sql = "UPDATE `catalog` SET `name` = '$section' WHERE `artikul` = '".$dataArray['artikul']."'";
            								}
            							}	*/	            				
            							
            							$row = $this->db->getRow("SELECT `id` FROM `catalog` WHERE `link` = '$subSectionHref'");
            							if (!$row) {
            								$sql = "INSERT INTO `catalog` (`name`, `level`, `type`, `link`, `metah1`, `metatitle`, `metakeywords`, `position`) VALUES ('$subSection', '$sectionArr[$sectionHref]', 'section', '$subSectionHref', '$subSection', '$subSection', '$subSection', '$subSectionPosition')";
            							} else {
            								//
            								$sql = "UPDATE `catalog` SET `name` = '$subSection' ".(!empty($sectionPosition) ? ", `position` = '$subSectionPosition'" : '')." WHERE `link` = '$subSectionHref'";
            							}
            						
            							$this->db->setQuery($sql);            						            						
            							$subSectionArr[$sectionHref][$subSectionHref] = $this->db->getLastInsertId();            					            					
            		
            						}
            					} 
					 			
            			            			
            					// Загрузка товаров
            						
            					if (!empty($subSection)) {
            						
            						$dataArray['metah1'] = $subSection;
						 			$dataArray['metatitle'] = $subSection;
						 			$dataArray['metakeywords'] = $subSection;
						 			$dataArray['artikul'] = ru2lat(trim($dataArray['artikul']));
            
            						$dataArray['link'] = ru2lat(trim($dataArray['name']));
            						$dataArray['link'] = preg_replace('/\W+/i', '-', $dataArray['link']);
            						$dataArray['level'] = $subSectionArr[$sectionHref][$subSectionHref];
            						$dataArray['type'] = 'page';
            						
            						if (isset($subSectionArr[$sectionHref][$subSectionHref])) { 	            							
										$fields = '';
										$values = '';
										$fieldsLength = count($fields);
										$i = 0;
										$i1 = 0;
										$uFields = '';	
										foreach ($dataArray as $field=>$value) {						
											if ($field != 'section' && $field != 'sub_section') {
												$fields .= ($i > 0 ? ", ": '')."`$field`";
												$values .= ($i > 0 ? ", ": '')."'$value'";
												if ($importType == '1') {
													if (!empty($value) && $field != 'level') {														
														$uFields .= ($i1 > 0 ? ", ": '')."`$field` = '$value'";
														$i1++;
													}	
												}
												
												if ($importType == '2' && $field != 'level') {													
													$uFields .= ($i1 > 0 ? ", ": '')."`$field` = '$value'";
													$i1++;														
												}
												
												$i++;
											}	
										}
										//$sql = "INSERT INTO `catalog` ($fields) VALUES ($values)";	
										
										if ($importType == '3') {
											$sql = "INSERT INTO `catalog` ($fields) VALUES ($values)";									
										} else {											
											$row = $this->db->getRow("SELECT `id` FROM `catalog` WHERE `artikul` = '".$dataArray['artikul']."'");
            								if (!$row) {
            									$sql = "INSERT INTO `catalog` ($fields) VALUES ($values)";									
            								} elseif (!empty($uFields)) {
            									$sql = "UPDATE `catalog` SET $uFields WHERE `artikul` = '".$dataArray['artikul']."'";
            								}							
										}	
										
										$this->db->setQuery($sql);  	 								
									}	
            					} else {
            						
            						$dataArray['link'] = ru2lat(trim($dataArray['name']));
            						$dataArray['link'] = preg_replace('/\W+/i', '-', $dataArray['link']);
            						$dataArray['level'] = $sectionArr[$sectionHref];
            						$dataArray['type'] = 'page';
            						
            						
            						$dataArray['metah1'] = $dataArray['name'];
						 			$dataArray['metatitle'] = $dataArray['name'];
						 			$dataArray['metakeywords'] = $dataArray['name'];
            						
            						if (isset($sectionArr[$sectionHref])) { 	            					
										$fields = '';
										$values = '';
										$fieldsLength = count($fields);
										$i = 0;
										$i1 = 0;
										$uFields = '';
										foreach ($dataArray as $field=>$value) {						
											if ($field != 'section' && $field != 'sub_section') {
												$fields .= ($i > 0 ? ", ": '')."`$field`";
												//$value = convertYesNo2Digit($value);
												
												if (strcasecmp('да', $value) == 0 || strcasecmp('есть', $value) == 0) {
													$value = '1';
												}
												
												if (strcasecmp('нет', $value) == 0 ) {
													$value = '0';
												}
												
												$values .= ($i > 0 ? ", ": '')."'$value'";
												if ($importType == '1' && $field != 'level') {
													if (!empty($value)) {														
														$uFields .= ($i1 > 0 ? ", ": '')."`$field` = '$value'";
														$i1++;
													}	
												}
												
												if ($importType == '2' && $field != 'level') {													
													$uFields .= ($i1 > 0 ? ", ": '')."`$field` = '$value'";
													$i1++;														
												}
												
												
												$i++;
											}	
										}
										
										if (!empty($dataArray['name'])) {
											if ($importType == '3') {
												$sql = "INSERT INTO `catalog` ($fields) VALUES ($values)";									
											} else {											
												$row = $this->db->getRow("SELECT `id` FROM `catalog` WHERE `artikul` = '".$dataArray['artikul']."'");
            									if (!$row) {
            									$sql = "INSERT INTO `catalog` ($fields) VALUES ($values)";									
            									} elseif (!empty($uFields)) {
            										$sql = "UPDATE `catalog` SET $uFields WHERE `artikul` = '".$dataArray['artikul']."'";
            									}							
											}	
            							
											$this->db->setQuery($sql);  
										}	
            						}
						 								
			 					}	
          					}					 	
            				
					 	}	
					 	$counter ++;
					 
					 	//var_dump($item);
						
					 }
					
					 $rowCounter++;	
				}
			}         
            
        }
        
        if ($this->_err) {
            $this->tpl->assign(array('CONTENT' => page::ViewErr()));
        }
        
        if (!empty($arr_err)) {
            $size = sizeof($arr_err);
            
            for ($i=0; $i<$size; $i++) {
                $this->tpl->assign(array('ERROR_IMPORT' => $arr_err[$i]));
            }
            
            $this->tpl->parse('IMPORT_ERR_ROW', '.import_err_row');
        }
        else {
            if (!empty($_FILES) && !$this->_err) {
                $this->generatesitemap();
                $this->generateYandexMarket();
                $this->tpl->parse('IMPORT_SUCCESS', '.import_success');
            }
        }
        
        $this->tpl->parse('CONTENT', '.import');
        return true;
	}
	
	public function importcatpics() {
		$path = '/upload/cat_pics';		
		if (is_dir($_SERVER['DOCUMENT_ROOT'].$path)) {
			
			$imgFiles = array();			
			$dh = opendir($_SERVER['DOCUMENT_ROOT'].$path);
			while ($file = readdir($dh)) {
				if ($file != '.' && $file != '..') {
					
					$fileNameTmp = strtoupper($file);
					$artikul = substr($fileNameTmp, 0, strrpos($fileNameTmp, '.'));
					$exec = substr($fileNameTmp, strrpos($fileNameTmp, '.')+1, strlen($fileNameTmp));
					
					if ($exec == 'GIF' || $exec == 'JPG') {	
						$artikul2 = str_replace('-', ' ', $artikul);					
						$row = $this->db->getResult("SELECT `id` FROM `catalog` WHERE `artikul` IN ('$artikul', '$artikul2')  ");
						 $pics = '';
		    			if($row){
						
		    				$sFile = $_SERVER['DOCUMENT_ROOT'].'/img/catalog/small/'.$row.'-'.$file;	
		    				$bFile = $_SERVER['DOCUMENT_ROOT'].'/img/catalog/big/'.$row.'-'.$file;	
		    				$rFile = $_SERVER['DOCUMENT_ROOT'].'/img/catalog/real/'.$row.'-'.$file;	
		    			
		    			
		    				
		    				if(is_file($sFile)){
								@chmod($sFile, 0666);
								@unlink($sFile);
							}
						
							if(is_file($bFile)){
								@chmod($bFile, 0666);
								@unlink($bFile);
							}			
						
							if(is_file($rFile)){
								@chmod($rFile, 0666);
								@unlink($rFile);
							}				
						
							//print $sFile.'<br>';
    						if ($this->uploadCatPic($_SERVER['DOCUMENT_ROOT'].$path.'/'.$file, $sFile, 186, 186) &&
				    			$this->uploadCatPic($_SERVER['DOCUMENT_ROOT'].$path.'/'.$file, $bFile, 400, 367) && 
				    			@copy($_SERVER['DOCUMENT_ROOT'].$path.'/'.$file, $rFile)) {
    			        			$this->db->setQuery("UPDATE `catalog` SET `pic` = '".$row.'-'.$file."' WHERE `artikul` LIKE '$artikul'");    			        			
    			        			
    			    			}  			  
    						}		
							
					}
				}
			}		
					
						
			closedir($dh);
		}	
			
			
		
	
		return true;
		
	}
	
	
	private function generatesitemap() {
	    ini_set('max_execution_time', '360');
	    
	    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
	    
	    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	    
	    $pages = $this->db->getAllRecords('SELECT * FROM `page` WHERE `type` <> "link" AND `level` = "0" ORDER BY `id`');
	    
	    foreach ($pages as $page) {
	        $priority = '1.0';
	        if ($page['type'] == 'section') $priority = '0.6';
	        if ($page['type'] == 'page' && $page['link'] != 'mainpage') $priority = '0.8';
	        
	        $sitemap .= '
<url>
    <loc>http://'.$_SERVER['HTTP_HOST'].'/'.(($page['link'] != 'mainpage') ? ($page['link']) : ('')).'</loc>
    <changefreq>monthly</changefreq>
    <priority>'.$priority.'</priority>
</url>
	        ';
	        
	        if ($page['type'] == 'section') {
	            $sitemap .= $this->getSubSitemap('page', $page['id'], 'http://'.$_SERVER['HTTP_HOST'].'/'.$page['link'].'/');
	        }
	    }
	    
	    $sitemap .= '
<url>
    <loc>http://'.$_SERVER['HTTP_HOST'].'/news</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
</url>
        ';
	    
	    $news = $this->db->getAllRecords('SELECT * FROM `news` ORDER BY `date` DESC');
	    
	    
	    
	    foreach ($news as $item) {
	        $sitemap .= '
<url>
    <loc>http://'.$_SERVER['HTTP_HOST'].'/news/'.$item['link'].'</loc>
    <lastmod>'.(convertDate($item['date'], 'Y.m.d')).'</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
</url>
	        ';
	    }
	    
	    $catalog = $this->db->getAllRecords('SELECT * FROM `catalog` WHERE `level` = "0" ORDER BY `position`');
	    
	    if (!empty($catalog)) {
	    
	    $sitemap .= '
<url>
    <loc>http://'.$_SERVER['HTTP_HOST'].'/catalog/</loc>
    <lastmod>'.(convertDate($item['date'], 'Y.m.d')).'</lastmod>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
</url>
	        ';
	    
	    foreach ($catalog as $cat) {
	        $priority = '0.8';
	        if ($cat['type'] == 'section') $priority = '0.6';
	        
	        $sitemap .= '
<url>
    <loc>http://'.$_SERVER['HTTP_HOST'].'/catalog/'.$cat['link'].'</loc>
    <changefreq>weekly</changefreq>
    <priority>'.$priority.'</priority>
</url>
	        ';
	        
	        if ($page['type'] == 'section') {
	            $sitemap .= $this->getSubSitemap('catalog', $cat['id'], 'http://'.$_SERVER['HTTP_HOST'].'/catalog/'.$cat['link'].'/', 'weekly');
	        }
	    }
	    
	    $sitemap .= '
</urlset>';
	    }
	    
	    @$fp = fopen('./sitemap.xml', 'w+');
	    
	    @fwrite($fp, $sitemap);
	    
	    @fclose($fp);
	    
	    @chmod('./s.xml', 775);
	    
	    return true;
	}
	
	private function getSubSitemap($table = 'page', $id = 0, $url = '', $update = 'monthly') {
	    $pages = $this->db->getAllRecords('SELECT * FROM `'.$table.'` WHERE `level` = "'.$id.'"');
	    
	    $sitemap = '';
	    
	    foreach ($pages as $page) {
	        $priority = '0.8';
	        if ($page['type'] == 'section') $priority = '0.6';
	        //if ($page['type'] == 'page' && $page['link'] != 'mainpage') $priority = 0.8;
	        
	        $sitemap .= '
<url>
    <loc>'.$url.$page['link'].'</loc>
    <changefreq>'.$update.'</changefreq>
    <priority>'.$priority.'</priority>
</url>
	        ';
	        
	        if ($page['type'] == 'section') {
	            $sitemap .= $this->getSubSitemap($table, $page['id'], $url.$page['link'].'/');
	        }
	    }
	    
	    return $sitemap;
	}
	
	public function generateYandexMarket() {
	    $yandex = '';
	    
	    $category = $this->db->getAllRecords('SELECT * FROM `catalog` WHERE `level` = "0"');
	    
	    $categories = '';
	    $i = 1;
	    $offers = '';
	    foreach ($category as $cat) {
$categories .= '
<category id="'.$i.'">'.$cat['name'].'</category>';

            
            
            $offer = $this->db->getAllRecords('SELECT * FROM `catalog` WHERE `type` = "page" AND `level` = "'.$cat['id'].'"');
            
            foreach ($offer as $item) {
$offers .= '
<offer id="'.$item['id'].'" available="true">
    <url>http://noutbuki.dp.ua/catalog/'.$cat['link'].'/'.$item['link'].'</url>
    <price>'.(($item['cost'] && $item['cost'] != '' && $item['cost'] != 'звоните') ? ($item['cost']) : (1)).'</price>
    <currencyId>UAH</currencyId>
    <categoryId>'.$i.'</categoryId>
    '.(($item['pic']) ? ('<picture>http://noutbuki.dp.ua/img/'.str_replace(' ', '-', $item['pic']).'</picture>') : ('')).'
    <delivery> false </delivery>
    <local_delivery_cost>10</local_delivery_cost>
    <name> '.htmlspecialchars(str_replace('ё', 'е', $item['name'])).' </name>
    <vendor> '.$item['proizvoditel'].' </vendor>
    <vendorCode> '.$item['artikul'].' </vendorCode>
    <description> '.htmlspecialchars(str_replace('ё', 'е', $item['name'])).'</description>
</offer>
';
	    }
	    $i++;
	    }
	    
	    //$offer = $this->db->getAllRecords('SELECT * FROM `catalog` WHERE `type` = "page"');
	    
	    
	    
	    
	    //print_r($category);
	    
$yandex .= '<?xml version="1.0" encoding="windows-1251"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="'.date('Y-m-d H:i:s').'">
<shop>
    <name>НОУТБУКИ</name>
    <company>Интернет магазин НОУТБУКИ в Днепропетровске</company>
    <url>http://noutbuki.dp.ua</url>
    <currencies>
        <currency id="UAH"/>
    </currencies>
    <categories>
        '.$categories.'
    </categories>
    <local_delivery_cost>300</local_delivery_cost>
    <offers>
        '.$offers.'
    </offers>
</shop>
</yml_catalog>';

        @$fp = fopen('./ynd.xml', 'w+');
        
        @fwrite($fp, $yandex);
        
        @fclose($fp);

	    return true;
	}
	
	/* Start User Section */
	public function profile(){
		$q = $this->db->getRow('SELECT * FROM `users` WHERE `id` = '.$this->userData['uid']);
		
		if($this->db->getNumRows() != 1){
			er_404();
			exit();
		}
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('users', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$email = gp($q, 'email');
		$_password = '';
		$fio = gp($q, 'fio');
		
		if(!empty($_POST) && !$this->_err){
			$email = gp($_POST, 'email');
			$password = (gp($_POST, 'password')) ? "`pass` = '".md5(gp($_POST, 'password'))."',":"";
			$_password = gp($_POST, 'password');
			$fio = gp($_POST, 'fio');
			
			$rows = $this->db->getResult('SELECT count(`id`) FROM `users` WHERE `id` <> "'.$this->userData['uid'].'" AND email="'.$email.'"');
			
			if($rows != 0){
				$this->_err .= 'Пользователь с таким email уже существует!<br>';
			}
			
			if($email == '' || !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)){
				$this->_err .= 'Неверно введён Email <br>';
			}
			
			$this->err .= (!$fio) ? 'Не указаны ФИО <br>':'';
		}
		
		if(!empty($_POST) && !$this->_err){
			$this->db->setQuery('UPDATE `users` SET `email` = "'.$email.'", '.$password.' `fio` = "'.$fio.'" WHERE `id` = "'.$this->userData['uid'].'"');
		}
		else{
			$this->tpl->assign(
				array(
					'EMAIL' => $email,
					'PASSWORD' => $_password,
					'FIO' => $fio,
				)
			);
		}
		
		if($this->_err){
			$this->tpl->assign(array('CONTENT' => page::ViewErr()));
		}
		
		if(!empty($_POST) && !$this->_err){
			$content = "Данные пользователя $fio изменены <meta http-equiv='refresh' content='1;URL=/admin/profile/'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		else{
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.users');
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}
	/* END User Section */
	
	public function settings() {
		$this->setMeta('Настройки сайта', null, null, 'Настройки сайта', ' <span>&rarr;</span> Настройки сайта');
		//$this->way = 'Настройки сайта';
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('settings', 'edit');
		$this->tpl->define_dynamic('settings_item', 'settings');
		$this->tpl->define_dynamic('end', 'edit');
		
		if(!empty($_POST) && !$this->_err){
			foreach($_POST as $key => $value){
				if(stristr($key, 'settings_')){
					$key = str_ireplace('settings_', '', $key);
					$this->db->setQuery('UPDATE `settings` SET `value` = "'.$value.'" WHERE `key` = "'.$key.'"');
				}
			}
			
			$content = "Данные изменены <meta http-equiv='refresh' content='2;URL=".$this->langUrl."/admin/settings/'>";
			$this->tpl->assign(array('CONTENT' => $this->ViewMessage($content)));
		}
		
		if ($this->_err) {
		    page::ViewErr();
		}
		
		if(empty($_POST) || $this->_err){
			$_settings = $this->db->getAllRecords('SELECT `key`, `value`, `name` FROM `settings`');
		
			$size = sizeof($_settings);
			
			for($i=0; $i<$size; ++$i){
				if($_settings[$i]['key'] != 'hpic'){
					$this->tpl->assign(
						array(
							'KEY' => $_settings[$i]['key'],
							'VALUE' => $_settings[$i]['value'],
							'NAME' => $_settings[$i]['name'],
						)
					);
					$this->tpl->parse('SETTINGS_ITEM', '.settings_item');
				}
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.settings');
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}
	
	public function phrases(){
		//$this->setMeta('Редактируемые поля');
		$this->setMeta('Редактируемые поля', null, null, 'Редактируемые поля', ' <span>&rarr;</span> Редактируемые поля');
		//$this->way = 'Редактируемые поля';
		
		$this->tpl->define_dynamic('list', 'adm/edition_list.tpl');
		$this->tpl->define_dynamic('phrases', 'list');
		$this->tpl->define_dynamic('phrases_row', 'phrases');
		
		$items = $this->db->getAllRecords('SELECT * FROM `lookups` WHERE `lang` = "'.$this->lang.'" ORDER BY `position`');
		//print_r($items);
		$items_num = sizeof($items);
		
		for ($i=0; $i<$items_num; $i++) {
			$this->tpl->assign(
				array(
					'KEY' => $items[$i]['key'],
					'VALUE' => stripslashes($items[$i]['Name']),
				)
			);
			$this->tpl->parse('PHRASES_ROW', '.phrases_row');
		}
		
		$this->tpl->assign(array('PAGES' => ''));
		
		if($items_num > 0){
			$this->tpl->parse('CONTENT', 'phrases');
		}
		else{
			$this->tpl->assign(array('CONTENT' => 'База буста!'));
		}
		return true;
	}
	
	public function edit_phrases() {
		$this->setMeta('Редактируемые поля', null, null, 'Редактируемые поля', ' <span>&rarr;</span> Редактируемые поля');
		//$this->way = 'Редактируемые поля';
		
		$id = end($this->w);
		if(empty($id)){
			er_404($this->langUrl.'/admin/phrases');
		}
		
		$this->tpl->define_dynamic('start', 'edit');
		if ($id == 'COLLAGE') {
			$this->tpl->define_dynamic('collage', 'edit');
		} else {
			$this->tpl->define_dynamic('body', 'edit');
		}	
		
		$this->tpl->define_dynamic('end', 'edit');
		
		
		
		$q = $this->db->getRow('SELECT * FROM `lookups` WHERE `key` = "'.$id.'" AND `lang` = "'.$this->lang.'"');
		
		if($this->db->getNumRows() != 1){
		    er_404($this->langUrl.'/admin/phrases');
		    exit;
		}
		
		$text = $q['value'];
		
		if(!empty($_POST)){
			$text = gpm($_POST, 'body', '');
			
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){
				
				$text = '';
				
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);					
				}
				
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    	
			    	@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);			    	
			        $text = '/pic/'.$id.'-'.$_FILES['collage']['name'];
			    }
				
			}
	
			
			$this->db->setQuery('UPDATE `lookups` SET `value` = "'.addslashes($text).'" WHERE `key` = "'.$id.'" AND `lang` = "'.$this->lang.'"');
			
			$content = "Данные изменены.  <meta http-equiv='refresh' content='1;URL=".$this->langUrl."/admin/phrases'>";
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		else{
			
			if ($id == 'COLLAGE') { 
				$this->tpl->assign(array('COLLAGE' => stripslashes($text)));	
			} else {
				$this->tpl->assign(array('TEXT' => stripslashes($text)));	
			}
			
			
			
			$this->tpl->parse('CONTENT', '.start');
			if ($id == 'COLLAGE') {
				$this->tpl->parse('CONTENT', '.collage');
			} else {
				$this->tpl->parse('CONTENT', '.body');
			}
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}
	
	public function meta_tags() {
	    $this->setMeta('Мета Теги', null, null, 'Мета Теги', ' <span>&rarr;</span> Мета Теги');
		//$this->setMeta('Мета Теги');
		//$this->way = 'Мета Теги';
		
		$this->tpl->define_dynamic('list', 'adm/edition_list.tpl');
		$this->tpl->define_dynamic('meta_tags', 'list');
		$this->tpl->define_dynamic('meta_tags_row', 'meta_tags');
		
		$items = $this->db->getAllRecords('SELECT * FROM `meta_tags` WHERE `lang` = "'.$this->lang.'" ORDER BY `id`');
		
		$items_num = sizeof($items);
		
		for($i=0; $i<$items_num; $i++){
			$this->tpl->assign(
				array(
					'ID' => $items[$i]['id'],
					'NAME' => $items[$i]['name'],
				)
			);
			$this->tpl->parse('META_TAGS_ROW', '.meta_tags_row');
		}
		
		$this->tpl->assign(array('PAGES' => ''));
		
		if($items_num > 0){        
			$this->tpl->parse('CONTENT', 'meta_tags');
		}
		else{
			$this->tpl->assign(array('CONTENT' => 'База буста!'));
		}
		
		return true;
	}
	
	public function edit_meta_tags(){
		$this->setMeta('Мета Теги', null, null, 'Мета Теги', ' <span>&rarr;</span> Мета Теги');
		//$this->way = ' <span>&rarr;</span> <a href="'.$this->langUrl.'/admin/meta_tags">Мета Теги</a>';
		
		$id = end($this->w);
		if(!ctype_digit($id)){
			er_404($this->langUrl.'/admin/meta_tags');
		}
		
		$this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('optimization_text', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
		$this->tpl->define_dynamic('hpic', 'edit');
		$this->tpl->define_dynamic('help', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$q = $this->db->getRow('SELECT * FROM `meta_tags` WHERE `id` = "'.$id.'"');
		
		if($this->db->getNumRows() != 1){
		    er_404($this->langUrl.'/admin/phrases');
		    exit;
		}
		
		$header = $q['metah1'];
		$title = $q['metatitle'];
		$keywords = $q['metakeywords'];
		$optimizationText = $q['optimization_text'];
		$description = $q['metadescription'];
		$body = $q['body'];
				
		$content = '';
		$referer = gp($_SERVER, 'HTTP_REFERER', $this->langUrl.'/');
		
		if(!empty($_POST)){			
		
			$collageFileName = '';
			if(!empty($_FILES) && $_FILES['collage']['size'] > 0){				
			    	
		    	if (is_file($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
					@unlink($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name']);
				}
			    	
				if (@copy($_FILES['collage']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'])) {				    				    	
					@chmod($_SERVER['DOCUMENT_ROOT'].'/pic/'.$id.'-'.$_FILES['collage']['name'], 0666);
		        	$collageFileName = $id.'-'.$_FILES['collage']['name'];
			        	
		    	}
				
			}
				
			
			$header = gpm($_POST, 'header', '');
			$title = gpm($_POST, 'title', '');
			$keywords = gpm($_POST, 'keywords', '');
			$description = gpm($_POST, 'description', '');
			$body = gpm($_POST, 'text', '');
			$optimizationText = gpm($_POST, 'optimization_text', '');
			
			
			$this->db->setQuery('UPDATE `meta_tags` SET `optimization_text`="'.$optimizationText.'", `collage`= "'.$collageFileName.'", `metah1` = "'.$header.'", `metatitle` = "'.$title.'", `metakeywords` = "'.$keywords.'", `metadescription` = "'.$description.'", `body` = "'.addslashes($body).'" WHERE `id` = '.$id);
		
		
			
			$content = "Данные изменены <meta http-equiv='refresh' content='2;URL=".$this->langUrl."/admin/meta_tags/'>";
			
			$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		}
		else{
			$this->tpl->assign(
				array(
					'HEADER' => $header,
					'TITLE' => $title,
					'KEYWORDS' => $keywords,
					'DESCRIPTION' => $description,
					'OPTIMIZATION_TEXT' => $optimizationText,
					'DELETE_COLLAGE'=>(!empty($q['collage']) ? " Удалить коллаж <a onclick=\"return confirm('Вы уверены что хотите удалить?'); return false;\" title=\"Удалить\" href=\"/admin/deletecmetacollage/$id\"><img height=\"12\" width=\"12\" src=\"/img/admin_icons/delete.png\"></a>" : ''),
					'REFERER' => $referer,
					'TEXT' => $body
				)
			);
			
			$help = array();
			
			$help[] = array('title' => 'Header', 'body' => 'В соответствующем поле прописывается заголок страницы.<br /><br />Например: <img src="/img/help/header.gif">', 'type' => 'header');
			$help[] = array('title' => 'Title', 'body' => 'Заголовок окна броузера.<br /><br />Например: <img src="/img/help/title.gif">', 'type' => 'title');
			$help[] = array('title' => 'Keywords', 'body' => 'Ключевые слова для сайта/страницы сайта', 'type' => 'keywords');
			$help[] = array('title' => 'Description', 'body' => 'Самые важные фразы, характеризующие тематику сайта/страницы.', 'type' => 'description');
			
						
			for ($i=0; $i<4; $i++) {
			    $this->tpl->assign(
                    array(
                        'HELP_TITLE' => $help[$i]['title'],
                        'HELP_TEXT' => $help[$i]['body'],
                        'HELP_TYPE' => $help[$i]['type'],
                        
                        
                    )
			    );
			    
			    $this->tpl->parse('CONTENT', '.help');
			}
			
			$this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.optimization_text');
			$this->tpl->parse('CONTENT', '.body');
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}
	
	/* Start Templates Section */
	private function templist() {
	    if(Auth::getPrivilege() != 'master') {
			er_404();
		}
		
		$this->setMeta('Список шаблонов', null, null, 'Список шаблонов', ' <span>&rarr;</span> Список шаблонов');
	    
	    $this->tpl->define_dynamic('_temp', 'adm/temp.tpl');
	    $this->tpl->define_dynamic('temp', '_temp');
	    $this->tpl->define_dynamic('templist', 'temp');
	    $this->tpl->define_dynamic('templist_item', 'templist');
	    
	    $tpl = $this->db->getAllRecords('SELECT * FROM `tpl`');
	    
	    $size = sizeof($tpl);
	    
	    for ($i=0; $i<$size; $i++) {
	        $this->tpl->assign(
	           array(
	               'NAME' => $tpl[$i]['name'],
	               'STATUS' => ($tpl[$i]['status'] == 1) ? '<span style="color: green; font-weight: bold;">online</span>' : '<span style="color: red; font-weight: bold;">offline</span>',
	               'PREVIEW' => $tpl[$i]['name'],
	               'ACTIVATE' => ($tpl[$i]['status'] == 1) ? '&nbsp;' : '<a href="/admin/activatetpl/'.$tpl[$i]['id'].'" title="Активировать"><img src="/img/admin_icons/topsale.png" alt="Активировать"></a>'
	           )
           );
           
           $this->tpl->parse('TEMPLIST_ITEM', '.templist_item');
	    }
	    
	    $this->tpl->parse('CONTENT', '.templist');
	    
	    return true;
	}
	
	private function activatetpl() {
	    if(Auth::getPrivilege() != 'master') {
			er_404();
		}
		
		$id = end($this->w);
		if(!ctype_digit($id)){
			er_404('/admin/templist');
		}
		
		$tpl = $this->db->getRow('SELECT * FROM `tpl` WHERE `id` = "'.$id.'"');
		
		if ($this->db->getNumRows() != 1) {
		    er_404('/admin/templist');
		}
		
		if ($tpl['status'] == 1) {
		    er_404('/admin/templist');
		}
		
		$this->db->setQuery('UPDATE `tpl` SET `status` = "0"');
		$this->db->setQuery('UPDATE `tpl` SET `status` = "1" WHERE `id` = "'.$id.'"');
		
		$content = "Шаблон активирован <meta http-equiv='refresh' content='2;URL=/admin/templist/'>";
			
		$this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
		
		return true;
	}
	
	private function reftpl() {
	    if(Auth::getPrivilege() != 'master') {
			er_404();
		}
		
		$tpl = $this->db->getAllRecords('SELECT * FROM `tpl` ORDER BY `id`');
		
		$folder = array();
		
        if ($handle = opendir('./tpl/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != "adm" && $file != "d" && is_dir('./tpl/'.$file)) {
                    $folder[] = $file;
                }
            }
            
            closedir($handle);
        }
        
        $size = sizeof($folder);
        
        $c = 0;
        
        for ($i=0; $i<$size; $i++) {
            if (!$this->checkFolderTpl($folder[$i], $tpl)) {
               if ($this->checkTpl($folder[$i])) {
                   $this->db->setQuery('INSERT INTO `tpl` (`path`, `status`, `name`) VALUES ("tpl/'.$folder[$i].'/", "0", "'.$folder[$i].'")');
                   $c++;
               }
            }
        }
		
        if ($c == 0) {
            $content = "Новых шаблонов не найдено! <meta http-equiv='refresh' content='2;URL=/admin/templist/'>";
        }
        elseif ($c > 0) {
            $content = "Были добавлены новые шаблоны! <meta http-equiv='refresh' content='2;URL=/admin/templist/'>";
        }
        else {
            er_404('/admin/templist');
        }
        
        $this->tpl->assign(array('CONTENT' => page::ViewMessage($content)));
        
		return true;
	}
	
	private function checkFolderTpl($folder, $tpl) {
	    if(Auth::getPrivilege() != 'master') {
			er_404();
		}
		
		for ($i=0; $i<sizeof($tpl); $i++) {
		    $name = explode('/', $tpl[$i]['path']);
		    $name = $name[1];
		    if ($folder == $name) {
		        return true;
		    }
		}
		
		return false;
	}
	
	private function checkTpl($folder) {
	    $path = './tpl/'.$folder;
	    
	    if (!is_dir($path)) {
	        return false;
	    }
	    
	    if (!file_exists($path.'/design.tpl') || !file_exists($path.'/index.tpl') || !file_exists($path.'/news.tpl')) {
	        return false;
	    }
	    
	    if (!is_dir($path.'/css')) {
	        return false;
	    }
	    
	    if (!file_exists($path.'/css/style.css')) {
	        return false;
	    }
	    
	    if (!is_dir($path.'/img')) {
	        return false;
	    }
	    
	    return true;
	}
	
	/* End Templates Section */
	
	private function uploadSinglePic($dir, $name, $width_small = 165, $height_small = 165) {
		ini_set('max_execution_time', '120');
		$result = array();
		$result['err'] = '';
		$result['pic'] = null;
		
		$tmp = explode('.', $name);
		$tmpSize = sizeof($tmp) - 1;
		
		$name = '';
		for($i=0; $i<$tmpSize; $i++){
			$name .= $tmp[$i];
		}
		
		$small_path = ".$dir";
		$big_path = ".$dir".'big/';
		
		if(!empty($_FILES['pic'])){
			$filename = $_FILES['pic']['tmp_name'];
		
			if($_FILES['pic']['type'] == 'image/jpeg' || $_FILES['pic']['type'] == 'image/pjpeg'){
				$type = $_FILES['pic']['type'];
				$types = array('image/png' => 'png', 'image/jpeg' => 'jpg', 'image/pjpeg' => 'jpg', 'image/gif' => 'gif');
				$extension = $types[$type];
				$name = $name.".".$extension;
				$result['pic'] = $name;
	
				$small_picture_name = $small_path.$name;
				$big_picture_name = $big_path.$name;
				
				if(copy($filename, $big_picture_name)){
					@chmod($small_picture_name, 0644);
					
					$thumb = imagecreatefromjpeg($filename);
					$img = getimagesize($filename);
	 
					if(($img[0]/$img[1]) >= 1){
						$height2_small=round(($width_small/$img[0])*$img[1]);
						$img2 = imagecreatetruecolor($width_small, $height_small);
						$color = imagecolorallocate($img2, 255, 255, 255);
						imagefill($img2, 0, 0, $color);
						$center = (($height_small-$height2_small)/2);
						
						imagecopyresampled($img2, $thumb, 0, $center, 0, 0, $width_small, $height2_small, $img[0], $img[1]);
					}
					else{
						$width2_small=round(($height_small/$img[1])*$img[0]);
						$img2 = imagecreatetruecolor($width_small, $height_small);
						$color = imagecolorallocate($img2, 255, 255, 255);
						imagefill($img2, 0, 0, $color);
						$center = (($width_small-$width2_small)/2);
						
						imagecopyresampled($img2, $thumb, $center, 0, 0, 0, $width2_small, $height_small, $img[0], $img[1]);
					}
					imagejpeg($img2, $small_picture_name, 85);
					imagedestroy($img2);
					@chmod($small_picture_name, 0644);
				}
				else{
					$result['err'] .= '<br>Ошибка загрузки файла.';
				}
			}
			else{
				$result['err'] .= '<br>Неправильный тип файла. Разрешены только *.jpg и *.jpeg.';
			}
		}
		$result = (!$result) ? true : $result;
		return $result;
	}
	
	private function upload_pic($files_name='hpic', $path, $name) {
		ini_set('max_execution_time', '120');
		ini_set('post_max_size', '32M');
		ini_set('upload_max_filesize', '32M');
		
		$result = array();
		$result['err'] = '';
		$result['pic'] = null;
		$types = array(
		'image/png' => 'png',
		'image/x-png' => 'png',
		'image/jpeg' => 'jpg',
		'image/pjpeg' => 'jpg',
		'image/gif' => 'gif', );
		
		$_exts = array();
		
		if(!empty($_FILES[$files_name]) && $_FILES[$files_name]['size'] > 0){
			$filename = $_FILES[$files_name]['tmp_name'];
			$type = $_FILES[$files_name]['type'];
			$ext = explode('.', $_FILES[$files_name]['name']);
		
			if(isset($types[$type]) || ($type == 'application/octet-stream' && in_array($ext[1], $_exts))){
				$extension = ($type != 'application/octet-stream') ? $types[$type]:$ext[1];
				$result['pic'] = $name.".".$extension;
				
				$size = getimagesize($_FILES[$files_name]['tmp_name']);
				
				if($size[0] != 417 || $size[1] != 206){
					$result['err'] .= 'Недопустимые размеры изображения!<br>';
					return $result;
				}
				
				if(copy($filename, '.'.$path.$name.".".$extension)){
					chmod('.'.$path.$name.".".$extension, 0644);
				}
				else{
					$result['err'] .= '<br>Ошибка загрузки файла.';
				}
			}
			else{
				$result['err'] .= '<br>Ошибка, допустимые форматы: png, jpeg & gif.';
			}
		
		}
		else{
			$result['err'] .=  '<br>Файл пуст!';
		}
		
		return $result;
	}
	
	private function uploadCatPic($from, $to, $maxwidth, $maxheight, $quality = 80) {
	    ini_set('max_execution_time', '120');
        
        // защита от Null-байт уязвимости PHP
		$from = preg_replace('/\0/uis', '', $from);
		$to = preg_replace('/\0/uis', '', $to);
        
		//$this->_err .= 'TEST';
		//return false;
		
		//if (copy($from, $to)) return true;
		//else {
		    //$this->_err .= '<br />Ошибка копирования файла!';
		    //return false;
		//}
		
		/*if (!$this->verify_image($from)) {
		    $this->_err .= '<br />HACKING ATTACK DETECT!';
		    return false;
		}*/
		
		// информация об изображении
		$imageinfo = @getimagesize($from);
		// если получить информацию не удалось - ошибка
		if (!$imageinfo) {
		    $this->_err .= '<br />Ошибка получения информации об изображении';
		    return false;
		}
		// получаем параметры изображения
		$width = $imageinfo[0];		// ширина
		$height = $imageinfo[1];	// высота
		$format = $imageinfo[2];	// ID формата (число)
		$mime = $imageinfo['mime'];	// mime-тип

		// определяем формат и создаём изображения
		switch ($format) {
			case 2: $img = imagecreatefromjpeg($from); break;	// jpg
			case 3: $img = imagecreatefrompng($from); break;	// png
			case 1: $img = imagecreatefromgif($from); break;	// gif
			default: $this->_err .= '<br />Неверный или недопустимый формат загружаемого файла!'; return false; break;
		}
		// если создать изображение не удалось - ошибка
		if (!$img) {
		    $this->_err .= '<br />Ошибка создания изображения!';
		    return false;
		}

		// меняем размеры изображения
		$newwidth = $width;
		$newheight = $height;
		// требуется квадратная картинка
		if ($maxwidth == $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// пропорции картинки одинаковы
				if ($width == $height) {
					$newwidth = $maxwidth;
					$newheight = $maxheight;
				}
				// ширина больше
				elseif ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется горизонтальная картинка
		elseif ($maxwidth > $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше
				if ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
					
					if ($newheight > $maxheight) {
					    $newheight = $maxheight;
                        $newwidth = intval(((float)$newheight / (float)$height) * $width);
					}
				}
				// высота больше или равна ширине
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
			    //echo '1';
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется вертикальная картинка
		elseif ($maxwidth < $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше или равна высоте
				if ($width >= $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}

		// если изменений над картинкой производить не надо - просто копируем её
		/*if ($newwidth == $width && $newheight == $height && $quality == 80) {
		    echo '123';
			if (copy($from, $to)) return true;
			else {
			    $this->_err .= '<br />Ошибка копирования файла!';
			    return false;
			}
		}*/

		// создаём новое изображение
		//$new = imagecreatetruecolor($newwidth, $newheight);
		$new = imagecreatetruecolor($maxwidth, $maxheight);
		$black = imagecolorallocate($new, 0, 0, 0);
		$white = imagecolorallocate($new, 255, 255, 255);
		// копируем старое в новое с учётом новых размеров
		imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
		//imagecolortransparent($new, $white);
		$center_w = round(($maxwidth-$newwidth)/2);
		$center_w = ($center_w < 0) ? 0 : $center_w;
		$center_h = round(($maxheight-$newheight)/2);
		$center_h = ($center_h < 0) ? 0 : $center_h;
		imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
		// создаём файл с новым изображением
		switch ($format) {
			case 2: // jpg
				if ($quality < 0) $quality = 0;
				if ($quality > 100) $quality = 100;
				imagejpeg($new, $to, $quality);
			break;
			case 3: // png
				$quality = intval($quality * 9 / 100);
				if ($quality < 0) $quality = 0;
				if ($quality > 9) $quality = 9;
				imagepng($new, $to, $quality);
			break;
			case 1: // gif
				imagegif($new, $to);
			break;
		}
		
		@chmod($to, 0644);
		
		return true;
	}
	
	private function uploadCatPic2($dir, $name, $width_small=133, $height_small=133, $width_big=442, $height_big=330) {
        ini_set('max_execution_time', '120');
        $result = array();
        $result['err'] = '';
        $result['pic'] = null;
        
        $big_path = $dir."big/";
        $small_path = $dir;
        //$width_big = 351;                             // ширина большой картинки
        //$height_big = 261;                             // высота большой картинки
        //$width_small = 149;                             // ширина маленькой картинки
        //$height_small = 127;                             // высота маленькой картинки

        //$space_y = 0;

        //$stamp_s = imagecreatefrompng('pic/markers/small.png');
        //$stamp_b = imagecreatefrompng('pic/markers/large.png');

        
        if (!empty($_FILES['pic'])) {
            $filename = $_FILES['pic']['tmp_name'];

            if ($_FILES['pic']['type'] == 'image/jpeg' || $_FILES['pic']['type'] == 'image/pjpeg') {
                $type = $_FILES['pic']['type'];
                $types = array('image/png'=>'png','image/jpeg'=>'jpg','image/pjpeg'=>'jpg','image/gif'=>'gif');
                $extension = $types[$type];
                //$name = $name;
                $result['pic'] = $name;
                //название для картинки
                $big_picture_name = $big_path.$name;
                //название для превьюшки
                $small_picture_name = $small_path.$name;

                if (copy($filename, $big_picture_name)) {
                    //готовим большую картинку из того, что закачали
                    $big = imagecreatefromjpeg($big_picture_name);
                    $img_big = getimagesize($big_picture_name);
                    //print_r($img_big); exit;
/*
                    if ($width_big > $img_big[0]) {
                        $width_big = $img_big[0];
                    }

                    if ($height_big > $img_big[1]) {
                        $height_big = $img_big[1];
                    }*/
                        
                    if (($img_big[0]/$img_big[1]) >= 1) {

                        $height2_big=round(($width_big/$img_big[0])*$img_big[1]);
                        $img2_big = imagecreatetruecolor($width_big, $height_big);
                        $color = imagecolorallocate($img2_big, 255, 255, 255);
                        imagefill($img2_big, 0, 0, $color);
                        $center = (($height_big-$height2_big)/2);
                        imagecopyresampled($img2_big,$big,0,$center,0,0,$width_big,$height2_big,$img_big[0],$img_big[1]);
                        //imagecopyresampled($img2_big,$stamp_b,0,$space_y,0,0,$width_big,$height_big,$width_big,$height_big);
                    } else {
                        
                        $width2_big=round(($height_big/$img_big[1])*$img_big[0]);
                        $img2_big = imagecreatetruecolor($width_big, $height_big);
                        $color = imagecolorallocate($img2_big, 255, 255, 255);
                        imagefill($img2_big, 0, 0, $color);
                        $center = (($width_big-$width2_big)/2);

                        imagecopyresampled($img2_big,$big,$center,0,0,0,$width2_big,$height_big,$img_big[0],$img_big[1]);
                        //imagecopyresampled($img2_big,$stamp_b,0,$space_y,0,0,$width_big,$height_big,$width_big,$height_big);
                    }
                    imagejpeg($img2_big, $big_picture_name, 85);
                    imagedestroy($img2_big);
                    @chmod($big_picture_name, 0644);

                    //готовим превьюшки
                    $thumb = imagecreatefromjpeg($filename);
                    $img = getimagesize($filename);

                    if (($img[0]/$img[1]) >= 1) {
                        //echo 1;
                        $height2_small=round(($width_small/$img[0])*$img[1]);
                        $img2 = imagecreatetruecolor($width_small, $height_small);
                        $color = imagecolorallocate($img2, 255, 255, 255);
                        imagefill($img2, 0, 0, $color);
                        $center = (($height_small-$height2_small)/2);

                        imagecopyresampled($img2, $thumb, 0, $center,0,0, $width_small, $height2_small, $img[0], $img[1]);
                        //imagecopyresampled($img2, $stamp_s,0,0,0,0,$width_small,$height_small,$width_small,$height_small);
                    } else {
                        //echo 2;
                        $width2_small=round(($height_small/$img[1])*$img[0]);
                        $img2 = imagecreatetruecolor($width_small, $height_small);
                        $color = imagecolorallocate($img2, 255, 255, 255);
                        imagefill($img2, 0, 0, $color);
                        $center = (($width_small-$width2_small)/2);

                        imagecopyresampled($img2, $thumb, $center,0,0,0, $width2_small, $height_small, $img[0], $img[1]);
                        //imagecopyresampled($img2, $stamp_s,0,0,0,0,$width_small,$height_small,$width_small,$height_small);
                    }
                    imagejpeg($img2, $small_picture_name, 85);
                    imagedestroy($img2);
                    @chmod($small_picture_name, 0644);
                } else {
                    $result['err'] .= '<br>Ошибка загрузки файла.';
                }
            } else {
                $result['err'] .= '<br>Неправильный тип файла. Разрешены только *.jpg и *.jpeg.';
            }
        }
        $result = (!$result) ? true:$result;
        return $result;
        // end
    }
	
}

?>