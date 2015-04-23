<?php

App::uses('Word', 'Model');

class Utils {
    public static function isSentenceStarted($sentenceId) {
        $wordModel = ClassRegistry::init('Word');
        $countArray = $wordModel->query("select count(*) as res from words inner join word_annotations on words.id = word_annotations.word_id where words.sentence_id = ".$sentenceId);
        return $countArray[0][0]['res'] > 0;
    }
}
?>
