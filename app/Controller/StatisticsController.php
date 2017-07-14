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

    public function words_add_info() {
        if ($this->request->is('post')) {
            $filter = $this->prepareFilter();

            $wordModel = ClassRegistry::init('Words');

            $query = "select * from documents inner join sentences on documents.id = sentences.document_id inner join words on sentences.id = words.sentence_id inner join word_annotations on words.id = word_annotations.word_id and word_annotations.type_id = 8 and word_annotations.text_value != ''";
            $queryCount = "select count(*) as total from documents inner join sentences on documents.id = sentences.document_id inner join words on sentences.id = words.sentence_id inner join word_annotations on words.id = word_annotations.word_id and word_annotations.type_id = 8 and word_annotations.text_value != ''";
            if (!in_array('any', $filter['selectedLanguages'])) {
            	$query = $query." and documents.language_id in (".join(',', $filter['selectedLanguages']).")";
                $queryCount = $queryCount." and documents.language_id in (".join(',', $filter['selectedLanguages']).")";
            }
            if (!in_array('any', $filter['selectedEpoques'])) {
            	$query = $query." and documents.epoque_id in (".join(',', $filter['selectedEpoques']).")";
                $queryCount = $queryCount." and documents.epoque_id in (".join(',', $filter['selectedEpoques']).")";
            }
            if (!in_array('any', $filter['selectedDocuments'])) {
            	$query = $query." and documents.id in (".join(',', $filter['selectedDocuments']).")";
                $queryCount = $queryCount." and documents.id in (".join(',', $filter['selectedDocuments']).")";
            }

            $totalCount = (int) $wordModel->query($queryCount)[0][0]['total'];
            $this->set('wordCount', $totalCount);

            $page = 0;
            if (!empty($this->request['data']['page'])) {
                $page = $this->request['data']['page'];
            }
            $this->set('page', $page);

            $this->set('totalPages', (int) ($totalCount / $this->RESULTS_PER_PAGE) + 1);
            $this->set('offset', $this->RESULTS_PER_PAGE*$page);

            $query = $query . " limit ".$this->RESULTS_PER_PAGE." offset ".($this->RESULTS_PER_PAGE*$page);


            $wordsAddInfo = $wordModel->query($query);
            $this->set('wordsAddInfo', $wordsAddInfo);
        }
	}

    public function sentences() {
        if ($this->request->is('post')) {
            $filter = $this->prepareFilter();
            $sentenceModel = ClassRegistry::init('Sentence');
            $sentenceModel->recursive = 1;
            $query = "select *, sentences.id as sent_id, (select group_concat(case words.split when 1 then concat(words.stem, '|', words.suffix) else words.text end order by words.position separator ' ') from words where words.sentence_id = sent_id) as sentence_text from sentences inner join sentence_annotations on sentences.id = sentence_annotations.sentence_id and type_id = 1 and sentence_annotations.text != '' inner join documents on documents.id = sentences.document_id inner join languages on languages.id = documents.language_id where true";
            if (!in_array('any', $filter['selectedLanguages'])) {
            	$query = $query." and documents.language_id in (".join(',', $filter['selectedLanguages']).")";
            }
            if (!in_array('any', $filter['selectedEpoques'])) {
            	$query = $query." and documents.epoque_id in (".join(',', $filter['selectedEpoques']).")";
            }
            if (!in_array('any', $filter['selectedDocuments'])) {
            	$query = $query." and documents.id in (".join(',', $filter['selectedDocuments']).")";
            }
            $sentences = $sentenceModel->query($query);
            $this->set('sentences', $sentences);
        }
	}

    private function prepareFilter() {
        $languageModel = ClassRegistry::init('Language');
        $languageModel->recursive = 1;
        $languages = $languageModel->find('all');
        $this->set('languages', $languages);

        $selectedLanguages = array('any');
        if (array_key_exists('languages', $this->request['data'])) {
            $selectedLanguages = $this->request['data']['languages'];
        }
        $this->set('selectedLanguages', $selectedLanguages);

        $epoqueModel = ClassRegistry::init('Epoque');
        $epoqueModel->recursive = 1;
        $epoques = $epoqueModel->find('all');
        $this->set('epoques', $epoques);

        $selectedEpoques = array('any');
        if (array_key_exists('epoques', $this->request['data'])) {
            $selectedEpoques = $this->request['data']['epoques'];
        }
        $this->set('selectedEpoques', $selectedEpoques);

        $documentModel = ClassRegistry::init('Document');
        $documentModel->recursive = 1;
        $conditions = array();
        if (!in_array('any', $selectedLanguages)) {
            $conditions['Document.language_id'] = $selectedLanguages;
        }
        if (!in_array('any', $selectedEpoques)) {
            $conditions['Document.epoque_id'] = $selectedEpoques;
        }
        $options = array();
        if (count($conditions > 0)) {
            $options['conditions'] = $conditions;
        }
        $documents = $documentModel->find('all', $options);
        $this->set('documents', $documents);

        $selectedDocuments = array('any');
        if (array_key_exists('documents', $this->request['data'])) {
            $selectedDocuments = $this->request['data']['documents'];
        }
        $this->set('selectedDocuments', $selectedDocuments);

        if (array_key_exists('documentsScrollTop', $this->request['data'])) {
            $this->set('documentsScrollTop', $this->request['data']['documentsScrollTop']);
        } else {
            $this->set('documentsScrollTop',0);
        }

        return array(
                'selectedLanguages' => $selectedLanguages,
                'selectedEpoques' => $selectedEpoques,
                'selectedDocuments' => $selectedDocuments
        );

    }

    public function singleWords() {
        if ($this->request->is('post')) {
            if (!empty($this->request['data']['mainValue'])) {
                $this->set('mainValue', $this->request['data']['mainValue']);
                $this->set('initial', $this->request['data']['initial']);

                $filter = $this->prepareFilter();

                $wordModel = ClassRegistry::init('Word');

                $mainParams = explode(',',$this->request['data']['mainValue']);
                $initial = $this->request['data']['initial'];

                $totalCount = $wordModel->query(QueryBuilder::matchingWordsCount($mainParams, $filter, $initial))[0][0]['total_count'];
                $this->set('wordCount', $totalCount);

                $page = 0;
                if (!empty($this->request['data']['page'])) {
                    $page = $this->request['data']['page'];
                }
                $this->set('page', $page);

                $this->set('totalPages', (int) ($totalCount / $this->RESULTS_PER_PAGE) + 1);
                $this->set('offset', $this->RESULTS_PER_PAGE*$page);
                $limit = $this->RESULTS_PER_PAGE;
                $offset = $this->RESULTS_PER_PAGE*$page;


                $words = $wordModel->query(QueryBuilder::matchingWordsIds($mainParams, $filter, $initial, $limit, $offset));

	            $wordTexts = array();
	            $contexts = array();

                foreach($words as $word) {
                    array_push($wordTexts, array(
                                               'id' => $word['words']['word_id'],
                                               'text' => $word[0]['word_text']
                    ));
                    array_push($contexts, $wordModel->query("select * from documents inner join languages on languages.id = documents.language_id left join epoques on epoques.id = documents.epoque_id inner join sentences on documents.id = sentences.document_id and sentences.id = ".$word['sentences']['sentence_id']." inner join words on sentences.id = words.sentence_id order by words.position;"));
                }

                $this->set('words', $wordTexts);
                $this->set('contexts', $contexts);

            } else {
                $this->Session->setFlash("Empty search query, add search criteria for all search fields.");
                return $this->redirect(array('action' => 'single_generator'));
            }

        }

    }

    public function collocations() {
        if ($this->request->is('post')) {
            if (!empty($this->request['data']['wordValues'][0]) &&
                !empty($this->request['data']['wordValues'][1])) {
                $this->set('wordValues', $this->request['data']['wordValues']);
                $this->set('immediate', $this->request['data']['immediate']);
                if ($this->request['data']['immediate'] == 1) {
                    $MAX_DIST = 1;
                } else {
                    // this is set to 0 for no limits
                    $MAX_DIST = 0;
                }

                $filter = $this->prepareFilter();


                $page = 0;
                if (!empty($this->request['data']['page'])) {
                    $page = $this->request['data']['page'];
                }
                $this->set('page', $page);

                $sentenceModel = ClassRegistry::init('Sentence');

                $totalCount = $sentenceModel->query(QueryBuilder::matchingSentencesCount($this->request['data']['wordValues'], $filter, $MAX_DIST, 0))[0][0]['total_count'];
                $this->set('totalPages', (int) ($totalCount / $this->RESULTS_PER_PAGE) + 1);
                $this->set('offset', $this->RESULTS_PER_PAGE*$page);


                $sentenceIdsRaw = $sentenceModel->query(QueryBuilder::matchingSentencesIds($this->request['data']['wordValues'], $filter, $MAX_DIST, 0, $this->RESULTS_PER_PAGE, $this->RESULTS_PER_PAGE*$page));

                $sentencesWithCollocations = array();
                foreach ($sentenceIdsRaw as $record) {
                    $sentenceId = $record['sub']['sentence_id'];
                    array_push($sentencesWithCollocations, $sentenceModel->query(QueryBuilder::sentenceWithCollocations($sentenceId, $this->request['data']['wordValues'])));
                }

                $this->set('sentencesWithCollocations', $sentencesWithCollocations);
                $this->set('sentencesTotalCount', $totalCount);
            } else {
                $this->Session->setFlash("Incomplete search query, add search criteria at least for two words.");
                return $this->redirect(array('action' => 'collocations_generator'));
            }
        }

    }

    public function proportional() {
        if ($this->request->is('post')) {
            if (!empty($this->request['data']['mainValue']) &&
                !empty($this->request['data']['specificValue'])) {
                $this->set('mainValue', $this->request['data']['mainValue']);
                $this->set('specificValue', $this->request['data']['specificValue']);

                $initial = $this->request['data']['initial'];
                $initialSpecific = $this->request['data']['initialSpecific'];

                if ($initial != 0) {
                    if ($initialSpecific != $initial) {
                        $initial = 0;
                    }
                }

                $this->set('initial', $initial);
                $this->set('initialSpecific', $this->request['data']['initialSpecific']);

                $filter = $this->prepareFilter();
	            $mainParams = explode(',',$this->request['data']['mainValue']);

	            $specificParams = array_merge($mainParams,  explode(',',$this->request['data']['specificValue']));

                $wordModel = ClassRegistry::init('Word');
                $this->set('mainCount', $wordModel->query(QueryBuilder::matchingWordsCount($mainParams, $filter, $initial))[0][0]['total_count']);
                $this->set('specificCount', $wordModel->query(QueryBuilder::matchingWordsCount($specificParams, $filter, $initialSpecific))[0][0]['total_count']);

                $tagModel = ClassRegistry::init('WordAnnotationTypeChoice');
                $tags = array();
                $rawTags = $tagModel->find('all');
                foreach ($rawTags as $rawTag) {
                    $tags[$rawTag['WordAnnotationTypeChoice']['id']] = $rawTag['WordAnnotationTypeChoice'];
                }
                $this->set('tags', $tags);

            } else {
                $this->Session->setFlash("Empty search query, add search criteria for all search fields.");
                return $this->redirect(array('action' => 'proportional_generator'));
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
                $this->Session->setFlash("Empty search query, add search criteria for all search fields.");
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
        $wordModel = ClassRegistry::init('Word');
        $query = "select lang, count(word_id) as c from (select words.id as word_id, languages.description as lang from documents inner join languages on documents.language_id  = languages.id inner join sentences on sentences.document_id = documents.id inner join words on words.sentence_id = sentences.id inner join word_annotations on words.id = word_annotations.word_id group by words.id) as sub group by lang";
        $this->set('annotatedWordsCounts', $wordModel->query($query));
    }


    public function single_generator() {
        $this->prepareGenerator();
    }

    public function collocations_generator() {
        $this->prepareGenerator();
    }

    public function proportional_generator() {
        $this->prepareGenerator();
    }

    private function prepareGenerator() {
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
