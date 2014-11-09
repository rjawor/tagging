<?php

class LanguagesController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        $this->Language->recursive = 0;
        $this->set('languages',
                   $this->Language->find('all')
                  );
    }
        
    public function add() {
        if ($this->request->is('post')) {
            $this->Language->create();
            if ($this->Language->save($this->request->data)) {
                $this->Session->setFlash(__('New language has been saved.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add new language.'));
        }
    }
        
    public function delete($id) {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        if ($this->Language->delete($id)) {
            $this->Session->setFlash(
                __('Language (id: %s) was successfully deleted.', h($id)),
                'flashes/success'
            );
            return $this->redirect(array('action' => 'index'));
        }
    }
}

?>
