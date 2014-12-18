<?php

App::uses('AppController', 'Controller');
App::uses('WordAnnotationType', 'Model');
App::uses('AnnotatedWord', 'Lib');


class WordsController extends AppController {



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
	        
//  	        CakeLog::write('debug', 'Annotated words: '.print_r($annotatedWords,true));

            $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
            $wordAnnotationTypes = $wordAnnotationTypeModel->find('all', array('order'=>'position'));
	        $suggestionsData = array();
	        $limit = 3;
	        $awIndex = 0;
	        $suggestionsIndex = 0;
	        while($awIndex < count($annotatedWords) && $suggestionsIndex < $limit) {
	            array_push($suggestionsData, $annotatedWords[$awIndex]->getSuggestionData($wordAnnotationTypes));
	            $awIndex++;
	            $suggestionsIndex++;
	        }
	        
	        $acIndex = 0;
	        $autocompleteWords = $this->getAutocompleteWords($wordId);
	        while($acIndex < count($autocompleteWords) && $suggestionsIndex < $limit) {
	            array_push($suggestionsData, $autocompleteWords[$acIndex]->getSuggestionData($wordAnnotationTypes));
	            $acIndex++;
	            $suggestionsIndex++;
	        }
        	    
//        	CakeLog::write('debug', 'suggestionsData: '.print_r($suggestionsData, true));
	        return json_encode(array("gridX" => $gridX, "count" => count($suggestionsData), "data"=>$suggestionsData));
		}
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
        for($i=0;$i<$length;$i++) {
            if ($annotatedWord->containsAnnotation($array[$i])) {
                unset($array[$i]);
            }
        }
        $array = array_values($array);
        array_push($array, $annotatedWord);
    }
    
    private function arrayContainsAnnotation($array, $annotatedWord) {
        foreach ($array as $word) {
            if ($word->containsAnnotation($annotatedWord)) {
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
    
    private function removeWord($wordId) {
        $this->Word->recursive = 0;

        $deletedWord = $this->Word->findById($wordId);
        $this->Word->updateAll(
                            array(
                                'position' => 'position-1'
                            ),
                            array(
                                'sentence_id' => $deletedWord['Word']['sentence_id'],
                                'position >' => $deletedWord['Word']['position']
                            )
                        );
        $this->Word->delete($deletedWord['Word']['id']);
    }

 

    public function insertWord($documentId, $documentOffset, $sentenceId, $position) {
        $this->autoRender = false;
        
        $this->insertEmptyWord($sentenceId, $position);
        
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position, 1));    
    }    

    public function insertAfterWord($documentId, $documentOffset, $sentenceId, $position) {
        $this->autoRender = false;
        $this->insertEmptyWord($sentenceId, $position+1);
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position+1, 1));    
    }    

    public function deleteWord($documentId, $documentOffset, $wordId, $position) {
        $this->autoRender = false;
        $this->removeWord($wordId);
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position));    
    }    


    public function markPostposition($documentId, $documentOffset, $sentenceId, $position) {
        $this->autoRender = false;
        
        $baseWord = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position)));
        $postposition = $this->Word->find('first', array('conditions'=> array('sentence_id'=>$sentenceId, 'position'=>$position+1)));
        
        $baseWord['Word']['postposition_id'] = $postposition['Word']['id'];
        $postposition['Word']['is_postposition'] = 1;        

        $this->Word->save($baseWord);
        $this->Word->save($postposition);
        
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position));    
    }    

    public function unmarkPostposition($documentId, $documentOffset, $sentenceId, $position) {
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
        
        
        return $this->redirect(array('controller' => 'dashboard', 'action' => 'index', $documentId, $documentOffset, $position));    
    }    
}

?>
