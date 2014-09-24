<?php

App::uses('AppController', 'Controller', 'Word', 'WordAnnotationTypeChoicesWordAnnotation');

class WordAnnotationsController extends AppController {

    public function saveWordTextAnnotation($wordId, $wordAnnotationTypeId, $text) {
        $this->autoRender = false;
        //CakeLog::write('debug', 'saveWordTextAnnotation: '.$wordId." ".$wordAnnotationTypeId." ".$text);
        
        if ($wordAnnotationTypeId == 0) { //modify word
            $wordModel = ClassRegistry::init('Word');
            $wordModel->save(array(
                                'id' => $wordId,
                                'text' => $text
                             ));
        } else {
            $wordAnnotation = $this->WordAnnotation->find('first', 
                                        array('conditions' => 
                                                array('WordAnnotation.type_id' => $wordAnnotationTypeId,
                                                      'WordAnnotation.word_id' => $wordId
                                                )
                                        )
                                   );

            $data = array('text_value' => $text,
                          'word_id' => $wordId,
                          'type_id' => $wordAnnotationTypeId
                    );

            if (count($wordAnnotation) == 0) {
                $this->WordAnnotation->create();
            } else {
                $data['id'] = $wordAnnotation['WordAnnotation']['id'];
            }
            
            $this->WordAnnotation->save($data);
        }
    }
      
    public function saveWordChoicesAnnotation($wordId, $wordAnnotationTypeId, $choices) {
        $this->autoRender = false;
        //CakeLog::write('debug', 'saveWordChoicesAnnotation: '.$wordId." ".$wordAnnotationTypeId." ".$choices);
        $wordAnnotation = $this->WordAnnotation->find('first', 
                                    array('conditions' => 
                                            array('WordAnnotation.type_id' => $wordAnnotationTypeId,
                                                  'WordAnnotation.word_id' => $wordId
                                            )
                                    )
                               );


        if (count($wordAnnotation) == 0) {
            $this->WordAnnotation->create();
            $data = array('word_id' => $wordId,
                          'type_id' => $wordAnnotationTypeId
                    );
            $this->WordAnnotation->save($data);
            $wordAnnotationId = $this->WordAnnotation->id;            
        } else {
            $wordAnnotationId = $wordAnnotation['WordAnnotation']['id'];
        }
        
        $data = array();
        
        $choicesArray = array();
        foreach (explode(",", $choices) as $choiceId) {
            array_push($data, array(
                                'word_annotation_id' => $wordAnnotationId,
                                'word_annotation_type_choice_id' => $choiceId
                              )
            );
        }
        $this->WordAnnotation->WordAnnotationTypeChoicesWordAnnotation->deleteAll(array('word_annotation_id' => $wordAnnotationId), false);
        $this->WordAnnotation->WordAnnotationTypeChoicesWordAnnotation->saveAll($data);
    }
}

?>
