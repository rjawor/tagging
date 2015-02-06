<?php

App::uses('QueryBuilder', 'Lib');
App::uses('AnnotatedWord', 'Lib');
App::uses('Word', 'Model');
App::uses('Sentence', 'Model');
App::uses('WordAnnotationTypeChoice', 'Model');
App::uses('WordAnnotationType', 'Model');



class StatisticsController extends AppController {
    public function singleWords() {
        if ($this->request->is('post')) {
	        $params = explode(',',$this->request['data']['mainValue']);	
            $wordModel = ClassRegistry::init('Word');
            $wordModel->recursive = 2;
            $words = $wordModel->find('all', array('order'=>'case Word.split when 1 then concat(Word.stem, Word.suffix) else Word.text end','conditions'=> array('Word.id IN ('.QueryBuilder::singleWord($params).')')));
	        $annotatedWords = array();
            foreach($words as $word) {
                array_push($annotatedWords, new AnnotatedWord($word));
            }
            $this->set('words', $annotatedWords);

            $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
            $wordAnnotationTypeModel->recursive = 0;
            $this->set('wordAnnotationTypes', $wordAnnotationTypeModel->find('all'));
            
        }

    }

    public function collocations() {
        if ($this->request->is('post')) {
            $MAX_DIST = 10;
            
	        $mainParams = explode(',',$this->request['data']['mainValue']);	
	        $collocationParams = explode(',',$this->request['data']['collocationValue']);	
            
            $sentenceModel = ClassRegistry::init('Sentence');
            $rawCollocations = $sentenceModel->query(QueryBuilder::collocations($mainParams, $collocationParams));
            
            $collocations = array();
            $prevMwId = -1;
            $minDist  = -1;                    
            foreach ($rawCollocations as $rawCollocation) {
                $mwId = $rawCollocation['MW']['id'];
                $dist = $rawCollocation[0]['dist'];
                if ($mwId != $prevMwId) {
                    $minDist  = $dist;                    
                }
                if ($dist == $minDist && $dist < $MAX_DIST + 1) {
                    array_push($collocations, array(
                                                  'mwId' => $mwId,
                                                  'mwText' => $this->getWordText($rawCollocation['MW']),
                                                  'cwText' => $this->getWordText($rawCollocation['CW']),
                                                  'sepWords' => ($dist-1)
                                              )
                              );                
                }
                $prevMwId = $mwId;                
            }
                        
            
            $this->set('collocations', $collocations);
        }

    }
    
    private function getWordText($wordData) {
        if ($wordData['split']) {
            return $wordData['stem']."|".$wordData['suffix'];
        } else {
            return $wordData['text'];
        }
    }

    public function index() {
    }
}


?>
