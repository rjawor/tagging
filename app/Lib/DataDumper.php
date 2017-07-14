<?php

App::uses('Sentence', 'Model');
App::uses('WordAnnotationType', 'Model');
App::uses('AnnotatedWord', 'Lib');


class DataDumper {

    public static function dumpTagsToFile($filePath) {
        $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
        $wordAnnotationTypes = $wordAnnotationTypeModel->find('all', array('order'=>'position'));


        $sentenceModel = ClassRegistry::init('Sentence');
        $sentenceModel->recursive = 3;        
        $sentences = $sentenceModel->find('all');
        $outputFile = fopen($filePath, "w");
        for($sentenceIndex=0;$sentenceIndex<count($sentences);$sentenceIndex++) {
            $sentence = $sentences[$sentenceIndex];
            
            for($wordIndex=0;$wordIndex<count($sentence['Word']);$wordIndex++) {
                $wordData = array(
                                'Word' => array(
                                              'id' => $sentence['Word'][$wordIndex]['id'],
                                              'text' => $sentence['Word'][$wordIndex]['text'],
                                              'stem' => $sentence['Word'][$wordIndex]['stem'],
                                              'suffix' => $sentence['Word'][$wordIndex]['suffix'],
                                              'split' => $sentence['Word'][$wordIndex]['split'],
                                          ),
                                'WordAnnotation' => $sentence['Word'][$wordIndex]['WordAnnotation']     
                            );
                $annotatedWord = new AnnotatedWord($wordData);
                $annotatedWordData = $annotatedWord->getSuggestionData($wordAnnotationTypes);
                fwrite($outputFile, $annotatedWordData['text']);
                if ($wordIndex<count($sentence['Word']) - 1) {
                    fwrite($outputFile, " ");
                }
            }
            if ($sentenceIndex<count($sentences) - 1) {
                fwrite($outputFile, "\n");
            }

        }
        fclose($outputFile);
    }
    
}

?>
