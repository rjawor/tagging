<?php

class Sentence extends AppModel {
    public $hasMany = array('Word'=> array ('order' => 'Word.position'), 'SentenceAnnotation');
}

?>
