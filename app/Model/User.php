<?php

App::uses('AppModel', 'Model', 'Role');

class User extends AppModel {

    public $belongsTo = 'Role';

    public $validate =
    array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'The name is required'
            ),
            
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'This name is taken'
            )
        ),

        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Password is required'
            )
        )
    );
}

?>
