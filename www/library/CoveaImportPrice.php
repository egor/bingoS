<?php
require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

require_once PATH . 'library/Pictures.php';

require_once PATH . 'library/FormManager.php';

require_once PATH . 'library/User.php';
class CoveaImportPrice  extends User implements Main_Interface {
    public function factory() {

        if (!$this->_isAdmin()) {
            return false;
        }
        print 12;
        return true;
    }

    public function main() {
        return $this->error404();
    }
}

?>
