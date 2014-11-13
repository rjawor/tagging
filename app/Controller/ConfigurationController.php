<?php

class ConfigurationController extends AppController {
    public function index() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
    }
}

?>
