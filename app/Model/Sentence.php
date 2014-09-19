<?php

class Sentence extends AppModel {
    public $hasMany = array('Word', 'SentenceAnnotation');
}

?>
