<?php

App::uses('AppController', 'Controller');

class SentenceAnnotationsController extends AppController {

    public function saveSentenceAnnotation($sentenceId, $sentenceAnnotationTypeId, $text) {
        $this->autoRender = false;
        //CakeLog::write('debug', 'saveSentenceAnnotation: '.$sentenceId." ".$sentenceAnnotationTypeId." ".$text);
        $sentenceAnnotation = $this->SentenceAnnotation->find('first', 
                                    array('conditions' => 
                                            array('SentenceAnnotation.type_id' => $sentenceAnnotationTypeId,
                                                  'SentenceAnnotation.sentence_id' => $sentenceId
                                            )
                                    )
                               );

        $data = array('text' => $text,
                      'sentence_id' => $sentenceId,
                      'type_id' => $sentenceAnnotationTypeId
                );

        if (count($sentenceAnnotation) == 0) {
            $this->SentenceAnnotation->create();
        } else {
            $data['id'] = $sentenceAnnotation['SentenceAnnotation']['id'];
        }
        
        $this->SentenceAnnotation->save($data);
        
    }
      
}

?>
