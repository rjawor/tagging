<?php

class Sentence extends AppModel {
    public $belongsTo = array('Document');
    public $hasMany = array('Word');
}

?>
