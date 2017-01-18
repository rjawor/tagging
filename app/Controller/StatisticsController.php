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
    private $RESULTS_PER_PAGE = 15;

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

    public function sentences() {
        if ($this->request->is('post')) {
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




            $sentenceModel = ClassRegistry::init('Sentence');
            $sentenceModel->recursive = 1;
            $query = "select *, sentences.id as sent_id, (select group_concat(case words.split when 1 then concat(words.stem, '|', words.suffix) else words.text end order by words.position separator ' ') from words where words.sentence_id = sent_id) as sentence_text from sentences inner join sentence_annotations on sentences.id = sentence_annotations.sentence_id and type_id = 1 and sentence_annotations.text != '' inner join documents on documents.id = sentences.document_id inner join languages on languages.id = documents.language_id";
            if (count($documentIds) > 0) {
            	$query = $query." where documents.id in (".join(',', $documentIds).")";
            }
            $sentences = $sentenceModel->query($query);
            $this->set('sentences', $sentences);
        }
	}
    public function singleWords() {
        if ($this->request->is('post')) {
            if (!empty($this->request['data']['mainValue'])) {
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

                $totalCount = $wordModel->find('count', $options);
                $this->set('word_count', $totalCount);

                $page = 0;
                if (!empty($this->request['data']['page'])) {
                    $page = $this->request['data']['page'];
                }
                $this->set('page', $page);

                $this->set('totalPages', (int) ($totalCount / $this->RESULTS_PER_PAGE) + 1);
                $this->set('offset', $this->RESULTS_PER_PAGE*$page);
                $options['limit'] = $this->RESULTS_PER_PAGE;
                $options['offset'] = $this->RESULTS_PER_PAGE*$page;


                $words = $wordModel->find('all', $options);

	            $annotatedWords = array();
	            $contexts = array();


                foreach($words as $word) {
                    array_push($annotatedWords, new AnnotatedWord($word));
                    array_push($contexts, $documentModel->query(" select * from documents inner join languages on languages.id = documents.language_id inner join sentences on documents.id = sentences.document_id and sentences.id = ".$word['Word']['sentence_id']." inner join words on sentences.id = words.sentence_id order by words.position;"));
                }

                if (count($annotatedWords) != count($contexts)) {
                    die("annotatedWords is of different length than contexts");
                }

                $this->set('words', $annotatedWords);
                $this->set('contexts', $contexts);

                $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
                $wordAnnotationTypeModel->recursive = 0;
                $this->set('wordAnnotationTypes', $wordAnnotationTypeModel->find('all'));

            } else {
                $this->Session->setFlash("Empty search query, add search criteria for all searched words.");
                return $this->redirect(array('action' => 'generator'));
            }

        }

    }

    public function collocations() {
        if ($this->request->is('post')) {
            if (!empty($this->request['data']['mainValue']) &&
                !empty($this->request['data']['collocationValue'])) {
                $this->set('mainValue', $this->request['data']['mainValue']);
                $this->set('collocationValue', $this->request['data']['collocationValue']);
                $this->set('immediate', $this->request['data']['immediate']);
                $this->set('documentFilter', array_key_exists('documentFilter', $this->request['data'])? $this->request['data']['documentFilter'] : 0  );
                if ($this->request['data']['immediate'] == 1) {
                    $MAX_DIST = 1;
                } else {
                    // this is set to 0 for no limits
                    $MAX_DIST = 0;
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

                $page = 0;
                if (!empty($this->request['data']['page'])) {
                    $page = $this->request['data']['page'];
                }
                $this->set('page', $page);

                $sentenceModel = ClassRegistry::init('Sentence');

                $totalCount = $sentenceModel->query(QueryBuilder::matchingSentencesCount($documentIds, $mainParams, $collocationParams, $MAX_DIST, 0))[0][0]['total_count'];
                $this->set('totalPages', (int) ($totalCount / $this->RESULTS_PER_PAGE) + 1);
                $this->set('offset', $this->RESULTS_PER_PAGE*$page);


                $sentenceIdsRaw = $sentenceModel->query(QueryBuilder::matchingSentencesIds($documentIds, $mainParams, $collocationParams, $MAX_DIST, 0, $this->RESULTS_PER_PAGE, $this->RESULTS_PER_PAGE*$page));

                $sentencesWithCollocations = array();
                foreach ($sentenceIdsRaw as $record) {
                    $sentenceId = $record['sub']['sentence_id'];
                    array_push($sentencesWithCollocations, $sentenceModel->query(QueryBuilder::sentenceWithCollocations($sentenceId, $mainParams, $collocationParams)));
                }

                $this->set('sentencesWithCollocations', $sentencesWithCollocations);
                $this->set('sentencesTotalCount', $totalCount);
            } else {
                $this->Session->setFlash("Empty search query, add search criteria for all searched words.");
                return $this->redirect(array('action' => 'generator'));
            }
        }

    }

    public function multicollocations() {
        if ($this->request->is('post')) {
            if (!empty($this->request['data']['multiWord1Value']) &&
                !empty($this->request['data']['multiWord2Value']) &&
                !empty($this->request['data']['multiWord3Value'])) {
                $this->set('multiWord1Value', $this->request['data']['multiWord1Value']);
                $this->set('multiWord2Value', $this->request['data']['multiWord2Value']);
                $this->set('multiWord3Value', $this->request['data']['multiWord3Value']);
                $this->set('documentFilter', array_key_exists('documentFilter', $this->request['data'])? $this->request['data']['documentFilter'] : 0  );

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


	            $multiWord1Params = explode(',',$this->request['data']['multiWord1Value']);
	            $multiWord2Params = explode(',',$this->request['data']['multiWord2Value']);
	            $multiWord3Params = explode(',',$this->request['data']['multiWord3Value']);

                $sentenceModel = ClassRegistry::init('Sentence');
                $rawCollocations = $sentenceModel->query(QueryBuilder::multicollocations($documentIds, $multiWord1Params, $multiWord2Params, $multiWord3Params));

                $contexts = array();
                foreach ($rawCollocations as $rawCollocation) {
                    array_push($contexts, $documentModel->query("select * from documents inner join languages on languages.id = documents.language_id inner join sentences on documents.id = sentences.document_id and sentences.id = ".$rawCollocation['sentences']['id']." inner join words on sentences.id = words.sentence_id order by words.position;"));

                }

                if (count($rawCollocations) != count($contexts)) {
                    die("annotatedWords is of different length than contexts");
                }

                $this->set('collocations', $rawCollocations);
                $this->set('contexts', $contexts);
            } else {
                $this->Session->setFlash("Empty search query, add search criteria for all searched words.");
                return $this->redirect(array('action' => 'generator'));
            }
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
