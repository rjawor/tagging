<?php

App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
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
    
    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
        return true;
    }
}

?>
