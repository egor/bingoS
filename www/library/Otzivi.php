<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Otzivi extends Main_Abstract implements Main_Interface {

    public function factory() {
        return true;
    }

    public function main() {
        $this->setMetaTags('Отзывы');
        $this->setWay('Отзывы');


        $this->showForm();

        $this->otziviList();

        return true;

        //return $this->error404();
    }

    protected function otziviList() {
        $length = 0;
        $this->tpl->define_dynamic('_otzivi_list', 'otzivi.tpl');
        $this->tpl->define_dynamic('otzivi_list', '_otzivi_list');
        $this->tpl->define_dynamic('otzivi_items', 'otzivi_list');
        $this->tpl->parse('OTZIVI_ITEMS', 'null');

        $start = 0;
        $navbar = $navTop = $navBot = '';
        $page = 1;
        

        $num_pages = $this->settings['num_page_items'];

        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `otzivi`");

        if ($count > 0) {
            if ($count > $num_pages) {
                if (isset($this->getParam['page'])) {
                    $page = (int) $this->getParam['page'];
                    $start = $num_pages * $page - $num_pages;

                    if ($start > $count) {
                        $start = 0;
                    }
                }

                $navbar = $this->loadPaginator((int) ceil($count / $num_pages), (int) $page, '/otzivi');

                if ($navbar) {
                    $navTop =  $navbar;
                    $navBot = $navTop;
                }
            }
        }
          $this->tpl->assign(
                    array('PAGES_TOP' => (empty ($navTop) ? '' : $navTop),
                    'PAGES_BOTTOM' => (empty($navBot) ? '' : $navBot))
                );


        if (($row = $this->db->fetchAll("SELECT *, date_format(`date`, '%H:%i %d.%m.%Y ') as `date_foemat` FROM `otzivi` " . (!$this->_isAdmin() ? "WHERE `status` = 'show'" : '') . " ORDER BY `date` DESC  LIMIT $start, $num_pages"))) {
            $length = count($row);
            $adminButtons = '';

            if ($this->_isAdmin()) {
                $adminClassName = 'plashka-admin-button';
            }
            
          
            foreach ($row as $res) {

                if ($this->_isAdmin()) {
                    $this->setAdminButtons('editotziv/' . $res['id'], 'deleteotziv/' . $res['id']);
                    $adminButtons = '<p class="plashka-admin-button"><a href="#" id="' . $res['id'] . '" onclick="otzivToggle(this); return false;">' . ($res['status'] == 'hide' ? 'Показать' : 'Скрыть') . '</a> {ADMIN_BUTTON_PANEL}</p>';
                }

                $this->tpl->assign(array(
                    'OTZIVI_ITEMS_ADMIN' => '',
                    'OTZIVI_GOODS_NAME' => $res['goods_name'],
                    'OTZIVI_LIST_FIO' => $res['fio'],
                    'OTZIVI_LIST_DATE' => $res['date_foemat'],
                    'OTZIVI_EMAIL' => $res['email'],
                    'OTZIVI_CITY' => $res['city'],
                    'OTZIVI_BODY' => $res['body'],
                    // 'ADMIM_CLASS_NAME' =>$adminClassName,
                    'OTZIVI_LIST_DESC_CLASS_NAME' => '',
                    'OTZIVI_LI_ID' => ($this->_isAdmin() ? "id='$res[id]'" : ''),
                    'ADMIN_BUTTONS' => $adminButtons,
                    
                ));

                $this->tpl->parse('OTZIVI_ITEMS', '.otzivi_items');
            }
        }

        $this->tpl->parse('CONTENT', '.otzivi_list');
        return $length;
    }

    protected

    function showForm() {
        $this->tpl->define_dynamic('_otzivi', 'otzivi.tpl');
        $this->tpl->define_dynamic('otzivi', '_otzivi');
        $this->tpl->define_dynamic('otzivi_captcha', 'otzivi');

        if ($this->_isAdmin()) {
            $this->tpl->parse('OTZIVI_CAPTCHA', 'null');
        }

        $goodsList = '';
        $sectionList = '';
        if (($row = $this->db->fetchAll("SELECT `id`, `name` FROM `catalog` WHERE `level`='0' ORDER BY `position`, `name`"))) {
            foreach ($row as $res) {
                $sectionList .= "<option value='$res[id]'>$res[name]</option>\n";
            }
        }

        $captcha = new Zend_Captcha_Png(array(
                    'name' => 'cptch',
                    'wordLen' => 6,
                    'timeout' => 1800,
                ));
        $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
        $captcha->setStartImage('./img/captcha.png');
        $id = $captcha->generate();


        $this->tpl->assign(
                array(
                    'OTZIVI_FIO' => '',
                    'OTZIVI_SECTION_LIST' => $sectionList,
                    'OTZIVI_GOODS_LIST' => $goodsList,
                    'OTZIVI_EMAIL' => '',
                    'OTZIVI_CITY' => '',
                    'OTZIVI_CONCLUSION' => '',
                    'CAPTCHA_ID' => $id
                )
        );



        $this->tpl->parse('CONTENT', '.otzivi');
    }

}

?>
