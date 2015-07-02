<?php

App::uses('Word', 'Model');
App::uses('Sentence', 'Model');

class Utils {
    public static function isSentenceStarted($sentenceId) {
        $wordModel = ClassRegistry::init('Word');
        $countArray = $wordModel->query("select count(*) as res from words inner join word_annotations on words.id = word_annotations.word_id where words.sentence_id = ".$sentenceId);
        return $countArray[0][0]['res'] > 0;
    }
    
    public static function getSentenceData($sentenceId) {
        $sentenceModel = ClassRegistry::init('Sentence');
        $sentenceModel->recursive = 3;
        $sentence = $sentenceModel->findById($sentenceId);
                    
        $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
        $wordAnnotationTypes = $wordAnnotationTypeModel->find('all', array('order' => 'position'));

        $sentenceAnnotationTypeModel = ClassRegistry::init('SentenceAnnotationType');
        $sentenceAnnotationTypes = $sentenceAnnotationTypeModel->find('all', array('order' => 'position'));            

        $sentenceIndex = 0;            

        $wordAnnotations = array();
        
        foreach ($wordAnnotationTypes as $wordAnnotationType) {
            $annotationObject = array('type' => $wordAnnotationType,
                                      'annotations' => array()
                                      );
            foreach ($sentence['Word'] as $word) {
                array_push($annotationObject['annotations'], Utils::getWordAnnotation($word, $wordAnnotationType));
            }
            array_push($wordAnnotations, $annotationObject);
        }

        $sentence['WordAnnotations'] = $wordAnnotations;
        
        $sentenceAnnotations = array();
        foreach ($sentenceAnnotationTypes as $sentenceAnnotationType) {
            $annotationObject = array('type' => $sentenceAnnotationType,
                                      'annotation' => Utils::getSentenceAnnotation($sentence, $sentenceAnnotationType)
                                      );
            array_push($sentenceAnnotations, $annotationObject);
        }
        
        $sentence['SentenceAnnotations'] = $sentenceAnnotations;
        
        return array(
            'sentence' => $sentence,
            'wordAnnotationCount' => count($wordAnnotationTypes),
            'wordAnnotationTypes' => $wordAnnotationTypes,
            'sentenceAnnotationCount' => count($sentenceAnnotationTypes)
        );
    
    }
    
    private static function getSentenceAnnotation($sentence, $sentenceAnnotationType) {
        foreach ($sentence['SentenceAnnotation'] as $sentenceAnnotation) {
            if ($sentenceAnnotation['type_id'] == $sentenceAnnotationType['SentenceAnnotationType']['id']) {
                return $sentenceAnnotation;
            }
        }
        return array();
    }

    private static function getWordAnnotation($word, $wordAnnotationType) {
        foreach ($word['WordAnnotation'] as $wordAnnotation) {
            if ($wordAnnotation['type_id'] == $wordAnnotationType['WordAnnotationType']['id']) {
                return $wordAnnotation;
            }
        }
        return array();
    }

}
?>
