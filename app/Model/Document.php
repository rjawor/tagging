<?php

class Document extends AppModel {
    public $belongsTo = array('Language', 'User');
    public $hasMany = array('Sentence');
}

?>
