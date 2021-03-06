<?php

App::uses('Language', 'Model', 'Sentence', 'Word');

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

    private function getEpoqueOptions() {
        $epoqueModel = ClassRegistry::init('Epoque');
        $epoques = $epoqueModel->find('all');
        $epoqueOptions = array();
        foreach ($epoques as $epoque) {
            $epoqueOptions[$epoque['Epoque']['id']] = $epoque['Epoque']['name'];
        }
        return $epoqueOptions;
    }

    public function index($folderId = 0) {
        $this->Document->recursive = 0;
        $folderModel = ClassRegistry::init('Catalogue', true);
        if ($folderId > 0) {
            $this->set('currentFolder', $folderModel->findById($folderId));
            $folderCondition = $folderId;
        } else {
            $this->set('currentFolder', null);
            $folderCondition = null;
        }

        $findOptions = array(
                            'conditions' => array('Document.catalogue_id' => $folderCondition)
                        );
        if (!empty($this->data['primary_sort'])) {
            $findOptions['order'] = array($this->parameterToColumn($this->data['primary_sort']));
            if (!empty($this->data['secondary_sort'])) {
                array_push($findOptions['order'], $this->parameterToColumn($this->data['secondary_sort']));
            }
        }

        $this->set('documents', $this->Document->find('all', $findOptions));

        $wordCountsRaw = $this->Document->query('select documents.id, count(words.id) as word_count  from documents inner join sentences on documents.id = sentences.document_id inner join words on sentences.id = words.sentence_id group by documents.id;');


        $wordCounts = array();

        foreach($wordCountsRaw as $wordCount) {

        	$wordCounts[$wordCount['documents']['id']] = $wordCount[0]['word_count'];
        }

        $this->set('wordCounts', $wordCounts);
        $this->set('folders', $folderModel->find('all'));
        $this->set('roleId', $this->Auth->user()['role_id']);
        $this->set('languageOptions', $this->getLanguageOptions());
        $this->set('epoqueOptions', $this->getEpoqueOptions());
        $this->set('primary_sort', isset($this->data['primary_sort'])? $this->data['primary_sort']:"");
        $this->set('secondary_sort', isset($this->data['secondary_sort']) ? $this->data['secondary_sort']:"");
    }

    private function parameterToColumn($param) {
        if ($param == 'name') {
            return "Document.name";
        } else if ($param == 'language') {
            return "Language.description";
        } else if ($param == 'epoque') {
            return "Epoque.name";
        }
    }

    public function view($id = null, $editMode = 0) {
        if ($editMode == 1 && $this->Auth->user()['role_id'] > 2) {
            $this->Session->setFlash('This action needs editor privileges.');
            $this->redirect(array('action' => 'index'));
        }
        if (!$id) {
            throw new NotFoundException(__('Invalid document id.'));
        }

        $this->Document->recursive = 2;
        $document = $this->Document->findById($id);
        if (!$document) {
            throw new NotFoundException(__('Invalid document'));
        }
        $this->set('document', $document);
        $this->set('editMode', $editMode);
        $this->set('roleId', $this->Auth->user()['role_id']);
    }

    public function add() {
        if ($this->Auth->user()['role_id'] > 2) {
            $this->Session->setFlash('This action needs editor privileges.');
            $this->redirect('/');
        }
        if ($this->request->is('post')) {
            $filePath = $this->data['Documents']['file']['tmp_name'];
            if ($this->data['Documents']['file']['type'] == "application/msword") {
                system("antiword -m UTF-8 -w 0 $filePath > ".$filePath."_converted");
                $filePath = $filePath."_converted";
            }
            $handle = @fopen($filePath, "r");
            if ($handle) {
                $document = array('Document' => array(
                                                    'name' => $this->data['Documents']['file']['name'],
                                                    'language_id' => $this->data['Documents']['language'],        'epoque_id' => $this->data['Documents']['epoque'],
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
                        $punct = ".,!?";
                        foreach($wordTexts as $wordText) {
                            if ($wordText != '') {
                                $prefix = "";
                                $suffix = "";
                                if (strpos($punct, $wordText[0]) !== FALSE) {
                                    $prefix = $wordText[0];
                                    $wordText = substr($wordText, 1);

                                }
                                if (strpos($punct, $wordText[strlen($wordText)-1]) !== FALSE) {
                                    $suffix = $wordText[strlen($wordText)-1];
                                    $wordText = substr($wordText, 0, strlen($wordText)-1);
                                }

                                if ($prefix != '') {
                                    array_push($sentence['Word'], array('position' => $wordPos, 'text' => $prefix));
                                    $wordPos++;
                                }
                                if ($wordText != '') {
                                    array_push($sentence['Word'], array('position' => $wordPos, 'text' => $wordText));
                                    $wordPos++;
                                }
                                if ($suffix != '') {
                                    array_push($sentence['Word'], array('position' => $wordPos, 'text' => $suffix));
                                    $wordPos++;
                                }
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
                unlink($filePath);
                #CakeLog::write('debug', print_r($document, true));
                $this->Document->saveAssociated($document, array('deep' => true));
            } else {
                $this->Session->setFlash('Error uploading file');
            }

            return $this->redirect(array('action' => 'index'));
        }
    }

    public function split($documentId, $sentenceId, $splitPos) {
        if ($this->Auth->user()['role_id'] > 2) {
            $this->Session->setFlash('This action needs editor privileges.');
            $this->redirect('/');
        }

	$sentenceModel = ClassRegistry::init('Sentence');
        $sentenceModel->recursive = 0;
        $wordModel = ClassRegistry::init('Word');
        $wordModel->recursive = 0;

        $currSentence = $sentenceModel->findById($sentenceId);

	$newSentenceId = $this->insertEmptySentence($documentId, $currSentence['Sentence']['position'] + 1);

	$wordModel->updateAll(
                        array(
                            'sentence_id' => $newSentenceId,
                            'position' => 'position-'.$splitPos
                        ),
                        array(
                            'sentence_id' => $sentenceId,
            			    'position >=' => $splitPos
                        )
                    );

        $this->redirect(array('action'=>'view', $documentId, 1, '#'=>'sentence'.$sentenceId));

    }

    private function insertEmptySentence($documentId, $position) {
        $sentenceModel = ClassRegistry::init('Sentence');
        $sentenceModel->recursive = 0;

	    $sentenceModel->updateAll(
                                array(
                                    'position' => 'position+1'
                                ),
                                array(
                                    'document_id' => $documentId,
                                    'position >=' => $position
                                )
                            );

	    $newSentence = array('Sentence'=>array('document_id' => $documentId, 'position' => $position));
	    $sentenceModel->create();
	    $sentenceModel->save($newSentence);
	    return $sentenceModel->id;
    }


    private function removeSentence($sentenceId) {
        $sentenceModel = ClassRegistry::init('Sentence');
        $sentenceModel->recursive = 0;

        $deletedSentence = $sentenceModel->findById($sentenceId);
        $sentenceModel->updateAll(
                            array(
                                'position' => 'position-1'
                            ),
                            array(
                                'document_id' => $deletedSentence['Sentence']['document_id'],
                                'position >' => $deletedSentence['Sentence']['position']
                            )
                        );
        $sentenceModel->delete($deletedSentence['Sentence']['id']);
    }

    public function joinNext($documentId, $sentenceId) {
        if ($this->Auth->user()['role_id'] > 2) {
            $this->Session->setFlash('This action needs editor privileges.');
            $this->redirect('/');
        }

        $sentenceModel = ClassRegistry::init('Sentence');
        $sentenceModel->recursive = 1;
        $wordModel = ClassRegistry::init('Word');
        $wordModel->recursive = 0;

        $currSentence = $sentenceModel->findById($sentenceId);
        $nextSentence = $sentenceModel->find('first', array('conditions' => array('document_id'=>$documentId, 'position' => $currSentence['Sentence']['position']+1)));

        if (!empty($nextSentence['SentenceAnnotation'])) {
            for($i=0;$i<count($nextSentence['SentenceAnnotation']);$i++) {
                $nextAnnotation = $nextSentence['SentenceAnnotation'][$i];

                $found = 0;
                if(!empty($currSentence['SentenceAnnotation'])) {
                    for($i=0;$i<count($currSentence['SentenceAnnotation']);$i++) {
                        if ($currSentence['SentenceAnnotation'][$i]['type_id'] == $nextAnnotation['type_id']) {
                            $currSentence['SentenceAnnotation'][$i]['text'] .= ' '.$nextAnnotation['text'];
                            $found = 1;
                        }
                    }
                }
                if (!$found) {
                    if (empty($currSentence['SentenceAnnotation'])) {
                        $currSentence['SentenceAnnotation'] = array();
                    }
                    array_push($currSentence['SentenceAnnotation'], array('type_id'=>$nextAnnotation['type_id'], 'text' => $nextAnnotation['text']));
                }
            }

            $sentenceModel->saveAll($currSentence);
        }
        $maxPosCurrSentence = $wordModel->find('first', array('fields' =>  array('max(position) AS max_pos'),
                                                              'conditions' => array('sentence_id' => $sentenceId)
                                                        )
                                              )[0]['max_pos'];
	    if (!isset($maxPosCurrSentence)) {
		    $maxPosCurrSentence=0;
	    }
        $wordModel->updateAll(
                        array(
                            'sentence_id' => $sentenceId,
                            'position' => 'position+'.$maxPosCurrSentence.'+1'
                        ),
                        array(
                            'sentence_id' => $nextSentence['Sentence']['id']
                        )
                    );

        $this->removeSentence($nextSentence['Sentence']['id']);

        $this->redirect(array('action'=>'view', $documentId, 1, '#'=>'sentence'.$sentenceId));

    }

    private function getSentenceAnnotation($sentence, $typeId) {
        if(!empty($sentence['SentenceAnnotation'])) {
            for($i=0;$i<count($sentence['SentenceAnnotation']);$i++) {
                if ($sentence['SentenceAnnotation'][$i]['type_id'] == $typeId) {
                    return $sentence['SentenceAnnotation'][$i];
                }
            }
        }

        return null;
    }

    public function delete($id) {
        if ($this->Auth->user()['role_id'] > 2) {
            $this->Session->setFlash('This action needs editor privileges.');
            $this->redirect('/');
        }
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

    public function moveToFolder($documentId, $folderId) {
        if ($this->Auth->user()['role_id'] > 2) {
            $this->Session->setFlash('This action needs editor privileges.');
            $this->redirect('/');
        }

        $document = $this->Document->findById($documentId);
        if (!$document) {
            throw new NotFoundException(__('Invalid document'));
        }

        if ($folderId > 0) {
            $folderValue = $folderId;
        } else {
            $folderValue = null;
        }

        $this->Document->id = $documentId;
        if ($this->Document->saveField('catalogue_id', $folderValue)) {
            $this->Session->setFlash(__('Your document has been moved.'), 'flashes/success');
        } else {
            $this->Session->setFlash(__('Unable to move your document.'));
        }
        return $this->redirect(array('action' => 'index'));

    }

    public function edit($id = null) {
        if ($this->Auth->user()['role_id'] > 2) {
            $this->Session->setFlash('This action needs editor privileges.');
            $this->redirect('/');
        }
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
        $this->set('epoqueOptions', $this->getEpoqueOptions());
    }

}

?>
