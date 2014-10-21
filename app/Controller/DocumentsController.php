<?php

App::uses('Language', 'Model');

class DocumentsController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        $this->Document->recursive = 0;
        $this->set('documents',
                   $this->Document->find('all', array(
                                                   'conditions' => array('user_id =' =>  $this->Auth->user('id'))
                                                )
                                        )
                  );
        $languageModel = ClassRegistry::init('Language');
        $languages = $languageModel->find('all');
        $languageOptions = array();
        foreach ($languages as $language) {
            $languageOptions[$language['Language']['id']] = $language['Language']['description'].' ('.$language['Language']['code'].')';
        }
        $this->set('languageOptions', $languageOptions);        
    }
    
    public function view($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid document id.'));
        }
        
        $this->Document->recursive = 2;
        $document = $this->Document->findById($id);
        if (!$document) {
            throw new NotFoundException(__('Invalid document'));
        }
        $this->set('document', $document);
    }
    
    public function add() {
        if ($this->request->is('post')) {
            $handle = @fopen($this->data['Documents']['file']['tmp_name'], "r");
            if ($handle) {
                $document = array('Document' => array(
                                                    'name' => $this->data['Documents']['file']['name'],
                                                    'language_id' => $this->data['Documents']['language'],
                                                    'user_id' => $this->Auth->user('id')
                                                ),
                                  'Sentence' => array()
                            );
                
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $wordTexts = preg_split("/\s+/", $buffer);
                    
                    if(count($wordTexts) > 0) {
                        $sentence = array('Word' => array());
                        foreach($wordTexts as $wordText) {
                            if ($wordText != '') {
                                array_push($sentence['Word'], array('text' => $wordText));
                            }
                        }
                        
                        if (count($sentence['Word']) > 0) {                  
                            array_push($document['Sentence'], $sentence);
                        }
                    }
                }
                if (!feof($handle)) {
                    $this->Session->setFlash('Error uploading file');
                }
                fclose($handle);
                #CakeLog::write('debug', print_r($document, true));
                $this->Document->saveAssociated($document, array('deep' => true));
            } else {
                $this->Session->setFlash('Error uploading file');
            }

            return $this->redirect(array('action' => 'index'));
        }
    }
    
    public function delete($id) {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        if ($this->Document->delete($id)) {
            $this->Session->setFlash(
                __('Dokument o id: %s został usunięty.', h($id))
            );
            return $this->redirect(array('action' => 'index'));
        }
    }
}

?>
