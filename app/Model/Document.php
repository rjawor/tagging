<?php

class Document extends AppModel {
    public $actsAs = array('Containable');
    public $belongsTo = array('Language');
}

?>
