<?php

define('ADMIN_PHP', 'ADMIN_PHP');


require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

require_once PATH . 'library/Pictures.php';

require_once PATH . 'library/FormManager.php';

require_once PATH . 'library/User.php';

class Admin extends User implements Main_Interface
{

    protected $catalogOptions = array();
    private $catalogImagesOptions = array(
        'real' => array('path' => '/img/catalog/real/', 'size' => array('width' => 600, 'height' => 600), 'stamp' => '/img/watermarks/watermark600x600.png'),
        'big' => array('path' => '/img/catalog/big/', 'size' => array('width' => 368, 'height' => 368), 'stamp' => '/img/watermarks/watermark370x370.png'),
        'small1' => array('path' => '/img/catalog/small_1/', 'size' => array('width' => 200, 'height' => 180), 'stamp' => false),
        'small2' => array('path' => '/img/catalog/small_2/', 'size' => array('width' => 122, 'height' => 120), 'stamp' => false),
    );

    public function factory()
    {

        if (!$this->_isAdmin()) {
            return false;
        }

        $this->tpl->define_dynamic('edit', 'adm/edit.tpl');
        $this->catalogOptions = $this->getConfig('catalog');

        return true;
    }

    public function main()
    {
        return $this->error404();
    }

    // Редактирование новостей
    public function addnews()
    {
        $this->setMetaTags('Добавление новости');
        $this->setWay('Добавление новости');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('s_pic', 'edit');
        $this->tpl->define_dynamic('s_show_pic', 's_pic');
        $this->tpl->define_dynamic('s_show_pic_dell', 's_show_pic');

        $this->tpl->define_dynamic('s_adress', 'edit');
        $this->tpl->define_dynamic('s_meta', 'edit');
        $this->tpl->define_dynamic('s_visible', 'edit');
        $this->tpl->define_dynamic('news', 'edit');
        $this->tpl->define_dynamic('s_body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $href = $this->ru2Lat($this->getVar('adm_href', ''));
        //$href = str_replace(' ', '_', $this->getVar('adm_href', ''));
        $topnews = $this->getVar('topnews', 0);
        $date = $this->getVar('date', date('d.m.Y'));
        $preview = $this->getVar('preview', '');
        $header = $this->getVar('header', '');
        $title = $this->getVar('title', '');
        $keywords = $this->getVar('keywords', '');
        $description = $this->getVar('description', '');
        $visible = $this->getVar('visible', 1);
        $body = $this->getVar('body', '');
        $this->tpl->parse('S_SHOW_PIC', 'null');

        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }

        $topnews_s = '';
        if ($topnews == 1) {
            $topnews_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $topnews_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }

        if (!empty($_POST)) {
            $referer = $this->getVar('HTTP_REFERER', '');

            if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `news` WHERE `href` = "' . $href . '" AND `language` = "' . $this->lang . '"');

                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        }

        if (!empty($_POST) && !$this->_err) {
            $date = explode('.', $date);

            $date = mktime(date('H'), date('i'), date('s'), $date[1], $date[0], $date[2]);

            $data = array(
                'href' => $href,
                'top' => $topnews,
                'date' => $date,
                'preview' => stripslashes($preview),
                'header' => $header,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'visibility' => $visible,
                'body' => stripslashes($body),
                'language' => $this->lang
            );

            $id = $this->db->fetchOne("SELECT MAX(`id`) FROM `news` WHERE `language`='" . $this->lang . "'") + 1;

            if ($pic = $this->getVar('pic')) {
                if (null !== $pic && $pic['error'] == 0) {
                    if (!$this->uploadCatPic($pic['tmp_name'], './img/news/' . $id . '-' . $pic['name'], 137, 102)) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }

                    $data['pic'] = $id . '-' . $pic['name'];
                }
            }
        }

        if (!empty($_POST) && !$this->_err) {
            $this->db->insert("news", $data);

            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Новость успешно добавлена<meta http-equiv='refresh' content='2;URL=$referer'>";

            $this->viewMessage($content);
        }

        if ($this->_err) {
            $this->viewErr();
        }

        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                    array(
                        'ADM_HREF' => $href,
                        'ADM_TOPNEWS' => $topnews_s,
                        'ADM_DATE' => $this->convertDate($date),
                        'ADM_PREVIEW' => stripslashes($preview),
                        'ADM_BODY' => stripslashes($body),
                        'VISIBLE_S' => $visible_s,
                        'ADM_HEADER' => $header,
                        'ADM_TITLE' => $title,
                        'ADM_KEYWORDS' => $keywords,
                        'ADM_DESCRIPTION' => $description,
                        'REFERER' => $referer,
                        'ADM_IMG_TITLE' => '',
                        'ADM_IMG_ALT' => '',
                        'ADM_MODULE_CLASS_NAME' => 'news'
                    )
            );

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.mce');

            $this->tpl->parse('CONTENT', '.s_adress');


            $this->tpl->parse('CONTENT', '.s_pic');

            $this->tpl->parse('CONTENT', '.s_visible');
            $this->tpl->parse('CONTENT', '.news');
            $this->tpl->parse('CONTENT', '.s_meta');
            $this->tpl->parse('CONTENT', '.s_body');
            $this->tpl->parse('CONTENT', '.end');
        }

        return true;
    }

    public function editnews()
    {
        $id = end($this->url);

        if (!ctype_digit($id)) {
            return $this->error404();
        }

        $news = $this->db->fetchRow("SELECT * FROM `news` WHERE `id` = '$id'");

        if (!$news) {
            return $this->error404();
        }

        $this->setMetaTags('Редактирование новости');
        $this->setWay('Редактирование новости');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('s_pic', 'edit');
        $this->tpl->define_dynamic('s_show_pic', 's_pic');
        $this->tpl->define_dynamic('s_show_pic_dell', 's_show_pic');
        $this->tpl->define_dynamic('s_adress', 'edit');
        $this->tpl->define_dynamic('s_meta', 'edit');
        $this->tpl->define_dynamic('s_visible', 'edit');
        $this->tpl->define_dynamic('news', 'edit');
        $this->tpl->define_dynamic('s_body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $href = $news['href'];
        $topnews = $news['top'];
        $date = $this->convertDate($news['date']);

        $preview = $news['preview'];
        $header = $news['header'];
        $title = $news['title'];
        $keywords = $news['keywords'];
        $description = $news['description'];
        $visible = $news['visibility'];
        $body = $news['body'];
        $pic = $news['pic'];

        $this->tpl->parse('S_SHOW_PIC', 'null');



        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/news/' . $pic)) {

            $this->tpl->parse('S_SHOW_PIC', '.s_show_pic');
            //$this->tpl->parse('S_SHOW_PIC_DELL', '.s_show_pic_dell');
            $this->tpl->assign('ADM_IMG_SRC', '/img/news/' . $pic);
        } else {
            $this->tpl->parse('S_SHOW_PIC_DELL', 'null');
        }


        if (!empty($_POST)) {
            $referer = $this->getVar('HTTP_REFERER', '');

            //$href = str_replace(' ', '_', $this->getVar('adm_href', ''));
            $href = $this->ru2Lat($this->getVar('adm_href', ''));
            $topnews = $this->getVar('topnews', 0);
            $date = $this->getVar('date', date('d.m.Y'));
            $preview = $this->getVar('preview', '');
            $header = $this->getVar('header', '');
            $title = $this->getVar('title', '');
            $keywords = $this->getVar('keywords', '');
            $description = $this->getVar('description', '');
            $visible = $this->getVar('visible');

            $body = $this->getVar('body', '');

            if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `news` WHERE `href` = '$href' AND `language` = '" . $this->lang . "' AND `id` <> '" . $id . "'");

                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        }

        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }

        $topnews_s = '';
        if ($topnews == 1) {
            $topnews_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $topnews_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }

        if (!empty($_POST) && !$this->_err) {
            $date = explode('.', $date);

            $date = mktime(date('H'), date('i'), date('s'), $date[1], $date[0], $date[2]);

            $data = array(
                'href' => $href,
                'top' => $topnews,
                'date' => $date,
                'preview' => stripslashes($preview),
                'header' => $header,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'visibility' => $visible,
                'body' => stripslashes($body)
            );

            if ($pic = $this->getVar('pic')) {
                if (null !== $pic && $pic['error'] == 0) {
                    if (!$this->uploadCatPic($pic['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img/news/' . $id . '-' . $pic['name'], 137, 102)) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }

                    $data['pic'] = $id . '-' . $pic['name'];
                }
            }
        }

        if (!empty($_POST) && !$this->_err) {
            $n = $this->db->update('news', $data, "id = $id");

            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Новость успешно отредактирована<meta http-equiv='refresh' content='2;URL=$referer'>";

            $this->viewMessage($content);
        }

        if ($this->_err) {
            $this->viewErr();
        }


        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                    array(
                        'ADM_HREF' => $href,
                        'ADM_TOPNEWS' => $topnews_s,
                        'ADM_DATE' => $date,
                        'ADM_PREVIEW' => stripslashes($preview),
                        'ADM_BODY' => stripslashes($body),
                        'VISIBLE_S' => $visible_s,
                        'ADM_HEADER' => $header,
                        'ADM_TITLE' => $title,
                        'ADM_KEYWORDS' => $keywords,
                        'ADM_DESCRIPTION' => $description,
                        'ADM_IMG_TITLE' => '',
                        'ADM_DELL_PHOT_METHOD' => 'deletenewsphoto',
                        'ADM_DELL_PHOTO_ID' => $id,
                        'ADM_IMG_ALT' => '',
                        'REFERER' => $referer
                    )
            );

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.mce');
            $this->tpl->parse('CONTENT', '.s_adress');
            $this->tpl->parse('CONTENT', '.s_pic');
            $this->tpl->parse('CONTENT', '.s_visible');
            $this->tpl->parse('CONTENT', '.news');
            $this->tpl->parse('CONTENT', '.s_meta');
            $this->tpl->parse('CONTENT', '.s_body');
            $this->tpl->parse('CONTENT', '.end');
        }

        return true;
    }

    public function deletenews()
    {
        $id = end($this->url);

        if (!ctype_digit($id)) {
            return $this->error404();
        }

        $news = $this->db->fetchRow("SELECT * FROM `news` WHERE `id` = '$id'");

        if (!$news) {
            return $this->error404();
        }

        $this->setMetaTags('Удаление новости');
        $this->setWay('Удаление новости');

        if ($news['pic'] != '' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/img/news/' . $news['pic'])) {
            @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/news/' . $news['pic'], 0666);
            @unlink($_SERVER['DOCUMENT_ROOT'] . '/img/news/' . $news['pic']);
        }

        $n = $this->db->delete('news', "id = $id");

        $referer = $this->getVar('HTTP_REFERER', $this->basePath);

        $content = "Новость успешно удалена<meta http-equiv='refresh' content='2;URL=$referer'>";
        $this->viewMessage($content);

        return true;
    }

    public function menu()
    {
        $type = end($this->url);

        switch ($type) {
            case 'horisontal' :
            case 'vertical' :
                break;
            default : return $this->error404();
                break;
        }

        $meta = ($type == 'horisontal' ? 'Горизонтальное ' : 'Вертикальное ') . 'меню';

        $this->setMetaTags($meta);
        $this->setWay($meta);

        $menus = $this->db->fetchAll("SELECT `id`, `header`, `href`, `type`, preview FROM `page` WHERE `level` = '0' AND `menu` = '$type' ORDER BY `position`, `header`");

        $this->tpl->define_dynamic('_section', 'pages.tpl');
        $this->tpl->define_dynamic('section', '_section');
        $this->tpl->define_dynamic('section_row', 'section');

        $this->tpl->parse('SECTION_ROW', 'null');

        $menuAdd = '';
        if ($this->_isAdmin()) {
            $templates = $this->loadAdminButtonsTemplate();

            $templates->assign(
                    array(
                        'ADD_SECTION_URL' => '/admin/addsection/horisontal',
                        'ADD_SECTION_TITLE' => 'Добавить раздел',
                        'ADD_PAGE_URL' => '/admin/addpage/horisontal',
                        'ADD_PAGE_TITLE' => 'Добавить страницу',
                        'ADD_LINK_URL' => '/admin/addlink/horisontal',
                        'ADD_LINK_TITLE' => 'Добавить ссылку'
                    )
            );

            $templates->parse('ADMIN_BUTTONS_ADD', 'admin_buttons_add');

            $menuAdd = $templates->prnt_to_var('ADMIN_BUTTONS_ADD');
        }

        $this->tpl->assign('PAGES_LIST_ADMIN', '');
        $this->tpl->assign('CONTENT', $menuAdd);

        $adminClassName = '';

        if ($menus) {
            foreach ($menus as $menu) {
                $this->setAdminButtons('/admin/editpage/' . $menu['id'], '/admin/deletepage/' . $menu['id']);
                $this->tpl->assign(
                        array(
                            'PAGE_ID' => $menu['id'],
                            'PAGE_ADM' => '', //$this->getAdminEdit('page', $menu['id']),
                            'ADMIM_CLASS_NAME' => $adminClassName,
                            'PAGE_ADRESS' => $menu['type'] == 'link' ? $menu['href'] : $this->basePath . $menu['href'],
                            'PAGE_PREVIEW' => stripslashes($menu['preview']),
                            'PAGE_HEADER' => stripslashes($menu['header']),
                            'ADMIN_BUTTONS_PARENT' => 'horisontal'
                        )
                );

                $menuEdit = '';
                if ($this->_isAdmin()) {
                    $templates->assign(
                            array(
                                'BUTTON_EDIT_URL' => '/admin/editpage/' . $menu['id'],
                                'BUTTON_EDIT_TITLE' => 'Редактировать элемент',
                                'BUTTON_DELETE_URL' => '/admin/deletepage/' . $menu['id'],
                                'BUTTON_DELETE_TITLE' => 'Удалить элемент'
                            )
                    );

                    $templates->parse('BUTTON_SETTINGS', 'null');
                    $templates->parse('BUTTON_FEATURES', 'null');

                    $templates->parse('ADMIN_BUTTONS_ACTION', 'admin_buttons_action');

                    $menuEdit = $templates->prnt_to_var('ADMIN_BUTTONS_ACTION');
                }

                $this->tpl->assign('PAGES_ITEM_ADMIN', $menuEdit);

                $this->tpl->parse('SECTION_ROW', '.section_row');
            }
            $this->tpl->assign(
                    array(
                        'PAGINATION' => ''
                    )
            );
            $this->tpl->parse('CONTENT', '.section');
        } else {
            $this->viewMessage('{EMPTY_SECTION}');
        }

        return true;
    }

    public function deletenewsphoto()
    {

        if (!is_numeric($id = end($this->url))) {
            $this->addErr("Ключ должен быть числом");
        }

        if (!$this->_err) {
            $this->setMetaTags('Удаление фото из новости');
            $this->setWay('Удаление фото из новости');
            if ($this->deletePicId($id, 'news', array('small' => $_SERVER['DOCUMENT_ROOT'] . '/img/news'))) {
                $content = "Фото удалено<meta http-equiv='refresh' content='2;URL=/admin/editnews/$id'>";
                $this->viewMessage($content);
            }
        } else {
            $this->setMetaTags('Ошибка !!');
            $this->setWay('Ошибка !!!');
            $this->viewErr();
        }

        return true;
    }

    // Удаляет картинки по ID и имени таблицы
    protected function deletePicId($id, $tableName, $pathArr = array(), $fieldName = 'pic')
    {
        $ret = false;
        if (($row = $this->db->fetchRow("SELECT `pic` FROM `$tableName` WHERE `id` = '$id'"))) {
            if (isset($pathArr['small']) && is_file($pathArr['small'] . '/' . $row['pic'])) {
                @chmod($pathArr['small'] . '/' . $row['pic'], 0666);
                @unlink($pathArr['small'] . '/' . $row['pic']);
                $ret = true;
            }

            if (isset($pathArr['big']) && is_file($pathArr['big'] . '/' . $row['pic'])) {
                @chmod($pathArr['big'] . '/' . $row['pic'], 0666);
                @unlink($pathArr['big'] . '/' . $row['pic']);
                $ret = true;
            }

            if (isset($pathArr['real']) && is_file($pathArr['real'] . '/' . $row['pic'])) {
                @chmod($pathArr['real'] . '/' . $row['pic'], 0666);
                @unlink($pathArr['real'] . '/' . $row['pic']);
                $ret = true;
            }
        } else {
            $this->addErr("Не могу найти данные по ID $id для удаления картинки");
        }

        return $ret;
    }

    // ============== Каталог
    // Добавление раздела
    public function addcatsection()
    {

        $this->setMetaTags("Добавление раздела");
        $this->setWay("Добавление раздела");

        $level = 0;
        $id = 0;
        if (count($this->url) > 0) {
            $id = end($this->url);
            if (is_numeric($id) && $id > 0) {
                if (!($id = $this->db->fetchOne('SELECT `id` FROM `catalog` WHERE `id` = "' . $id . '"'))) {
                    $this->addErr("Не удалось найти раздел");
                } else {
                    $level = $id;
                }
            }
        }


        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('js', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('name', 'edit');
        $this->tpl->define_dynamic('adress', 'edit');

        $this->tpl->define_dynamic('pic', 'edit');
        $this->tpl->define_dynamic('show_pic', 'pic');
        $this->tpl->define_dynamic('pic_title', 'edit');
        $this->tpl->define_dynamic('pic_alt', 'edit');
        $this->tpl->define_dynamic('pic_alt_title_info', 'edit');
        $this->tpl->define_dynamic('pos', 'edit');
        $this->tpl->define_dynamic('visible', 'edit');

        $this->tpl->define_dynamic('meta', 'edit');
        $this->tpl->define_dynamic('preview', 'edit');
        $this->tpl->define_dynamic('body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $name = $this->getVar('name', '');
        $href = $this->ru2Lat($this->getVar('adm_href', ''));
        $header = $this->getVar('header', '');
        $title = $this->getVar('title', '');
        $picTitle = $this->getVar('pic_title', '');
        $picAlt = $this->getVar('pic_alt', '');

        $keywords = $this->getVar('keywords', '');
        $description = $this->getVar('description', '');
        $preview = $this->getVar('preview', '');
        $position = $this->getVar('position', '9999');
        $body = $this->getVar('body', '');
        // $referrer = $this->getVar('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
        $referrer = $this->refererInit($id, 'catalog');

        $visible = $this->getVar('visible', '1');
        $pic = '';

        $this->tpl->parse('SHOW_PIC', 'null');

        if (!empty($_POST) && empty($name)) {
            $this->addErr("Поле <b>Название</b> не может быть пустым");
        }

        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }

        $this->tpl->assign(
                array('ADM_HEADER' => $header,
                    'ADM_NAME' => $name,
                    'ADM_TITLE' => $title,
                    'ADM_PIC_TITLE' => $picTitle,
                    'ADM_PIC_ALT' => $picAlt,
                    'ADM_HREF' => $href,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'ADM_PREVIEW' => $preview,
                    'ADM_POSITION' => stripslashes($position),
                    'VISIBLE_S' => $visible_s,
                    'REFERER' => $referrer,
                    'ADM_BODY' => stripslashes($body)
        ));


        if (!empty($_POST) && !$this->_err) {

            if (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] > 0) {

                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $pic)) {
                    @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $pic);
                }
                $pos = strrpos($_FILES['pic']['name'], '.');
                $fNameTmp = substr($_FILES['pic']['name'], 0, $pos);
                $fNameTmp = $this->ru2Lat($fNameTmp);

                $fExTmp = substr($_FILES['pic']['name'], $pos, strlen($_FILES['pic']['name']));
                $_FILES['pic']['name'] = $fNameTmp . $fExTmp;

                $handle = new upload($_FILES['pic']);


                if ($handle->uploaded) {
                    $handle->image_resize = true;
                    $handle->image_x = 101;
                    $handle->image_y = 99;
                    $handle->image_ratio_fill = 'LT';
                    $handle->file_overwrite = false;
                    $handle->file_safe_name = false;
                    $handle->image_background_color = '#FFFFFF';

                    //$handle->image_text = $text ;
                    //$handle->image_ratio_y        = true;



                    $handle->process($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/section/");
                    if ($handle->processed) {
                        //echo 'image resized';
                        $pic = $handle->file_dst_name;
                    } else {
                        $this->addErr("Ошибка загрузки картинки: " . $handle->error);
                    }
                }
            }

            if (empty($picTitle)) {
                $picTitle = $name;
            }

            if (empty($picAlt)) {
                $picAlt = $name;
            }

            if (empty($header)) {
                $header = $name;
            }

            if (empty($title)) {
                $title = $name;
            }

            $data = array(
                'artikul' => uniqid(),
                'name' => $name,
                'href' => $href,
                'body' => $body,
                'preview' => $preview,
                'header' => $header,
                'title' => $title,
                'pic_title' => $picTitle,
                'pic_alt' => $picAlt,
                'pic' => $pic,
                'position' => $position,
                'level' => $level,
                'keywords' => $keywords,
                'description' => $description,
                'visibility' => $visible,
                'language' => $this->lang,
                'changed' => date('Y-m-d H:i:s'),
                'type' => 'section'
            );


            $this->addSectionOption($level, $href, $action = 'add');



            if (isset($data['pic']) && empty($data['pic'])) {
                unset($data['pic']);
            }


            $this->db->insert('catalog', $data);

            $content = "Новый раздел добавлен <meta http-equiv='refresh' content='2;URL=$referrer' />";

            $this->viewMessage($content);
            return true;
        } else {
            $this->viewErr();
        }


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.js');
        $this->tpl->parse('CONTENT', '.mce');
        $this->tpl->parse('CONTENT', '.name');
        $this->tpl->parse('CONTENT', '.adress');
        $this->tpl->parse('CONTENT', '.meta');

        $this->tpl->parse('CONTENT', '.pic');
        $this->tpl->parse('CONTENT', '.pic_alt_title_info');
        $this->tpl->parse('CONTENT', '.pic_title');
        $this->tpl->parse('CONTENT', '.pic_alt');
        $this->tpl->parse('CONTENT', '.pos');
        $this->tpl->parse('CONTENT', '.visible');

        $this->tpl->parse('CONTENT', '.preview');
        //$this->tpl->parse('CONTENT', '.body');
        $this->tpl->parse('CONTENT', '.end');
        return true;
    }

    // Редактирование раздела
    public function editcatsection()
    {

        $this->setMetaTags("Редактирование раздела");
        $this->setWay("Редактирование раздела");
        $section = false;

        if (count($this->url) > 0) {
            $id = end($this->url);
            if (is_numeric($id)) {
                $section = $this->db->fetchRow('SELECT * FROM `catalog` WHERE `id` = "' . $id . '"');
            } else {
                $section = false;
            }
        }

        if (!$section) {
            $this->addErr("Не удалось найти раздел");
            $this->viewErr();
            return true;
        }

        $this->setMetaTags("Редактирование раздела " . $section['name']);
        $this->setWay("Редактирование раздела" . $section['name']);

        $this->tpl->define_dynamic('edit', 'adm/edit_catalog_slider.tpl');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('js', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('name', 'edit');
        $this->tpl->define_dynamic('adress', 'edit');
        $this->tpl->define_dynamic('section_pic', 'edit');
        $this->tpl->define_dynamic('show_pic', 'section_pic');
        $this->tpl->define_dynamic('show_pic_dell', 'section_pic');

        $this->tpl->define_dynamic('pos', 'edit');
        $this->tpl->define_dynamic('pic_title', 'edit');
        $this->tpl->define_dynamic('pic_alt', 'edit');
        $this->tpl->define_dynamic('pic_alt_title_info', 'edit');
        $this->tpl->define_dynamic('visible', 'edit');
        $this->tpl->define_dynamic('meta', 'edit');
        $this->tpl->define_dynamic('s1_preview', 'edit');
        $this->tpl->define_dynamic('body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');


        $name = $this->gp($section, 'name', '');
        $href = $this->gp($section, 'href', '');
        $header = $this->gpm($section, 'header', '');
        $title = $this->gpm($section, 'title', '');

        $keywords = $this->gpm($section, 'keywords', '');
        $description = $this->gpm($section, 'description', '');
        $preview = $this->gpm($section, 'preview', '');
        $body = $this->gpm($section, 'body', '');
        $picTitle = $this->gp($section, 'pic_title', '');
        $picAlt = $this->gp($section, 'pic_alt', '');
        $pic = $this->gp($section, 'pic', '');
        $visible = $this->gp($section, 'visibility', '1');
        $position = $this->gp($section, 'position', '9999');

        $formManagerOptions = array('small1' => array('path' => '/img/catalog/section/', 'size' => array('width' => 145, 'height' => 107)));


        $referrer = $this->getVar('HTTP_REFERER', $this->gpm($_SERVER, 'HTTP_REFERER', ''));



        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $pic)) {

            $this->tpl->assign(array(
                'ADM_IMG_SRC' => '/img/catalog/section/' . $pic,
                'ADM_IMG_ALT' => $picAlt,
                'ADM_IMG_TITLE' => $picTitle,
                'CAT_ID' => $section['id'],
                'ADM_DELL_PHOT_METHOD' => 'deletesectionimage',
                'ADM_DELL_PHOTO_ID' => $section['id']
            ));
            $this->tpl->parse('SHOW_PIC', '.show_pic');
            $this->tpl->parse('SHOW_PIC_DELL', '.show_pic_dell');
        } else {
            $this->tpl->parse('SHOW_PIC', 'null');
            $this->tpl->parse('SHOW_PIC_DELL', 'null');
        }

        if (isset($_POST['dell_pic'])) {
            if (!empty($pic)) {
                $picPath1 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small/' . $pic;

                if (is_file($picPath1)) {
                    @chmod($picPath1, 0666);
                    @unlink($picPath1);
                }
            }
        }

        if (!empty($_POST) && empty($name)) {
            $this->addErr("Поле <b>Название</b> не может быть пустым");
        }

        if (!empty($_POST)) {
            $name = $this->getVar('name', $section['name']);
            $href = $this->ru2Lat($this->getVar('adm_href', ''));
            $header = $this->getVar('header', '');
            $title = $this->getVar('title', '');
            $keywords = $this->getVar('keywords', '');
            $description = $this->getVar('description', '');
            $preview = $this->getVar('preview', '');
            $body = $this->getVar('body', '');
            $picTitle = $this->getVar('pic_title', '');
            $picAlt = $this->getVar('pic_alt', '');
            $visible = $this->getVar('visible', '1');
            $position = $this->getVar('position', '9999');
        }

        $visible_s = '';
        if ($visible == '1') {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }

        $this->tpl->assign(
                array('ADM_HEADER' => $header,
                    'ADM_NAME' => $name,
                    'ADM_TITLE' => $title,
                    'ADM_HREF' => $href,
                    'ADM_PIC_TITLE' => $picTitle,
                    'ADM_PIC_ALT' => $picAlt,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'ADM_PREVIEW' => stripslashes($preview),
                    'ADM_BODY' => stripslashes($body),
                    'VISIBLE_S' => $visible_s,
                    'ADM_POSITION' => $position,
                    'REFERER' => $referrer
        ));




        if (!empty($_POST) && !$this->_err) {

            $data = array(
                'name' => $name,
                'href' => $href,
                'body' => $body,
                'preview' => $preview,
                'header' => $header,
                'title' => $title,
                'pic_title' => $picTitle,
                'pic_alt' => $picAlt,
                'keywords' => $keywords,
                'description' => $description,
                'changed' => time(),
                'visibility' => $visible,
                'position' => $position,
                'language' => $this->lang,
            );
//
//            if (isset($_SESSION['form_manager'][$id]['img'])) {
//                if (isset($pic) && !empty($pic) && is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $formManagerOptions['small1']['path'] . '/' . $pic)) {
//                    @chmod($_SERVER['DOCUMENT_ROOT'] . '/' . $formManagerOptions['small1']['path'] . '/' . $pic, 0666);
//                    @unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $formManagerOptions['small1']['path'] . '/' . $pic);
//                }
//
//                $data['pic'] = $_SESSION['form_manager'][$id]['img'];
//                unset($_SESSION['form_manager']);
//            }
//
//            if (isset($data['pic']) && empty($data['pic'])) {
//                unset($data['pic']);
//            }


            if (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] > 0) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $_FILES['pic']['name'])) {
                    @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $_FILES['pic']['name'], 0666);
                    @unlink($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $_FILES['pic']['name']);
                }
                // var_dump($_FILES['pic']['size']); die;
                $this->uploadCatPic($_FILES['pic']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $_FILES['pic']['name'], 145, 107, 100, false);
                $data['pic'] = $_FILES['pic']['name'];
                // var_dump($_FILES['form_manager']['name']['pic_pic']); die;
            } else {
                $pic = '';
            }

            if (empty($picTitle)) {
                $picTitle = $name;
            }

            if (empty($picAlt)) {
                $picAlt = $name;
            }

            if (empty($header)) {
                $header = $name;
            }

            if (empty($title)) {
                $title = $name;
            }

            $this->addSectionOption($section['level'], $section['href'], $action = 'update');


            $this->db->update('catalog', $data, "id=$id");
            $referrer = $this->getVar('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
            $content = "Данные изменены<meta http-equiv='refresh' content='2;URL=$referrer'>";
            $this->viewMessage($content);
            return true;
        } else {
            $this->viewErr();
        }


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.js');
        $this->tpl->parse('CONTENT', '.mce');
        $this->tpl->parse('CONTENT', '.name');
        $this->tpl->parse('CONTENT', '.adress');
        $this->tpl->parse('CONTENT', '.s1_preview');
        $this->tpl->parse('CONTENT', '.meta');

