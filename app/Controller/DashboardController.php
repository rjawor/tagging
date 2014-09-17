<?php

App::uses('AppController', 'Controller', 'User');

class DashboardController extends AppController {


    public function index() {
        $userModel = ClassRegistry::init('User');
        $currentUser = $userModel->findById($this->Auth->user('id'));
        die(print_r($currentUser, true));
        /*
        if(!$document_id) {
            $this->Session->setFlash(__('Wybierz dokument z menu Dokumenty'));
        }*/
    }
    
}

?>
