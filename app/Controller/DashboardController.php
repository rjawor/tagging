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
            
            $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
            $wordAnnotationTypes = $wordAnnotationTypeModel->find('all');
         
            $sentenceAnnotationTypeModel = ClassRegistry::init('SentenceAnnotationType');
            $sentenceAnnotationTypes = $sentenceAnnotationTypeModel->find('all');            

            $sentenceIndex = 0;            
            foreach ($documentWindow['Sentence'] as $sentence) {
                $wordAnnotations = array();
                
                foreach ($wordAnnotationTypes as $wordAnnotationType) {
                    $annotationObject = array('type' => $wordAnnotationType,
                                              'annotations' => array()
                                              );
                    foreach ($sentence['Word'] as $word) {
                        array_push($annotationObject['annotations'], $this->getWordAnnotation($word, $wordAnnotationType));
                    }
                    array_push($wordAnnotations, $annotationObject);
                }
                
                $documentWindow['Sentence'][$sentenceIndex]['WordAnnotations'] = $wordAnnotations;
                
                $sentenceAnnotations = array();
                foreach ($sentenceAnnotationTypes as $sentenceAnnotationType) {
                    $annotationObject = array('type' => $sentenceAnnotationType,
                                              'annotation' => $this->getSentenceAnnotation($sentence, $sentenceAnnotationType)
                                              );
                    array_push($sentenceAnnotations, $annotationObject);
                }
                
                $documentWindow['Sentence'][$sentenceIndex]['SentenceAnnotations'] = $sentenceAnnotations;

                $sentenceIndex++;
            }

            $this->set('documentWindow', $documentWindow);
            $this->set('wordAnnotationCount', count($wordAnnotationTypes));
            $this->set('sentenceAnnotationCount', count($sentenceAnnotationTypes));
            $this->set('hotKeys', array('q', 'w', 'e', 'r', 'a', 's', 'd', 'f', 'z', 'x', 'c', 'v'));
        }
    }
    
    private function getSentenceAnnotation($sentence, $sentenceAnnotationType) {
        foreach ($sentence['SentenceAnnotation'] as $sentenceAnnotation) {
            if ($sentenceAnnotation['type_id'] == $sentenceAnnotationType['SentenceAnnotationType']['id']) {
                return $sentenceAnnotation;
            }
        }
        return array();
    }

    private function getWordAnnotation($word, $wordAnnotationType) {
        foreach ($word['WordAnnotation'] as $wordAnnotation) {
            if ($wordAnnotation['type_id'] == $wordAnnotationType['WordAnnotationType']['id']) {
                return $wordAnnotation;
            }
        }
        return array();
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