//        $this->tpl->parse('CONTENT', '.s_pic');
//        $this->tpl->parse('CONTENT', '.pic');
//        $this->tpl->parse('CONTENT', '.pic_alt_title_info');
//        $this->tpl->parse('CONTENT', '.pic_title');
//        $this->tpl->parse('CONTENT', '.pic_alt');
//        $formManager1 = new FormManager($this->tpl, $this->db, 'catalog', $id);
//        $formManager1->addField(array('type' => 'image', 'name' => 'pic', 'title' => 'Изображение', 'group' => 'Изображения/Галерея'));
//        $formManager1->setOptions($formManagerOptions);
//
//        $formManager1->show(false);


        $this->tpl->parse('CONTENT', '.section_pic');
        $this->tpl->parse('CONTENT', '.pos');
        $this->tpl->parse('CONTENT', '.visible');

        //$this->tpl->parse('CONTENT', '.body');
        $this->tpl->parse('CONTENT', '.end');
        return true;
    }

    // Добавлеят опции в таблицу настроек каталога

    private function addSectionOption($level, $href, $action = 'add')
    {

        // Проверяем наличие записи для всех разделов. Если нет - добавляем.

        if (!($sectionOptionId = $this->db->fetchOne("SELECT `id` FROM `catalog_options` WHERE `section_href` = '0'"))) {
            $this->db->insert('catalog_options', array(
                'section_href' => '0',
                'is_use_sub_section' => '0',
                'is_use_unique_goods_names' => '0',
                'is_unique_url_fields' => 'none',
                'is_show_empty_pic' => '1',
                'is_show_empty_price' => '1',
                'is_show_hits' => '1',
                'is_show_new' => '1',
                'is_show_actions' => '1',
                'new_index_length' => '3',
                'hits_index_length' => '3',
                'action_index_length' => '3'));
        }

        // Проверяем наличие параметров для редактируемого раздела.

        if (!($sectionOptionId = $this->db->fetchOne("SELECT `id` FROM `catalog_options` WHERE `section_href` = '$href'"))) {

            // Если создаем раздел - сразу ставим значение отсутствия подразделов. При добавлении подраздела поменяем. В любом случае запись должна существовать.
            if ($level == '0') {

                if (!($parentHref = $this->db->fetchOne("SELECT `href` from `catalog` WHERE `id` = '$level'"))) {
                    $this->db->insert('catalog_options', array(
                        'section_href' => $href,
                        'is_use_sub_section' => '0'
                    ));
                }
            } else {
                // Если создаем или редактируем подраздел - устанавливаем наличие подразделов.
                // Получаем ссылку на раздел.

                if (($parentHref = $this->db->fetchOne("SELECT `href` from `catalog` WHERE `id` = '$level'"))) {
                    // Проверяем наличие записи.
                    if (!($sectionOptionId = $this->db->fetchOne("SELECT `id` FROM `catalog_options` WHERE `section_href` = '$parentHref'"))) {
                        $this->db->insert('catalog_options', array(
                            'section_href' => $parentHref,
                            'is_use_sub_section' => '1'
                        ));
                    } else {
                        $this->db->update('catalog_options', array(
                            'section_href' => $parentHref,
                            'is_use_sub_section' => '1'
                                ), "id=$sectionOptionId");
                    }
                }
            }
        }
    }

    // Удаление раздела
    public function deletecatsection()
    {
        $this->setMetaTags("Удаление раздела");
        $this->setWay("Удаление раздела");

        $section = false;

        if (count($this->url) > 0) {
            $id = end($this->url);
            if (is_numeric($id)) {
                $section = $this->db->fetchRow('SELECT `id`, `pic`, `artikul`, `href` FROM `catalog` WHERE `id` = "' . $id . '"');
            } else {
                $section = false;
            }
        }

        if (!$section) {
            $this->addErr("Не удалось найти раздел");
            $this->viewErr();
            return true;
        }


        if (!empty($section['pic'])) {
            $picPath1 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small/' . $section['pic'];
            $picPath2 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/big/' . $section['pic'];
            $picPath3 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/real/' . $section['pic'];

            if (is_file($picPath1)) {
                @chmod($picPath1, 0666);
                @unlink($picPath1);
            }

            if (is_file($picPath2)) {
                @chmod($picPath2, 0666);
                @unlink($picPath2);
            }

            if (is_file($picPath3)) {
                @chmod($picPath3, 0666);
                @unlink($picPath3);
            }
        }


        $tableName = 'catalog-fields-' . $section['artikul'];

        if ($this->isTableExists($tableName)) {
            $this->db->query("DROP TABLE `$tableName`");
        }

        $this->db->delete('catalog', "id=$section[id]");
        $this->db->delete('catalog_options', "section_href='$section[href]'");
        $this->db->delete('catalog_section_fields', "catalog_section_href='$section[artikul]'");
        $this->deleteSubSectionRec($section['id']);

        $refferer = '/catalog';

        $referrer = $this->getVar('HTTP_REFERER', $this->gpm($_SERVER, 'HTTP_REFERER', ''));



        $content = "Данные удалены<meta http-equiv='refresh' content='2;URL=$refferer'>";
        $this->viewMessage($content);

        return true;
    }

    // Удаление подразделов, товаров их картинок
    private function deleteSubSectionRec($id)
    {
        $list = $this->db->fetchAll("SELECT `id`, `pic`, `name` FROM `catalog` WHERE `level`='$id'");
        if (isset($list)) {
            foreach ($list as $lst) {
                if (!empty($lst['pic'])) {
                    $picPath1 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small/' . $lst['pic'];
                    $picPath2 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/big/' . $lst['pic'];
                    $picPath3 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/real/' . $lst['pic'];

                    if (is_file($picPath1)) {
                        @chmod($picPath1, 0666);
                        @unlink($picPath1);
                    }

                    if (is_file($picPath2)) {
                        @chmod($picPath2, 0666);
                        @unlink($picPath2);
                    }

                    if (is_file($picPath3)) {
                        @chmod($picPath3, 0666);
                        @unlink($picPath3);
                    }
                }

                $this->db->delete('catalog', "id=$lst[id]");
                $this->deleteSubSectionRec($lst['id']);
                $this->deleteForeshorteningPageId($lst['id']);
                $this->deleteMiniGalleryPageId($lst['id']);
            }
        }
    }

    // Добавление товара
    public function addcatpage()
    {

        $this->setMetaTags("Добавление товара");
        $this->setWay("Добавление товара");

        $level = 0;
        $id = 0;
        if (count($this->url) > 0) {
            $id = end($this->url);
            if (is_numeric($id) && $id > 0) {
                if (!($section = $this->db->fetchRow('SELECT * FROM `catalog` WHERE `id` = "' . $id . '"'))) {
                    $this->addErr("Не удалось найти раздел");
                } else {
                    $level = $section['id'];
                }
            }
        }

        $this->tpl->define_dynamic('edit', 'adm/edit_catalog_slider.tpl');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('js', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('name', 'edit');
        $this->tpl->define_dynamic('adress', 'edit');
        $this->tpl->define_dynamic('cost', 'edit');
        $this->tpl->define_dynamic('cost_old', 'edit');

        $this->tpl->define_dynamic('pic', 'edit');
        $this->tpl->define_dynamic('show_pic', 'pic');
        $this->tpl->define_dynamic('pic_title', 'edit');
        $this->tpl->define_dynamic('pic_alt', 'edit');

        $this->tpl->define_dynamic('pic_alt_title_info', 'edit');
        $this->tpl->define_dynamic('artikul', 'edit');
        $this->tpl->define_dynamic('visible', 'edit');
        $this->tpl->define_dynamic('pos', 'edit');
        $this->tpl->define_dynamic('availability', 'edit');
        $this->tpl->define_dynamic('status', 'edit');

        $this->tpl->define_dynamic('proizvoditel', 'edit');



        if ($this->getCatOption('isFeatured')) {
            $this->tpl->define_dynamic('featured_products', 'edit');
        }

        if ($this->getCatOption('isUsedComplete')) {
            $this->tpl->define_dynamic('used_complete', 'edit');
        }

        if ($this->getCatOption('isForeshortening')) {
            $this->tpl->define_dynamic('gallery_foreshortening_pic', 'edit');
            $this->tpl->define_dynamic('gallery_foreshortening_pic_list', 'gallery_foreshortening_pic');
        }


        if ($this->getCatOption('isMiniGallery')) {
            $this->tpl->define_dynamic('gallery_pic', 'edit');
            $this->tpl->define_dynamic('gallery_pic_list', 'gallery_pic');
        }

        $this->tpl->define_dynamic('meta', 'edit');
        $this->tpl->define_dynamic('preview', 'edit');
        $this->tpl->define_dynamic('body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');


        $name = $this->getVar('name', '');
        $href = $this->ru2Lat($this->getVar('adm_href', ''));
        $header = $this->getVar('header', '');
        $title = $this->getVar('title', '');
        $keywords = $this->getVar('keywords', '');
        $description = $this->getVar('description', '');
        $preview = $this->getVar('preview', '');
        $body = $this->getVar('body', '');
        $picTitle = $this->getVar('pic_title', '');
        $picAlt = $this->getVar('pic_alt', '');
        $visible = $this->getVar('visible', '1');

        $status = $this->getVar('status', 'simple');
        $availability = $this->getVar('availability', '1');
        $artikul = $this->getVar('artikul', '');
        $proizvoditel = $this->getVar('proizvoditel', '');
        $position = $this->getVar('position', '9999');


        $cost = $this->getVar('cost', '');
        $cost_old = $this->getVar('cost_old', '');
        $featured_products = $this->getVar('featured_products', '');
        $used_complete = $this->getVar('used_complete', '');

        // $referrer = $this->getVar('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
        $referrer = $this->refererInit($id, 'catalog');


        $pic = '';

        $this->tpl->parse('SHOW_PIC', 'null');

        $availability_s = '';
        if ($availability == 1) {
            $availability_s .= "<option value='1' selected>Есть</option>
            <option value='0'>Нет</option>";
        } else {
            $availability_s .= "<option value='1'>Есть</option>
            <option value='0' selected>Нет</option>";
        }

        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }




        if (!empty($_POST) && empty($name)) {
            $this->addErr("Поле <b>Название</b> не может быть пустым");
        }


        $statusS = "<option value='simple' " . ($status == 'simple' ? 'selected' : '') . "></option>
       	<option value='action' " . ($status == 'action' ? 'selected' : '') . ">Акция</option>
        <option value='new' " . ($status == 'new' ? 'selected' : '') . ">Новинка</option>
        <option value='hit' " . ($status == 'hit' ? 'selected' : '') . ">Хит</option>";



        $assignArray = array('ADM_HEADER' => $header,
            'ADM_NAME' => $name,
            'ADM_TITLE' => $title,
            'ADM_HREF' => $href,
            'ADM_PIC_TITLE' => $picTitle,
            'ADM_PIC_ALT' => $picAlt,
            'ADM_COST' => $cost,
            'ADM_COST_OLD' => $cost_old,
            'ADM_ARTIKUL' => $artikul,
            'ADM_PROISVODITEL' => $proizvoditel,
            'ADM_KEYWORDS' => $keywords,
            'ADM_DESCRIPTION' => $description,
            'ADM_PREVIEW' => stripslashes($preview),
            'ADM_BODY' => stripslashes($body),
            'VISIBLE_S' => $visible_s,
            'STATUS_S' => $statusS,
            'AVAILABILITY_S' => $availability_s,
            'ADM_POSITION' => $position,
            'REFERER' => $referrer,
            'ADM_MODULE_CLASS_NAME' => '',
            'REFF' => $referrer
        );

        if ($this->getCatOption('isFeatured')) {
            $assignArray['ADM_FEATURED_PRODUCTS'] = $featured_products;
        }

        if ($this->getCatOption('isUsedComplete')) {
            $assignArray['ADM_USED_COMPLETE'] = $used_complete;
        }
        $this->tpl->assign($assignArray);

        if ($this->getCatOption('isForeshortening')) {
            if (!$this->_err) {
                $this->drowForeshortening($artikul);
            }
        }

        if ($this->getCatOption('isMiniGallery')) {
            if (!$this->_err) {
                $this->drowMiniGallery($artikul);
            }
        }


        if (!empty($_POST) && !$this->_err) {

            if (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] > 0) {

                $pos = strrpos($_FILES['pic']['name'], '.');
                $fNameTmp = substr($_FILES['pic']['name'], 0, $pos);
                $fNameTmp = $this->ru2Lat($artikul . ' ' . $fNameTmp);

                $fExTmp = substr($_FILES['pic']['name'], $pos, strlen($_FILES['pic']['name']));
                $_FILES['pic']['name'] = $fNameTmp . $fExTmp;

                $_name = $_FILES['pic']['name'];
                $tempFile = $_FILES['pic']['tmp_name'];

                $handle = new upload($_FILES['pic']);

                if ($handle->uploaded) {


                    $handle->image_x = 420;
                    $handle->image_y = 420;

                    $isSize = true;

                    if ($handle->image_src_x < 420) {
                        $isSize = false;
                    }

                    if ($handle->image_src_y < 420) {
                        $isSize = false;
                    }


                    if (!$isSize) {
                        $this->addErr("Изображение не должно быть меньше " . $handle->image_x . "px X " . $handle->image_y . "px (" . $handle->image_src_x . "px X " . $handle->image_src_y . "px)");
                    }

                    if (!$this->_err) {

                        $handle->image_resize = true;
                        $handle->image_ratio_fill = 'LT';
                        $handle->image_background_color = '#FFFFFF';
                        $handle->file_auto_rename = true;
                        $handle->file_safe_name = false;
                        $handle->process($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/big/');
                        if ($handle->processed) {
                            @copy($tempFile, $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/real/' . $_FILES['pic']['name']);
                        } else {
                            $this->addErr($handle->error);
                        }
                    }
                }


                // Small Image
                if (!$this->_err) {
                    $handle = new upload($_FILES['pic']);
                    if ($handle->uploaded) {
                        $handle->image_resize = true;
                        $handle->image_x = 200;
                        $handle->image_y = 180;
                        $handle->file_auto_rename = true;
                        $handle->file_safe_name = false;
                        $handle->image_ratio_fill = 'LT';
                        $handle->image_background_color = '#FFFFFF';
                        $handle->process($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small/");
                        if ($handle->processed) {
                            $pic = $handle->file_dst_name;
                        } else {
                            $this->addErr($handle->error);
                        }
                    }
                    $handle->clean();
                }
            }

            if (empty($href)) {
                $href = $this->ru2Lat($name);
            }

            if (empty($picTitle)) {
                $picTitle = $name;
            }

            if (empty($picAlt)) {
                $picAlt = $name;
            }

            if (empty($artikul)) {
                $artikul = $name;
            }

            if (empty($header)) {
                $header = $name;
            }

            if (empty($title)) {
                $title = $name;
            }

            $data = array(
                'name' => $name,
                'type' => 'page',
                'href' => $href,
                'body' => $body,
                'preview' => $preview,
                'header' => $header,
                'title' => $title,
                'pic' => $pic,
                'pic_title' => $picTitle,
                'pic_alt' => $picAlt,
                'cost' => $cost,
                'cost_old' => $cost_old,
                'artikul' => $artikul,
                'proizvoditel' => $proizvoditel,
                'keywords' => $keywords,
                'position' => $position,
                'description' => $description,
                'visibility' => $visible,
                'status' => $status,
                'availability' => $availability,
                'changed' => date('Y-m-d H:i:s'),
                'level' => $level,
                'language' => $this->lang,
            );

            if ($this->getCatOption('isFeatured')) {
                $data['featured_products'] = $featured_products;
            }


            if ($this->getCatOption('isUsedComplete')) {
                $data['used_complete'] = $used_complete;
            }

            if (!$this->_err) {


                if ($this->getCatOption('isMiniGallery')) {

                    if (!$this->_err) {
                        $this->uploadMiniGallery($artikul);
                    }
                }

                if ($this->getCatOption('isForeshortening')) {

                    if (!$this->_err) {
                        $this->uploadForeshortening($artikul);
                    }
                }

                if (isset($_SESSION['form_manager'][$id]['img'])) {

                    $data['pic'] = $_SESSION['form_manager'][$id]['img'];
                    //unset($_SESSION['form_manager']);
                }

                $this->db->insert('catalog', $data);

                if (isset($section['level'])) {
                    $sectionArtikul = '';

                    if ($section['level'] != '0') {
                        $data = $this->dataTreeManager($id);
                        $sectionArtikul = $data['sectionArtikul'];
                    } else {
                        $sectionArtikul = $section['artikul'];
                    }
                    print "UPDATE `catalog_gallery` SET `goods_artikul` = '$artikul' WHERE  `goods_artikul` = 'new-page-$sectionArtikul'";
                    $this->db->query("UPDATE `catalog_gallery` SET `goods_artikul` = '$artikul' WHERE  `goods_artikul` = 'new-page-$sectionArtikul'");
                }

                //  $id = $this->db->lastInsertId();

                $content = "Новый товар добавлен <meta http-equiv='refresh' content='2;URL=$referrer' />";
                $this->viewMessage($content);
                return true;
            }
        }

        if ($this->_err) {
            $this->viewErr();
        }


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.js');
        $this->tpl->parse('CONTENT', '.mce');
        $this->tpl->parse('CONTENT', '.name');
        $this->tpl->parse('CONTENT', '.adress');
        $this->tpl->parse('CONTENT', '.artikul');

        $this->tpl->parse('CONTENT', '.meta');

        $this->tpl->parse('CONTENT', '.cost_old');
        $this->tpl->parse('CONTENT', '.cost');
        $this->tpl->parse('CONTENT', '.status');
        $this->tpl->parse('CONTENT', '.availability');

        $this->tpl->parse('CONTENT', '.proizvoditel');

        $this->tpl->parse('CONTENT', '.visible');
        $this->tpl->parse('CONTENT', '.pos');


        $formManager1 = new FormManager($this->tpl, $this->db, 'catalog', $id);
        $formManager1->setOptions($this->catalogImagesOptions);
        $formManager1->addField(array('type' => 'image', 'name' => 'pic', 'title' => 'Изображение', 'group' => 'Изображения/Галерея'));




//        $this->tpl->parse('CONTENT', '.pic');
//        $this->tpl->parse('CONTENT', '.pic_alt_title_info');
//        $this->tpl->parse('CONTENT', '.pic_title');
//        $this->tpl->parse('CONTENT', '.pic_alt');

        if ($this->getCatOption('isForeshortening')) {
            //  $this->tpl->parse('CONTENT', '.gallery_foreshortening_pic');
        }


        if ($this->getCatOption('isMiniGallery')) {
            $formManager1->addField(array('type' => 'gallery', 'name' => 'pics', 'title' => 'Галерея', 'group' => 'Изображения/Галерея'/* , 'gallery_type'=>'foreshortening' */));
        }

        $formManager1->show(false);

        if ($this->getCatOption('isUsedComplete')) {
            $this->tpl->parse('CONTENT', '.used_complete');
        }

        if ($this->getCatOption('isFeatured')) {
            $this->tpl->parse('CONTENT', '.featured_products');
        }


        $this->tpl->parse('CONTENT', '.preview');
        $this->tpl->parse('CONTENT', '.body');




        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    // Редактирование товара
    public function editcatpage()
    {

        $this->setMetaTags("Редактирование товара");
        $this->setWay("Редактирование товара");

        $section = false;

        if (count($this->url) > 0) {
            $id = end($this->url);
            if (is_numeric($id)) {
                $section = $this->db->fetchRow('SELECT * FROM `catalog` WHERE `id` = "' . $id . '"');
            } else {
                $section = false;
            }
        }

        if (!$section) {
            $this->addErr("Не удалось найти раздел");
            $this->viewErr();
        }

        $this->setMetaTags("Редактирование товара " . $section['name'] . ' ' . $section['artikul']);
        $this->setWay("Редактирование товара " . $section['name'] . ' ' . $section['artikul']);

        $this->tpl->define_dynamic('edit', 'adm/edit_catalog_slider.tpl');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('js', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('name', 'edit');
        $this->tpl->define_dynamic('adress', 'edit');
        $this->tpl->define_dynamic('cost', 'edit');
        $this->tpl->define_dynamic('cost_old', 'edit');

        $this->tpl->define_dynamic('pic', 'edit');
        $this->tpl->define_dynamic('show_pic', 'pic');
        $this->tpl->define_dynamic('pic_title', 'edit');
        $this->tpl->define_dynamic('pic_alt', 'edit');
        $this->tpl->define_dynamic('link_3d', 'edit');

        $this->tpl->define_dynamic('pic_alt_title_info', 'edit');


//        $this->tpl->define_dynamic('s_pic', 'edit');
        //      $this->tpl->define_dynamic('s_show_pic', 's_pic');
        //    $this->tpl->define_dynamic('s_show_pic_dell', 's_show_pic');




        $this->tpl->define_dynamic('artikul', 'edit');

        $this->tpl->define_dynamic('visible', 'edit');
        $this->tpl->define_dynamic('pos', 'edit');
        $this->tpl->define_dynamic('availability', 'edit');
        $this->tpl->define_dynamic('status', 'edit');

        $this->tpl->define_dynamic('proizvoditel', 'edit');

        if ($this->getCatOption('isUsedComplete')) {
            $this->tpl->define_dynamic('used_complete', 'edit');
        }

        if ($this->getCatOption('isFeatured')) {
            $this->tpl->define_dynamic('featured_products', 'edit');
        }

        if ($this->getCatOption('isForeshortening')) {
            $this->tpl->define_dynamic('gallery_foreshortening_pic', 'edit');
            $this->tpl->define_dynamic('gallery_foreshortening_pic_list', 'gallery_foreshortening_pic');
        }


        if ($this->getCatOption('isMiniGallery')) {
            $this->tpl->define_dynamic('gallery_pic', 'edit');
            $this->tpl->define_dynamic('gallery_pic_list', 'gallery_pic');
        }

        $this->tpl->define_dynamic('meta', 'edit');
        $this->tpl->define_dynamic('preview', 'edit');
        $this->tpl->define_dynamic('body', 'edit');
        $this->tpl->define_dynamic('sectionfields_form', 'edit');
        $this->tpl->define_dynamic('end', 'edit');


        $name = $this->gp($section, 'name', '');
        $href = $this->gp($section, 'href', '');
        $header = $this->gp($section, 'header', '');
        $title = $this->gp($section, 'title', '');
        $id3d = $this->gp($section, 'id_3d', '');

        $title3d = $this->gp($section, 'title_3d', '');

        $position = $this->gp($section, 'position', '');
        $keywords = $this->gp($section, 'keywords', '');
        $description = $this->gp($section, 'description', '');
        $preview = $this->gpm($section, 'preview', '');
        $body = $this->gpm($section, 'body', '');
        $picTitle = $this->gp($section, 'pic_title', '');
        $picAlt = $this->gp($section, 'pic_alt', '');

        $visible = $this->gp($section, 'visibility', '');

        $status = $this->gp($section, 'status', 'simple');
        $availability = $this->gp($section, 'availability', '1');
        $pic = $this->gp($section, 'pic', '');
        $cost = $this->gp($section, 'cost', '');
        $cost_old = $this->gp($section, 'cost_old', '');

        $artikul = $this->gp($section, 'artikul', '');
        $proizvoditel = $this->gp($section, 'proizvoditel', '');

        $featured_products = $this->gp($section, 'featured_products', '');
        $used_complete = $this->gp($section, 'used_complete', '');
        $this->tpl->parse('SHOW_PIC', 'null');
        $referrer = $this->refererInit($id, 'catalog');

        //$referrer = $this->getVar('HTTP_REFERER', $this->gpm($_SERVER, 'HTTP_REFERER', ''));


        $sectionArtikul = $this->dataTreeManager($id, array('fields' => '`id`, `href`, `name`, `level` , `type`, `artikul`', 'ret' => 'data'));


        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small/' . $pic)) {

            $this->tpl->parse('S_SHOW_PIC', '.s_show_pic');
            //$this->tpl->parse('S_SHOW_PIC_DELL', '.s_show_pic_dell');
            $this->tpl->assign('ADM_IMG_SRC', '/img/catalog/small/' . $pic);
        } else {
            $this->tpl->parse('S_SHOW_PIC_DELL', 'null');
        }




        if (isset($sectionArtikul[2]['artikul'])) {
            $sectionArtikul = $sectionArtikul[2]['artikul'];
        } elseif (isset($sectionArtikul[1]['artikul'])) {
            $sectionArtikul = $sectionArtikul[1]['artikul'];
        }

        $sectionFieldsValue = false;
        $sectionFieldsTableExists = false;
        if ($this->isTableExists('catalog-fields-' . $sectionArtikul)) {
            $sectionFieldsValue = $this->db->fetchRow("SELECT * FROM `catalog-fields-$sectionArtikul` WHERE `catalog_artikul` = '$artikul'");
            $sectionFieldsTableExists = true;
        }

        $fileName = '/img/nophoto_s.jpg';

        if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small/' . $pic)) {
            $fileName = '/img/catalog/small/' . $pic;
        }


        $this->tpl->parse('SHOW_PIC', '.show_pic');


        if (isset($_POST['dell_pic'])) {
            if (!empty($picTmpName)) {
                $picPath1 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small2/' . $pic;
                $picPath2 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small1/' . $pic;
                $picPath3 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/big/' . $pic;
                $picPath4 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/real/' . $pic;

                if (is_file($picPath1)) {
                    @chmod($picPath1, 0666);
                    @unlink($picPath1);
                }

                if (is_file($picPath2)) {
                    @chmod($picPath2, 0666);
                    @unlink($picPath2);
                }

                if (is_file($picPath3)) {
                    @chmod($picPath3, 0666);
                    @unlink($picPath3);
                }

                if (is_file($picPath4)) {
                    @chmod($picPath3, 0666);
                    @unlink($picPath3);
                }
            }
        }


        if (!empty($_POST) && empty($name)) {
            $this->addErr("Поле <b>Название</b> не может быть пустым");
        }

        if (!empty($_POST)) {
            $name = $this->getVar('name', '');
            $id3d = $this->getVar('id_3d', '');
            $title3d = $this->getVar('title_3d', '');
            $href = $this->ru2Lat($this->getVar('adm_href', ''));
            $header = $this->getVar('header', '');
            $title = $this->getVar('title', '');
            $keywords = $this->getVar('keywords', '');
            $description = $this->getVar('description', '');
            $preview = $this->getVar('preview', '');
            $body = $this->getVar('body', '');
            $picTitle = $this->getVar('pic_title', '');
            $picAlt = $this->getVar('pic_alt', '');
            $visible = $this->getVar('visible', '1');

            $status = $this->getVar('status', 'simple');
            $availability = $this->getVar('availability', '1');
            $artikul = $this->getVar('artikul', '');
            $proizvoditel = $this->getVar('proizvoditel', '');
            $position = $this->getVar('position', '9999');

            $cost = $this->getVar('cost', '');
            $cost_old = $this->getVar('cost_old', '');
            $featured_products = $this->getVar('featured_products', '');
            $used_complete = $this->getVar('used_complete', '');
        }

        $this->tpl->assign(array(
            'ADM_IMG_SRC' => $fileName,
            'ADM_IMG_ALT' => $picAlt,
            'ADM_IMG_TITLE' => $picTitle,
            'ADM_DELL_PHOT_METHOD' => 'deletecatalogimage',
            'ADM_DELL_PHOTO_ID' => $id,
            'CAT_ID' => ''// $section['id']
        ));


        $availability_s = '';
        if ($availability == 1) {
            $availability_s .= "<option value='1' selected>Есть</option>
            <option value='0'>Нет</option>";
        } else {
            $availability_s .= "<option value='1'>Есть</option>
            <option value='0' selected>Нет</option>";
        }

        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }


        $statusS = "<option value='simple' " . ($status == 'simple' ? 'selected' : '') . "></option>
        <option value='action' " . ($status == 'action' ? 'selected' : '') . ">Акция</option>
        <option value='new' " . ($status == 'new' ? 'selected' : '') . ">Новинка</option>
        <option value='hit' " . ($status == 'hit' ? 'selected' : '') . ">Хит</option>";



        $assignArray = array('ADM_HEADER' => $header,
            'ADM_NAME' => $name,
            'ADM_ID_3D' => $id3d,
            'ADM_TITLE_3D' => $title3d,
            'ADM_TITLE' => $title,
            'ADM_HREF' => $href,
            'ADM_PIC_TITLE' => $picTitle,
            'ADM_PIC_ALT' => $picAlt,
            'ADM_COST' => $cost,
            'ADM_COST_OLD' => $cost_old,
            'ADM_ARTIKUL' => $artikul,
            'ADM_PROISVODITEL' => $proizvoditel,
            'ADM_KEYWORDS' => $keywords,
            'ADM_DESCRIPTION' => $description,
            'ADM_PREVIEW' => stripslashes($preview),
            'ADM_BODY' => stripslashes($body),
            'VISIBLE_S' => $visible_s,
            'STATUS_S' => $statusS,
            'AVAILABILITY_S' => $availability_s,
            'ADM_POSITION' => $position,
            'REFERER' => $referrer,
            'ADM_MODULE_CLASS_NAME' => '',
            'REFF' => $referrer
        );

        if ($this->getCatOption('isFeatured')) {
            $assignArray['ADM_FEATURED_PRODUCTS'] = $featured_products;
        }
        if ($this->getCatOption('isUsedComplete')) {
            $assignArray['ADM_USED_COMPLETE'] = $used_complete;
        }

        $this->tpl->assign($assignArray);

        if ($this->getCatOption('isForeshortening')) {
            if (!$this->_err) {
                $this->drowForeshortening($artikul);
            }
        }



        if ($this->getCatOption('isMiniGallery')) {
            if (!$this->_err) {
                $this->drowMiniGallery($artikul);
            }
        }

        if (!empty($_POST) && !$this->_err) {
            //==
            if (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] > 0) {

                $pos = strrpos($_FILES['pic']['name'], '.');
                $fNameTmp = substr($_FILES['pic']['name'], 0, $pos);
                $fNameTmp = $this->ru2Lat($artikul . ' ' . $fNameTmp);

                $fExTmp = substr($_FILES['pic']['name'], $pos, strlen($_FILES['pic']['name']));
                $_FILES['pic']['name'] = $fNameTmp . $fExTmp;

                $_name = $_FILES['pic']['name'];
                $tempFile = $_FILES['pic']['tmp_name'];


                //str_ireplace('.gif', '', str_ireplace('.png', '', str_ireplace('.jpg', '', str_ireplace('.jpeg', '', $_FILES['Filedata']['name']))));


                $handle = new upload($_FILES['pic']);

                if ($handle->uploaded) {


                    $handle->image_x = 420;
                    $handle->image_y = 420;

                    $isSize = true;

                    /*

                      if ($handle->image_src_x < 420) {
                      $isSize = false;
                      }

                      if ($handle->image_src_y < 420) {
                      $isSize = false;
                      } */


                    if (!$isSize) {
                        $this->addErr("Изображение не должно быть меньше " . $handle->image_x . "px X " . $handle->image_y . "px (" . $handle->image_src_x . "px X " . $handle->image_src_y . "px)");
                    }

                    if (!$this->_err) {

                        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small1/$pic")) {
                            @chmod($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small1/$pic", 0666);
                            @unlink($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small1/$pic");
                        }

                        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small2/$pic")) {
                            @chmod($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small2/$pic", 0666);
                            @unlink($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small2/$pic");
                        }

                        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/big/$pic")) {
                            @chmod($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/big/$pic", 0666);
                            @unlink($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/big/$pic");
                        }

                        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/real/$pic")) {
                            @chmod($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/real/$pic", 0666);
                            @unlink($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/real/$pic");
                        }

                        @copy($tempFile, $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/real/' . $handle->file_dst_name);
                    }
                }
                //
                // Small Image
            }

            if ($this->_err) {
                $this->viewErr();
                $pic = '';
            }

            //==



            if (empty($picTitle)) {
                $picTitle = $name;
            }

            if (empty($picAlt)) {
                $picAlt = $name;
            }

            if (empty($artikul)) {
                $artikul = $name;
            }

            if (empty($header)) {
                $header = $name;
            }

            if (empty($title)) {
                $title = $name;
            }

            $data = array(
                'name' => $name,
                'id_3d' => $id3d,
                'title_3d' => $title3d,
                'href' => $href,
                'body' => $body,
                'preview' => $preview,
                'header' => $header,
                'title' => $title,
                'pic' => $pic,
                'pic_title' => $picTitle,
                'pic_alt' => $picAlt,
                'cost' => $cost,
                'cost_old' => $cost_old,
                'artikul' => $artikul,
                'proizvoditel' => $proizvoditel,
                'keywords' => $keywords,
                'position' => $position,
                'description' => $description,
                'visibility' => $visible,
                'status' => $status,
                'availability' => $availability,
                'changed' => time(),
                'language' => $this->lang,
            );



            if ($this->getCatOption('isFeatured')) {
                $data['featured_products'] = $featured_products;
            }

            if ($this->getCatOption('isUsedComplete')) {
                $data['used_complete'] = $used_complete;
            }

            if (isset($_SESSION['form_manager'][$id]['img'])) {

                $data['pic'] = $_SESSION['form_manager'][$id]['img'];
                unset($_SESSION['form_manager']);
            }

            if (isset($data['pic']) && empty($data['pic'])) {
                unset($data['pic']);
            }


            if ($this->getCatOption('isMiniGallery')) {
                if (!$this->_err) {
                    $this->deleteMiniGallery();
                }

                if (!$this->_err) {
                    $this->uploadMiniGallery($artikul);
                }
            }

            if ($this->getCatOption('isForeshortening')) {
                if (!$this->_err) {
                    $this->deleteForeshortening();
                }

                if (!$this->_err) {
                    $this->uploadForeshortening($artikul);
                }
            }

            if (!$this->_err) {
                $this->db->update('catalog', $data, "id=$id");

                if ($sectionFieldsTableExists) {

                    $data1 = $this->readSectionPost($sectionArtikul);
                    $data1['catalog_artikul'] = $artikul;
                    if (count($data1) > 0) {
                        if (!$sectionFieldsValue) {
                            $this->db->insert("catalog-fields-$sectionArtikul", $data1);
                        } else {
                            $this->db->update("catalog-fields-$sectionArtikul", $data1, "catalog_artikul='$data[artikul]'");
                        }
                    }
                }

                $referrer = $this->getVar('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
                $content = "Товар изменены<meta http-equiv='refresh' content='2;URL=$referrer'>";
                $this->viewMessage($content);
                return true;
            } else {
                $this->viewErr();
            }
        } else {
            $this->viewErr();
        }


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.js');
        $this->tpl->parse('CONTENT', '.mce');
        $this->tpl->parse('CONTENT', '.name');
        $this->tpl->parse('CONTENT', '.adress');
        $this->tpl->parse('CONTENT', '.artikul');

        $this->tpl->parse('CONTENT', '.meta');

        $this->tpl->parse('CONTENT', '.cost_old');
        $this->tpl->parse('CONTENT', '.cost');
        $this->tpl->parse('CONTENT', '.link_3d');
        $this->tpl->parse('CONTENT', '.status');
        $this->tpl->parse('CONTENT', '.availability');

        $this->tpl->parse('CONTENT', '.proizvoditel');

        $this->tpl->parse('CONTENT', '.visible');
        $this->tpl->parse('CONTENT', '.pos');

        //   $this->tpl->parse('CONTENT', '.s_pic');
        //  $this->tpl->parse('CONTENT', '.pic');
        //  $this->tpl->parse('CONTENT', '.pic_alt_title_info');
        //  $this->tpl->parse('CONTENT', '.pic_title');
        // $this->tpl->parse('CONTENT', '.pic_alt');
        // Проверка аплода из манагера форм


        $formManager1 = new FormManager($this->tpl, $this->db, 'catalog', $id);
        $formManager1->addField(array('type' => 'image', 'name' => 'pic', 'title' => 'Изображение', 'group' => 'Изображения/Галерея'));
        $formManager1->addField(array('type' => 'gallery', 'name' => 'pics', 'title' => 'Галерея', 'group' => 'Изображения/Галерея'/* , 'gallery_type'=>'foreshortening' */));
        $formManager1->setOptions($this->catalogImagesOptions);

        $formManager1->show(false);


        if ($this->getCatOption('isForeshortening')) {
            //  $this->tpl->parse('CONTENT', '.gallery_foreshortening_pic');
        }


        if ($this->getCatOption('isMiniGallery')) {
            // $this->tpl->parse('CONTENT', '.gallery_pic');
        }

        if ($this->getCatOption('isUsedComplete')) {
            $this->tpl->parse('CONTENT', '.used_complete');
        }

        if ($this->getCatOption('isFeatured')) {
            $this->tpl->parse('CONTENT', '.featured_products');
        }

        $this->tpl->parse('CONTENT', '.preview');
        $this->tpl->parse('CONTENT', '.body');



        $form = $this->getSectionForms($sectionArtikul, $sectionFieldsValue);
        if (!empty($form)) {
            $this->tpl->assign('ADM_SECTION_FIELD_FORM', $form);
            $this->tpl->parse('CONTENT', '.sectionfields_form');
        }

        $this->tpl->parse('CONTENT', '.end');
        return true;
    }

    /*
     * Тестируется новый метод работы формы.
     * Форма создается при помощи функции FormManager
     * Для работы с картинками (добавление и удаление) нужно создать две функции в классе Admin
     * deleteИмяТаблицыВБазеimage
     * uploadИмяТаблицыВБазеimage
     * В этих функциях нужно создать объект FormManager и вызвать метод deleteImage/uploadImage
     *
     *
     */

    // Редактирование товара. Поля добавляются в базе и в массиве в настройках. Выводятся и сохраняются автоматически
    public function editcatpage2()
    {
        $this->setMetaTags("Редактирование товара");
        $this->setWay("Редактирование товара");
        $id = false;

        if (count($this->url) > 0) {
            $id = end($this->url);
        }

        if (($goods = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `id`='$id'"))) {
            
        }

        $catalogOptions = $this->getCatalogOptions(true);

        $sectionArtikulArray = $this->dataTreeManager($id, array('fields' => '`id`, `href`, `name`, `level` , `type`, `artikul`', 'ret' => 'data'));
        $sectionFieldsValue = array();


        if (is_array($sectionArtikulArray) && count($sectionArtikulArray) > 0) {
            foreach ($sectionArtikulArray as $val) {
                if (isset($val['level']) && $val['level'] == 0 && isset($val['artikul'])) {
                    $sectionArtikul = $val['artikul'];
                }
            }
        }

        $sectionFieldsTableExists = false;

        if ($this->isTableExists('catalog-fields-' . $sectionArtikul)) {
            $sectionFieldsValue = $this->db->fetchRow("SELECT * FROM `catalog-fields-$sectionArtikul` WHERE `catalog_artikul` = '$goods[artikul]'");
            $sectionFieldsTableExists = true;
        }

        if ($sectionFieldsTableExists) {
            $data = $this->readSectionPost($sectionArtikul);
            $data['catalog_artikul'] = $goods['artikul'];

            if (count($data) > 0) {
                if (!$sectionFieldsValue) {
                    $this->db->insert("catalog-fields-$sectionArtikul", $data);
                } else {
                    $this->db->update("catalog-fields-$sectionArtikul", $data, "id=$id");
                }
            }
        }

        $catalogOptions = $this->getCatalogOptions(true);


        $formManager = new FormManager($this->tpl, $this->db, 'catalog', $id);
        $formManager->setFields($this->getDefaultFieldsForm());
        $formManager->setGroupType('Описание', 'mce');

        $formManager->setFieldType('pic', 'image');
        //sprint "SELECT `pic` FROM `catalog_gallery` WHERE `goods_artikul` = '$goods[href]' ORDER BY `position`, `id` ";

        if (($catalogGalleryImage = $this->db->fetchOne("SELECT `pic` FROM `catalog_gallery` WHERE `goods_artikul` = '$goods[href]' ORDER BY `position`, `id` "))) {

            $formManager->setFieldValue('catalog_gallery', $catalogGalleryImage);
        }
        $form = $this->getSectionForms($sectionArtikul, $sectionFieldsValue);
        if (!empty($form)) {
            $formManager->setOtherFileds($form);
        }
        $formManager->show();
        $formManager->setOptions($catalogOptions);
        $formManager->save();



        return true;
    }

    public function deletecatalogimage()
    {
        $id = false;
        if (count($this->url) > 0) {
            $id = end($this->url);
        }

        if (is_numeric($id)) {
            $formManager1 = new FormManager($this->tpl, $this->db, 'catalog', $id);
            $catalogOptions = $this->getCatalogOptions(true);
            if (isset($catalogOptions['catalogFormOptions'])) {
                $catalogOptions = $catalogOptions['catalogFormOptions'];
            }
            $formManager1->setOptions($catalogOptions);
            $formManager1->deleteImage();
        }
        die;
    }

    // Удаление товара
    public function deletecatpage()
    {
        $this->setMetaTags("Удаление товара");
        $this->setWay("Удаление товара");

        $section = false;

        if (count($this->url) > 0) {
            $id = end($this->url);
            if (is_numeric($id)) {
                $section = $this->db->fetchRow('SELECT `artikul`, `id`, `pic`, `href` FROM `catalog` WHERE `id` = "' . $id . '"');
            } else {
                $section = false;
            }
        }

        if (!$section) {
            $this->addErr("Не удалось найти раздел");
            $this->viewErr();
            return true;
        }



        $this->deleteForeshorteningPageId($section['artikul']);
        $this->deleteMiniGalleryPageId($section['artikul']);


        if (!empty($section['pic'])) {
            $picPath1 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small/' . $section['pic'];
            $picPath2 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/big/' . $section['pic'];
            $picPath3 = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/real/' . $section['pic'];

            if (is_file($picPath1)) {
                @chmod($picPath1, 0666);
                @unlink($picPath1);
            }

            if (is_file($picPath2)) {
                @chmod($picPath2, 0666);
                @unlink($picPath2);
            }

            if (is_file($picPath3)) {
                @chmod($picPath3, 0666);
                @unlink($picPath3);
            }
        }

        $this->db->delete('catalog', "id=$section[id]");

        $refferer = '/catalog';

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {

            $refferer = str_replace($section['href'], '', $_SERVER['HTTP_REFERER']);
            $refferer = preg_replace('/\/+$/', '', $refferer); // Удаляем возможные слэши в конце url
        }

        $content = "Данные удалены<meta http-equiv='refresh' content='2;URL=$refferer/'>";
        $this->viewMessage($content);

        return true;
    }

    // Удаление картинки товара
    public function deletesectionimage()
    {

        if (!is_numeric($id = end($this->url))) {
            $this->addErr("Ключ должен быть числом");
        }

        if (!($row = $this->db->fetchRow("SELECT `pic` FROM `catalog` WHERE `id`='$id'"))) {
            $this->addErr("Не могу найти раздел");
        }


        if (!$this->_err) {
            $this->setMetaTags('Удаление фото');
            $this->setWay('Удаление фото');


            if (!empty($row['pic']) && is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $row['pic'])) {
                @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $row['pic'], 0777);
                @unlink($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/section/' . $row['pic']);
                $content = "Фото удалено<meta http-equiv='refresh' content='2;URL=/admin/editcatsection/$id'>";
                $this->viewMessage($content);
            }


            if ($this->_err) {
                $this->setMetaTags('Ошибка !!');
                $this->setWay('Ошибка !!!');
                $this->viewErr();
            }
        } else {
            $this->setMetaTags('Ошибка !!');
            $this->setWay('Ошибка !!!');
            $this->viewErr();
        }

        return true;
    }

    // Загрузка картинок товара для разных ракурсов. Вызывается при добавлении и редактировании товара
    private function uploadForeshortening($goodsArtikul)
    {
        if (count($_FILES) > 0) {

            foreach ($_FILES as $key => $file) {

                if (isset($file['size']) && $file['size'] > 0 && substr($key, 0, strlen($key) - 1) == 'gallery_foreshortening_pic') {

                    //$file['name'] = $this->ru2Lat($goodsId.' '.$file['name']);

                    $pos = strrpos($file['name'], '.');
                    $fNameTmp = substr($file['name'], 0, $pos);
                    $fNameTmp = $this->ru2Lat($goodsArtikul . ' ' . $fNameTmp);

                    $fExTmp = substr($file['name'], $pos, strlen($file['name']));
                    $file['name'] = $fNameTmp . $fExTmp;


                    $_name = $file['name'];
                    $tempFile = $file['tmp_name'];
                    $handle = new upload($file);

                    $title = '';
                    $alt = '';

                    $titleName = str_replace('gallery_foreshortening_pic', 'gallery_foreshortening_text', $key);
                    $altName = str_replace('gallery_foreshortening_pic', 'gallery_foreshortening_alt', $key);

                    if (isset($_POST[$titleName])) {
                        $title = $_POST[$titleName];
                    }

                    if (isset($_POST[$altName])) {
                        $alt = $_POST[$altName];
                    }

                    if ($handle->uploaded) {
                        $handle->image_x = 420;
                        $handle->image_y = 420;

                        $isSize = true;

                        /*

                          if ($handle->image_src_x < 420) {
                          $isSize = false;
                          }

                          if ($handle->image_src_y < 420) {
                          $isSize = false;
                          } */


                        if (!$isSize) {
                            $this->addErr("Ошибка при добавлении дополнительных изображений к товару! <br />Изображение не должно быть меньше " . $handle->image_x . "px X " . $handle->image_y . "px (Файл:$file[name] " . $handle->image_src_x . "px X " . $handle->image_src_y . "px)");
                            return false;
                        }


                        //	$handle->file_new_name_body   = 'image_resized';
                        $handle->image_resize = true;

                        $handle->image_ratio_fill = 'LT';
                        $handle->image_background_color = '#FFFFFF';
                        $handle->file_auto_rename = true;
                        $handle->file_safe_name = false;
                        //$handle->image_text = $text ;
                        $handle->process($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/foreshortening/big/');
                        if ($handle->processed) {
                            //echo 'image resized';
                            @copy($tempFile, $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/foreshortening/real/' . $handle->file_dst_name);
                            //$handle->clean();
                        } else {
                            $this->addErr($handle->error);

                            return false;
                        }

                        $handle = new upload($file);
                        if ($handle->uploaded) {
                            //$handle->file_new_name_body   = 'image_resized';
                            $handle->image_resize = true;
                            $handle->image_x = 100;
                            $handle->image_y = 100;
                            $handle->file_auto_rename = true;
                            $handle->image_ratio_fill = 'LT';
                            $handle->image_background_color = '#FFFFFF';
                            $handle->file_safe_name = false;
                            //$handle->image_text = $text ;
                            //$handle->image_ratio_y        = true;
                            $handle->process($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/foreshortening/small/");
                            if ($handle->processed) {
                                $this->db->insert("catalog_gallery", array(
                                    'goods_artikul' => $goodsArtikul,
                                    'gallery_type' => 'foreshortening',
                                    'pic' => $handle->file_dst_name,
                                    'title' => $title,
                                    'alt' => $alt
                                ));
                            } else {
                                $this->addErr($handle->error);

                                return false;
                            }
                        }
                    }
                }
            }
        }
    }

    // Вываливает список загруженных картинок в форме редактирования товара для фоток с других ракурсов.
    private function drowForeshortening($goodsArtikul)
    {
        $this->tpl->parse('GALLERY_FORESHORTENING_PIC_LIST', 'null');
        $items = $this->db->fetchAll("SELECT * FROM `catalog_gallery` WHERE `goods_artikul` = '$goodsArtikul'  AND `gallery_type` = 'foreshortening'");
        if ($items) {
            foreach ($items as $item) {
                $this->tpl->assign(array(
                    'F_ID' => $item['id'],
                    'F_SRC' => $item['pic'],
                    'F_ALT' => $item['alt'],
                    'F_TITLE' => $item['title']
                ));

                $this->tpl->parse('GALLERY_FORESHORTENING_PIC_LIST', '.gallery_foreshortening_pic_list');
            }
        }
    }

    // Удаляет картинки с галереи ракурсов выбранные галочной

    private function deleteForeshortening($artikul = null)
    {

        //dell_foreshortening_pic
        if (!empty($artikul)) {

            $picVal = $this->db->fetchRow("SELECT * FROM `catalog_gallery`  WHERE `goods_artikul` = '$artikul' AND `gallery_type` = 'foreshortening'");
            if ($picVal) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/foreshortening';
                if (is_file($filePath . '/big/' . $picVal['pic'])) {
                    @chmod($filePath . '/big/' . $picVal['pic'], 0666);
                    @unlink($filePath . '/big/' . $picVal['pic']);
                }

                if (is_file($filePath . '/small/' . $picVal['pic'])) {
                    @chmod($filePath . '/small/' . $picVal['pic'], 0666);
                    @unlink($filePath . '/small/' . $picVal['pic']);
                }

                if (is_file($filePath . '/real/' . $picVal['pic'])) {
                    @chmod($filePath . '/real/' . $picVal['pic'], 0666);
                    @unlink($filePath . '/real/' . $picVal['pic']);
                }
                $this->db->query("DELETE FROM `catalog_gallery` WHERE `id`='$id' AND `gallery_type` = 'foreshortening'");
            }
            return true;
        }

        if ($artikul === null && isset($_POST['dell_foreshortening_pic']) && is_array($_POST['dell_foreshortening_pic']) && count($_POST['dell_foreshortening_pic']) > 0) {

            foreach ($_POST['dell_foreshortening_pic'] as $key => $value) {
                if (is_numeric($key)) {
                    $this->deleteForeshortening($key);
                }
            }
        }
    }

    // Удаляет картинки с галереи ракурсов при удалеии товара или раздела

    private function deleteForeshorteningPageId($artukul = null)
    {

        //dell_foreshortening_pic
        if (!empty($artukul)) {

            $picVal = $this->db->fetchAll("SELECT * FROM `catalog_gallery`  WHERE `goods_artikul` = '$artukul' AND `gallery_type` = 'foreshortening'");

            if ($picVal) {

                foreach ($picVal as $pic) {

                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/foreshortening';
                    if (is_file($filePath . '/big/' . $pic['pic'])) {
                        @chmod($filePath . '/big/' . $pic['pic'], 0666);
                        @unlink($filePath . '/big/' . $pic['pic']);
                    }

                    if (is_file($filePath . '/small/' . $pic['pic'])) {
                        @chmod($filePath . '/small/' . $pic['pic'], 0666);
                        @unlink($filePath . '/small/' . $pic['pic']);
                    }

                    if (is_file($filePath . '/real/' . $pic['pic'])) {
                        @chmod($filePath . '/real/' . $pic['pic'], 0666);
                        @unlink($filePath . '/real/' . $pic['pic']);
                    }
                }

                $this->db->query("DELETE FROM `catalog_gallery` WHERE `goods_artikul`='$artukul' AND `gallery_type` = 'foreshortening'");
            }
        }
    }

    // Мини галерея в описании товара
    // Загрузка картинок товара для минигалереи. Вызывается при добавлении и редактировании товара
    private function uploadMiniGallery($goodsArtikul)
    {
        if (count($_FILES) > 0) {

            foreach ($_FILES as $key => $file) {
                if (isset($file['size']) && $file['size'] > 0 && substr($key, 0, strlen($key) - 1) == 'gallery_pic') {

                    $pos = strrpos($file['name'], '.');
                    $fNameTmp = substr($file['name'], 0, $pos);
                    $fNameTmp = $this->ru2Lat($goodsArtikul . ' ' . $fNameTmp);

                    $fExTmp = substr($file['name'], $pos, strlen($file['name']));
                    $file['name'] = $fNameTmp . $fExTmp;


                    $_name = $file['name'];
                    $tempFile = $file['tmp_name'];
                    $handle = new upload($file);

                    $title = '';
                    $alt = '';

                    $titleName = str_replace('gallery_pic', 'gallery_title', $key);
                    $altName = str_replace('gallery_pic', 'gallery_alt', $key);

                    if (isset($_POST[$titleName])) {
                        $title = $_POST[$titleName];
                    }

                    if (isset($_POST[$altName])) {
                        $alt = $_POST[$altName];
                    }

                    $handle = new upload($file);

                    if ($handle->uploaded) {
                        //$handle->file_new_name_body   = 'image_resized';
                        $handle->image_resize = true;
                        $handle->image_x = 100;
                        $handle->image_y = 100;
                        $handle->file_auto_rename = true;
                        $handle->image_ratio_fill = 'LT';
                        $handle->image_background_color = '#FFFFFF';
                        $handle->file_safe_name = false;
                        //$handle->image_text = $text ;
                        //$handle->image_ratio_y        = true;
                        $handle->process($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/gallery/small/");
                        if ($handle->processed) {
                            @copy($tempFile, $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/gallery/real/' . $handle->file_dst_name);
                            $this->db->insert("catalog_gallery", array(
                                'goods_artikul' => $goodsArtikul,
                                'gallery_type' => 'gallery',
                                'pic' => $handle->file_dst_name,
                                'title' => $title,
                                'alt' => $alt
                            ));
                        } else {
                            $this->addErr($handle->error);
                            return false;
                        }
                    }
                }
            }
        }
    }

    // Вываливает список загруженных картинок в форме редактирования товара для фоток с минигалереи.
    private function drowMiniGallery($goodsArtikul)
    {
        $this->tpl->parse('GALLERY_PIC_LIST', 'null');
        $items = $this->db->fetchAll("SELECT * FROM `catalog_gallery` WHERE `goods_artikul` = '$goodsArtikul' AND `gallery_type` = 'gallery'");

        if ($items) {
            foreach ($items as $item) {
                $this->tpl->assign(array(
                    'G_ID' => $item['id'],
                    'G_SRC' => $item['pic'],
                    'G_ALT' => $item['alt'],
                    'G_TITLE' => $item['title']
                ));

                $this->tpl->parse('GALLERY_PIC_LIST', '.gallery_pic_list');
            }
        }
    }

    // Удаляет картинки с минигалереи выбранные галочной
    private function deleteMiniGallery($id = null)
    {
        //dell_foreshortening_pic

        if ($id !== null && is_numeric($id)) {

            $picVal = $this->db->fetchRow("SELECT * FROM `catalog_gallery`  WHERE `id` = '$id' AND `gallery_type` = 'gallery'");

            if ($picVal) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/gallery';
                if (is_file($filePath . '/big/' . $picVal['pic'])) {
                    @chmod($filePath . '/big/' . $picVal['pic'], 0666);
                    @unlink($filePath . '/big/' . $picVal['pic']);
                }

                if (is_file($filePath . '/small/' . $picVal['pic'])) {
                    @chmod($filePath . '/small/' . $picVal['pic'], 0666);
                    @unlink($filePath . '/small/' . $picVal['pic']);
                }

                if (is_file($filePath . '/real/' . $picVal['pic'])) {
                    @chmod($filePath . '/real/' . $picVal['pic'], 0666);
                    @unlink($filePath . '/real/' . $picVal['pic']);
                }
                $this->db->query("DELETE FROM `catalog_gallery` WHERE `id` = '$id' AND `gallery_type` = 'gallery'");
            }
            return true;
        }

        if ($id === null && isset($_POST['dell_gallery_pic']) && is_array($_POST['dell_gallery_pic']) && count($_POST['dell_gallery_pic']) > 0) {

            foreach ($_POST['dell_gallery_pic'] as $key => $value) {
                if (is_numeric($key)) {
                    $this->deleteMiniGallery($key);
                }
            }
        }
    }

    // Удаляет картинки с минигалереи при удалеии товара или раздела
    private function deleteMiniGalleryPageId($artikul = null)
    {
        //dell_foreshortening_pic

        if (!empty($artikul)) {

            $picVal = $this->db->fetchAll("SELECT * FROM `catalog_gallery`  WHERE `goods_artikul` = '$artikul' AND `gallery_type` = 'gallery'");

            if ($picVal) {
                foreach ($picVal as $pic) {
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/gallery';
                    if (is_file($filePath . '/big/' . $pic['pic'])) {
                        @chmod($filePath . '/big/' . $pic['pic'], 0666);
                        @unlink($filePath . '/big/' . $pic['pic']);
                    }

                    if (is_file($filePath . '/small/' . $pic['pic'])) {
                        @chmod($filePath . '/small/' . $pic['pic'], 0666);
                        @unlink($filePath . '/small/' . $pic['pic']);
                    }

                    if (is_file($filePath . '/real/' . $pic['pic'])) {
                        @chmod($filePath . '/real/' . $pic['pic'], 0666);
                        @unlink($filePath . '/real/' . $pic['pic']);
                    }
                }
                $this->db->query("DELETE FROM `catalog_gallery` WHERE `goods_artikul` = '$artikul' AND `gallery_type` = 'gallery'");
            }
        }
    }

    // Преобразует массив из конфига каталога для FormManager
    private function getDefaultFieldsForm()
    {
        $catalogOptions = $this->getCatalogOptions(true);

        if (isset($catalogOptions['defaultFields'])) {
            $defaultFields = $catalogOptions['defaultFields'];
        }


        $ret = array();
        if (isset($defaultFields) && is_array($defaultFields) && count($defaultFields) > 0 && isset(CatalogFormOptions::$defaultFieldsGroups) && is_array(CatalogFormOptions::$defaultFieldsGroups) && count(CatalogFormOptions::$defaultFieldsGroups) > 0) {
            $uniqType = array();
            $uniqGroup = array();
            foreach (CatalogFormOptions::$defaultFieldsGroups as $group => $defaultFieldsGroup) {
                if (is_array($defaultFieldsGroup) && count($defaultFieldsGroup) > 0) {

                    foreach ($defaultFieldsGroup as $key => $value) {
                        $field = array();
                        $name = $value;
                        $title = $value;
                        if (is_numeric($key) && isset($defaultFields[$value])) {
                            $title = $defaultFields[$value];
                            //  $field[$value] = $defaultFields[$value];
                        } elseif (!is_numeric($key)) {
                            $field[$key] = $value;
                            $title = $value;
                            $name = $key;
                        }

                        if (!is_array($title)) {

                            if (!isset($field['name'])) {
                                $field['name'] = $name;
                            }

                            if (!isset($field['type']) || empty($field['type'])) {
                                $field['type'] = 'varchar';
                            }

                            if (!isset($field['title'])) {
                                $field['title'] = $title;
                            }

                            $ret[$group][] = $field;
                        } else {
                            if (!isset($title['name'])) {
                                $title['name'] = $name;
                            }
                            $ret[$group][] = $title;
                        }
                    }
                }
            }
        }
        // print_r($ret);
        //  die;
        return $ret;
    }

    // Если есть в разделе дополнительные поля - вываливает форму

    private function getSectionForms($sectionArtikul, $data = array())
    {
        $ret = '';
        $arr = $this->drowSectionFields($sectionArtikul, true);
        if (count($arr) > 0) {
            $ret = " \n";
            foreach ($arr as $group => $val) {

                if ($group != 'A2') {

                    if ($group == 'A1') {
                        $group = 'Основная группа';
                    }
                    $ret .= "<tr class='features'><th colspan='2' style='text-align: center; color: #000; font-size: 14px;'>$group</th></tr>\n";
                    foreach ($val as $subGroup => $val1) {
                        if ($subGroup != 'A2') {
                            if ($subGroup == 'A1') {
                                $subGroup = 'Основная подгруппа';
                            }
                            $ret .= "<tr class='features'><th colspan='2'>$subGroup</th></tr>\n";

                            foreach ($val1 as $field) {
                                $value = '';

                                if (!empty($_POST)) {
                                    $value = $this->getVar($field['name'], '');
                                } elseif (isset($data[$field['name']])) {
                                    $value = $data[$field['name']];
                                }

                                if ($field['type'] != 'mce') {
                                    $type = $this->getField($field['type'], $field['name'], $value);
                                    $ret .= "<tr class='features'><td>$field[title]</td> <td>" . $type['html'] . "</td></tr>\n";
                                } else {
                                    $type = $this->getField($field['type'], $field['name']);
                                    $ret .= "<tr class='features'><td colspan='2'>$field[title]</td></tr><tr class='features'> <td colspan='2'>" . $type['html'] . "</td></tr>\n";
                                }
                            }
                        }
                    }
                }
            }
            $ret .= "\n";
        }

        return $ret;
    }

    // Если есть в разделе дополнительные поля - читает данные с _POST
    // Вываливает список разделов и подразделов в форме админки
    private function showAdmSections($level = 0, $activeLevel = '', $isShowOnlySection = false)
    {
        $ret = '';
        if (($row = $this->db->fetchAll("SELECT `id`, `name` FROM `catalog` WHERE `type` = 'section' AND `level` = '$level'"))) {
            foreach ($row as $res) {
                $ret .= "<option value='$res[id]'>$res[name]</option>\n";
                if ($level == 0 && !$isShowOnlySection) {
                    $ret .= $this->showAdmSections($res['id'], $activeLevel);
                }
            }
        }
        return $ret;
    }

    private function readSectionPost($sectionArtikul, $defaultFields = array())
    {
        $data = array();

        if (count($defaultFields) <= 0) {
            $defaultFields = $this->getCatalogOptions(true);
            if (isset($defaultFields['defaultFields'])) {
                $defaultFields = $defaultFields['defaultFields'];
            }
        }

        if (!empty($_POST)) {
            $row = $this->db->fetchAll("SELECT `name`, `type`, `group`, `sub_group` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$sectionArtikul' AND `language` = '$this->lang'");

            if ($row) {
                foreach ($row as $res) {

                    if (!isset($defaultFields[$res['name']])) {
                        $val = $this->getVar($res['name'], false);
                        if ($val !== false) {
                            if ($res['type'] == 'mce') {
                                $val = addslashes($val);
                            }
                            $data[$res['name']] = $val;
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function settingcatalog()
    {
        $this->setMetaTags("Настройка каталога");
        $this->setWay("Настройка каталога");

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('cat_setting', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $level = 0;

        $settings = $this->db->fetchRow("SELECT * FROM `settings_catalog` WHERE `level` = '$level'");

        $isShowPics = '';

        $this->tpl->assign(
                array('IS_SHOW_EMPTY_PIC_S' => (isset($settings['IS_SHOW_EMPTY_PIC'])),
                    'IS_SHOW_EMPTY_PRICE_S' => '',
                    'IS_SHOW_EMPTY_HITS_S' => '',
                    'IS_SHOW_EMPTY_NEW_S' => '',
                    'IS_SHOW_EMPTY_ACTIONS_S' => '')
        );

        $this->tpl->parse('CONTENT', 'start');
        $this->tpl->parse('CONTENT', '.cat_setting');
        $this->tpl->parse('CONTENT', '.end');
        return true;
    }

    public function adddeliveryservice()
    {
        $this->setMetaTags("Службы доставки");
        $this->setWay("Службы доставки");
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('end', 'edit');



        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.mce');
        $this->tpl->parse('CONTENT', '.meta');
        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    public function editdeliveryservice()
    {
        $this->setMetaTags("Службы доставки");
        $this->setWay("Службы доставки");
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('end', 'edit');



        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.mce');
        $this->tpl->parse('CONTENT', '.meta');
        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    public function deliveryservice()
    {
        $this->setMetaTags("Службы доставки");
        $this->setWay("Службы доставки");

        $this->tpl->define_dynamic('_delivery', 'adm/delivery.tpl');
        $this->tpl->define_dynamic('delivery', '_delivery');
        $this->tpl->define_dynamic('delivery_item', 'delivery');

        $this->setH1Admin(false, false, '/admin/adddelivery/');

        $delivery = $this->db->fetchAll('SELECT * FROM `delivery`');
        $this->tpl->parse('DELIVERY_ITEM', 'null');

        if ($delivery) {
            $size = count($delivery);
            for ($i = 0; $i < $size; $i++) {
                $this->setAdminButtons('/admin/eeditdelivery/' . $delivery[$i]['id'], '/admin/deletedelivery/' . $delivery[$i]['id']);
                $this->tpl->assign(
                        array(
                            'ID' => $delivery[$i]['id'],
                            'VISIBLE' => ($delivery[$i]['visibility'] == '1' ? 'Видимый' : 'Скрытый'),
                            'NAME' => $delivery[$i]['name'],
                            'ADMIM_CLASS_NAME' => ($this->_isAdmin() ? 'plashka-admin-button' : '')
                        )
                );

                $this->tpl->parse('DELIVERY_ITEM', '.delivery_item');
            }
        }

        $this->tpl->parse('CONTENT', '.delivery');
        return true;
    }

    public function adddelivery()
    {

        $meta = 'Добавление службы доставки';
        $this->setMetaTags($meta);
        $this->setWay($meta);
        //$idm = gp($this->w, 2);
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('name', 'edit');
        $this->tpl->define_dynamic('visible', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $name = $this->getVar('name');
        $visible = $this->getVar('visible', '1');

        $visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }

        if (!empty($_POST)) {
            $numRow = $this->db->fetchOne('SELECT count(`id`) FROM `delivery` WHERE `name` = "' . $name . '"');
            if ($numRow > 0) {
                $this->addErr('Элемент с таким именем уже существует!<br>');
            }
        }

        if (!empty($_POST) && !$this->_err) {

            $this->db->query('INSERT INTO `delivery` ( `name`, `visibility`)	VALUES ("' . $name . '", "' . $visible . '" )');
            $content = "Элемент добавлен. <meta http-equiv='refresh' content='1;URL=/admin/deliveryservice'>";
            $this->viewMessage($content);
        }
        if ($this->_err) {
            $this->viewErr();
        }

        if (empty($name)) {
            $name = '';
        }

        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                    array(
                        'VISIBLE_S' => $visible_s,
                        'ADM_NAME' => $name,
                    )
            );



            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.name');
            $this->tpl->parse('CONTENT', '.visible');
            $this->tpl->parse('CONTENT', '.end');
        }
        return true;
    }

    public function editdelivery()
    {
        $id = end($this->url);

        if (!ctype_digit($id)) {
            return false;
        }
        $meta = 'Редактирование службы доставки';
        $this->setMetaTags($meta);
        $this->setWay($meta);

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('name', 'edit');
        $this->tpl->define_dynamic('visible', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $name = $this->getVar('name');
        $visible = $this->getVar('visible', '1');
        $visible_s = '';


        $deliveryValues = $this->db->fetchAll('SELECT * FROM `delivery` WHERE `id` = "' . $id . '"');
        if (count($deliveryValues) <= 0) {
            $this->addErr('Элемента с таким id не существует!<br>');
        }

        if (!empty($_POST) && !$this->_err) {

            $this->db->query('UPDATE `delivery` SET  `name` = "' . $name . '", `visibility` =  "' . $visible . '" WHERE `id` = "' . $id . '"');
            $content = "Данные изменены . <meta http-equiv='refresh' content='1;URL=/admin/deliveryservice'>";
            $this->viewMessage($content);
        }

        if ($this->_err) {
            $this->viewErr();
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
                        'ADM_NAME' => $deliveryValues[0]['name'],
                    )
            );

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.name');
            $this->tpl->parse('CONTENT', '.visible');
            $this->tpl->parse('CONTENT', '.end');
        }
        return true;
    }

    public function deletedelivery()
    {
        $meta = 'Удаление службы доставки';
        $this->setMetaTags($meta);
        $this->setWay($meta);

        if (!$id = $this->gp($this->url, 2))
            return false;
        $length = $this->db->fetchRow('SELECT * FROM `delivery` WHERE `id` = ' . $id);

        if (count($length) <= 0) {
            $this->addErr('Элемента с таким id не существует!<br>');
        }

        if ($this->_err) {
            $this->viewErr();
        } else {
            $query = 'DELETE FROM `delivery` WHERE `id` = ' . $id;
            $this->db->query($query);
            $content = "Элемент удалён.<meta http-equiv='refresh' content='1;URL=/admin/deliveryservice'>";
            $this->viewMessage($content);
        }

        return true;
    }

    public function section_meta_tags()
    {

        $this->setMetaTags("Добавление метатегов");
        $this->setWay("Добавление метатегов");

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('js', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('name', 'edit');
        //$this->tpl->define_dynamic('adress', 'edit');
        $this->tpl->define_dynamic('meta', 'edit');
        $this->tpl->define_dynamic('body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');


        $name = $this->getVar('name', '');
        //$href = $this->ru2Lat($this->getVar('adm_href', ''));
        $preview = $this->getVar('preview', '');
        $header = $this->getVar('header', '');
        $title = $this->getVar('title', '');
        $keywords = $this->getVar('keywords', '');
        $description = $this->getVar('description', '');
        $body = $this->getVar('body', '');



        if (!empty($_POST) && empty($name)) {
            $this->addErr("Поле <b>Название</b> не может быть пустым");
        }

        $this->tpl->assign(
                array('ADM_HEADER' => $header,
                    'ADM_NAME' => $name,
                    'ADM_TITLE' => $title,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'ADM_BODY' => $body
        ));



        if (!empty($_POST) && !$this->_err) {

            $id = $this->db->fetchOne("SELECT `id` FROM `meta_tags` WHERE `href` = 'catalog'");

            $data = array(
                'name' => $name,
                'href' => 'catalog',
                'body' => $body,
                'header' => $header,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'language' => $this->lang,
            );

            if ($id) {
                $this->db->update('meta_tags', $data, "id=$id");
            } else {
                $this->db->insert('meta_tags', $data);
            }


            $content = "Метатеги добавлены<meta http-equiv='refresh' content='2;URL=/catalog'>";
            $this->viewMessage($content);
        } else {
            $this->viewErr();
        }


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.js');
        $this->tpl->parse('CONTENT', '.mce');
        $this->tpl->parse('CONTENT', '.name');
        //$this->tpl->parse('CONTENT', '.adress');
        $this->tpl->parse('CONTENT', '.meta');
        $this->tpl->parse('CONTENT', '.body');
        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    public function deletemetatag()
    {
        $id = false;

        $this->setMetaTags("Удаление метатегов");
        $this->setWay("Удаление метатегов");

        if (count($this->url) > 0) {
            $id = end($this->url);
            if (is_numeric($id)) {
                $id = $this->db->fetchOne('SELECT `id` FROM `meta_tags` WHERE `id` = "' . $id . '"');
            } else {
                $id = false;
            }
        }

        if (!$id) {
            $this->addErr("Не удалось найти элемент");
            $this->viewErr();
        } else {
            $this->db->delete('meta_tags', "id=$id");
            $content = "Метатеги удалены<meta http-equiv='refresh' content='2;URL=/catalog'>";
            $this->viewMessage($content);
        }



        return true;
    }

    public function addlink()
    {
        return $this->addpages('link');
    }

    public function addpage()
    {
        return $this->addpages('page');
    }

    public function addsection()
    {
        return $this->addpages('section');
    }

    private function addpages($type = null)
    {
        if (null === $type) {
            return $this->error404();
        }

        $id = end($this->url);

        if ($id == 'horisontal' || $id == 'vertical') {
            $level = 0;
            $menu = $id;
        } elseif (ctype_digit($id) && $id > 0) {
            $level = $id;
            $menu = 'none';
        } else {
            return $this->error404();
        }

        switch ($type) {
            case 'section' : $meta = 'Добавление нового раздела';
                break;
            case 'page' : $meta = 'Добавление новой страницы';
                break;
            case 'link' : $meta = 'Добавление новой сылки';
                break;
            default : return $this->error404();
                break;
        }

        $this->setMetaTags($meta);
        $this->setWay($meta);

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('s_adress', 'edit');
        $this->tpl->define_dynamic('s_pos', 'edit');
        $this->tpl->define_dynamic('s_meta', 'edit');
        $this->tpl->define_dynamic('header', 'edit');
        $this->tpl->define_dynamic('s_visible', 'edit');
        $this->tpl->define_dynamic('top', 'edit');
        $this->tpl->define_dynamic('s_preview', 'edit');
        $this->tpl->define_dynamic('s_body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        //$href = str_replace(' ', '_', $this->getVar('adm_href', ''));
        $href = $this->ru2Lat($this->getVar('adm_href', ''));
        $position = $this->getVar('position', 9999);
        $preview = $this->getVar('preview', '');
        $header = $this->getVar('header', '');
        $title = $this->getVar('title', '');
        $keywords = $this->getVar('keywords', '');
        $description = $this->getVar('description', '');
        $visible = $this->getVar('visible', 1);
        $top = $this->getVar('top', 0);
        $body = $this->getVar('body', '');

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

        if (!empty($_POST)) {
            $referer = $this->getVar('HTTP_REFERER', '');

            if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `page` WHERE `href` = "' . $href . '" AND `language` = "' . $this->lang . '"');

                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        }

        if (!empty($_POST) && !$this->_err) {
            $data = array(
                'href' => $href,
                'position' => $position,
                'preview' => stripslashes($preview),
                'header' => $header,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'visibility' => $visible,
                'body' => stripslashes($body),
                'level' => $level,
                'menu' => $menu,
                'top' => $top,
                'type' => $type,
                'language' => $this->lang
            );

            $this->db->insert("page", $data);

            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Элемент успешно добавлен<meta http-equiv='refresh' content='2;URL=$referer'>";

            $this->viewMessage($content);
        }

        if ($this->_err) {
            $this->viewErr();
        }

        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                    array(
                        'ADM_HREF' => $href,
                        'ADM_POSITION' => $position,
                        'ADM_PREVIEW' => stripslashes($preview),
                        'ADM_BODY' => stripslashes($body),
                        'VISIBLE_S' => $visible_s,
                        'TOP_S' => $top_s,
                        'ADM_HEADER' => $header,
                        'ADM_TITLE' => $title,
                        'ADM_KEYWORDS' => $keywords,
                        'ADM_DESCRIPTION' => $description,
                        'REFERER' => $referer
                    )
            );

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.mce');
            $this->tpl->parse('CONTENT', '.s_adress');
            if ($type == 'link')
                $this->tpl->parse('CONTENT', '.header');
            $this->tpl->parse('CONTENT', '.s_pos');
            $this->tpl->parse('CONTENT', '.s_visible');
            if ($type == 'page')
                $this->tpl->parse('CONTENT', '.top');
            if ($type != 'link')
                $this->tpl->parse('CONTENT', '.s_meta');
            if ($menu == 'none' && $type != 'link')
                $this->tpl->parse('CONTENT', '.s_preview');
            if ($type != 'link')
                $this->tpl->parse('CONTENT', '.s_body');
            $this->tpl->parse('CONTENT', '.end');
        }

        return true;
    }

    public function editpage()
    {
        $id = end($this->url);

        if ($id == 'mainpage') {
            $page = $this->db->fetchRow("SELECT * FROM `page` WHERE `language` = '" . $this->lang . "' AND `href` = '$id'");
            $type = 'mainpage';
        } elseif (ctype_digit($id) && $id > 0) {
            $page = $this->db->fetchRow("SELECT * FROM `page` WHERE `id` = '$id'");
            $type = $page['type'];
        } else {
            return $this->error404();
        }

        if (!$page) {
            return $this->error404();
        }

        $id = $page['id'];

        switch ($type) {
            case 'mainpage' : $meta = 'Редактирование главной страницы';
                break;
            case 'section' : $meta = 'Редактирование раздела';
                break;
            case 'page' : $meta = 'Редактирование страницы';
                break;
            case 'link' : $meta = 'Редактирование ссылки';
                break;
            default : return $this->error404();
                break;
        }

        $this->setMetaTags($meta);
        $this->setWay($meta);

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('s_adress', 'edit');
        $this->tpl->define_dynamic('s_pos', 'edit');
        $this->tpl->define_dynamic('s_meta', 'edit');
        $this->tpl->define_dynamic('s_post', 'edit');
        $this->tpl->define_dynamic('header', 'edit');
        $this->tpl->define_dynamic('s_visible', 'edit');
        $this->tpl->define_dynamic('top', 'edit');
        $this->tpl->define_dynamic('s_preview', 'edit');
        $this->tpl->define_dynamic('s_body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $href = $page['href'];
        $position = $page['position'];
        $preview = $page['preview'];
        $header = $page['header'];
        $title = $page['title'];
        $keywords = $page['keywords'];
        $description = $page['description'];
        $visible = $page['visibility'];
        $top = $page['top'];
        $body = $page['body'];

        if (!empty($_POST)) {
            if ($type == 'mainpage') {
                $href = 'mainpage';
                $visible = 1;
            } else {
                //$href = str_replace(' ', '_', $this->getVar('adm_href', ''));
                $href = $this->ru2Lat($this->getVar('adm_href', ''));
                $visible = $this->getVar('visible', 1);
            }

            $position = $this->getVar('position', 9999);
            $preview = $this->getVar('preview', '');
            $header = $this->getVar('header', '');
            $title = $this->getVar('title', '');
            $keywords = $this->getVar('keywords', '');
            $description = $this->getVar('description', '');
            $visible = $this->getVar('visible', 1);
            $top = $this->getVar('top', 0);
            $body = $this->getVar('body', '');

            $referer = $this->getVar('HTTP_REFERER', '');

            if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `page` WHERE `href` = '$href' AND `language` = '" . $this->lang . "' AND `id` <> $id");

                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        }

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

        if (!empty($_POST) && !$this->_err) {
            $data = array(
                'href' => $href,
                'position' => $position,
                'preview' => stripslashes($preview),
                'header' => $header,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'visibility' => $visible,
                'body' => stripslashes($body),
                'top' => $top
            );



            $n = $this->db->update('page', $data, "id = $id");

            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Элемент успешно изменен<meta http-equiv='refresh' content='2;URL=$referer'>";

            $this->viewMessage($content);
        }

        if ($this->_err) {
            $this->viewErr();
        }

        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                    array(
                        'ADM_HREF' => $href,
                        'ADM_POSITION' => $position,
                        'ADM_PREVIEW' => $preview,
                        'ADM_BODY' => $body,
                        'VISIBLE_S' => $visible_s,
                        'TOP_S' => $top_s,
                        'ADM_HEADER' => $header,
                        'ADM_TITLE' => $title,
                        'ADM_KEYWORDS' => $keywords,
                        'ADM_DESCRIPTION' => $description,
                        'REFERER' => $referer
                    )
            );

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.mce');

            if ($type != 'mainpage')
                $this->tpl->parse('CONTENT', '.s_adress');

            if ($type == 'link')
                $this->tpl->parse('CONTENT', '.header');
            if ($type != 'mainpage')
                $this->tpl->parse('CONTENT', '.s_pos');
            if ($type != 'mainpage')
                $this->tpl->parse('CONTENT', '.s_visible');
            if ($type == 'page')
                $this->tpl->parse('CONTENT', '.top');
            if ($type != 'link')
                $this->tpl->parse('CONTENT', '.s_meta');
            if ($type != 'mainpage' && $type != 'link')
                $this->tpl->parse('CONTENT', '.s_preview');
            if ($type != 'link')
                $this->tpl->parse('CONTENT', '.s_body');
            $this->tpl->parse('CONTENT', '.end');
        }

        return true;
    }

    public function deletepage()
    {
        $id = end($this->url);

        if (!ctype_digit($id)) {
            return $this->error404();
        }

        $page = $this->db->fetchRow("SELECT * FROM `page` WHERE `id` = '$id'");

        if (!$page) {
            return $this->error404();
        }

        switch ($page['type']) {
            case 'section' : $meta = 'Удаление раздела';
                break;
            case 'page' : $meta = 'Удаление страницы';
                break;
            case 'link' : $meta = 'Удаление ссылки';
                break;
            default : return $this->error404();
                break;
        }

        $this->setMetaTags($meta);
        $this->setWay($meta);

        $n = $this->db->delete('page', "id = $id");

        if ($page['type'] == 'section') {
            $this->deleteSubPages($page['id']);
        }

        $referer = $this->getVar('HTTP_REFERER', $this->basePath);

        $content = "Элемент(ы) успешно удален(ы)<meta http-equiv='refresh' content='2;URL=$referer'>";
        $this->viewMessage($content);

        return true;
    }

    private function deleteSubPages($id = null)
    {
        if (null === $id) {
            return $this->error404();
        }

        $pages = $this->db->fetchAll("SELECT `id`, `type` FROM `page` WHERE `level` = '$id'");

        $n = $this->db->delete('page', "level = $id");

        if ($pages) {
            foreach ($pages as $page) {
                if ($page['type'] == 'section') {
                    $this->deleteSubPages($page['id']);
                }
            }
        }
    }

    public function lookups()
    {
        $this->setMetaTags('Редактируемые поля');
        $this->setWay('Редактируемые поля');

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {

                $this->db->query("UPDATE `lookups` SET `value` = '" . addslashes($value) . "' WHERE `key` = '" . $key . "' AND `language` = '" . $this->lang . "'");
            }

            $content = "Элементы успешно обновлены<meta http-equiv='refresh' content='2;URL=" . $this->basePath . 'admin/lookups' . "'>";
            $this->viewMessage($content);

            return true;
        }

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('lookups', 'edit');
        $this->tpl->define_dynamic('lookups_item', 'lookups');
        $this->tpl->define_dynamic('end', 'edit');

        foreach ($this->lookups as $lookups) {
            $this->tpl->assign(
                    array(
                        'LOOK_NAME' => $lookups['name'],
                        'LOOK_KEY' => $lookups['key'],
                        'LOOK_VALUE' => $lookups['value']
                    )
            );

            $this->tpl->parse('LOOKUPS_ITEM', '.lookups_item');
        }

        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.lookups');
        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    public function metatags()
    {
        $this->setMetaTags('Мета-Теги');
        $this->setWay('Мета-Теги');

        $meta = $this->db->fetchAll("SELECT `id`, `name`, `href` FROM `meta_tags` WHERE `language` = '" . $this->lang . "' ORDER BY `id`");

        $this->tpl->define_dynamic('_section', 'pages.tpl');
        $this->tpl->define_dynamic('section', '_section');
        $this->tpl->define_dynamic('section_row', 'section');

        $this->tpl->assign('PAGES_LIST_ADMIN', '');

        foreach ($meta as $item) {
            $this->setAdminButtons('/admin/editmetatag/' . $item['id'], false);
            $this->tpl->assign(
                    array(
                        'PAGE_ADRESS' => $this->basePath . $item['href'],
                        'PAGE_HEADER' => stripslashes($item['name']),
                        'PAGE_PREVIEW' => '',
                        'PAGINATION' => ''
                    )
            );

            $metaEdit = '';
            if ($this->_isAdmin()) {
                $templates = $this->loadAdminButtonsTemplate();

                $templates->assign(
                        array(
                            'BUTTON_EDIT_URL' => '/admin/editmetatag/' . $item['id'],
                            'BUTTON_EDIT_TITLE' => 'Редактировать страницу'
                        )
                );

                $templates->parse('BUTTON_SETTINGS', 'null');
                $templates->parse('BUTTON_FEATURES', 'null');
                $templates->parse('BUTTON_DELETE', 'null');

                $templates->parse('ADMIN_BUTTONS_ACTION', 'admin_buttons_action');

                $metaEdit = $templates->prnt_to_var('ADMIN_BUTTONS_ACTION');
            }

            $this->tpl->assign('PAGES_ITEM_ADMIN', $metaEdit);

            $this->tpl->parse('SECTION_ROW', '.section_row');
        }
        $this->tpl->parse('CONTENT', '.section');

        return true;
    }

    public function editmetatag()
    {
        $id = end($this->url);

        if (!ctype_digit($id)) {
            return $this->error404();
        }

        $meta = $this->db->fetchRow("SELECT * FROM `meta_tags` WHERE `id` = $id");

        if (!$meta) {
            return $this->error404();
        }

        $this->setMetaTags('Мета-Теги : ' . $meta['name']);
        $this->setWay('Мета-Теги : ' . $meta['name']);

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('s_meta', 'edit');
        $this->tpl->define_dynamic('s_body', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $header = $meta['header'];
        $title = $meta['title'];
        $keywords = $meta['keywords'];
        $description = $meta['description'];
        $body = $meta['body'];

        if (!empty($_POST)) {
            $header = $this->getVar('header', '');
            $title = $this->getVar('title', '');
            $keywords = $this->getVar('keywords', '');
            $description = $this->getVar('description', '');
            $body = $this->getVar('body', '');
            $referer = $this->getVar('HTTP_REFERER', '');

            $data = array(
                'header' => $header,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'body' => $body
            );

            $this->db->update('meta_tags', $data, "id = $id");

            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Элемент успешно изменен<meta http-equiv='refresh' content='2;URL=$referer'>";

            $this->viewMessage($content);

            return true;
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
            $this->tpl->assign(
                    array(
                        'ADM_BODY' => $body,
                        'ADM_HEADER' => $header,
                        'ADM_TITLE' => $title,
                        'ADM_KEYWORDS' => $keywords,
                        'ADM_DESCRIPTION' => $description,
                        'REFERER' => $referer
                    )
            );

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.mce');
            $this->tpl->parse('CONTENT', '.s_meta');
            $this->tpl->parse('CONTENT', '.s_body');
            $this->tpl->parse('CONTENT', '.end');
        }

        return true;
    }

    public function settings()
    {
        $this->setMetaTags('Настройки сайта');
        $this->setWay('Настройки сайта');

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (stristr($key, 'settings_')) {
                    $key = str_ireplace('settings_', '', $key);
                    if ($key != 'password') {
                        $this->db->query('UPDATE `settings` SET `value` = "' . $value . '" WHERE `key` = "' . $key . '"');
                    } else {
                        if ($value != '') {
                            $pass = crypt($value, 'tEXFVrqY');
                            $this->db->query("UPDATE `users` SET `pass` = '" . $pass . "' WHERE `login` = 'admin'");
                        }
                    }
                }
            }

            $content = "Данные изменены<meta http-equiv='refresh' content='2;URL=" . $this->basePath . "admin/settings'>";
            $this->viewMessage($content);

            return true;
        }

        $settings = $this->db->fetchAll("SELECT * FROM `settings` ORDER BY `id`");

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('settings', 'edit');
        $this->tpl->define_dynamic('settings_item', 'settings');
        $this->tpl->define_dynamic('end', 'edit');

        foreach ($settings as $sett) {
            $this->tpl->assign(
                    array(
                        'NAME' => $sett['name'],
                        'KEY' => $sett['key'],
                        'VALUE' => $sett['value']
                    )
            );

            $this->tpl->parse('SETTINGS_ITEM', '.settings_item');
        }

        $this->tpl->assign(
                array(
                    'NAME' => 'Пароль администратора',
                    'KEY' => 'password',
                    'VALUE' => ''
                )
        );

        $this->tpl->parse('SETTINGS_ITEM', '.settings_item');

        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.settings');
        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    // База заказов
    public function orders()
    {

        $this->setMetaTags('База заказов');
        $this->setWay('База заказов');
        $this->_orders();
        return true;
    }

    public function orderupdatestatus()
    {
        $this->setMetaTags('Изменение статуса заказа');
        $this->setWay('Изменение статуса заказа');

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Описание заказа с номером $id не найдено");
        }

        if ($this->_err) {
            $this->viewErr();
            return true;
        }

        $sql = "SELECT * FROM `orders` WHERE `id` = '$id'";
        $orderDetail = $this->db->fetchRow($sql);

        if (!$orderDetail) {
            $this->addErr("Описание заказа с номером $id не найдено");
            $this->viewErr();
            return true;
        }

        if (isset($_POST['status'])) {
            $sql = "UPDATE `orders` SET `status`='$_POST[status]' WHERE `orders`.`id`='$id'";
            $this->db->query($sql);
            $content = "Статус заказа изменен<meta http-equiv='refresh' content='2;URL=/admin/orders'>";
            $this->viewMessage($content);
        }



        return true;
    }

    // Заказ подробнее
    public function order_detail()
    {

        $this->setMetaTags('Просмотр заказа:');
        $this->setWay('Просмотр заказа');

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Описание заказа с номером $id не найдено");
        }

        if ($this->_err) {
            $this->viewErr();
            return true;
        }

        $sql = "SELECT * FROM `orders` WHERE `id` = '$id'";
        $orderDetail = $this->db->fetchRow($sql);

        //  foreach ($orderDetail as $key => $value) {
        //print "<tr>\n<td class=\"width\">:</td>\n<td> {ORDER_DETAIL_".strtoupper($key)."} </td> \n </tr> \n";
        // print "'ORDER_DETAIL_" . strtoupper($key) . "'=>\$orderDetail['$key'],\n";
        // }


        if (!$orderDetail) {
            $this->addErr("Описание заказа с номером $id не найдено");
            $this->viewErr();
            return true;
        }

        $this->tpl->define_dynamic('_order_detail', 'adm/orders.tpl');
        $this->tpl->define_dynamic('order_detail', '_order_detail');
        $this->tpl->define_dynamic('deliver_service_fields', 'order_detail');
        $this->tpl->define_dynamic('deliver_service', 'order_detail');
        $this->tpl->define_dynamic('order_detail_list', 'order_detail');
        $this->tpl->define_dynamic('order_detail_list_empty', 'order_detail');
        $this->tpl->parse('ORDER_DETAIL_LIST_EMPTY', 'null');
        $this->tpl->parse('ORDER_DETAIL_LIST', 'null');

        $id = $orderDetail['id'];
        $idItem = "00001";

        if ($id < 10) {
            $idItem = "0000$id";
        }

        if ($id >= 10 && $id < 100) {
            $idItem = "000$id";
        }

        if ($id >= 100 && $id < 1000) {
            $idItem = "00$id";
        }

        if ($id >= 1000 && $id < 10000) {
            $idItem = "0$id";
        }

        if ($id >= 10000) {
            $idItem = $id;
        }

        if (empty($orderDetail['delivery_service']) || $orderDetail['delivery_service'] == 'Самовывоз') {
            $this->tpl->parse('DELIVER_SERVICE_FIELDS', 'null');
            $this->tpl->parse('DELIVER_SERVICE', 'null');
        } elseif ($orderDetail['delivery_service'] == 'К дому, офису') {
            $this->tpl->parse('DELIVER_SERVICE_FIELDS', 'null');
        }



        $this->tpl->assign(array(
            'ORDER_DETAIL_ID' => $idItem,
            'ORDER_DETAIL_SURNAME' => $orderDetail['surname'],
            'ORDER_DETAIL_NAME' => $orderDetail['name'],
            'ORDER_DETAIL_PATRONYMIC' => $orderDetail['patronymic'],
            'ORDER_DETAIL_EMAIL' => $orderDetail['email'],
            'ORDER_DETAIL_CITY_PHONE' => $orderDetail['city_phone'],
            'ORDER_DETAIL_MOBILE_PHONE' => $orderDetail['mobile_phone'],
            'ORDER_DETAIL_DELIVERY_SERVICE' => $orderDetail['delivery_service'],
            'ORDER_DETAIL_WAREHOUSE_NUMBER' => $orderDetail['warehouse_number'],
            'ORDER_DETAIL_INSURANCE' => $orderDetail['insurance'],
            'ORDER_DETAIL_PAYMENT_METHOD' => $orderDetail['payment_method'],
            'ORDER_DETAIL_CITY' => $orderDetail['city'],
            'ORDER_DETAIL_STREET' => $orderDetail['street'],
            'ORDER_DETAIL_HOUSE_NUM' => $orderDetail['house_num'],
            'ORDER_DETAIL_AP_NUM' => $orderDetail['ap_num'],
            'ORDER_DETAIL_DOP_INFO' => $orderDetail['dop_info'],
            'ORDER_DETAIL_PASSPORT' => $orderDetail['passport'],
            'ORDER_DETAIL_PASSPORT_NUMBER' => $orderDetail['passport_number'],
            'ORDER_DETAIL_WHEN_THE_PASSPORT_IS_GIVEN' => $orderDetail['when_the_passport_is_given'],
            'ORDER_DETAIL_ISSUED_PASSPORT' => $orderDetail['issued_passport'],
            'ORDER_DETAIL_REGISTRATION_ADDRESS' => $orderDetail['registration_address'],
            'ORDER_DETAIL_INN' => $orderDetail['inn'],
            'ORDER_DETAIL_TOTAL_SUMM' => $orderDetail['total_summ'],
            'ORDER_DETAIL_TOTAL_COUNT' => $orderDetail['total_count'],
        ));

        if (($row = $this->db->fetchAll("SELECT * FROM `orders_goods` WHERE `order_id` = '$orderDetail[id]'"))) {
            $counter = 1;
            foreach ($row as $res) {
                $this->tpl->assign(array(
                    'ORDER_DETAIL_LIST_COUNTER' => $counter,
                    'ORDER_DETAIL_LIST_NAME' => $res['name'],
                    'ORDER_DETAIL_LIST_URL' => $res['url'],
                    'ORDER_DETAIL_LIST_ARTIKUL' => $res['goods_artikul'],
                    'ORDER_DETAIL_LIST_COST' => $res['cost'],
                    'ORDER_DETAIL_LIST_COUNT' => $res['count'],
                    'ORDER_DETAIL_LIST_SUMM' => $res['total_summ'],
                ));
                $this->tpl->parse('ORDER_DETAIL_LIST', '.order_detail_list');
                $counter++;
            }
        }

        //  var_dump($orderDetail);
        // die;
        $this->tpl->parse('CONTENT', '.order_detail');

        return true;
    }

    public function orderdelete()
    {
        $this->setMetaTags('Удаление заказа');
        $this->setWay('Удаление заказа');

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Описание заказа с номером $id не найдено");
        }

        if ($this->_err) {
            $this->viewErr();
            return true;
        }

        $sql = "SELECT * FROM `orders` WHERE `id` = '$id'";
        $orderDetail = $this->db->fetchRow($sql);

        if (!$orderDetail) {
            $this->addErr("Описание заказа с номером $id не найдено");
            $this->viewErr();
            return true;
        }

        $sql = "DELETE FROM `orders` WHERE `orders`.`id`='$id'";
        $this->db->query($sql);

        $sql = "DELETE FROM `orders_goods` WHERE `orders_goods`.`order_id` = '$id'";
        $this->db->query($sql);

        $content = "Заказ удален<meta http-equiv='refresh' content='2;URL=/admin/orders'>";
        $this->viewMessage($content);

        return true;
    }

    // Загрузчик картинок каталога на базе опций вверху класса

    protected function catalogImagesUploader($fileData)
    {
        if (is_array($fileData) && count($fileData) > 0) {

            if (is_array($fileData['name']) && count($fileData['name']) > 0) {
                $ret = array();
                foreach ($fileData['name'] as $index => $file) {
                    if (isset($fileData['size']) && $fileData['size'] > 0) {
                        foreach ($this->catalogImagesOptions as $key => $value) {
                            if (isset($value['path'])) {
                                if (is_file($_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'][$index])) {
                                    @chmod($_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'][$index], 0666);
                                    @unlink($_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'][$index]);
                                }



                                if (!in_array($fileData['name'][$index], $ret)) {
                                    $ret[] = $fileData['name'][$index];
                                }

                                $stamp = null;

                                if (isset($value['stamp'])) {
                                    $stamp = $_SERVER['DOCUMENT_ROOT'] . '/' . $value['stamp'];
                                }

                                if (isset($value['size']['width']) && isset($value['size']['height'])) {
                                    $this->uploadCatPic($fileData['tmp_name'][$index], $_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'][$index], $value['size']['width'], $value['size']['height'], 100, $stamp);
                                } elseif (isset($value['size']) && $value['size'] == 'copy') {
                                    @copy($fileData['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'][$index]);
                                }
                            }
                        }
                    }
                }
                return $ret;
            } else {

                if (isset($this->catalogImagesOptions) && count($this->catalogImagesOptions) > 0) {
                    foreach ($this->catalogImagesOptions as $key => $value) {
                        if (isset($value['path'])) {
                            if (is_file($_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'])) {
                                @chmod($_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'], 0666);
                                @unlink($_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name']);
                            }

                            $stamp = null;

                            if (isset($value['stamp'])) {
                                $stamp = $_SERVER['DOCUMENT_ROOT'] . '/' . $value['stamp'];
                            }

                            if (isset($value['size']['width']) && isset($value['size']['height'])) {
                                $this->uploadCatPic($fileData['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name'], $value['size']['width'], $value['size']['height'], 100, $stamp);
                            } elseif (isset($value['size']) && $value['size'] == 'copy') {
                                @copy($fileData['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $value['path'] . '/' . $fileData['name']);
                            }
                        }
                    }
                }
            }
            return $fileData['name'];
        }
        return false;
    }

    public function loadcatpics()
    {

        if (isset($_POST['file_data_name']) && isset($_FILES[$_POST['file_data_name']])) {
            $fileName = $this->catalogImagesUploader($_FILES[$_POST['file_data_name']]);
            die($this->catalogImagesOptions['small1']['path'] . $fileName);
        }

        $admFieldLoaded = '';

        if (isset($_FILES['pic_pic'])) {
            $fields = $this->catalogImagesUploader($_FILES['pic_pic']);

            if (is_array($fields) && count($fields) > 0) {
                foreach ($fields as $file) {
                    if (!empty($file)) {
                        $admFieldLoaded .= '<li><table><tr><td colspan="2" class="ui-state-default">Файл: ' . $file . '</td></tr><tr><td><img src="' . $this->catalogImagesOptions['small1']['path'] . $file . '" /></td></tr></table></li>';
                    }
                }
            }
        }

        if (!empty($admFieldLoaded)) {
            $admFieldLoaded .= '<style>.gallery-images-list-body {display: block;}</style>';
        }


        $this->setMetaTags('Загрузка картинок каталога');
        $this->setWay('Загрузка картинок каталога');

        $this->tpl->define_dynamic('loadcatpics', 'adm/loadcatpics.tpl');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $this->tpl->assign(
                array(
                    'ADM_FIELD_TITLE' => '',
                    'ADM_FIELD_NAME' => 'pic',
                    'SESSION_NAME' => session_id(),
                    'ADM_FIELD_LOADED' => $admFieldLoaded,
                    'ADM_FIELD_SCRIPT_URL' => '/admin/loadcatpics')
        );


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.loadcatpics');
        $this->tpl->parse('CONTENT', '.end');


        return true;
    }

    public function loadcatpics2()
    {
        $this->setMetaTags('Загрузка картинок каталога');
        $this->setWay('Загрузка картинок каталога');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('catalog_upload_pic', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        if (count($_FILES) > 0) {
            foreach ($_FILES as $key => $file) {
                if (isset($file['size']) && $file['size'] > 0 && substr($key, 0, strlen($key) - 1) == 'catalog_upload_pic') {
                    //$file['name'] = $this->ru2Lat($goodsId.' '.$file['name']);
                    $_name = $file['name'];
                    if (($id = $this->db->fetchOne("SELECT `id` FROM `catalog` WHERE `pic`='$_name'"))) {

                        $tempFile = $file['tmp_name'];
                        $handle = new upload($file);

                        $title = '';
                        $alt = '';

                        $titleName = str_replace('catalog_upload_pic', 'catalog_upload_text', $key);
                        $altName = str_replace('catalog_upload_pic', 'catalog_upload_alt', $key);

                        if (isset($_POST[$titleName])) {
                            $title = $_POST[$titleName];
                        }

                        if (isset($_POST[$altName])) {
                            $alt = $_POST[$altName];
                        }

                        if ($handle->uploaded) {
                            $handle->image_x = 420;
                            $handle->image_y = 420;

                            $isSize = true;

                            if ($handle->image_src_x < 420) {
                                $isSize = false;
                            }

                            if ($handle->image_src_y < 420) {
                                $isSize = false;
                            }


                            if (!$isSize) {
                                $this->addErr("Ошибка при добавлении дополнительных изображений к товару! <br />Изображение не должно быть меньше " . $handle->image_x . "px X " . $handle->image_y . "px (Файл:$file[name] " . $handle->image_src_x . "px X " . $handle->image_src_y . "px)");
                            }


                            //	$handle->file_new_name_body   = 'image_resized';
                            $handle->image_resize = true;

                            $handle->image_ratio_fill = 'LT';
                            $handle->image_background_color = '#FFFFFF';
                            $handle->file_auto_rename = true;
                            $handle->file_safe_name = false;
                            //$handle->image_text = $text ;
                            $handle->process($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/big/');
                            if ($handle->processed) {
                                @copy($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img/catalog/real/' . $handle->file_dst_name);
                            } else {
                                $this->addErr($handle->error);
                            }

                            $handle = new upload($file);
                            if ($handle->uploaded) {
                                //$handle->file_new_name_body   = 'image_resized';
                                $handle->image_resize = true;
                                $handle->image_x = 200;
                                $handle->image_y = 180;
                                $handle->file_auto_rename = true;
                                $handle->image_ratio_fill = 'LT';
                                $handle->image_background_color = '#FFFFFF';
                                $handle->file_safe_name = false;
                                //$handle->image_text = $text ;
                                //$handle->image_ratio_y        = true;
                                $handle->process($_SERVER['DOCUMENT_ROOT'] . "/img/catalog/small/");
                                if ($handle->processed) {
                                    
                                } else {
                                    $this->addErr($handle->error);
                                }
                            }
                        }
                    } else {
                        $this->addErr("Не могу найти имя файла [$_name] в базе данных");
                    }
                }
            }
        }

        if ($this->_err) {
            $this->viewErr();
        }

        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.catalog_upload_pic');
        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    private function uploadGalleryPics($argOptions = array())
    {

        $options = array(
            'width' => (isset($argOptions['width']) ? $argOptions['width'] : 146),
            'height' => (isset($argOptions['height']) ? $argOptions['height'] : 106),
            'page_id' => (isset($argOptions['page_id']) ? $argOptions['page_id'] : null)
        );

        if (count($_FILES) > 0) {
            $index = 1;
            foreach ($_FILES as $key => $file) {
                if ($file['size'] > 0) {
                    $galleryType = 'matereals';
                    $tmpName = substr($key, 0, strlen($key) - 1);



                    if ($tmpName == 'gallery_works_pic') {
                        $galleryType = 'works';
                    }

                    $bigPicPath = PATH . 'img/gallery/' . $galleryType . '/big';
                    $smallPicPath = PATH . 'img/gallery/' . $galleryType . '/small';

                    $file['name'] = $this->ru2Lat($file['name']);
                    $file['name'] = str_replace('-', '_', $file['name']);

                    $file['name'] = $options['page_id'] . '_' . $file['name'];

                    $handle = new upload($file);
                    $handle->image_resize = true;
                    $handle->image_x = $options['width'];
                    $handle->image_y = $options['height'];
                    $handle->file_auto_rename = false;
                    $handle->image_ratio_fill = true;
                    $handle->file_overwrite = false;   // Перезаписывает если файл уже существует
                    $handle->image_background_color = '#FFFFFF';
                    //$handle->image_watermark = PATH.'img/watermark_146x106.png';
                    // Загрузка описания

                    $handle->process($smallPicPath);

                    if ($handle->processed) {
                        @chmod($smallPicPath . '/' . $file['name'], 0666);
                        //@copy($file['tmp_name'], $bigPicPath.'/'.$file['name']);
                        $handle1 = new upload($file);
                        $handle1->image_resize = true;
                        $handle1->image_x = 800;
                        $handle1->image_y = 600;
                        $handle1->file_auto_rename = false;
                        $handle1->image_ratio_fill = true;
                        $handle1->file_overwrite = false;   // Перезаписывает если файл уже существует
                        $handle1->image_background_color = '#FFFFFF';
                        //$handle1->image_watermark = PATH.'img/watermark_800x600.png';
                        $handle1->process($bigPicPath);
                        if ($handle1->processed) {
                            @chmod($bigPicPath . '/' . $file['name'], 0666);
                        }


                        if (is_numeric($options['page_id'])) {
                            $galleryTextFieldName = str_replace('pic', 'text', $key);
                            if (isset($_POST[$galleryTextFieldName])) {

                                $this->db->insert('catatog_gallery', array('page_id' => $options['page_id'], 'pic' => $file['name'], 'title' => $_POST[$galleryTextFieldName], 'gallery_type' => $galleryType));
                            }
                        }
                    }
                }
                $index++;
            }
        }
    }

    public function addbannerimg()
    {

        $this->setMetaTags("Добавление графического банера");
        $this->setWay("Добавление графического банера");

        $this->tpl->define_dynamic('start', 'edit');

        $this->tpl->define_dynamic('banners_pic', 'edit');

        $this->tpl->define_dynamic('end', 'edit');

        $name = $this->getVar('name', '');
        $href = $this->getVar('href', '');
        $alt = $this->getVar('alt', '');
        $title = $this->getVar('title', '');
        $layout = $this->getVar('layout', 'left');
        $position = $this->getVar('position', '9999');
        $showAs = $this->getVar('show_as', 'all');
        $isFlashFile = (isset($_FILES['pic']['type']) && $_FILES['pic']['type'] == 'application/x-shockwave-flash' );

        $pic = '';

        $isInserted = false;

        if (!empty($_POST)) {
            if (empty($name)) {
                $this->addErr("Поле Имя не должно быть пустым");
            }

            if (empty($href) && !$isFlashFile) {
                $this->addErr("Поле Ссылка не должно быть пустым");
            }


            if (!empty($_POST)) {
                $row = $this->db->fetchAll("SELECT * FROM `banners` WHERE `layout` = '$layout'");
                $isInserted = false;
                if (!$row) {
                    $isInserted = true;
                } else {
                    switch ($layout) {
                        case('top'): {
                                if (count($row) >= 2) {
                                    $this->addErr("Верхних банеров не может быть больше двух");
                                } else {
                                    $isInserted = true;
                                }
                                break;
                            } case('bottom'): {

                                if (count($row) >= 8) {
                                    $this->addErr("Нижних банеров не может быть больше 8");
                                } else {
                                    $isInserted = true;
                                }
                                break;
                            } default: {
                                $isInserted = true;
                            }
                    }
                }
            }

            if (empty($_FILES) || (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] <= 0 )) {
                $this->addErr("Укажите файл");
            } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $_FILES['pic']['name'])) {
                $this->addErr("Файл с именем [ " . $_FILES['pic']['name'] . " ] уже существует (/img/bnrs/$layout/" . $_FILES['pic']['name'] . " )");
            } elseif (!$this->_err) {
                $pic = $_FILES['pic']['name'];
                if (!@copy($_FILES['pic']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $_FILES['pic']['name'])) {
                    $this->addErr("Ошибка копирования файла. ");
                }
            }
        }



        if ($this->_err) {
            $this->viewErr();
        } else {
            $data = array(
                'name' => $name,
                'href' => $href,
                'alt' => $alt,
                'title' => $title,
                'pic' => $pic,
                'position' => $position,
                'layout' => $layout,
                'show_as' => $showAs,
                'type' => (!$isFlashFile ? 'img' : 'swf'),
                'language' => $this->lang
            );

            if ($isInserted) {
                $this->db->insert('banners', $data);
                $content = "Банер добавлен<meta http-equiv='refresh' content='2;URL=/admin/banners'>";
                $this->viewMessage($content);
            }
        }

        if (empty($_POST) || $this->_err) {

            if ($this->_err) {
                $this->viewErr();
            }

            $this->tpl->assign(array(
                'ADM_BANNER_NAME' => $name,
                'ADM_BANNER_HREF' => $href,
                'ADM_BANNER_ALT' => $alt,
                'ADM_BANNER_TITLE' => $title,
                'ADM_BANNER_LAYOUT_TOP' => ($layout == 'top' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_LEFT' => ($layout == 'left' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_BOTTOM' => ($layout == 'bottom' ? 'selected' : ''),
                'ADM_BANNER_POSITION' => $position,
                'ADM_BANNER_SHOW_AS_ALL' => ($showAs == 'all' ? 'selected' : ''),
                'ADM_BANNER_SHOW_AS_INDEX' => ($showAs == 'index' ? 'selected' : ''),
                'ADM_BANNER_SHOW_AS_HIDE' => ($showAs == 'hide' ? 'selected' : '')
            ));
            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.banners_pic');
            $this->tpl->parse('CONTENT', '.end');
        }



        return true;
    }

    public function editbannerimg()
    {

        $this->setMetaTags("Редактирование графического банера");
        $this->setWay("Редактирование графического банера");

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Банера с индексом $id не найдено");
            $this->viewErr();
            return true;
        }

        $bannerItem = $this->db->fetchRow("SELECT * FROM `banners` WHERE `id` = '$id'");

        if (!$bannerItem) {
            $this->addErr("Банера с индексом $id не найдено");
            $this->viewErr();
            return true;
        }

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('banners_pic', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $name = $bannerItem['name'];
        $href = $bannerItem['href'];
        $alt = $bannerItem['alt'];
        $title = $bannerItem['title'];
        $layout = $bannerItem['layout'];
        $position = $bannerItem['position'];
        $showAs = $bannerItem['show_as'];
        $pic = $bannerItem['pic'];

        $isFlashFile = ($bannerItem['type'] == 'swf');

        if (!empty($_POST)) {

            $layoutBase = $layout;

            $name = $this->getVar('name', '');
            $href = $this->getVar('href', '');
            $alt = $this->getVar('alt', '');
            $title = $this->getVar('title', '');
            $layout = $this->getVar('layout', 'left');
            $position = $this->getVar('position', '9999');
            $showAs = $this->getVar('show_as', 'all');

            if (!empty($pic) && $layoutBase != $layout && (empty($_FILES) || (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] <= 0))) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $pic)) {
                    $this->addErr("Файл с именем [ " . $_FILES['pic']['name'] . " ] уже существует (/img/bnrs/$layout/" . $_FILES['pic']['name'] . " )");
                } else {

                    if (!@copy($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layoutBase . '/' . $pic, $_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $pic)) {
                        $this->addErr("Ошибка копирования файла. ");
                    } else {
                        @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layoutBase . '/' . $pic, 0666);
                        @unlink($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layoutBase . '/' . $pic);
                    }
                }
            }


            if (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] > 0) {

                $isFlashFile = (isset($_FILES['pic']['type']) && $_FILES['pic']['type'] == 'application/x-shockwave-flash' );
            }


            if (empty($name)) {
                $this->addErr("Поле Имя не должно быть пустым");
            }

            if (empty($href) && !$isFlashFile) {
                $this->addErr("Поле Ссылка не должно быть пустым");
            }
            if (isset($_FILES['pic']['size'])) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $_FILES['pic']['name'])) {
                    $this->addErr("Файл с именем [ " . $_FILES['pic']['name'] . " ] уже существует (/img/bnrs/$layout/" . $_FILES['pic']['name'] . " )");
                } elseif (!$this->_err && (isset($_FILES['pic']['size']) && $_FILES['pic']['size'] > 0)) {
                    if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $pic)) {
                        @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $pic, 0666);
                        @unlink($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $pic);
                    }

                    if (!@copy($_FILES['pic']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $_FILES['pic']['name'])) {
                        $this->addErr("Ошибка копирования файла. ");
                    } else {
                        @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $layout . '/' . $_FILES['pic']['name']);
                        $pic = $_FILES['pic']['name'];
                    }
                }
            }
        }



        if ($this->_err) {
            $this->viewErr();
        } else {
            $data = array(
                'name' => $name,
                'href' => $href,
                'alt' => $alt,
                'title' => $title,
                'pic' => $pic,
                'position' => $position,
                'layout' => $layout,
                'show_as' => $showAs,
                'type' => (!$isFlashFile ? 'img' : 'swf'),
                'language' => $this->lang
            );

            if (empty($data['pic'])) {
                unset($data['pic']);
            }


            if (!empty($_POST)) {
                $this->db->update('banners', $data, "id=$id");
                $content = "Банер обновлен<meta http-equiv='refresh' content='2;URL=/admin/banners'>";
                $this->viewMessage($content);
            }
        }

        if (empty($_POST) || $this->_err) {

            $this->tpl->assign(array(
                'ADM_BANNER_NAME' => $name,
                'ADM_BANNER_HREF' => $href,
                'ADM_BANNER_ALT' => $alt,
                'ADM_BANNER_TITLE' => $title,
                'ADM_BANNER_LAYOUT_TOP' => ($layout == 'top' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_LEFT' => ($layout == 'left' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_BOTTOM' => ($layout == 'bottom' ? 'selected' : ''),
                'ADM_BANNER_POSITION' => $position,
                'ADM_BANNER_SHOW_AS_ALL' => ($showAs == 'all' ? 'selected' : ''),
                'ADM_BANNER_SHOW_AS_INDEX' => ($showAs == 'index' ? 'selected' : ''),
                'ADM_BANNER_SHOW_AS_HIDE' => ($showAs == 'hide' ? 'selected' : '')
            ));
            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.banners_pic');
            $this->tpl->parse('CONTENT', '.end');
        }



        return true;
    }

    public function deletebanner()
    {

        $this->setMetaTags("Удаление графического банера");
        $this->setWay("Удаление графического банера");

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Банера с индексом $id не найдено");
            $this->viewErr();
            return true;
        }

        $bannerItem = $this->db->fetchRow("SELECT * FROM `banners` WHERE `id` = '$id'");

        if (!$bannerItem) {
            $this->addErr("Банера с индексом $id не найдено");
            $this->viewErr();
            return true;
        }

        if (!empty($bannerItem['pic']) && is_file($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $bannerItem['layout'] . '/' . $bannerItem['pic'])) {
            @chmod($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $bannerItem['layout'] . '/' . $bannerItem['pic'], 0666);
            @unlink($_SERVER['DOCUMENT_ROOT'] . '/img/bnrs/' . $bannerItem['layout'] . '/' . $bannerItem['pic']);
        }

        $this->db->delete('banners', "id=$id");
        $content = "Банер удален <meta http-equiv='refresh' content='2;URL=/admin/banners'>";
        $this->viewMessage($content);

        return true;
    }

    public function addbannerhtml()
    {
        $this->setMetaTags("Добавление банера в виде HTML кода");
        $this->setWay("Добавление банера в виде HTML кода");


        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('banners_html', 'edit');
        $this->tpl->define_dynamic('end', 'edit');

        $name = $this->getVar('name', '');
        $layout = $this->getVar('layout', 'left');
        $position = $this->getVar('position', '9999');
        $showAs = $this->getVar('show_as', 'all');
        $body = $this->getVar('body', '');

        if (!empty($_POST)) {
            if (empty($name)) {
                $this->addErr("Поле Имя не должно быть пустым");
            }

            if (!$this->_err) {
                $data = array(
                    'name' => $name,
                    'position' => $position,
                    'layout' => $layout,
                    'show_as' => $showAs,
                    'html_code' => $body,
                    'type' => 'html',
                    'language' => $this->lang
                );
                $this->db->insert('banners', $data);
                $content = "Банер добавлен<meta http-equiv='refresh' content='2;URL=/admin/banners'>";
                $this->viewMessage($content);
            }
        }

        if ($this->_err) {
            $this->viewErr();
        }

        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(array(
                'ADM_BANNER_NAME' => $name,
                'ADM_BANNER_LAYOUT_TOP' => ($layout == 'top' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_LEFT' => ($layout == 'left' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_BOTTOM' => ($layout == 'bottom' ? 'selected' : ''),
                'ADM_BANNER_POSITION' => $position,
                'ADM_BANNER_SHOW_AS_ALL' => ($showAs == 'all' ? 'selected' : ''),
                'ADM_BANNER_SHOW_AS_INDEX' => ($showAs == 'index' ? 'selected' : ''),
                'ADM_BANNER_SHOW_AS_HIDE' => ($showAs == 'hide' ? 'selected' : ''),
                'ADM_BODY' => $body
            ));

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.mce');
            $this->tpl->parse('CONTENT', '.banners_html');
            $this->tpl->parse('CONTENT', '.end');
        }




        return true;
    }

    public function editbannerhtml()
    {

        $this->setMetaTags("Добавление банера в виде HTML кода");
        $this->setWay("Добавление банера в виде HTML кода");

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Банера с индексом $id не найдено");
            $this->viewErr();
            return true;
        }

        $bannerItem = $this->db->fetchRow("SELECT * FROM `banners` WHERE `id` = '$id'");

        if (!$bannerItem) {
            $this->addErr("Банера с индексом $id не найдено");
            $this->viewErr();
            return true;
        }

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('mce', 'edit');
        $this->tpl->define_dynamic('banners_html', 'edit');
        $this->tpl->define_dynamic('end', 'edit');


        $name = $bannerItem['name'];
        $layout = $bannerItem['layout'];
        $position = $bannerItem['position'];
        $showAs = $bannerItem['show_as'];
        $body = $bannerItem['html_code'];

        if (!empty($_POST)) {
            $name = $this->getVar('name', '');
            $layout = $this->getVar('layout', '');
            $position = $this->getVar('position', '9999');
            $showAs = $this->getVar('show_as', 'all');
            $body = $this->getVar('body', '');

            if (empty($name)) {
                $this->addErr("Поле Имя не должно быть пустым");
            }

            if (!$this->_err) {
                $data = array(
                    'name' => $name,
                    'position' => $position,
                    'layout' => $layout,
                    'show_as' => $showAs,
                    'type' => 'html',
                    'html_code' => $body,
                    'language' => $this->lang
                );
                $this->db->update('banners', $data, "id=$id");
                $content = "Банер добавлен<meta http-equiv='refresh' content='2;URL=/admin/banners'>";
                $this->viewMessage($content);
            }
        }

        if ($this->_err) {
            $this->viewErr();
        }

        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(array(
                'ADM_BANNER_NAME' => $name,
                'ADM_BANNER_LAYOUT_TOP' => ($layout == 'top' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_LEFT' => ($layout == 'left' ? 'selected' : ''),
                'ADM_BANNER_LAYOUT_BOTTOM' => ($layout == 'bottom' ? 'selected' : ''),
                'ADM_BANNER_POSITION' => $position,
                'ADM_BANNER_SHOW_AS_ALL' => ($showAs == 'all' ? 'selected' : ''),
                'ADM_BANNER_SHOW_AS_INDEX' => ($showAs == 'index' ? 'selected' : ''),
                'ADM_BODY' => stripcslashes($body),
                'ADM_BANNER_SHOW_AS_HIDE' => ($showAs == 'hide' ? 'selected' : '')
            ));

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.mce');
            $this->tpl->parse('CONTENT', '.banners_html');
            $this->tpl->parse('CONTENT', '.end');
        }




        return true;
    }

    public function banners()
    {

        $this->setMetaTags("Банеры");
        $this->setWay("Банеры");
        $this->tpl->define_dynamic('_banners', 'adm/banners.tpl');
        $this->tpl->define_dynamic('banners', '_banners');
        $this->tpl->define_dynamic('banners_list', 'banners');


        $bannersList = $this->db->fetchAll('SELECT * FROM `banners` WHERE `language`="' . $this->lang . '"  ORDER BY `layout`, `position`, `name`');

        if ($bannersList) {
            $this->tpl->parse('BANNERS_EMPTY', 'null');

            foreach ($bannersList as $banner) {



                $layout = 'Слева под меню';

                if ($banner['layout'] == 'top') {
                    $layout = 'Вверху сайта';
                }

                if ($banner['layout'] == 'bottom') {
                    $layout = 'Внизу сайта (Наши партнеры)';
                }

                if ($banner['layout'] == 'hide') {
                    $status = 'Скрытый';
                }

                $status = 'Выводится на всех страницах';

                if ($banner['layout'] == 'index') {
                    $status = 'Выводится только на главной';
                }

                $type = 'Картинка';


                if ($banner['type'] == 'html') {
                    $type = "HTML код";
                }

                if ($banner['type'] == 'swf') {
                    $type = "Флеш ролик";
                }

                $name = $banner['name'];

                if ($banner['type'] == 'img' && $type != 'Флеш ролик') {
                    $name = "<a href = '/img/bnrs/$banner[layout]/$banner[pic]' rel='lightbox[photo-item]' style='font-size: 14px;'>$name</a>";
                }

                $this->tpl->assign(array(
                    'BANNER_ID' => $banner['id'],
                    'BANNER_BIG_IMG_SRC' => $banner['pic'],
                    'BANNER_NAME' => $name,
                    'BANNER_LAYOUT' => $layout,
                    'BANNER_TYPE' => $type,
                    'BANNER_SHOW_AS' => $status,
                    'ADM_TYPE' => ($banner['type'] != 'html' ? 'img' : 'html')
                ));

                $this->tpl->parse('BANNERS_LIST', '.banners_list');
            }
        } else {
            $this->tpl->parse('BANNERS_LIST', 'null');
        }

        $this->tpl->parse('CONTENT', 'banners');
        return true;
    }

    // Комментарии

    public function comments()
    {
        $this->setMetaTags("Отзывы");
        $this->setWay("Отзывы");
        $this->tpl->define_dynamic('_comments_list', 'adm/comments.tpl');
        $this->tpl->define_dynamic('comments_list', '_comments_list');
        $this->tpl->define_dynamic('comments_list_items', 'comments_list');
        // $this->tpl->parse('COMMENTS_LIST_ITEMS', 'null');
        $page = 1;
        if (isset($this->getParam['page'])) {
            $page = (int) $this->getParam['page'];

            if ($page < 1) {
                return $this->error404();
            }
        }
        $selectItem = $this->db->select();
        $selectItem->from(array('c' => 'comments'), array('id1' => 'id',
                    'visible1' => 'visible',
                    'goods_artikul',
                    'fio',
                    'period_of_operation',
                    'dignity',
                    'shortcomings',
                    'recommendations',
                    'conclusion',
                    'points',
                    'tip_helpful_yes',
                    'tip_helpful_no',
                    'date1' => 'date')
                )
                ->join(array('c1' => 'catalog'), 'c.goods_artikul=c1.artikul');


        $selectItem->order(array('id DESC'));
        //  die($selectItem->__toString());
        $selectCount = $this->db->select();
        $selectCount->from('comments', array(Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN => 'COUNT(id)'));
        // $selectCount->where('level = "?"', $id);


        $adapter = new Zend_Paginator_Adapter_DbSelect($selectItem);
        $adapter->setRowCount($selectCount);

        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(10);

        $navbar = $this->_loadPaginator($paginator, '/admin/comments');
        $navBott = '';

        if ($navbar) {
            $navBott = '<div class="pager_right">' . $navbar . '</div>';
        }

        $this->tpl->assign('PAGINATOR', $navBott);

        $pic = '/img/no-foto/no-foto-200x180.gif';


        if (count($paginator)) {
            foreach ($paginator as $res) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/img/catalog/small_1/' . $res['pic'])) {
                    $pic = '/img/catalog/small_1/' . $res['pic'];
                }

                if (($parenSectionInfo = $this->dataTreeManager($res['id']))) {
                    $itemUrl = $parenSectionInfo['links'];
                }
                
               // var_dump($itemUrl);

                $this->tpl->assign(array(
                    'COMMENT_ITEM_ID' => $res['id1'],
                    'COMMENT_ITEM_CATALOG_PIC' => $pic,
                    'COMMENT_ITEM_CATALOG_NAME' => (!empty($res['name']) ? $res['name'] : 'Ответ админа'),
                    'COMMENT_ITEM_FIO' => $res['fio'],
                    'COMMENT_ITEM_HREF' => '/catalog/'.$itemUrl,
                    'COMMENT_ITEM_DATE' => $res['date1'],
                    'COMMENT_ITEM_ARTIKUL' => $res['goods_artikul'],
                    'COMMENT_ITEM_STATUS' => ($res['visible1'] == '0' ? '<span id="span-comment-ststus-' . $res['id1'] . '">Скрытый:</span> <a href="#" onclick="commentaryActive(' . $res['id1'] . ',  this); return false;">Активировать</a>' : '<span id="span-comment-ststus-' . $res['id1'] . '">Актывный:</span> <a href="#" onclick="commentaryActive(' . $res['id1'] . ',  this); return false;">Скрыть</a>'),
                    'COMMENT_ITEM_PERIOD_OF_OPERATION' => $res['period_of_operation'],
                    'COMMENT_ITEM_DIGNITY' => $res['dignity'],
                    'COMMENT_ITEM_SHORTCOMINGS' => $res['shortcomings'],
                    'COMMENT_ITEM_RECOMMENDATIONS' => $res['recommendations'],
                    'COMMENT_ITEM_CONCLUSION' => $res['conclusion']
                ));
                $this->tpl->parse('COMMENTS_LIST_ITEMS', '.comments_list_items');
            }
        }

        $this->tpl->parse('CONTENT', '.comments_list');
        return true;
    }

    public function editcomments()
    {
        $this->setMetaTags("Редактировать отзыв");
        $this->setWay("Редактировать отзыв");

        $this->tpl->define_dynamic('_comments', 'adm/comments.tpl');
        $this->tpl->define_dynamic('comments', '_comments');


        $id = end($this->url);

        if (empty($id) || !is_numeric($id) || !($row = $this->db->fetchRow("SELECT * FROM `comments` WHERE `id` = '$id'"))) {
            $this->addErr("Не могу найти комментарий");
            $this->viewErr();
            return true;
        }

        if (!$this->_err) {
            $fio = $row['fio'];
            $period_of_operation = $row['period_of_operation'];
            $dignity = $row['dignity'];
            $shortcomings = $row['shortcomings'];
            $recommendations = $row['recommendations'];
            $conclusion = $row['conclusion'];
            $visible = $row['visible'];
            $goodsId = $this->db->fetchOne("SELECT `id` FROM `catalog` WHERE `artikul` = '$row[goods_artikul]'");
        }

        if (!empty($_POST)) {
            $fio = mysql_escape_string($this->getVar('fio', ''));
            $period_of_operation = mysql_escape_string($this->getVar('period_of_operation', ''));
            $dignity = mysql_escape_string($this->getVar('dignity', ''));
            $shortcomings = mysql_escape_string($this->getVar('shortcomings', ''));
            $recommendations = mysql_escape_string($this->getVar('recommendations', ''));
            $conclusion = mysql_escape_string($this->getVar('conclusion', ''));
            $visible = mysql_escape_string($this->getVar('visible', ''));
        }

        $referrer = "";
        /* if ($goodsId && ($dataTree = $this->dataTreeManager($goodsId))) {

          $referrer = '/catalog/' . $dataTree['links'];
          } */

        if (!empty($_POST) && !$this->_err) {
            $data = array(
                'date' => date('Y-m-d'),
                'visible' => $visible,
                'fio' => $fio,
                'period_of_operation' => $period_of_operation,
                'dignity' => $dignity,
                'shortcomings' => $shortcomings,
                'recommendations' => $recommendations,
                'conclusion' => $conclusion,
            );
            $this->db->update('comments', $data, "id='$id'");

            $content = "<h2>Отзыв обновлен</h2> <meta http-equiv='refresh' content='2;URL=/admin/comments' />";

            $this->viewMessage($content);
            return true;
        }


        if (!empty($_POST) && !$this->_err) {

            $fio = '';
            $dignity = '';
            $period_of_operation = '';
            $shortcomings = '';
            $recommendations = '';
            $conclusion = '';
        }

        $this->tpl->assign(array(
            'CAPTCHA_ID' => $id,
            'COMMENT_FIO' => $fio,
            'COMMENT_FIO_VISIBLE' => ($visible == '1' ? 'selected' : ''),
            'COMMENT_FIO_HIDDEN' => ($visible == '0' ? 'selected' : ''),
            'COMMENT_PERIOD_OF_OPERATION' => $period_of_operation,
            'COMMENT_DIGNITY' => $dignity,
            'COMMENT_SHORTCOMMINGS' => $shortcomings,
            'COMMENT_RECOMENDATIONS' => $recommendations,
            'COMMENT_CONCLUSION' => $conclusion
        ));

        $this->tpl->parse('CONTENT', '.comments');

        return true;
    }

    public function deletecomments()
    {
        $this->setMetaTags("Удалить отзыв");
        $this->setWay("Удалить отзыв");

        $id = end($this->url);

        if (empty($id) || !is_numeric($id) || !($row = $this->db->fetchRow("SELECT * FROM `comments` WHERE `id` = '$id'"))) {
            $this->addErr("Не могу найти комментарий");
            $this->viewErr();
            return true;
        }

        if (!$this->_err) {
            $goodsId = $this->db->fetchOne("SELECT `id` FROM `catalog` WHERE `artikul` = '$row[goods_artikul]'");
        }


        $referrer = "";
        /* if ($goodsId && ($dataTree = $this->dataTreeManager($goodsId))) {

          $referrer = '/catalog/' . $dataTree['links'];
          } */

        if (!$this->_err) {
            $this->db->delete('comments', "id='$id'");

            $content = "<h2>Отзыв удален</h2> <meta http-equiv='refresh' content='2;URL=/admin/comments' />";

            $this->viewMessage($content);
            return true;
        }

        $this->tpl->parse('CONTENT', '.comments');

        return true;
    }

    protected function uploadCatPic($from, $to, $maxwidth, $maxheight, $quality = 80, $stampPath = null)
    {
        ini_set('max_execution_time', '120');

        // защита от Null-байт уязвимости PHP
        $from = preg_replace('/\0/uis', '', $from);
        $to = preg_replace('/\0/uis', '', $to);

        $stamp = null;

        if ($stampPath != null && is_file($stampPath)) {
            $stamp = imagecreatefrompng($stampPath);
        }


        // информация об изображении
        $imageinfo = @getimagesize($from);
        // если получить информацию не удалось - ошибка
        if (!$imageinfo) {
            $this->_err .= '<br />Ошибка получения информации об изображении';
            return false;
        }
        // получаем параметры изображения
        $width = $imageinfo[0];  // ширина
        $height = $imageinfo[1]; // высота
        $format = $imageinfo[2]; // ID формата (число)
        $mime = $imageinfo['mime']; // mime-тип
        // определяем формат и создаём изображения
        switch ($format) {
            case 2: $img = imagecreatefromjpeg($from);
                break; // jpg
            case 3: $img = imagecreatefrompng($from);
                break; // png
            case 1: $img = imagecreatefromgif($from);
                break; // gif
            default: $this->_err .= '<br />Неверный или недопустимый формат загружаемого файла!';
                return false;
                break;
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
                    $newheight = intval(((float) $newwidth / (float) $width) * $height);
                }
                // высота больше
                else {
                    $newheight = $maxheight;
                    $newwidth = intval(((float) $newheight / (float) $height) * $width);
                }
            }
            // размеры картинки больше только по X
            elseif ($width > $maxwidth) {
                $newwidth = $maxwidth;
                $newheight = intval(((float) $newwidth / (float) $width) * $height);
            }
            // размеры картинки больше только по Y
            elseif ($height > $maxheight) {
                $newheight = $maxheight;
                $newwidth = intval(((float) $newheight / (float) $height) * $width);
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
                    $newheight = intval(((float) $newwidth / (float) $width) * $height);

                    if ($newheight > $maxheight) {
                        $newheight = $maxheight;
                        $newwidth = intval(((float) $newheight / (float) $height) * $width);
                    }
                }
                // высота больше или равна ширине
                else {
                    $newheight = $maxheight;
                    $newwidth = intval(((float) $newheight / (float) $height) * $width);
                }
            }
            // размеры картинки больше только по X
            elseif ($width > $maxwidth) {
                $newwidth = $maxwidth;
                $newheight = intval(((float) $newwidth / (float) $width) * $height);
            }
            // размеры картинки больше только по Y
            elseif ($height > $maxheight) {
                $newheight = $maxheight;
                $newwidth = intval(((float) $newheight / (float) $height) * $width);
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
                    $newheight = intval(((float) $newwidth / (float) $width) * $height);
                }
                // высота больше
                else {
                    $newheight = $maxheight;
                    $newwidth = intval(((float) $newheight / (float) $height) * $width);
                }
            }
            // размеры картинки больше только по X
            elseif ($width > $maxwidth) {
                $newwidth = $maxwidth;
                $newheight = intval(((float) $newwidth / (float) $width) * $height);
            }
            // размеры картинки больше только по Y
            elseif ($height > $maxheight) {
                $newheight = $maxheight;
                $newwidth = intval(((float) $newheight / (float) $height) * $width);
            }
            // в остальных случаях ничего менять не надо
            else {
                $newwidth = $width;
                $newheight = $height;
            }
        }

        // если изменений над картинкой производить не надо - просто копируем её
        /* if ($newwidth == $width && $newheight == $height && $quality == 80) {
          echo '123';
          if (copy($from, $to)) return true;
          else {
          $this->_err .= '<br />Ошибка копирования файла!';
          return false;
          }
          } */

        // создаём новое изображение
        //$new = imagecreatetruecolor($newwidth, $newheight);
        $new = imagecreatetruecolor($maxwidth, $maxheight);
        $black = imagecolorallocate($new, 0, 0, 0);
        $white = imagecolorallocate($new, 255, 255, 255);
        // копируем старое в новое с учётом новых размеров
        imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
        //imagecolortransparent($new, $white);
        $center_w = round(($maxwidth - $newwidth) / 2);
        $center_w = ($center_w < 0) ? 0 : $center_w;
        $center_h = round(($maxheight - $newheight) / 2);
        $center_h = ($center_h < 0) ? 0 : $center_h;
        imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
        if ($stamp != null) {
            imagecopyresampled($new, $stamp, 0, 0, 0, 0, $maxwidth - 1, $maxheight - 1, $maxwidth - 1, $maxheight - 1);
            //imagecopymerge($new,$stamp, 0, 0, 0, 0, $width, $height, 20);
        }
        // создаём файл с новым изображением
        switch ($format) {
            case 2: // jpg
                if ($quality < 0)
                    $quality = 0;
                if ($quality > 100)
                    $quality = 100;
                imagejpeg($new, $to, $quality);
                break;
            case 3: // png
                $quality = intval($quality * 9 / 100);
                if ($quality < 0)
                    $quality = 0;
                if ($quality > 9)
                    $quality = 9;
                imagepng($new, $to, $quality);
                break;
            case 1: // gif
                imagegif($new, $to);
                break;
        }

        @chmod($to, 0644);

        return true;
    }

    public function export()
    {
        set_time_limit(1200);
        $this->setMetaTags("Экспорт каталога");
        $this->setWay("Экспорт каталога");

        if (!empty($_POST)) {
            require_once PATH . 'library/CatalogManager.php';
            $catalogManager = new CatalogManager($this->db, $this->tpl);
            $catalogManager->runExport();
        } else {
            $this->tpl->define_dynamic('start', 'edit');
            $this->tpl->define_dynamic('export', 'edit');
            $this->tpl->define_dynamic('end', 'edit');

            $this->tpl->assign('ADM_EXPORT_FILE_NAME', 'Каталог ' . date('d-m-Y H.i'));

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.export');
            $this->tpl->parse('CONTENT', '.end');
        }
        set_time_limit(30);


        return true;
    }

    public function ajaximport()
    {


        if (!empty($_FILES) && isset($_FILES['file']['size']) && $_FILES['file']['size'] > 0) {
            $exec = substr($_FILES['file']['name'], strrpos('.', $_FILES['file']['name']), strlen($_FILES['file']['name']));
            @copy($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/importTmp/' . uniqid() . '.' . $exec);
        }

        if (!empty($_POST)) {

            if (isset($_POST['ajaxImportAction'])) {

                switch ($_POST['ajaxImportAction']) {
                    case ('step1'): {



                            break;
                        }
                }

                die('hello');
            } else {
                die('runImport');
            }
        }

        $this->setMetaTags('Импорт товаров');
        $this->setWay('Импорт товаров');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('import', 'edit');
        $this->tpl->define_dynamic('end_ajax', 'edit');



        if (!empty($_FILES)) {



            require_once PATH . 'library/CatalogManager.php';
            $catalogManager = new CatalogManager($this->db, $this->tpl);



            if ($catalogManager->initImport()) {
                //$this->viewMessage("Проверка структуры ...");

                if ($catalogManager->readLists()) {
                    if ($catalogManager->testListsValues()) {
                        
                    } else {
                        $this->_err = $catalogManager->getErrs();
                    }
                } else {
                    $this->_err = $catalogManager->getErrs();
                }
            } else {

                $this->_err = $catalogManager->getErrs();
            }

            if (!$this->_err) {
                $catalogManager->runImport();
            } else {
                $this->viewErr();
            }
        }

        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.import');
        $this->tpl->parse('CONTENT', '.end_ajax');

        return true;
    }

    public function import()
    {

        set_time_limit(1200);
        $this->setMetaTags('Импорт товаров');
        $this->setWay('Импорт товаров');

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('import', 'edit');
        $this->tpl->define_dynamic('import_section_list_title', 'import');
        $this->tpl->define_dynamic('import_section_list', 'import');
        $this->tpl->define_dynamic('end', 'edit');

        $this->tpl->parse('IMPORT_SECTION_LIST', 'null');
        $this->tpl->parse('IMPORT_SECTION_LIST_TITLE', 'null');

        $existsFieldsValue = '';

        if (isset($_POST['vals_all']) && is_array($_POST['vals_all']) && count($_POST['vals_all']) > 0) {

            $recordCounter = 0;
            $recordCounter2 = 0;


            foreach ($_POST['vals_all'] as $updateArtikul => $sheetVals) {
                if (is_array($sheetVals)) {

                    if (isset($_POST['replace_field'][$updateArtikul]) && is_array($_POST['replace_field'][$updateArtikul]) && count($_POST['replace_field'][$updateArtikul]) > 0) {

                        $xData = array('changed' => '0');
                        foreach ($_POST['replace_field'][$updateArtikul] as $xField) {

                            if (isset($_POST['vals_all'][$updateArtikul][$xField])) {
                                $xData[$xField] = $_POST['vals_all'][$updateArtikul][$xField];
                            }
                        }
                        $this->db->update('catalog', $xData, "artikul='$updateArtikul'");
                    } elseif (isset($_POST['replace_all'][$recordCounter2]) && $_POST['replace_all'][$recordCounter2] == $recordCounter) {
                        // Записываем все поля

                        if (isset($_POST['vals_all'][$updateArtikul]) && is_array($_POST['vals_all'][$updateArtikul]) && count($_POST['vals_all'][$updateArtikul]) > 0) {
                            $sql = "";
                            foreach ($_POST['vals_all'][$updateArtikul] as $key => $value) {
                                if (!empty($value)) {
                                    if ($key == 'body' || $key == 'preview') {
                                        $value = stripslashes($value);
                                    }
                                    $sql .= "`$key`='$value', ";
                                }
                            }

                            if (!empty($sql)) {
                                $sql = "UPDATE `catalog` SET $sql `changed` = 0 WHERE artikul='$updateArtikul'";
                                $this->db->query($sql);
                            }

                            $recordCounter2++;

                            // $this->db->update('catalog', $_POST['vals_all'][$updateArtikul], "artikul='$updateArtikul'");
                        }
                    } else {
                        
                    }

                    $recordCounter++;
                }
            }
        }

        // die;

        if (!empty($_FILES)) {
            require_once PATH . 'library/CatalogManager.php';
            $catalogManager = new CatalogManager($this->db, $this->tpl);
            if ($catalogManager->initImport()) {
                $this->viewMessage("Проверка структуры ...");

                if ($catalogManager->readLists()) {
                    if ($catalogManager->testListsValues()) {
                        
                    } else {
                        $this->_err = $catalogManager->getErrs();
                    }
                } else {
                    $this->_err = $catalogManager->getErrs();
                }
            } else {

                $this->_err = $catalogManager->getErrs();
            }


            if (!$this->_err) {

                $catalogManager->runImport();
                $recordsExists = $catalogManager->getExistsRecords();
                $existsFieldsValueTitle = '';

                $showTableValue = '';

                if (count($recordsExists) > 0) {
                    $defaultFields = $catalogManager->getDefaultFields();
                    $counter = 0;
                    $hiddenFields = '';
                    foreach ($recordsExists as $sheetTitle => $record) {

                        if (isset($record['inAdmin']) && is_array($record['inAdmin']) && count($record['inAdmin']) > 0) {
                            $existsFieldsValueTitle = "<p><b> В файде импорта найдены товары которые редактировались на сайте </b></p><br />\n <table>\n";
                            $existsFieldsValue1 = "<tr><td>Лист: <a href='#' class='show-fields' id='$counter'>$sheetTitle </a> </td><td colspan='2'>Заменить все поля <input type='checkbox' name='replace_all[]' value='$counter' /></td></tr>\n";

                            $counterRecords = 0;
                            foreach ($record['inAdmin'] as $fields) {
                                if (isset($fields['db']) && is_array($fields['db'])) {
                                    $existsFieldsValue2 = "<tr class='fields-exists field-exists-$counter'><td colspan='3'>&nbsp;Запись № " . ($counterRecords + 1) . " </td></tr>\n";
                                    $isEmpty = true;
                                    foreach ($fields['db'] as $field => $value) {
                                        $fieldTmp = $field;
                                        $artikul = $fields['db']['artikul'];
                                        if (isset($fields['file'][$fieldTmp])) {
                                            $value2 = $fields['file'][$fieldTmp];
                                            if (isset($defaultFields[$field])) {
                                                $field = $defaultFields[$field];
                                                if (is_array($field)) {

                                                    if (isset($field['value'])) {
                                                        if (is_array($field['value'])) {
                                                            foreach ($field['value'] as $k1 => $v1) {
                                                                if ($k1 == $value) {
                                                                    $value = $v1;
                                                                    $value2 = $v1;
                                                                }
                                                            }
                                                        } else {
                                                            $value = $field['value'];
                                                        }
                                                    }

                                                    if (isset($field['title'])) {
                                                        $field = $field['title'];
                                                    } else {
                                                        $field = '';
                                                    }
                                                }

                                                if (!empty($field) && $fieldTmp != 'artikul' && $value != $value2 && !($fieldTmp == 'position' && empty($value2) && $value == '9999' )) {
                                                    if (!empty($value2)) {

                                                        $isEmpty = false;

                                                        $existsFieldsValue .= "<tr class='fields-exists field-exists-$counter'><td>&nbsp;&nbsp;&nbsp; <b>$field</b> </td><td>
                                       Сейчас: <i>$value</i> <br />
                                       Заменить на: <b>$value2</b>
                                       </td><td> <input type='checkbox' name='replace_field[$artikul][]' value='$fieldTmp' /><input type='hidden' name='vals_all[$artikul][$fieldTmp]' value='$value2'></td></tr>\n";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (!$isEmpty) {
                                        $showTableValue = $existsFieldsValue2 . $existsFieldsValue;
                                    }
                                }
                                $counterRecords++;
                            }
                            if (!$isEmpty) {
                                $showTableValue = $existsFieldsValue . $existsFieldsValue;
                            }
                        }

                        $counter++;
                    }
                    if (!empty($existsFieldsValueTitle)) {
                        $existsFieldsValueTitle .= $showTableValue . '</table>';
                    }
                }
            } else {
                $this->viewErr();
            }
        }

        if (empty($_POST) && empty($_FILE)) {
            if (($row = $this->db->fetchAll("SELECT `name`, `artikul` FROM `catalog` WHERE `level` = '0'"))) {
                $this->tpl->parse('IMPORT_SECTION_LIST_TITLE', '.import_section_list_title');
                foreach ($row as $res) {
                    $this->tpl->assign(array(
                        'ADM_IMPORT_SECTION_NAME' => $res['name'],
                        'ADM_IMPORT_SECTION_ARTIKUL' => $res['artikul']
                    ));

                    $this->tpl->parse('IMPORT_SECTION_LIST', '.import_section_list');
                }
            }
        }


        $this->tpl->assign('FIELD_EXISTS', $existsFieldsValue);
        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.import');
        $this->tpl->parse('CONTENT', '.end');
        set_time_limit(30);
        return true;
    }

    // Управление полями разделов
    // Редактирование полей раздела. Добавление групп
    public function addfieldsectiongroup()
    {
        $this->setMetaTags('Создать группу');
        $this->setWay('Создать группу');

        $level = end($this->url);

        if (!is_numeric($level)) {
            $levle = 0;
        }

        $name = $this->getVar('name', '');
        $sectionName = $this->getVar('section_name', false);
        $position = $this->getVar('position', 'after-base-group');
        $sql = "SELECT * FROM `catalog_section_fields` WHERE `language`='" . $this->lang . "' ";
        $sectionRow = $this->db->fetchAll($sql);
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('name', 'edit');

        //$this->tpl->define_dynamic('sectionfields_group', 'edit');

        $this->tpl->define_dynamic('sectionfields_section_name', 'edit');
        $this->tpl->define_dynamic('sectionfields_position', 'edit');

        $this->tpl->define_dynamic('end', 'edit');
        $this->tpl->parse('SECTIONFIELDS_GROUP', 'null');
        $admSectionFieldPosition = '<option value="after-base-group">Под основной группой</option>' . "\n";
        $admSectionFieldSectionName = '<option value="catalog">Каталог</option>' . "\n";
        if ($sectionRow) {
            foreach ($sectionRow as $sectionRes) {
                $admSectionFieldPosition .= '<option value="' . $sectionRes['position'] . '">Под группой "' . $sectionRes['title'] . '"</option>' . "\n";
            }
        }

        //  if ($type == 'section') {
        $row = $this->db->fetchAll("SELECT `name`, `href`, `id` FROM `catalog` WHERE `level` = '0'");
        if ($row) {
            foreach ($row as $res) {
                $admSectionFieldSectionName .= '<option value="' . $res['href'] . '" ' . ($sectionName == $res['href'] ? 'selected' : '') . '>' . $res['name'] . '</option>' . "\n";
                $row1 = $this->db->fetchAll("SELECT `name`, `href`, `id` FROM `catalog` WHERE `level` = '$res[id]'");
                if ($row1) {
                    foreach ($row1 as $res1) {
                        $admSectionFieldSectionName .= '<option value="' . $res1['href'] . '" ' . ($sectionName == $res1['href'] ? 'selected' : '') . '> -- ' . $res1['name'] . '</option>' . "\n";
                    }
                }
            }
        }
        //}


        if (!empty($_POST) && !$this->_err) {

            if ($position == 'after-base-group') {
                $position = 0;
            }

            $this->db->query("UPDATE `catalog_section_fields` SET `position` = (`position`+1) WHERE `position` <= $position AND `position` < 9999");

            $this->db->insert('catalog_section_fields', array('name' => $this->ru2Lat(trim($name)), 'title' => $name, 'section_href' => $sectionName, 'type' => 'section', 'position' => $position, 'level' => $level, 'language' => $this->lang));
        }

        $this->tpl->assign(array(
            'ADM_SECTION_FIELD_SECTION_NAME' => $admSectionFieldSectionName,
            'ADM_NAME' => $name,
            'ADM_SECTION_FIELD_POSITION' => $admSectionFieldPosition
        ));


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.name');

        $this->tpl->parse('CONTENT', '.sectionfields_section_name');
        $this->tpl->parse('CONTENT', '.sectionfields_position');

        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    // Редактирование полей раздела. Добавление подгрупп
    public function addfieldsubsectiongroup()
    {
        $this->setMetaTags('Создать подгруппу');
        $this->setWay('Создать подгруппу');

        $level = end($this->url);

        if (!is_numeric($level)) {
            $levle = 0;
        }

        $name = $this->getVar('name', '');
        $sectionName = $this->getVar('section_name', false);
        $position = $this->getVar('position', 'after-base-sub-group');


        $sql = "SELECT * FROM `catalog_section_fields` WHERE `language`='" . $this->lang . "' AND `level` = '$level' ORDER BY `position`";

        $sectionRow = $this->db->fetchAll($sql);
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('name', 'edit');

        //$this->tpl->define_dynamic('sectionfields_group', 'edit');

        $this->tpl->define_dynamic('sectionfields_section_name', 'edit');
        $this->tpl->define_dynamic('sectionfields_position', 'edit');

        $this->tpl->define_dynamic('end', 'edit');
        $this->tpl->parse('SECTIONFIELDS_GROUP', 'null');
        $admSectionFieldPosition = '<option value="after-base-group">Под основной группой</option>' . "\n";
        $admSectionFieldSectionName = '<option value="catalog">Каталог</option>' . "\n";
        if ($sectionRow) {
            foreach ($sectionRow as $sectionRes) {
                $admSectionFieldPosition .= '<option value="' . $sectionRes['position'] . '">Под группой "' . $sectionRes['title'] . '"</option>' . "\n";
            }
        }


        if (!empty($_POST) && !$this->_err) {

            if ($position == 'after-base-sub-group') {
                $position = 0;
            }

            $this->db->query("UPDATE `catalog_section_fields` SET `position` = (`position`+1) WHERE `position` <= $position AND `position` < 9999");

            $this->db->insert('catalog_section_fields', array('name' => $this->ru2Lat(trim($name)), 'title' => $name, 'section_href' => '', 'type' => 'sub_section', 'position' => $position, 'level' => $level, 'language' => $this->lang));
        }

        $this->tpl->assign(array(
            'ADM_NAME' => $name,
            'ADM_SECTION_FIELD_POSITION' => $admSectionFieldPosition
        ));


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.name');

        if ($sectionRow)
            $this->tpl->parse('CONTENT', '.sectionfields_position');

        $this->tpl->parse('CONTENT', '.end');

        return true;
    }

    public function addsectionfield()
    {

        $meta = "Создать поле";

        $this->setMetaTags($meta);
        $this->setWay($meta);

        $level = end($this->url);

        if (!is_numeric($level)) {
            $levle = 0;
        }

        $name = $this->getVar('name', '');
        $sectionName = $this->getVar('section_name', false);
        $position = $this->getVar('position', 'after-base-sub-group');
        $type = $this->getVar('type', 'varchar');

        $sql = "SELECT * FROM `catalog_section_fields` WHERE `language`='" . $this->lang . "' AND `level` = '$level'";

        $sectionRow = $this->db->fetchAll($sql);

        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('name', 'edit');



        $this->tpl->define_dynamic('sectionfields_section_name', 'edit');
        $this->tpl->define_dynamic('sectionfields_position', 'edit');
        $this->tpl->define_dynamic('sectionfields_type', 'edit');

        $this->tpl->define_dynamic('end', 'edit');

        $this->tpl->parse('SECTIONFIELDS_GROUP', 'null');

        $admSectionFieldPosition = '<option value="after-base-group">Под основной группой</option>' . "\n";
        //$admSectionFieldPosition .= '<option value="after-base-sub-group">Под основной подгруппой</option>'."\n";

        $admSectionFieldSectionName = '<option value="catalog">Каталог</option>' . "\n";

        if ($sectionRow) {
            foreach ($sectionRow as $sectionRes) {
                $admSectionFieldPosition .= '<option value="' . $sectionRes['position'] . '">Под группой "' . $sectionRes['title'] . '"</option>' . "\n";
            }
        }



        if (!empty($_POST) && !$this->_err) {

            if ($position == 'after-base-sub-group' || $position == 'after-base-group') {
                $position = 0;
            }

            $this->db->query("UPDATE `catalog_section_fields` SET `position` = (`position`+1) WHERE `position` <= $position AND `position` < 9999");

            $this->db->insert('catalog_section_fields', array('name' => $this->ru2Lat(trim($name)), 'title' => $name, 'type' => $type, 'position' => $position, 'level' => $level, 'language' => $this->lang));
        }

        $this->tpl->assign(array(
            'ADM_NAME' => $name,
            'ADM_SECTION_FIELD_TYPE' => $this->getField('select'),
            'ADM_SECTION_FIELD_POSITION' => $admSectionFieldPosition
        ));


        $this->tpl->parse('CONTENT', '.start');
        $this->tpl->parse('CONTENT', '.name');
        $this->tpl->parse('CONTENT', '.sectionfields_type');
        if ($sectionRow)
            $this->tpl->parse('CONTENT', '.sectionfields_position');

        $this->tpl->parse('CONTENT', '.end');
        return true;
    }

    protected function getSectionFields($level, $type = 'basic')
    {
        $ret = '';
        if ($type == 'basic') {
            $adminButtons1 = $this->getAdminButtons(array(array('section' => array('name' => 'Подгруппа', 'target' => 'fieldsubsectiongroup', 'id' => '0'))));
            $adminButtons2 = $this->getAdminButtons(array(array('page' => array('name' => 'Поле', 'target' => 'sectionfield', 'id' => '0'))));
            $ret = '<tr style="text-align: center; background-color: #cccccc; color: #fff; "><th colspan="2" style="width: 520px;">Основная группа</th><th>' . $adminButtons1 . '</th></tr>' . "\n";
            $ret .= '<tr style="background-color: #c0c0c0; color: #fff;"><th colspan="2"  >Основная подгруппа </th><th>' . $adminButtons2 . '</th></tr>' . "\n";
            $row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `language` = '" . $this->lang . "' AND `level` = 0 AND (`type` <> 'section' AND `type` != 'sub_section') ORDER BY `position`");
            if ($row) {
                foreach ($row as $res) {
                    $ret .= '<tr><td>' . $res['title'] . '</td><td></td></tr>' . "\n";
                }
            }
        } elseif ($type == 'section') {
            $row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `language` = '" . $this->lang . "' AND `level` = '$level' AND `type` = 'section' ORDER BY `position`");
            if ($row) {
                foreach ($row as $res) {
                    $adminButtons1 = $this->getAdminButtons(array(array('section' => array('name' => 'Подгруппа', 'target' => 'fieldsubsectiongroup', 'id' => $res['id']))));
                    $ret .= '<tr style="background-color: #c0c0c0; color: #fff;"><th colspan="2">' . $res['title'] . ' </th><th>' . $adminButtons1 . '</th></tr>' . "\n";
                    $ret .= $this->getSectionFields($res['id'], $type = 'sub_section');
                }
            }
        } elseif ($type == 'sub_section') {
            $row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `language` = '" . $this->lang . "' AND `level` = '$level' AND `type` = 'sub_section' ORDER BY `position`");
            if ($row) {
                foreach ($row as $res) {
                    $adminButtons2 = $this->getAdminButtons(array(array('page' => array('name' => 'Поле', 'target' => 'sectionfield', 'id' => $res['id']))));
                    $ret .= '<tr style="background-color: #c0c0c0; color: #fff;"><th colspan="2">' . $res['title'] . ' </th><th>' . $adminButtons2 . '</th></tr>' . "\n";
                    $ret .= $this->getSectionFields($res['id'], $type = 'page');
                }
            }
        } elseif ($type == 'page') {
            $row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `language` = '" . $this->lang . "' AND `level` = '$level' ORDER BY `position`");
            if ($row) {
                foreach ($row as $res) {

                    $ret .= '<tr><td colspan="2">' . $res['title'] . ' </td><td></td></tr>' . "\n";
                    $ret .= $this->getSectionFields($res['id'], $type = 'page');
                }
            }
        }

        return $ret;
    }

    public function sectionfields()
    {
        $level = false;

        $header = array(
            'title' => "Управление полями разделоав каталога",
            'keywords' => "Управление полями разделоав каталога",
            'description' => "Управление полями разделоав каталога",
            'header' => "Управление полями разделоав каталога" . "<p>" .
            $this->getAdminButtons(array(
                array('section' => array('name' => 'Создать группу', 'target' => 'fieldsectiongroup'))
            )) . "</p>"
        );

        $this->setMetaTags($header);
        $this->setWay("Управление полями разделоав каталога");

        $this->tpl->define_dynamic('_section_fields', 'adm/section_fields.tpl');
        $this->tpl->define_dynamic('section_fields', '_section_fields');

        $sectionList = $this->getSectionFields(0) . $this->getSectionFields(0, 'section');

        $this->tpl->assign('SECTION_FIELDS_LIST', $sectionList);

        $this->tpl->parse('CONTENT', '.section_fields');


        return true;
    }

    // Управление полями раздела. Вариант - 2

    public function changecatsection3()
    {
        
        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Не могу найти раздел № $id");
        }

        if (!($section = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `id` = '$id'"))) {
            $this->addErr("Не могу найти раздел № $id");
        }

        $action = '';
        $actionId = '';

        if (isset($this->url[(count($this->url) - 3)]) && $this->url[(count($this->url) - 3)] != 'changecatsection') {
            $actionId = $this->url[(count($this->url) - 2)];
            $action = $this->url[(count($this->url) - 3)];
        }
        $name = '';
        $nameOld = '';
        $fieldNameOld = '';
        $group = '';
        $subGroup = '';
        $groupPosition = '';
        $subGroupPosition = '';
        $fieldPosition = '';

        if (in_array($action, array('editgroup', 'deletegroup', 'editsubgrup', 'deletesubgroup', 'editfield', 'deletefield')) && is_numeric($actionId)) {

            if (($sectionRow = $this->db->fetchRow("SELECT * FROM `catalog_section_fields` WHERE `id` = '$actionId'"))) {
                $fieldNameOld = $sectionRow['name'];
                $name = $sectionRow['title'];
                $nameOld = $name;
                $group = $sectionRow['group'];
                $subGroup = $sectionRow['sub_group'];
            }
        }

        if (!$this->_err) {
            $this->setMetaTags("Управление полями раздела ($section[header])");
            $this->setWay("Управление полями раздела ($section[header])");

            $this->tpl->define_dynamic('start', 'edit');
            $this->tpl->define_dynamic('section_fields_form', 'edit');
            $this->tpl->define_dynamic('end', 'edit');

            $name = $this->getVar('sectionfields_name', $name);
            $group = $this->getVar('group', $group);
            $newGroup = $this->getVar('new_group', '');
            $subGroup = $this->getVar('sub_group', $subGroup);
            $newSubGroup = $this->getVar('new_sub_group', '');
            $type = $this->getVar('type', 'varchar');
            $position = $this->getVar('position', '');

            $catalogHref = $this->getVar('sectionfields_catalog_href', $section['artikul']);

            $sectionGroupStyle = '';
            $sectionNewGroupStyle = 'display:none';
            $sectionSubGroupStyle = '';
            $sectionNewSubGroupStyle = 'display:none';

            $sectionHref = $section['artikul'];

            if (!empty($_POST)) {

                if (empty($name)) {
                    $this->addErr("Название поля не может быть пустым");
                } else {
                    $isExists = $this->db->fetchOne("SELECT count(id) FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `group` = '$group' AND `sub_group` = '$subGroup' AND `title` = '$name' AND `type` = '$type' AND `language`='" . $this->lang . "'");
                    if ($isExists > 0 && $action != 'editfield') {
                        $this->addErr("Поле \"$name\" уже существует");
                    }
                }

                if ($group == 'new') {
                    if (empty($newGroup)) {
                        $this->addErr("Укажите название новой группы или выберите из списка");
                    } else {
                        $isExists = $this->db->fetchOne("SELECT count(id) FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `group` = '$newGroup'  AND `language`='" . $this->lang . "'");
                        if ($isExists > 0) {
                            $this->addErr("Группа \"$newGroup\" уже существует");
                        } else {
                            $group = $newGroup;
                        }
                    }
                }




                if ($subGroup == 'new') {

                    if (empty($newSubGroup)) {
                        $this->addErr("Укажите название новой подгруппы или выберите из списка");
                    } else {
                        $isExists = $this->db->fetchOne("SELECT count(id) FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `sub_group` = '$newSubGroup' AND `language`='" . $this->lang . "'");
                        if ($isExists > 0) {
                            $this->addErr("Подгруппа \"$newSubGroup\" уже существует");
                        } else {
                            $subGroup = $newSubGroup;
                        }
                    }
                }
            }

            $tableName = "catalog-fields-$catalogHref";
            $isTableExists = false;
            $allTables = $this->db->fetchAll("SHOW TABLES");
            if ($allTables) {
                foreach ($allTables as $table) {
                    list ($tmpName, $tmpTableName) = each($table);
                    if ($tableName == $tmpTableName) {
                        $isTableExists = true;
                        break;
                    }
                }
            }

            if (!$this->_err) {
                if ($action == 'deletefield' && is_numeric($actionId)) {
                    // Удаление поля

                    if ($isTableExists) {

                        if (($isFieldExists = $this->db->fetchOne("DESC `$tableName` `$fieldNameOld`"))) {
                            $sql = "ALTER TABLE `$tableName` DROP `$fieldNameOld`";
                            $this->db->query($sql);
                        }
                        $sql = "DELETE FROM `catalog_section_fields` WHERE `id`='$actionId'";
                        $this->db->query($sql);
                        $content = "<h2>Поле удалено</h2> <meta http-equiv='refresh' content='1;URL=/admin/changecatsection/$id' />";
                        $this->viewMessage($content);
                    }
                } elseif ($action == 'editgroup' && is_numeric($actionId)) {
                    // Редактирование группы

                    $sectionGroupStyle = 'display:none';
                    $sectionNewGroupStyle = '';
                    $name = '';
                    $newGroup = $group;
                    $groupPosition = $position;
                } elseif ($action == 'editsubgrup' && is_numeric($actionId)) {
                    // Редактирование подгруппы
                    $sectionSubGroupStyle = 'display:none';
                    $sectionNewSubGroupStyle = '';
                    $name = '';
                    if ($subGroup == 'A1') {
                        $subGroup = 'Основная подгруппа';
                    }
                    $newSubGroup = $subGroup;
                    $subGroupPosition = $position;
                }
            }


            if (!empty($_POST) && !$this->_err) {
                // Редактирование поля
                $fieldName = $this->ru2Lat(trim($name));
                // Редактирование поля

                if ($action == 'editfield' && is_numeric($actionId)) {

                    $dbType = $this->getField('db', $type);
                    if (!$isTableExists) {
                        $sql = "CREATE TABLE `$tableName` (`id`INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `catalog_artikul` VARCHAR (255), `$fieldName` $dbType)DEFAULT CHARSET=utf8;";
                        $this->db->query($sql);
                    } else {
                        $sql = "ALTER TABLE `$tableName` CHANGE `$fieldNameOld` `$fieldName` $dbType";
                        $this->db->query($sql);
                        $content = "<h2>Поле удалено</h2> <meta http-equiv='refresh' content='1;URL=/admin/changecatsection/$id' />";
                        $this->viewMessage($content);
                    }



                    $this->db->update('catalog_section_fields', array('name' => $fieldName,
                        'title' => $name,
                        'catalog_section_href' => $catalogHref,
                        'group' => $group,
                        'sub_group' => $subGroup,
                        'before_name' => $position,
                        'type' => $type), "id=$actionId");
                } elseif ($action == 'editgroup' && is_numeric($actionId)) {
                    // Редактирование группы
                    $this->db->update('catalog_section_fields', array('name' => $fieldName,
                        'title' => $name,
                        'catalog_section_href' => $catalogHref,
                        'group' => $group,
                        'sub_group' => $subGroup,
                        'before_name_group' => $groupPosition,
                        'type' => $type), "id=$actionId");
                } elseif ($action == 'editsubgrup' && is_numeric($actionId)) {
                    // Редактирование подгруппы

                    $this->db->update('catalog_section_fields', array('name' => $fieldName,
                        'title' => $name,
                        'catalog_section_href' => $catalogHref,
                        'group' => $group,
                        'sub_group' => $subGroup,
                        'before_name_sub_group' => $subGroupPosition,
                        'type' => $type), "id=$actionId");
                } else {
                    // Добавление поля
                    $dbType = $this->getField('db', $type);
                    if (!$isTableExists) {
                        $sql = "CREATE TABLE `$tableName` (`id`INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `catalog_artikul` VARCHAR (255), `$fieldName` $dbType)DEFAULT CHARSET=utf8";
                        $this->db->query($sql);
                    } else {
                        $sql = "ALTER TABLE `$tableName` ADD `$fieldName` $dbType";
                        $this->db->query($sql);
                    }

                    $this->db->insert('catalog_section_fields', array('name' => $fieldName, 'title' => $name, 'catalog_section_href' => $catalogHref, 'group' => $group, 'sub_group' => $subGroup, 'type' => $type));
                }

                $name = '';
                $newSubGroup = '';
                $newGroup = '';
            } elseif ($this->_err) {
                $this->setMetaTags("Ошибка!");
                $this->setMetaTags("Ошибка!");
                $this->viewErr();

                if ($group == 'new') {
                    $sectionGroupStyle = 'display:none';
                    $sectionNewGroupStyle = '';
                }

                if ($subGroup == 'new') {
                    $sectionSubGroupStyle = 'display:none';
                    $sectionNewSubGroupStyle = '';
                }
            }


            $sectionFieldsGroup = '<option value="A1">Основная группа</option>' . "\n";
            $subGroupList = $this->db->fetchAll("SELECT DISTINCT `group` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `group` != 'A1' AND `language`='" . $this->lang . "'");
            if ($subGroupList) {
                foreach ($subGroupList as $subGroupElement) {
                    $sectionFieldsGroup .= '<option value="' . $subGroupElement['group'] . '" ' . ($subGroupElement['group'] == $group || $subGroupElement['group'] == $newGroup ? 'selected' : '') . '>' . $subGroupElement['group'] . '</option>' . "\n";
                }
            }
            $sectionFieldsGroup .= '<option value="new">Новая группа</option>' . "\n";

            $sectionFieldsSubGroup = '<option value="A1">Основная подгруппа</option>' . "\n";

            $subSubGroupList = $this->db->fetchAll("SELECT DISTINCT `sub_group` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `sub_group` != 'A1' AND `language`='" . $this->lang . "'");
            if ($subSubGroupList) {
                foreach ($subSubGroupList as $subSubGroupElement) {
                    $sectionFieldsSubGroup .= '<option value="' . $subSubGroupElement['sub_group'] . '" ' . ($subSubGroupElement['sub_group'] == $subGroup || $subSubGroupElement['sub_group'] == $newSubGroup ? 'selected' : '') . '>' . $subSubGroupElement['sub_group'] . '</option>' . "\n";
                }
            }


            $sectionFieldsSubGroup .= '<option value="new">Новая подгруппа</option>' . "\n";


            // Выбор расположения
            $sectionFieldsPosition = '';
            $sectionFieldsArray = $this->drowSectionFields($section['artikul'], true);
            $sectionFieldsPosition .= "<option value=''></option>";
            $sectionFieldsPosition .= "<option value='first'>Первым</option>";
            $sectionFieldsPosition .= "<option value='last'>Последним</option>";
            if (is_array($sectionFieldsArray) && count($sectionFieldsArray) > 0) {
                foreach ($sectionFieldsArray as $groupTmp1 => $valTmp2) {
                    $groupTmpName = $groupTmp1;
                    if ($groupTmp1 == 'A1') {
                        $groupTmp1 = 'Основная группа';
                    }

                    if (!empty($groupTmp1)) {
                        $sectionFieldsPosition .= "<option value='group-$groupTmp1'>-|Под группой: $groupTmp1 </option>";
                    }

                    foreach ($valTmp2 as $subGroupTmp1 => $val1) {
                        $subGroupTmpName = $subGroupTmp1;
                        if ($subGroupTmp1 == 'A1') {
                            $subGroupTmp1 = 'Основная подгруппа';
                        }
                        if (!empty($subGroupTmp1)) {
                            $sectionFieldsPosition .= "<option value='$subGroupTmp1'>---| Под подгруппой: $subGroupTmp1 </option>";
                        }

                        foreach ($val1 as $fieldTmp1) {
                            if (!empty($fieldTmp1['title'])) {
                                $sectionFieldsPosition .= "<option value='$fieldTmp1[title]'>------| Под полем: $fieldTmp1[title] </option>";
                            }
                        }
                    }
                }
            }





            $this->tpl->assign(
                    array('ADM_SECTION_FIELD_NAME' => $name,
                        'ADM_SECTION_ID' => $id,
                        'ADM_SECTION_FIELD_GROUP' => $sectionFieldsGroup,
                        'ADM_NEW_GROUP' => $newGroup,
                        'ADM_SECTION_FIELD_SUB_GROUP' => $sectionFieldsSubGroup,
                        'ADM_NEW_SUB_GROUP' => $newSubGroup,
                        'ADM_CATALOG_HREF' => $catalogHref,
                        'ADM_SECTION_HREF' => $sectionHref,
                        'ADM_SECTION_FIELD_TYPE' => $this->getField('select'),
                        'ADM_SECTION_FIELD_RESULT' => $this->drowSectionFields($section['artikul'], false, $id),
                        'ADM_SECTION_FIELD_GROUP_STYLE' => $sectionGroupStyle,
                        'ADM_NEW_GROUP_STYLE' => $sectionNewGroupStyle,
                        'ADM_SECTION_FIELD_SUB_GROUP_STYLE' => $sectionSubGroupStyle,
                        'ADM_SECTION_FIELD_POSITION' => $sectionFieldsPosition,
                        'ADM_NEW_SUB_GROUP_STYLE' => $sectionNewSubGroupStyle,
                    )
            );



            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.section_fields_form');

            $this->tpl->parse('CONTENT', '.end');
        } else {
            $this->setMetaTags("Ошибка!");
            $this->setMetaTags("Ошибка!");

            $this->viewErr();
        }



        return true;
    }

    public function copysectionoptions($functionType = '')
    {

        $id = end($this->url);

        if (!is_numeric($id)) {
            $this->addErr("Не могу найти раздел № $id");
        }

        if (!($section = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `id` = '$id'"))) {
            $this->addErr("Не могу найти раздел № $id");
        }

        $sectionHref = $section['artikul'];


        $group = $this->getVar('group', '');
        $newGroup = $this->getVar('new_group', '');
        $subGroup = $this->getVar('sub_group', '');
        $newSubGroup = $this->getVar('new_sub_group', '');
        $sectionGroupStyle = '';
        $sectionNewGroupStyle = 'display:none';
        $sectionSubGroupStyle = '';
        $sectionNewSubGroupStyle = 'display:none';
        $catalogHref = $this->getVar('sectionfields_catalog_href', $section['artikul']);

        if (isset($_POST['copy_fields_options']) && is_array($_POST['copy_fields_options']) && count($_POST['copy_fields_options']) > 0) {

            $catalogHref = $this->getVar('sectionfields_catalog_href', $section['artikul']);
            $copyFieldsOptions = $_POST['copy_fields_options'];
            $copyFieldsValues = $_POST['copy_fields_values'];

            $type = $this->getVar('type', 'varchar');
            $tableName = "catalog-fields-$catalogHref";
            $copyFieldsOptionsLength = count($copyFieldsOptions);
            $isTableExists = false;


            for ($i = 0; $i < $copyFieldsOptionsLength; ++$i) {

                $options = explode(';', $copyFieldsOptions[$i]);
                $valArr = array();
                foreach ($options as $option) {
                    list($key, $value) = explode(':', $option);
                    $valArr[$key] = $value;
                }

                $data = array(
                    'name' => $valArr['name'],
                    'type' => $valArr['type'],
                    'title' => $copyFieldsValues[$i],
                    'catalog_section_href' => $section['artikul']
                );


                if (empty($valArr['name'])) {
                    $this->addErr("Название поля не может быть пустым");
                } else {
                    $isExists = $this->db->fetchOne("SELECT count(id) FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `title` = '$valArr[name]' AND `language`='" . $this->lang . "'");
                    if ($isExists > 0) {
                        $this->addErr("Поле \"$valArr[name]\" уже существует");
                    }
                }

                if ($group == 'new') {
                    if (empty($newGroup)) {
                        $this->addErr("Укажите название новой группы или выберите из списка");
                    } else {
                        $isExists = $this->db->fetchOne("SELECT count(id) FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `group` = '$newGroup'  AND `language`='" . $this->lang . "'");
                        if ($isExists > 0) {
                            $this->addErr("Группа \"$newGroup\" уже существует");
                        } else {
                            $group = $newGroup;
                        }
                    }
                }




                if ($subGroup == 'new') {

                    if (empty($newSubGroup)) {
                        $this->addErr("Укажите название новой подгруппы или выберите из списка");
                    } else {
                        $isExists = $this->db->fetchOne("SELECT count(id) FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `sub_group` = '$newSubGroup' AND `language`='" . $this->lang . "'");
                        if ($isExists > 0) {
                            $this->addErr("Подгруппа \"$newSubGroup\" уже существует");
                        } else {
                            $subGroup = $newSubGroup;
                        }
                    }
                }
                $data['group'] = $group;
                $data['sub_group'] = $subGroup;

                $dbType = $this->getField('db', $valArr['type']);
                $allTables = $this->db->fetchAll("SHOW TABLES");
                if ($allTables) {
                    foreach ($allTables as $table) {
                        list ($tmpName, $tmpTableName) = each($table);
                        if ($tableName == $tmpTableName) {
                            $isTableExists = true;
                            break;
                        }
                    }
                }


                if (!$isTableExists) {
                    $sql = "CREATE TABLE `$tableName` (`id`INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `catalog_artikul` VARCHAR (255), `$valArr[name]` $dbType) $this->dbFieldCharset;";

                    $this->db->query($sql);
                } else {
                    $sql = "ALTER TABLE `$tableName` ADD `$valArr[name]` $dbType";
                    $this->db->query($sql);
                }



                $this->db->insert('catalog_section_fields', $data);

                $name = '';
                $newSubGroup = '';
                $newGroup = '';
            }
            // var_dump($_POST);

            if (!$this->_err) {
                $content = "Поля добавлены <meta http-equiv='refresh' content='2;URL=/admin/changecatsection/$id'>";
                $this->viewMessage($content);
            }
        }


        if ($this->_err) {
            if ($group == 'new') {
                $sectionGroupStyle = 'display:none';
                $sectionNewGroupStyle = '';
            }

            if ($subGroup == 'new') {
                $sectionSubGroupStyle = 'display:none';
                $sectionNewSubGroupStyle = '';
            }
        }

        if (!$this->_err) {
            $this->setMetaTags("Управление полями раздела ($section[header])");
            $this->setMetaTags("Управление полями раздела ($section[header])");


            $this->tpl->define_dynamic('start', 'edit');
            $this->tpl->define_dynamic('section_fields_copy', 'edit');
            $this->tpl->define_dynamic('end', 'edit');

            $sectionFieldsGroup = '<option value="A1">Основная группа</option>' . "\n";
            $subGroupList = $this->db->fetchAll("SELECT DISTINCT `group` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `group` != 'A1' AND `language`='" . $this->lang . "'");
            if ($subGroupList) {
                foreach ($subGroupList as $subGroupElement) {
                    $sectionFieldsGroup .= '<option value="' . $subGroupElement['group'] . '" ' . ($subGroupElement['group'] == $group || $subGroupElement['group'] == $newGroup ? 'selected' : '') . '>' . $subGroupElement['group'] . '</option>' . "\n";
                }
            }
            $sectionFieldsGroup .= '<option value="new">Новая группа</option>' . "\n";

            $sectionFieldsSubGroup = '<option value="A1">Основная подгруппа</option>' . "\n";

            $subSubGroupList = $this->db->fetchAll("SELECT DISTINCT `sub_group` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$catalogHref' AND `sub_group` != 'A1' AND `language`='" . $this->lang . "'");
            if ($subSubGroupList) {
                foreach ($subSubGroupList as $subSubGroupElement) {
                    $sectionFieldsSubGroup .= '<option value="' . $subSubGroupElement['sub_group'] . '" ' . ($subSubGroupElement['sub_group'] == $subGroup || $subSubGroupElement['sub_group'] == $newSubGroup ? 'selected' : '') . '>' . $subSubGroupElement['sub_group'] . '</option>' . "\n";
                }
            }

            $sectionFieldsSubGroup .= '<option value="new">Новая подгруппа</option>' . "\n";




            // Прорисовка полей из других разделов
            $sectionLink1 = ' <img src="/img/admin_icons/stock_insert-fields.png" />&nbsp;Копировать поля из шаблонов';
            $sectionLink2 = ' <a href="/admin/copytemplatesoptions/' . $id . '" class="im-href"><img src="/img/admin_icons/template.png" />&nbsp;Копировать поля из шаблонов</a>';
            if ($functionType != 'templates') {

                $sectionFieldsJSParams = 'var sectionFieldsTemplates = {';

                if (($row = $this->db->fetchAll("SELECT DISTINCT `catalog_section_href` FROM `catalog_section_fields` WHERE `catalog_section_href` != '$section[artikul]'"))) {
                    $sectCount = 0;
                    foreach ($row as $res) {
                        $sectionInfo = $this->db->fetchRow("SELECT `name`, `href` FROM `catalog` WHERE `artikul` = '$res[catalog_section_href]'");
                        if (isset($sectionInfo['name'])) {
                            if ($sectCount > 0) {
                                $sectionFieldsJSParams .= ',';
                            }
                            $sectionFieldsJSParams .= "'$sectionInfo[href]':{\n";
                            $sectionFieldsJSParams .= "'title': '$sectionInfo[name]',\n";
                            $sectionFieldsJSParams .= "'fields': [\n";
                            if (($sections = $this->db->fetchAll("SELECT `name`, `title`, `type` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$res[catalog_section_href]'"))) {
                                $counter = 0;
                                foreach ($sections as $section1) {
                                    if ($counter > 0) {
                                        $sectionFieldsJSParams .= ",\n";
                                    }

                                    $sectionFieldsJSParams .= "{'name':'$section1[name]',";
                                    $sectionFieldsJSParams .= "'title':'$section1[title]',";
                                    $sectionFieldsJSParams .= "'type':'$section1[type]',";
                                    $sectionFieldsJSParams .= "'isSerch':false,";
                                    $sectionFieldsJSParams .= "'isFilter':false}";

                                    $counter++;
                                }
                            }
                            $sectionFieldsJSParams .= ']';
                            $sectionFieldsJSParams .= '}';

                            $sectCount++;
                        }
                    }
                }


                $sectionFieldsJSParams .= '};';
                $sectionFieldsJSParams = "<script type=\"text/javascript\">$sectionFieldsJSParams</script>";
            } else {

                $sectionLink1 = ' <a href="/admin/copysectionoptions/' . $id . '" class="im-href"><img src="/img/admin_icons/stock_insert-fields.png" />&nbsp;Копировать поля из шаблонов</a>';
                $sectionLink2 = ' <img src="/img/admin_icons/template.png" />&nbsp;Копировать поля из шаблонов';
                $sectionFieldsJSParams = "<script type=\"text/javascript\" src=\"/js/jq-section-fields-templates.js\"></script>";
            }

            $this->tpl->assign(
                    array(
                        'ADM_SECTION_ID' => $id,
                        'ADM_SECTION_FIELD_GROUP' => $sectionFieldsGroup,
                        'ADM_NEW_GROUP' => $newGroup,
                        'ADM_CATALOG_HREF' => $catalogHref,
                        'ADM_SECTION_HREF' => $sectionHref,
                        'ADM_SECTION_FIELD_SUB_GROUP' => $sectionFieldsSubGroup,
                        'ADM_NEW_SUB_GROUP' => $newSubGroup,
                        'ADM_SECTION_FIELDS_IS_PARAMS' => $sectionFieldsJSParams,
                        'ADM_SECTION_FIELD_GROUP_STYLE' => $sectionGroupStyle,
                        'ADM_NEW_GROUP_STYLE' => $sectionNewGroupStyle,
                        'ADM_SECTION_FIELD_SUB_GROUP_STYLE' => $sectionSubGroupStyle,
                        'ADM_NEW_SUB_GROUP_STYLE' => $sectionNewSubGroupStyle,
                        'ADM_SECTION_FIELDS_LINKS1' => $sectionLink1,
                        'ADM_SECTION_FIELDS_LINKS2' => $sectionLink2,
                        'ADM_SECTION_FIELD_TYPE' => $this->getField('select')));

            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.section_fields_copy');
            $this->tpl->parse('CONTENT', '.end');
        } else {
            $this->setMetaTags("Ошибка!");
            $this->setMetaTags("Ошибка!");

            $this->viewErr();
        }

        //section_fields_copy
        return true;
    }

    public function copytemplatesoptions()
    {
        return $this->copysectionoptions('templates');
    }

    // Удаляет подразделы и переностит товоры в раздел
    protected function dellSubSections($sectionId)
    {

        $row = $this->db->fetchAll("SELECT `id` FROM `catalog` WHERE `level` = '$sectionId'");

        if ($row) {
            $this->db->query("DELETE FROM `catalog` WHERE `level` = '$sectionId' AND `type`='section'");
            foreach ($row as $res) {

                $this->db->query("UPDATE `catalog` SET `level` = '$sectionId' WHERE `level` = '$res[id]'");
            }
        }

        return 0;
    }

    // Если в разделе небыло подразделов но, в нем были товары, а затем раздел захотели разбить на подразделы - создается системный скрытый подраздел в который переносятся все товары
    protected function addSysSubSection($sectionLevel)
    {

        $sysSubSectionName = 'Системный скрытый раздел с товарами';
        $sysSubSectionHref = $this->ru2Lat($sysSubSectionName);

        $data = array(
            'type' => 'section',
            'level' => $sectionLevel,
            'name' => $sysSubSectionName,
            'header' => $sysSubSectionName,
            'title' => $sysSubSectionName,
            'keywords' => $sysSubSectionName,
            'description' => $sysSubSectionName,
            'href' => $sysSubSectionHref,
            'visibility' => '0'
        );
        if ($id = $this->db->fetchOne("SELECT `id` FROM `catalog` WHERE `href`='$sysSubSectionHref' AND `level`='$sectionLevel'")) {
            $this->db->update("catalog", $data, "id=$id");
            $level = $id;
        } else {
            $this->db->insert("catalog", $data);
            $level = $this->db->lastInsertId();
        }

        $this->db->query("UPDATE `catalog` SET `level`='$level' WHERE `type`='page' AND `level`='$sectionLevel' ");
    }

    // Настрайки каталога
    public function catalogoptions()
    {


        $title = 'Настройка каталога. Для всех разделов';
        $options = array();

        $sectionId = 0;
        $sectionHref = '0';
        if (is_numeric(($sectionId = end($this->url)))) {
            $sectionHref = $this->db->fetchOne("SELECT `href` FROM `catalog` WHERE `id` = '$sectionId' AND `type` = 'section' ");
            if ($sectionHref) {
                $options = $this->db->fetchRow("SELECT * FROM `catalog_options` WHERE `section_href` = '$sectionHref'");
            }
        } else {
            $sectionId = 0;
            $options = $this->db->fetchRow("SELECT * FROM `catalog_options` WHERE `section_href` = '0'");
        }



        if ($this->_err) {
            $title = "Ошибка!";
            $this->setMetaTags($title);
            $this->setWay($title);
            $this->viewErr();
        } else {

            $this->setMetaTags($title);
            $this->setWay($title);

            $this->tpl->define_dynamic('start', 'edit');
            $this->tpl->define_dynamic('mce', 'edit');
            $this->tpl->define_dynamic('cat_setting', 'edit');
            $this->tpl->define_dynamic('cat_global_setting', 'edit');
            $this->tpl->define_dynamic('end', 'edit');

            //
            if ($sectionId == 0) {
                $isShowHits = (isset($options['is_show_hits']) ? $options['is_show_hits'] : '1');
                $isShowNew = (isset($options['is_show_new']) ? $options['is_show_new'] : '1');
                $isShowActions = (isset($options['is_show_actions']) ? $options['is_show_actions'] : '3');
                $newIndexLength = (isset($options['new_index_length']) ? $options['new_index_length'] : '3');
                $hitsIndexLength = (isset($options['hits_index_length']) ? $options['hits_index_length'] : '3');
                $actionIndexLength = (isset($options['action_index_length']) ? $options['action_index_length'] : '3');
            }

            $uniqueUrlFields = array('none');
            $uniqueUrlFieldsData = '';

            $isUseSubSection = (isset($options['is_use_sub_section']) ? $options['is_use_sub_section'] : '1');
            $isUseUniqueGoodsNames = (isset($options['is_use_unique_goods_names']) ? $options['is_use_unique_goods_names'] : '0');
            $uniqueUrlFields = (isset($options['is_unique_url_fields']) ? explode(';', $options['is_unique_url_fields']) : array('none'));
            $isShowEmptyPic = (isset($options['is_show_empty_pic']) ? $options['is_show_empty_pic'] : '1');
            $isShowEmptyPrice = (isset($options['is_show_empty_price']) ? $options['is_show_empty_price'] : '1');
            $httpRefferer = '/catalog';
            if (isset($_SERVER['HTTP_REFERER'])) {
                $httpRefferer = $_SERVER['HTTP_REFERER'];
            }


            if (!empty($_POST)) {

                $httpRefferer = $this->getVar('HTTP_REFERER');
                $isUseSubSection = $this->getVar('is_use_sub_section', '1');
                $isUseUniqueGoodsNames = $this->getVar('is_use_unique_goods_names', '0');

                if (isset($_POST['is_unique_url_fields']) && is_array($_POST['is_unique_url_fields']) && count($_POST['is_unique_url_fields']) > 0) {
                    $uniqueUrlFields = $_POST['is_unique_url_fields'];
                }

                if ($sectionId == 0) {
                    $isShowHits = $this->getVar('is_show_hits', '1');
                    $isShowNew = $this->getVar('is_show_new', '1');
                    $isShowActions = $this->getVar('is_show_actions', '1');
                    $newIndexLength = $this->getVar('new_index_length', '1');
                    $hitsIndexLength = $this->getVar('hits_index_length', '12');
                    $actionIndexLength = $this->getVar('action_index_length', '12');
                    $uniqueUrlFieldsData = implode(';', $uniqueUrlFields);
                }




                if ($sectionId == 0) {
                    $sections = $this->db->fetchAll("SELECT `id` FROM `catalog` WHERE `type` = 'section' AND `level` = '0'");
                    if ($sections) {
                        foreach ($sections as $section) {
                            if ($isUseSubSection == '0') {
                                $length = $this->dellSubSections($section['id']);
                            } elseif ($isUseSubSection == '1') {
                                $this->addSysSubSection($section['id']);
                            }
                        }
                    }
                } else {
                    if ($isUseSubSection == '0') {
                        $length = $this->dellSubSections($sectionId);
                    } else {
                        $this->addSysSubSection($sectionId);
                    }
                }




                if ($isUseUniqueGoodsNames == 0) {
                    $uniqueUrlFields = array('none');
                }
                $isShowEmptyPic = $this->getVar('is_show_empty_pic', '1');
                $isShowEmptyPrice = $this->getVar('is_show_empty_price', '1');

                if ($sectionId == 0) {
                    $data = array(
                        'is_show_hits' => $isShowHits,
                        'is_show_new' => $isShowNew,
                        'is_show_actions' => $isShowActions,
                        'new_index_length' => $newIndexLength,
                        'hits_index_length' => $hitsIndexLength,
                        'action_index_length' => $actionIndexLength
                    );
                }



                $data['section_href'] = $sectionHref;
                $data['is_use_sub_section'] = $isUseSubSection;
                $data['is_use_unique_goods_names'] = $isUseUniqueGoodsNames;
                $data['is_unique_url_fields'] = $uniqueUrlFieldsData;
                $data['is_show_empty_pic'] = $isShowEmptyPic;
                $data['is_show_empty_price'] = $isShowEmptyPrice;



                if (isset($options['is_use_sub_section'])) {

                    $this->db->update('catalog_options', $data, "section_href='$sectionHref'");
                    $content = "Данные изменены <meta http-equiv='refresh' content='2;URL=$httpRefferer'>";
                    $this->viewMessage($content);
                } else {

                    $this->db->insert('catalog_options', $data);
                    $content = "Данные добавлены <meta http-equiv='refresh' content='2;URL=$httpRefferer'>";
                    $this->viewMessage($content);
                }
            } else {



                if ($sectionId == 0) {
                    $isShowHitsS = "<option value='1' " . ($isShowHits == '1' ? 'selected' : '') . ">Да</option>\n<option value='0' " . ($isShowHits == '0' ? 'selected' : '') . ">Нет</option>\n";
                    $isShowNewS = "<option value='1' " . ($isShowNew == '1' ? 'selected' : '') . ">Да</option>\n<option value='0' " . ($isShowNew == '0' ? 'selected' : '') . ">Нет</option>\n";
                    $isShowActionsS = "<option value='1' " . ($isShowActions == '1' ? 'selected' : '') . ">Да</option>\n<option value='0' " . ($isShowActions == '0' ? 'selected' : '') . ">Нет</option>\n";
                    if (($countOptions = $this->db->fetchOne("SELECT `id` FROM `catalog_options` WHERE `section_href` != '0'")) > 0) {
                        $isUseSubSection = 2;
                    }
                }

                $isUseSubSectionS = "<option value='1' " . ($isUseSubSection == '1' ? 'selected' : '') . ">Да</option>\n<option value='0' " . ($isUseSubSection == '0' ? 'selected' : '') . ">Нет</option><option value='2' " . ($isUseSubSection == '2' ? 'selected' : '') . ">Использовать настройки подразделов</option>\n";
                $isUseUniqueGoodsNamesS = "<option value='1' " . ($isUseUniqueGoodsNames == '1' ? 'selected' : '') . ">Да</option>\n<option value='0' " . ($isUseUniqueGoodsNames == '0' ? 'selected' : '') . ">Нет</option>\n";
                $sectionType = 'разделе';
                if ((isset($isUseSubSection) && $isUseSubSection == '1' ) || is_string($sectionHref)) {
                    $sectionType = 'подразделе';
                }

                if (!defined('CATALOG_MANAGER_PHP')) {
                    require_once PATH . 'library/CatalogManager.php';
                }

                $uniqueUrlFieldsS = '';
                $catalogManager1 = new CatalogManager();
                if (count(($sysFieldsArr = $catalogManager1->getDefaultFields())) > 0) {
                    $uniqueUrlFieldsS = '';


                    foreach ($sysFieldsArr as $sysFieldName => $sysField) {
                        if ($sysFieldName != 'pic'
                                && $sysFieldName != 'body'
                                && $sysFieldName != 'name'
                                && $sysFieldName != 'pic_alt'
                                && $sysFieldName != 'pic_title'
                                && $sysFieldName != 'visibility'
                                && $sysFieldName != 'description'
                        ) {
                            $title = $sysField;
                            if (is_array($sysField)) {
                                $title = 'no-title';
                                if (isset($sysField['title'])) {
                                    $title = $sysField['title'];
                                }
                            }


                            $uniqueUrlFieldsS .= "<div class='div-uniq-url-fields'><input type='checkbox' name='is_unique_url_fields[]' value='$sysFieldName' " . (in_array($sysFieldName, $uniqueUrlFields) ? 'checked' : '') . " /> $title </div>";
                        }
                    }
                }

                $isShowEmptyPicS = "<option value='1' " . ($isShowEmptyPic == '1' ? 'selected' : '') . ">Да</option>\n<option value='0' " . ($isShowEmptyPic == '0' ? 'selected' : '') . ">Нет</option>\n";
                $isShowEmptyPriceS = "<option value='1' " . ($isShowEmptyPrice == '1' ? 'selected' : '') . ">Да</option>\n<option value='0' " . ($isShowEmptyPrice == '0' ? 'selected' : '') . ">Нет</option>\n";


                if ($sectionId == 0) {
                    $tplArr = array(
                        'ADM_IS_SHOW_EMPTY_NEW_S' => $isShowNewS,
                        'ADM_IS_SHOW_EMPTY_ACTIONS_S' => $isShowActionsS,
                        'ADM_IS_SHOW_EMPTY_HITS_S' => $isShowHitsS,
                        'ADM_IS_SHOW_EMPTY_PIC_S' => $isShowEmptyPicS,
                        'NEW_INDEX_LENGTH' => $newIndexLength,
                        'HITS_INDEX_LENGTH' => $hitsIndexLength,
                        'ADM_IS_SHOW_NEW_STYLE_PREFIX' => ($isShowNew == '1' ? '-visible' : ''),
                        'ADM_IS_SHOW_HITS_STYLE_PREFIX' => ($isShowHits == '1' ? '-visible' : ''),
                        'ADM_IS_SHOW_ACTIONS_STYLE_PREFIX' => ($isShowActions == '1' ? '-visible' : ''),
                        'ACTION_INDEX_LENGTH' => $actionIndexLength
                    );
                }
                $tplArr['ADM_IS_USE_SUB_SECTION'] = $isUseSubSectionS;
                $tplArr['ADM_SECTION_TYPE'] = $sectionType;
                $tplArr['ADM_UNIQUE_STYLE_PREFIX'] = ($isUseUniqueGoodsNames == '1' ? 'visible' : '');
                $tplArr['ADM_IS_UNIQUE_GOODS_NAMES_S'] = $isUseUniqueGoodsNamesS;
                $tplArr['ADM_UNIQUE_URL_FIELDS'] = $uniqueUrlFieldsS;
                $tplArr['ADM_IS_SHOW_EMPTY_PRICE_S'] = $isShowEmptyPriceS;
                $tplArr['ADM_IS_SHOW_EMPTY_PIC_S'] = $isShowEmptyPicS;
                $tplArr['REFERER'] = $httpRefferer;

                $this->tpl->assign($tplArr);

                $this->tpl->parse('CONTENT', '.start');
                $this->tpl->parse('CONTENT', '.mce');
                $this->tpl->parse('CONTENT', '.cat_setting');
                if (is_numeric($sectionHref) && $sectionHref == 0) {
                    $this->tpl->parse('CONTENT', '.cat_global_setting');
                }
                $this->tpl->parse('CONTENT', '.end');
            }
        }
        return true;
    }

    // Управление полями раздела. Вариант - 3 с использованием ajax и jQuery Tree

    public function changecatsection()
    {
        return false;
        $id = end($this->url);


        if (!is_numeric($id)) {
            $this->addErr("Не могу найти раздел № $id");
        }

        if (!($section = $this->db->fetchRow("SELECT * FROM `catalog` WHERE `id` = '$id'"))) {
            $this->addErr("Не могу найти раздел № $id");
        }

        if (!$this->_err) {
            $this->setMetaTags("Управление полями раздела ($section[header])");
            $this->setWay("Управление полями раздела ($section[header])");
            $this->tpl->define_dynamic('_changecatsection', 'adm/changecatsection.tpl');
            $this->tpl->define_dynamic('changecatsection', '_changecatsection');
            $this->tpl->define_dynamic('changecatsection_top_list', 'changecatsection');
            $this->tpl->parse('CHANGECATSECTION_TOP_LIST', 'null');


            $sectionFieldsJSParams = 'var sectionFieldsTemplates = {';

            if (($row = $this->db->fetchAll("SELECT DISTINCT `catalog_section_href` FROM `catalog_section_fields` WHERE `catalog_section_href` != '$section[artikul]'"))) {
                $sectCount = 0;
                foreach ($row as $res) {
                    $sectionInfo = $this->db->fetchRow("SELECT `name`, `href` FROM `catalog` WHERE `artikul` = '$res[catalog_section_href]'");
                    if (isset($sectionInfo['name'])) {
                        if ($sectCount > 0) {
                            $sectionFieldsJSParams .= ',';
                        }
                        $sectionFieldsJSParams .= "'$sectionInfo[href]':{\n";
                        $sectionFieldsJSParams .= "'title': '$sectionInfo[name]',\n";
                        $sectionFieldsJSParams .= "'fields': [\n";
                        if (($sections = $this->db->fetchAll("SELECT `name`, `title`, `type` FROM `catalog_section_fields` WHERE `catalog_section_href` = '$res[catalog_section_href]'"))) {
                            $counter = 0;
                            foreach ($sections as $section1) {
                                if ($counter > 0) {
                                    $sectionFieldsJSParams .= ",\n";
                                }

                                $sectionFieldsJSParams .= "{'name':'$section1[name]',";
                                $sectionFieldsJSParams .= "'title':'$section1[title]',";
                                $sectionFieldsJSParams .= "'type':'$section1[type]',";
                                $sectionFieldsJSParams .= "'isSerch':false,";
                                $sectionFieldsJSParams .= "'isFilter':false}";

                                $counter++;
                            }
                        }
                        $sectionFieldsJSParams .= ']';
                        $sectionFieldsJSParams .= '}';

                        $sectCount++;
                    }
                }
            }
            $sectionFieldsJSParams .= '};';
            $sectionFieldsJSParams = "<script type=\"text/javascript\">$sectionFieldsJSParams</script>";


            $this->tpl->assign(
                    array(
                        'SECTION_ARTIKUL' => $section['artikul'],
                        'ADM_SECTION_FIELDS_IS_PARAMS' => $sectionFieldsJSParams,
                        'ADM_SECTION_FIELD_TYPE' => $this->getField('select')
                    )
            );
            $grp = array();
            //
//         if (($row = $this->db->fetchAll("SELECT * FROM `catalog_section_fields` WHERE `catalog_section_href`='$section[artikul]' AND `group`='Технические характеристики' AND `sub_group`='Размеры см'  ORDER BY `title_position`"))) {
//            foreach ($row as $res) {
//              // if (!isset($grp[$res['sub_group']])) {
//                  print "group: $res[title]: pos: $res[title_position] <br>";
//                //  print 'id: '.$res['id'].' val: '.$res['title'].' pos: '.$res['title_position'].' <br>';
//                  $grp[$res['sub_group']] = '';
//             //  }
//            }
//         }


            $this->tpl->parse('CONTENT', '.changecatsection');
        } else {
            $this->setMetaTags("Ошибка!!! ($section[header])");
            $this->setWay("Управление полями раздела. Ошибка!!! ($section[header])");
            $this->viewErr();
        }
        return true;
    }

}