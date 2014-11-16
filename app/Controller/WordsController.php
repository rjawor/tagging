<?php

App::uses('AppController', 'Controller');

class WordsController extends AppController {

    public function saveWord() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $wordId = $this->request->data['wordId'];
            $text = $this->request->data['text'];
            //CakeLog::write('debug', 'saveWord: wordId='.$wordId." text=".$text);
            $data = array(
                'Word' => array(
                    'id' => $wordId,
                    'text' => $text
                )
            );
            

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
