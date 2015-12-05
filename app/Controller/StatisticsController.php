<?php

App::uses('QueryBuilder', 'Lib');
App::uses('AnnotatedWord', 'Lib');
App::uses('DataDumper', 'Lib');
App::uses('Word', 'Model');
App::uses('Sentence', 'Model');
App::uses('Language', 'Model');
App::uses('Document', 'Model');
App::uses('WordAnnotationTypeChoice', 'Model');
App::uses('WordAnnotationType', 'Model');



class StatisticsController extends AppController {
    public function taggerdownload() {
        $this->viewClass = 'Media';
        // Download app/tmp/example.txt
        $params = array(
            'id'        => 'hindi.txt',
            'name'      => 'hindi',
            'download'  => true,
            'extension' => 'txt',
            'path'      => APP . 'tmp' . DS
        );
        
        DataDumper::dumpTagsToFile($params['path'].$params['id']);
        $this->set($params);
    }

    public function singleWords() {
        if ($this->request->is('post')) {
            $this->set('mainValue', $this->request['data']['mainValue']);
            $documentModel = ClassRegistry::init('Document');
            $documentModel->recursive = 1;
            $documents = $documentModel->find('all');
            $this->set('documents', $documents);

            $languageModel = ClassRegistry::init('Language');
            $languageModel->recursive = 1;
            $languages = $languageModel->find('all');
            $this->set('languages', $languages);

            $this->set('documentFilter', array_key_exists('documentFilter', $this->request['data'])? $this->request['data']['documentFilter'] : 0  );
            
            if (isset($this->request['data']['documentIds'])) {
    	        $documentIds = $this->request['data']['documentIds'];
            } else {
                $documentIds = array();
            }
            
            $this->set('documentIds', $documentIds);
	        $params = explode(',',$this->request['data']['mainValue']);	
            $wordModel = ClassRegistry::init('Word');
            $wordModel->recursive = 2;
            $options = array('order'=>'case Word.split when 1 then concat(Word.stem, Word.suffix) else Word.text end','conditions'=> array('Word.id IN ('.QueryBuilder::singleWordChoices($params).')'));
            if (!empty($documentIds)) {
                array_push($options['conditions'],'Word.id IN ('.QueryBuilder::singleWordDocuments($documentIds).')');
            } else {
                array_push($options['conditions'],'false');            
            }
            $words = $wordModel->find('all', $options);
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
            $this->set('mainValue', $this->request['data']['mainValue']);
            $this->set('collocationValue', $this->request['data']['collocationValue']);
            $this->set('immediate', $this->request['data']['immediate']);
            $this->set('documentFilter', array_key_exists('documentFilter', $this->request['data'])? $this->request['data']['documentFilter'] : 0  );
            if ($this->request['data']['immediate'] == 1) {
                $MAX_DIST = 1;
            } else {
                $MAX_DIST = 10;
            }

            $languageModel = ClassRegistry::init('Language');
            $languageModel->recursive = 1;
            $languages = $languageModel->find('all');
            $this->set('languages', $languages);

            $documentModel = ClassRegistry::init('Document');
            $documentModel->recursive = 1;
            $documents = $documentModel->find('all');
            $this->set('documents', $documents);
            
            if (isset($this->request['data']['documentIds'])) {
    	        $documentIds = $this->request['data']['documentIds'];
            } else {
                $documentIds = array();
            }
            
            $this->set('documentIds', $documentIds);

	        $mainParams = explode(',',$this->request['data']['mainValue']);	
	        $collocationParams = explode(',',$this->request['data']['collocationValue']);	
            
            $sentenceModel = ClassRegistry::init('Sentence');
            $rawCollocations = $sentenceModel->query(QueryBuilder::collocations($documentIds, $mainParams, $collocationParams));
            
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
                    if ($dist > 0) { // it might be that the word can be its own collocation

                        array_push($collocations, array(
                                                      'mwId' => $mwId,
                                                      'mwText' => $this->getWordText($rawCollocation['MW']),
                                                      'cwText' => $this->getWordText($rawCollocation['CW']),
                                                      'sepWords' => ($dist-1)
                                                  )
                                  );
                    }            
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
        $documentModel = ClassRegistry::init('Document');
        $documentModel->recursive = 0;
        $documents = $documentModel->find('all');
        $documentIds = array();
        foreach($documents as $document) {
            array_push($documentIds, $document['Document']['id']);
        }
        $this->set('documentIds', $documentIds);

    }

    public function generator() {
        $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
        $wordAnnotationTypes = $wordAnnotationTypeModel->find('all', array('order' => 'position'));
        $this->set('wordAnnotationTypes', $wordAnnotationTypes);

        $documentModel = ClassRegistry::init('Document');
        $documentModel->recursive = 0;
        $documents = $documentModel->find('all');
        $documentIds = array();
        foreach($documents as $document) {
            array_push($documentIds, $document['Document']['id']);
        }
        $this->set('documentIds', $documentIds);

    }

}


?>
