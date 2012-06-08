<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class CommentsActions extends Main_Abstract_Base implements Main_Interface
{

    public function factory()
    {

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $value = str_replace('{', '', $value);
                $value = str_replace('}', '', $value);
                $value = strip_tags($value);
                $_POST[$key] = $value;
            }
        }
        return true;
    }

    public function main()
    {

        return true;
    }

    public function add()
    {
        if (!empty($_POST)) {
            $goodsId = $this->getVar('goods_id');
            $fio = $this->getVar('name_1');
            $periodOfOperation = $this->getVar('status');
            $dignity = $this->getVar('name_2');
            $shortcomings = $this->getVar('name_3');
            $recommendations = $this->getVar('name_4');
            $conclusion = $this->getVar('name_5');
          //  $rate = $this->getVar('rate');
            $captchaId = $this->getVar('captcha_id');
            $code = $this->getVar('captcha_input');
            $points = $this->getVar('rate');
            $returnToUrl = $this->dataTreeManager($goodsId);
            $returnToUrl = '/catalog/' . $returnToUrl['links'];

            $_SESSION['comments']['data'] = array(
                'goods_artikul' => $this->db->fetchOne("SELECT `artikul` FROM `catalog` WHERE `id`='$goodsId'"),
                'fio' => $fio,
                'period_of_operation' => $periodOfOperation,
                'dignity' => $dignity,
                'shortcomings' => $shortcomings,
                'recommendations' => $recommendations,
                'conclusion' => $conclusion,
                'points' => $points,
                'date' => date('Y-m-d')
            );

            $ankorName = '';
            if (empty($fio)) {
                $_SESSION['comments']['error'][$goodsId]['name_1'] = 'Это поле необходимо заполнить!';
                $ankorName = 'name_1';
            } elseif (isset($_SESSION['comments']['error'][$goodsId]['name_1'])) {
                unset($_SESSION['comments']['error'][$goodsId]['name_1']);
            }

            if (empty($conclusion)) {
                if (empty($ankorName))
                    $ankorName = 'name_5';

                $_SESSION['comments']['error'][$goodsId]['name_5'] = 'Это поле необходимо заполнить!';
            } elseif ($_SESSION['comments']['error'][$goodsId]['name_5']) {
                unset($_SESSION['comments']['error'][$goodsId]['name_5']);
            }

            if (!$this->_isAdmin()) {
                if (!$this->isCaptcha()) {
                    if (empty($ankorName))
                        $ankorName = 'captcha';
                    $_SESSION['comments']['error'][$goodsId]['captcha'] = $this->_err;
                } elseif ($_SESSION['comments']['error'][$goodsId]['captcha']) {
                    //unset($_SESSION['comments']['error'][$goodsId]['captcha']);
                }
            }

            if (!isset($_SESSION['comments']['error'])) {
                $ankorName = 'message';
                $_SESSION['comments']['isSave'] = true;
                $this->db->insert('comments', $_SESSION['comments']['data']);
            } else {
                $_SESSION['comments']['isSave'] = false;
            }

            header("Location: $returnToUrl#$ankorName");
            exit();
        }



        return true;
    }

    public function adminMessage()
    {
        if (!empty($_POST)) {
            $id = end($this->url);
            $adminMessage = $this->getVar('admin_message');
            if (is_numeric($id) && intval($id) > 0 && $this->_isAdmin() ) {

                if (($row = $this->db->fetchRow("SELECT `comments`.*, `catalog`.`id` as `goods_id`, `catalog`.`artikul` as `goods_artikul` FROM `comments`, `catalog` WHERE `comments`.`id` = '$id' AND `catalog`.`artikul`=`comments`.`goods_artikul`"))) {

                    if (isset($_SESSION['comments']['isSave'])) {
                        unset($_SESSION['comments']['isSave']);
                    }

                    $returnToUrl = $this->dataTreeManager($row['goods_id']);
                    $returnToUrl = '/catalog/' . $returnToUrl['links'];
                    $page = '';


                    if (isset($_SESSION['comments']['data'])) {
                        unset($_SESSION['comments']['data']);
                    }

                    $this->db->insert('comments', array(
                        'fio'=>'Администратор',
                        'conclusion' => $adminMessage,
                        'parent_id' => $id,
                        'goods_artikul' => $row['goods_artikul'],
                        'date' => date('Y-m-d')
                    ));

                    if (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) {
                        $page = "?page=$_GET[page]";
                    }
                    header("Location: $returnToUrl$page#toggle");
                    exit();
                }
            }
        }


        return false;
    }

    public function delete()
    {
        $id = end($this->url);

        if (is_numeric($id) && intval($id) > 0  && $this->_isAdmin() ) {
            if (($row = $this->db->fetchRow("SELECT `comments`.*, `catalog`.`id` as `goods_id` FROM `comments`, `catalog` WHERE `comments`.`id` = '$id' AND `catalog`.`artikul`=`comments`.`goods_artikul`"))) {

                if (isset($_SESSION['comments']['isSave'])) {
                    unset($_SESSION['comments']['isSave']);
                }

                $returnToUrl = $this->dataTreeManager($row['goods_id']);
                $returnToUrl = '/catalog/' . $returnToUrl['links'];
                $page = '';

                
                $this->db->delete('comments', "id=$id");
                $this->db->delete('comments', "parent_id=$id");

                if (isset($_SESSION['comments']['data'])) {
                    unset($_SESSION['comments']['data']);
                }

                if (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) {
                    $page = "?page=$_GET[page]";
                }

                header("Location: $returnToUrl$page#toggle");
                exit();
            }
        }
        return false;
    }

    public function view()
    {
        $id = end($this->url);
       
        if (is_numeric($id) && intval($id) > 0 && $this->_isAdmin() ) {
            if (($row = $this->db->fetchRow("SELECT `comments`.*, `catalog`.`id` as `goods_id` FROM `comments`, `catalog` WHERE `comments`.`id` = '$id' AND `catalog`.`artikul`=`comments`.`goods_artikul`"))) {
                $_SESSION['comments']['data'] = $row;
                $_SESSION['comments']['isSave'] = false;

                $returnToUrl = $this->dataTreeManager($row['goods_id']);
                $returnToUrl = '/catalog/' . $returnToUrl['links'];
                $page = '';
                if (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) {
                    $page = "?page=$_GET[page]";
                }

                $_SESSION['comments']['action'] = '/commentsactions/update/' . $row['id'];

                header("Location: $returnToUrl$page#toggle");
                exit();
            }
        }
        return false;
    }

    public function update()
    {
        if (!empty($_POST)) {
            $id = end($this->url);
            $adminMessage = $this->getVar('admin_message');
            if (is_numeric($id) && intval($id) > 0  && $this->_isAdmin() ) {

                if (($row = $this->db->fetchRow("SELECT `comments`.*, `catalog`.`id` as `goods_id`, `catalog`.`artikul` as `goods_artikul` FROM `comments`, `catalog` WHERE `comments`.`id` = '$id' AND `catalog`.`artikul`=`comments`.`goods_artikul`"))) {

                    if (isset($_SESSION['comments']['isSave'])) {
                        unset($_SESSION['comments']['isSave']);
                    }

                    $returnToUrl = $this->dataTreeManager($row['goods_id']);
                    $returnToUrl = '/catalog/' . $returnToUrl['links'];
                    $page = '';


                    if (isset($_SESSION['comments']['data'])) {
                        unset($_SESSION['comments']['data']);
                    }

                    $goodsId = $this->getVar('goods_id');
                    $fio = $this->getVar('name_1');
                    $periodOfOperation = $this->getVar('status');
                    $dignity = $this->getVar('name_2');
                    $shortcomings = $this->getVar('name_3');
                    $recommendations = $this->getVar('name_4');
                    $conclusion = $this->getVar('name_5');
                    $points = $this->getVar('rate');
                    $captchaId = $this->getVar('captcha_id');
                    $code = $this->getVar('captcha_input');
                   // $points = $this->getVar('goods_id');
                    $returnToUrl = $this->dataTreeManager($goodsId);
                    $returnToUrl = '/catalog/' . $returnToUrl['links'];

                   // var_dump($_POST); die;

                    $this->db->update('comments', array(
                        'fio' => $fio,
                        'period_of_operation' => $periodOfOperation,
                        'dignity' => $dignity,
                        'shortcomings' => $shortcomings,
                        'recommendations' => $recommendations,
                        'conclusion' => $conclusion,
                        'points' => $points,
                        'date' => date('Y-m-d')
                    ), "id=$id");

                    if (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) {
                        $page = "?page=$_GET[page]";
                    }
                    header("Location: $returnToUrl$page#toggle");
                    exit();
                }
            }
        }


        return false;
    }

    public function activate()
    {
         $id = end($this->url);

        if (is_numeric($id) && intval($id) > 0 && $this->_isAdmin() ) {
            if (($row = $this->db->fetchRow("SELECT `comments`.*, `catalog`.`id` as `goods_id` FROM `comments`, `catalog` WHERE `comments`.`id` = '$id' AND `catalog`.`artikul`=`comments`.`goods_artikul`"))) {

                if (isset($_SESSION['comments']['isSave'])) {
                    unset($_SESSION['comments']['isSave']);
                }

                $returnToUrl = $this->dataTreeManager($row['goods_id']);
                $returnToUrl = '/catalog/' . $returnToUrl['links'];
                $page = '';

                $this->db->update('comments', array('visible'=>'1'), "id=$id");

                if (isset($_SESSION['comments']['data'])) {
                    unset($_SESSION['comments']['data']);
                }

                if (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) {
                    $page = "?page=$_GET[page]";
                }

                header("Location: $returnToUrl$page#toggle");
                exit();
            }
        }
        return false;
    }
    public function deactivate()
    {
         $id = end($this->url);

        if (is_numeric($id) && intval($id) > 0 && $this->_isAdmin() ) {
            if (($row = $this->db->fetchRow("SELECT `comments`.*, `catalog`.`id` as `goods_id` FROM `comments`, `catalog` WHERE `comments`.`id` = '$id' AND `catalog`.`artikul`=`comments`.`goods_artikul`"))) {

                if (isset($_SESSION['comments']['isSave'])) {
                    unset($_SESSION['comments']['isSave']);
                }

                $returnToUrl = $this->dataTreeManager($row['goods_id']);
                $returnToUrl = '/catalog/' . $returnToUrl['links'];
                $page = '';

                $this->db->update('comments', array('visible'=>'0'), "id=$id");

                if (isset($_SESSION['comments']['data'])) {
                    unset($_SESSION['comments']['data']);
                }

                if (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) {
                    $page = "?page=$_GET[page]";
                }

                header("Location: $returnToUrl$page#toggle");
                exit();
            }
        }
        return false;
    }

}

