<?php

App::uses('AppModel', 'Model');

class User extends AppModel {

    public $hasMany = array('Document');

    public $validate =
    array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Nazwa użytkownika jest wymagana'
            ),
            
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'Ta nazwa użytkownika jest zajęta'
            )
        ),

        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Hasło jest wymagane'
            )
        )
    );
}

?>
