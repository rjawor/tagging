<?php

class WordAnnotationType extends AppModel {
    public $hasMany = array('WordAnnotationTypeChoice' => array ('order' => 'WordAnnotationTypeChoice.position'));
}

?>
