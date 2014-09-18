<?php

App::uses('AppController', 'Controller', 'User');

class DashboardController extends AppController {

    public function index() {
        $userModel = ClassRegistry::init('User');

        $currentUser = $userModel->findById($this->Auth->user('id'));

        $document_id = $currentUser['User']['current_document_id'];
        $offset = $currentUser['User']['current_document_offset'];
        
        if (!$offset) {
            $offset = 0;
        }
        
        if (!$document_id) {
            $this->Session->setFlash(__('Wybierz dokument z menu Dokumenty'));
        } else {
            $this->set('documentWindow', 1);
        }
    }
    
    public function setCurrentDocument($document_id, $offset) {
        $userModel = ClassRegistry::init('User');
        $currentUser = $userModel->findById($this->Auth->user('id'));
        
        $currentUser['User']['current_document_id'] = $document_id;
        $currentUser['User']['current_document_offset'] = $offset;
        
        $userModel->save($currentUser);
        
        
        return $this->redirect(array('action' => 'index'));
    }
    
}

?>
