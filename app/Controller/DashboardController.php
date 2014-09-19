<?php

App::uses('AppController', 'Controller', 'User', 'Document', 'WordAnnotationType', 'SentenceAnnotationType');

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
            $this->set('offset', $offset);            
            
            $documentModel = ClassRegistry::init('Document');
            $documentModel->recursive = 4;
            
            $documentWindow = $documentModel->findById($documentId); 
            $this->set('documentWindow', $documentWindow);
            
            $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
            $this->set('wordAnnotationTypes', $wordAnnotationTypeModel->find('all'));            

            $sentenceAnnotationTypeModel = ClassRegistry::init('SentenceAnnotationType');
            $this->set('sentenceAnnotationTypes', $sentenceAnnotationTypeModel->find('all'));            
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
