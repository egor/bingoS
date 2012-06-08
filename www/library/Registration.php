<?php

require_once PATH . 'library/Abstract.php';
require_once PATH . 'library/Interface.php';

class Registration extends Main_Abstract implements Main_Interface {

    protected $options = array(
        'isActivateUser' => true, // Активировать ли пользователя после регистрации
        'goToUrl' => '/user/profile'    // Перейти на урл после регистрации
    );

    public function factory() {
        return true;
    }

    public function main() {

        $this->tpl->define_dynamic('_registration', 'registration.tpl');
        $this->tpl->define_dynamic('registration', '_registration');

        $name = $this->getVar('name', '');
        $email = $this->getVar('email', '');
        $password = $this->getVar('password', '');

        if (!empty($_POST)) {
            $this->isUserExists();
            // $this->isCaptcha();

            if (!$this->_err) {
                $this->db->insert('users', array(
                    'login' => $email,
                    'pass' => crypt($password, $this->cryptKey),
                    'name' => $name
                ));

                if ($this->options['isActivateUser']) {
                    $authAdapter = new Zend_Auth_Adapter_DbTable($this->db);
                    $authAdapter->setTableName('users');
                    $authAdapter->setIdentityColumn('login');
                    $authAdapter->setCredentialColumn('pass');

                    $authAdapter->setIdentity($email);
                    $authAdapter->setCredential(crypt($password, $this->cryptKey));

                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($authAdapter);

                    $data = $authAdapter->getResultRowObject(null, 'pass');
                    $auth->getStorage()->write($data);
                    $this->viewMessage('Здравствуйте, ' . Zend_Auth::getInstance()->getIdentity()->name . '!<meta http-equiv="refresh" content="1;URL=' . $this->options['goToUrl'] . '">');
                    
                } else {
                    $this->viewMessage('Здравствуйте, ' . Zend_Auth::getInstance()->getIdentity()->name . '!<meta http-equiv="refresh" content="1;URL=' . $this->basePath . '">');
                }
            }
        }

        if ($this->_err) {
            $this->viewErr();
        }

        if (empty($_POST) || $this->_err) {
            $captcha = new Zend_Captcha_Png(array(
                        'name' => 'cptch',
                        'wordLen' => 6,
                        'timeout' => 1800,
                    ));
            $captcha->setFont('./Zend/Captcha/Fonts/ANTIQUA.TTF');
            $captcha->setStartImage($_SERVER['DOCUMENT_ROOT'] . '/img/captcha.png');
            $id = $captcha->generate();

            $this->tpl->assign(
                    array(
                        'REGISTRATION_USER_NAME' => $name,
                        'REGISTRATION_E_MAIL' => $email,
                        'REGISTRATION_PASSWORD' => $password,
                        'CAPTCHA_ID' => $id
                    )
            );
            $this->tpl->parse('CONTENT', '.registration');
        }
        return true;
    }

}

?>
