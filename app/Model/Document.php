<?php

class Document extends AppModel {
    public $belongsTo = array('Language', 'User', 'Epoque');
    public $hasMany = array('Sentence' => array ('order' => 'Sentence.position'));
}

?>
