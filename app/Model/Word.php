<?php

class Word extends AppModel {
    public $belongsTo = array('Sentence');
    public $hasMany = array('WordAnnotation');
}

?>
