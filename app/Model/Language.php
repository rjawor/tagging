<?php

class Language extends AppModel {
    public $actsAs = array('Containable');
    public $hasMany = array('Document');
}

?>
