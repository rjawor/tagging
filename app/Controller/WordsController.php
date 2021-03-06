<?php

App::uses('AppController', 'Controller');
App::uses('WordAnnotationType', 'Model');
App::uses('WordAnnotation', 'Model');
App::uses('WordAnnotationTypeChoicesWordAnnotation', 'Model');
App::uses('AnnotatedWord', 'Lib');
App::uses('History', 'Lib');


class WordsController extends AppController {


    public function getWordReferences() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $gridX = $this->request->data['gridX'];
            $gridY = $this->request->data['gridY'];
            $wordId = $this->request->data['wordId'];

            $documentModel = ClassRegistry::init('Document');
            $result = $documentModel->query("select documents.id, sentences.position as sentence_pos, words.position as word_pos from documents inner join sentences on documents.id = sentences.document_id inner join words on sentences.id = words.sentence_id where words.id = ".$wordId);


            $documentId = $result[0]['documents']['id'];
            $sentencePos = $result[0]['sentences']['sentence_pos'];

            $documentModel->recursive = 2;
            $document = $documentModel->findById($documentId);

            $documentData = array();
            foreach($document['Sentence'] as $sentence) {
                if ($sentence['position'] > $sentencePos) {
                    break;
                }
                $sentenceData = array();
                foreach($sentence['Word'] as $word) {
                    $wordText = $word['split'] ? $word['stem'].'|'.$word['suffix'] : $word['text'];
                    array_push($sentenceData, array('wordId'=>$word['id'], 'wordText' => $wordText));
                }
                array_push($documentData, $sentenceData);
            }

            return json_encode(array('document' => $documentData,'wordId'=>$wordId, 'gridX' => $gridX, 'gridY' => $gridY));
        }
    }

    public function getSuggestions() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
	        $gridX = $this->request->data['gridX'];

	        $wordId = $this->request->data['wordId'];
            $this->Word->recursive = 0;
	        $word = $this->Word->findById($wordId);

            $identicalWords = array();
//	        CakeLog::write('debug', 'Getting suggestions for word: '.print_r($word,true));
            $this->Word->recursive = 2;
	        if ($word['Word']['split']) {
	            if (isset($word['Word']['stem']) || isset($word['Word']['suffix'])) {
    	            $identicalWords = $this->Word->find('all', array('conditions' => array('id !=' => $wordId, 'stem'=>$word['Word']['stem'], 'suffix'=>$word['Word']['suffix'])));
	            }
	        } else {
	            if (isset($word['Word']['text'])) {
    	            $identicalWords = $this->Word->find('all', array('conditions' => array('id !=' => $wordId, 'text'=>$word['Word']['text'])));
    	        }
	        }

//	        CakeLog::write('debug', 'Identical words: '.print_r($identicalWords,true));

            $annotatedWords = array();
            foreach ($identicalWords as $identicalWord) {
                $annotatedWord = new AnnotatedWord($identicalWord);
                if ($annotatedWord->hasAnnotations() && !$this->arrayContainsAnnotation($annotatedWords, $annotatedWord)) {
                    $this->insertIntoWordsArray($annotatedWords, $annotatedWord);
                }
            }
            usort($annotatedWords, array($this, 'compareAnnotatedWords'));

//  	        CakeLog::write('debug', 'Annotated words: '.print_r($annotatedWords,true));

            $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
            $wordAnnotationTypes = $wordAnnotationTypeModel->find('all', array('order'=>'position'));
	        $suggestionsData = array();
	        $limit = 3;
	        $awIndex = 0;
	        $suggestionsIndex = 0;
	        while($awIndex < count($annotatedWords) && $suggestionsIndex < $limit) {
	            array_push($suggestionsData, array('wordId' => $annotatedWords[$awIndex]['word']->getId(),'suggestionCount' => $annotatedWords[$awIndex]['count'], 'suggestion'=>$annotatedWords[$awIndex]['word']->getSuggestionData($wordAnnotationTypes)));
	            $awIndex++;
	            $suggestionsIndex++;
	        }

	        $acIndex = 0;
	        $autocompleteWords = $this->getAutocompleteWords($wordId);
	        while($acIndex < count($autocompleteWords) && $suggestionsIndex < $limit) {
	            array_push($suggestionsData, array('suggestionCount' => 0, 'suggestion' => $autocompleteWords[$acIndex]->getSuggestionData($wordAnnotationTypes)));
	            $acIndex++;
	            $suggestionsIndex++;
	        }

