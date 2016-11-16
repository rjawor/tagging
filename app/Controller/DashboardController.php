<?php

App::uses('AppController', 'Controller');
App::uses('User', 'Model');
App::uses('Document', 'Model');
App::uses('Sentence', 'Model');
App::uses('WordAnnotationType', 'Model');
App::uses('SentenceAnnotationType', 'Model');
App::uses('Utils', 'Lib');
App::uses('History', 'Lib');

class DashboardController extends AppController {

    public function index($documentId = -1, $offset = -1, $gridX = 0, $editMode = 0) {
        $contextSize = 1;

        $userModel = ClassRegistry::init('User');
        $currentUser = $userModel->findById($this->Auth->user('id'));

        if ($documentId < 0) {
            $documentId = $currentUser['User']['current_document_id'];
            $offset = $currentUser['User']['current_document_offset'];
            if (!$offset) {
                $offset = 0;
            }
            History::clear($this->Session);
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

            $sentenceData = Utils::getSentenceData($sentencesWindow[$currentSentenceIndex]['Sentence']['id']);

            $this->set('sentence', $sentenceData['sentence']);
            $this->set('wordAnnotationCount', $sentenceData['wordAnnotationCount']);
            $this->set('wordAnnotationTypes', $sentenceData['wordAnnotationTypes']);
            $this->set('sentenceAnnotationCount', $sentenceData['sentenceAnnotationCount']);


            $this->set('sentencesCount', $sentencesCount);
            $this->set('sentencesWindow', $sentencesWindow);
            $this->set('currentSentenceIndex', $currentSentenceIndex);
            $this->set('offset', $offset);
            $this->set('gridX', $gridX);
            $this->set('editMode', $editMode);
            $this->set('documentId', $documentId);
            $this->set('userRoleId', $currentUser['User']['role_id']);
            $this->set('hotKeys', array('q', 'w', 'e', 'r', 'a', 's', 'd', 'f', 'z', 'x', 'c', 'v', 't', 'y', 'u','i','o','g','h','j','k','l','b','n','m'));
        }
    }

    public function viewWord($wordId) {
        $this->autoRender = false;
        $documentModel = ClassRegistry::init('Document');
        $data = $documentModel->query("SELECT * FROM documents INNER JOIN sentences ON documents.id = sentences.document_id INNER JOIN words ON sentences.id = words.sentence_id WHERE words.id = ".$wordId);
        $documentId = $data[0]['documents']['id'];
        $offset = $data[0]['sentences']['position'];
        $gridX = $data[0]['words']['position'];

        return $this->redirect(array('action' => 'index', $documentId, $offset, $gridX));

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
