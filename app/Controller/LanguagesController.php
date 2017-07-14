<?php

class LanguagesController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
        $this->Language->recursive = 0;
        $this->set('languages',
                   $this->Language->find('all')
                  );
    }
        
    public function add() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
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
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
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

    public function edit($id = null) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
        if (!$id) {
            throw new NotFoundException(__('Invalid language'));
        }

        $language = $this->Language->findById($id);
        if (!$language) {
            throw new NotFoundException(__('Invalid language'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->Language->id = $id;
            if ($this->Language->save($this->request->data)) {
                $this->Session->setFlash(__('Your language has been updated.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to update your language.'));
        }

        if (!$this->request->data) {
            $this->request->data = $language;
        }
    }
}

?>
