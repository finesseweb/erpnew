<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initViewHelpers() {
        $front = Zend_Controller_Front::getInstance();
        $front->setBaseUrl('/academic/');
    }

}