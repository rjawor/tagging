<?php

App::uses('AppController', 'Controller');

class WordsController extends AppController {

    public function saveWord($wordId, $split, $text, $stem, $suffix) {
        $this->autoRender = false;
        //CakeLog::write('debug', 'saveWord: wordId='.$wordId." split=".$split." text=".$text." stem=".$stem." suffix=".$suffix);
        if ($split) {
            $data = array(
                'Word' => array(
                    'id' => $wordId,
                    'split' => $split,
                    'stem' => $stem,
                    'suffix' => $suffix
                )
            );
        } else {
            $data = array(
                'Word' => array(
                    'id' => $wordId,
                    'split' => $split,
                    'text' => $text
                )
            );
        
        }

        $this->Word->save($data);
        
    }    
}

?>