//        	CakeLog::write('debug', 'suggestionsData: '.print_r($suggestionsData, true));
	        return json_encode(array("gridX" => $gridX, "count" => count($suggestionsData), "data"=>$suggestionsData));
		}
    }

    private function compareAnnotatedWords($a, $b) {
        if ($a['count'] == $b['count']) {
           return 0;
        }

        return ($a['count'] < $b['count']) ? 1 : -1;
    }

    private function getAutocompleteWords($wordId) {


        $rules = array(
                     array(
                        "conditions" => array(
                                            "Word.split" => "1",
                                            "Word.suffix" => "i"
                                        ),
                        "complete" => array("Word" => array(
                                                          "text" => "*|i",
                                                          "split" => 0,
                                                          "id" => -1,
                                                          "stem" => "",
                                                          "suffix" => ""
                                                      ),

                                           "WordAnnotation" => array(
                                                                   array(
                                                                       "type_id" => 2,
                                                                        "WordAnnotationTypeChoice" => array(
                                                                                                          array(
                                                                                                              "id" => 46,
                                                                                                              "value" => "LOC",
                                                                                                              "description" => "locative"
                                                                                                          )
                                                                                                      )

                                                                   ),
                                                                   array(
                                                                       "type_id" => 3,
                                                                        "WordAnnotationTypeChoice" => array(
                                                                                                          array(
                                                                                                              "id" => 78,
                                                                                                              "value" => "NOUN",
                                                                                                              "description" => "noun"
                                                                                                          )
                                                                                                      )

                                                                   )
                                                               )
                                      )
                     )
                 );

        $result = array();
        foreach ($rules as $rule) {
            $count = $this->Word->find('count', array('conditions' => array("Word.id" => $wordId)+$rule["conditions"]));
            if($count > 0) {
                array_push($result, new AnnotatedWord($rule['complete']));
            }
        }

        return $result;
    }

    private function insertIntoWordsArray(&$array, $annotatedWord) {
        $length = count($array);
        $baseCount = 0;
        for($i=0;$i<$length;$i++) {
            if ($annotatedWord->containsAnnotation($array[$i]['word'])) {
                $baseCount += $array[$i]['count'];
                unset($array[$i]);
            }
        }
        $array = array_values($array);
        array_push($array, array('word'=>$annotatedWord, 'count' => ($baseCount + 1)));
    }

    private function arrayContainsAnnotation(&$array, $annotatedWord) {
        for ($i=0;$i<count($array);$i++) {
            if ($array[$i]['word']->containsAnnotation($annotatedWord)) {
            	$array[$i]['count'] = $array[$i]['count'] + 1;
                return true;
            }
        }
        return false;
    }


    public function saveWord() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $wordId = $this->request->data['wordId'];
            $text = $this->request->data['text'];

            $split = isset($this->request->data['wordSplit']) && $this->request->data['wordSplit'] == '1';

            if ($split) {
                $data = array(
                    'Word' => array(
                        'id' => $wordId,
                        'text' => $text,
                        'split' => $split,
                        'stem' => $this->request->data['stem'],
                        'suffix' => $this->request->data['suffix']
                    )
                );
            } else {
                $data = array(
                    'Word' => array(
                        'id' => $wordId,
                        'text' => $text,
                        'split' => $split
                    )
                );

            }


            $this->Word->save($data);
        }
    }

    private function insertEmptyWord($sentenceId, $position) {
        $this->Word->recursive = 0;

	    $this->Word->updateAll(
                                array(
                                    'position' => 'position+1'
                                ),
                                array(
                                    'sentence_id' => $sentenceId,
                                    'position >=' => $position
                                )
                            );

	    $newWord = array('Word'=>array('sentence_id' => $sentenceId, 'position' => $position));
	    $this->Word->create();
	    $this->Word->save($newWord);
	    return $this->Word->id;
    }

    private function removeWord($sentenceId, $position, $preventHistory = 0) {
        $this->Word->recursive = 0;

        $deletedWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position)));
        $this->Word->updateAll(
                            array(
                                'position' => 'position-1'
                            ),
                            array(
                                'sentence_id' => $deletedWord['Word']['sentence_id'],
                                'position >' => $deletedWord['Word']['position']
                            )
                        );
        History::offsetOperations($this->Session, $deletedWord['Word']['position'] + 1, -1);
        $this->Word->delete($deletedWord['Word']['id']);
    }



    public function insertWord($documentId, $documentOffset, $sentenceId, $position, $preventHistory = 0) {
        $this->autoRender = false;
        $this->insertEmptyWord($sentenceId, $position);

        History::offsetOperations($this->Session, $position + 1, 1);
        if ($preventHistory == 0) {
            History::storeOperation($this->Session, array("type"=>"insertWord",
                                                          "documentId" => $documentId,
                                                          "documentOffset" => $documentOffset,
                                                          "sentenceId" => $sentenceId,
                                                          "position" => $position
                                                    ));
        }
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position, 1-$preventHistory));
    }

    public function insertAfterWord($documentId, $documentOffset, $sentenceId, $position, $preventHistory = 0) {
        $this->autoRender = false;
        $this->insertEmptyWord($sentenceId, $position+1);

        History::offsetOperations($this->Session, $position+2, 1);
        if ($preventHistory == 0) {
            History::storeOperation($this->Session, array("type"=>"insertWord",
                                                          "documentId" => $documentId,
                                                          "documentOffset" => $documentOffset,
                                                          "sentenceId" => $sentenceId,
                                                          "position" => $position+1
                                                    ));
        }
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position+1, 1-$preventHistory));
    }

    public function deleteWord($documentId, $documentOffset, $sentenceId, $position, $preventHistory = 0) {
        $this->autoRender = false;
        $this->removeWord($sentenceId, $position, $preventHistory);
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position));
    }


    public function markPostposition($documentId, $documentOffset, $sentenceId, $position, $preventHistory = 0) {
        $this->autoRender = false;

        $baseWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position)));
        $postposition = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position+1)));

        $baseWord['Word']['postposition_id'] = $postposition['Word']['id'];
        $postposition['Word']['is_postposition'] = 1;

        $this->Word->save($baseWord);
        $this->Word->save($postposition);

        if ($preventHistory == 0) {
            History::storeOperation($this->Session, array("type"=>"markPostposition",
                                                          "documentId" => $documentId,
                                                          "documentOffset" => $documentOffset,
                                                          "sentenceId" => $sentenceId,
                                                          "position" => $position
                                                    ));
        }
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position));
    }

    public function unmarkPostposition($documentId, $documentOffset, $sentenceId, $position, $preventHistory = 0) {
        $this->autoRender = false;

        $currentWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position)));
        if ($currentWord['Word']['is_postposition']) {
            $postposition = $currentWord;
            $baseWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position-1)));
        } else {
            $baseWord = $currentWord;
            $postposition = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position+1)));
        }

        $baseWord['Word']['postposition_id'] = null;
        $postposition['Word']['is_postposition'] = 0;

        $this->Word->save($baseWord);
        $this->Word->save($postposition);

        if ($preventHistory == 0) {
            History::storeOperation($this->Session, array("type"=>"unmarkPostposition",
                                                          "documentId" => $documentId,
                                                          "documentOffset" => $documentOffset,
                                                          "sentenceId" => $sentenceId,
                                                          "position" => $baseWord['Word']['position']
                                                    ));
        }


        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position));
    }

    public function copyFromPrev($documentId, $documentOffset, $sentenceId, $position) {
        $this->autoRender = false;
        $currentWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position)));
        $prevWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position-1)));
        $this->copyAnnotations($prevWord['Word']['id'], $currentWord['Word']['id']);

        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position, 1));
    }

    public function copyFromNext($documentId, $documentOffset, $sentenceId, $position) {
        $this->autoRender = false;
        $currentWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position)));
        $nextWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position+1)));
        $this->copyAnnotations($nextWord['Word']['id'], $currentWord['Word']['id']);

        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position, 1));
    }

    private function copyAnnotations($sourceWordId, $targetWordId) {
        $wordAnnotationModel = ClassRegistry::init('WordAnnotation');
        $wordAnnotationTypeChoicesWordAnnotationModel = ClassRegistry::init('WordAnnotationTypeChoicesWordAnnotation');

        $sourceAnnotations = $wordAnnotationModel->find('all', array('conditions'=> array('word_id'=>$sourceWordId, 'type_id !='=>1)));
        //CakeLog::write('debug',"copying from word: ".$sourceWordId." to word: ".$targetWordId." annotations: ". print_r($sourceAnnotations, true));
        if (count($sourceAnnotations > 0)) {
            $wordAnnotationModel->deleteAll(array('word_id' => $targetWordId, 'type_id !='=>1));
        }
        foreach ($sourceAnnotations as $sourceAnnotation) {
            $newAnnotation = array(
                'word_id' => $targetWordId,
                'text_value' => $sourceAnnotation['WordAnnotation']['text_value'],
                'type_id' => $sourceAnnotation['WordAnnotation']['type_id']
            );
            $wordAnnotationModel->create();
            $wordAnnotationModel->save($newAnnotation);
            $newId = $wordAnnotationModel->id;
            foreach($sourceAnnotation['WordAnnotationTypeChoice'] as $choice) {
                $newConnection = array(
                    'word_annotation_id' => $newId,
                    'word_annotation_type_choice_id' => $choice['id']
                );
                $wordAnnotationTypeChoicesWordAnnotationModel->create();
                $wordAnnotationTypeChoicesWordAnnotationModel->save($newConnection);
            }
        }

    }

}

?>
