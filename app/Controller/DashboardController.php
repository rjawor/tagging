<?php

App::uses('AppController', 'Controller', 'User', 'Document');

class DashboardController extends AppController {

    public function index() {
        $userModel = ClassRegistry::init('User');

        $currentUser = $userModel->findById($this->Auth->user('id'));

        $documentId = $currentUser['User']['current_document_id'];
        $offset = $currentUser['User']['current_document_offset'];
        
        if (!$offset) {
            $offset = 0;
        }
        
        if (!$documentId) {
            $this->Session->setFlash(__('Wybierz dokument z menu Dokumenty'));
        } else {
            $documentModel = ClassRegistry::init('Document');
            $documentModel->recursive = 3;
            
            $documentWindow = $documentModel->findById($documentId); 
            $this->set('documentWindow', $documentWindow);
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
