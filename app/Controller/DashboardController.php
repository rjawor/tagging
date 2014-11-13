<?php

App::uses('AppController', 'Controller', 'User', 'Document', 'Sentence', 'WordAnnotationType', 'SentenceAnnotationType');

class DashboardController extends AppController {

    public function index($documentId = -1, $offset = -1) {
        $contextSize = 1;

        $userModel = ClassRegistry::init('User');
        $currentUser = $userModel->findById($this->Auth->user('id'));

        if ($documentId < 0) {
            $documentId = $currentUser['User']['current_document_id'];
            $offset = $currentUser['User']['current_document_offset'];
            if (!$offset) {
                $offset = 0;
            }
        }

        if (!$documentId) {
            $this->Session->setFlash(__('Select a document in the Documents view'));
        } else {
            $sentenceModel = ClassRegistry::init('Sentence');
            $sentencesCount = $sentenceModel->find('count', array(
                                                                'conditions' => array (
                                                                                    'document_id' => $documentId
                                                                                ),
                                                                'recursive' => -1
                                                            )
                                                   );
            

            if ($offset < $contextSize) {
                $computedOffset = 0;
                $limit = $contextSize - $offset + 1;
            } else {
                $computedOffset = $offset - $contextSize;
                $limit = 2 * $contextSize + 1;
            }

            $sentencesWindow = $sentenceModel->find('all', array(
                                                                'conditions' => array (
                                                                                    'document_id' => $documentId
                                                                                ),
                                                                'recursive' => 1,
                                                                'offset' => $computedOffset,
                                                                'order' => 'position',
                                                                'limit' => $limit
                                                            )
                                                    );
                                                    
            if ($offset < $contextSize) {
                $currentSentenceIndex = $offset;
            } else {
                $currentSentenceIndex = $contextSize;
            }
           
            
            $sentenceModel->recursive = 3;
            $sentence = $sentenceModel->findById($sentencesWindow[$currentSentenceIndex]['Sentence']['id']);
                        
            $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
            $wordAnnotationTypes = $wordAnnotationTypeModel->find('all', array('order' => 'position'));

            $sentenceAnnotationTypeModel = ClassRegistry::init('SentenceAnnotationType');
            $sentenceAnnotationTypes = $sentenceAnnotationTypeModel->find('all', array('order' => 'position'));            

            $sentenceIndex = 0;            

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

            $sentence['WordAnnotations'] = $wordAnnotations;
            
            $sentenceAnnotations = array();
            foreach ($sentenceAnnotationTypes as $sentenceAnnotationType) {
                $annotationObject = array('type' => $sentenceAnnotationType,
                                          'annotation' => $this->getSentenceAnnotation($sentence, $sentenceAnnotationType)
                                          );
                array_push($sentenceAnnotations, $annotationObject);
            }
            
            $sentence['SentenceAnnotations'] = $sentenceAnnotations;


            $this->set('sentence', $sentence);
            $this->set('sentencesCount', $sentencesCount);
            $this->set('sentencesWindow', $sentencesWindow);
            $this->set('currentSentenceIndex', $currentSentenceIndex);
            $this->set('wordAnnotationCount', count($wordAnnotationTypes));
            $this->set('wordAnnotationTypes', $wordAnnotationTypes);
            $this->set('sentenceAnnotationCount', count($sentenceAnnotationTypes));
            $this->set('offset', $offset);
            $this->set('documentId', $documentId);
            $this->set('userRoleId', $currentUser['User']['role_id']);
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
        $this->autoRender = false;

        $userModel = ClassRegistry::init('User');
        $currentUser = $userModel->findById($this->Auth->user('id'));
        
        $currentUser['User']['current_document_id'] = $document_id;
        $currentUser['User']['current_document_offset'] = $offset;
        
        $userModel->save($currentUser);
        
        
        return $this->redirect(array('action' => 'index'));
    }
    
}

?>
