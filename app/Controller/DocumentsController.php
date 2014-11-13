<?php

App::uses('Language', 'Model');

class DocumentsController extends AppController {
    public $helpers = array('Html', 'Form');

    private function getLanguageOptions() {
        $languageModel = ClassRegistry::init('Language');
        $languages = $languageModel->find('all');
        $languageOptions = array();
        foreach ($languages as $language) {
            $languageOptions[$language['Language']['id']] = $language['Language']['description'].' ('.$language['Language']['code'].')';
        }
        return $languageOptions;    
    }

    public function index() {
        $this->Document->recursive = 0;
        $this->set('documents', $this->Document->find('all'));
        $this->set('languageOptions', $this->getLanguageOptions());
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
                
                $sentencePos = 0;
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $wordTexts = preg_split("/\s+/", $buffer);
                    
                    if(count($wordTexts) > 0) {
                        $sentence = array('Word' => array());
                        
                        $wordPos = 0;
                        foreach($wordTexts as $wordText) {
                            if ($wordText != '') {
                                array_push($sentence['Word'], array('position' => $wordPos, 'text' => $wordText));
                                $wordPos++;
                            }
                        }
                        
                        if (count($sentence['Word']) > 0) {                  
                            $sentence['position'] = $sentencePos;
                            $sentencePos++;
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
                __('Document (id: %s) was successfully deleted.', h($id)),
                'flashes/success'
            );
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function edit($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid document'));
        }

        $document = $this->Document->findById($id);
        if (!$document) {
            throw new NotFoundException(__('Invalid document'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->Document->id = $id;
            if ($this->Document->save($this->request->data)) {
                $this->Session->setFlash(__('Your document has been updated.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to update your document.'));
        }

        if (!$this->request->data) {
            $this->request->data = $document;
        }
        $this->set('languageOptions', $this->getLanguageOptions());
    }

}

?>
