<?php

class WordAnnotation extends AppModel {
    public $hasAndBelongsToMany = array('WordAnnotationTypeChoice' => array ('order' => 'WordAnnotationTypeChoice.value'));
}

?>
