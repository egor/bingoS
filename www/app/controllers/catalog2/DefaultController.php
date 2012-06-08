<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DefaultController
 *
 * @author kot
 */
class DefaultController extends Controller
{
    public function indexAction() {
        print "Catalog2 index";
        return true;
    }
    
    public function testAction ($arg) {
        
        print "Catalog2 test";
        return true;
    }
}


