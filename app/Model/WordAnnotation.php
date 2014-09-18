<?php

class WordAnnotation extends AppModel {
    public $belongsTo = array('Word');
    public $hasAndBelongsToMany = array('WordAnnotationChoice');
}

?>
