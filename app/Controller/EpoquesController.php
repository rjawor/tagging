<?php

class EpoquesController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $this->Epoque->recursive = 0;
        $this->set('epoques',
                   $this->Epoque->find('all')
                  );
    }

    public function add() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        if ($this->request->is('post')) {
            $this->Epoque->create();
            if ($this->Epoque->save($this->request->data)) {
                $this->Session->setFlash(__('New epoque has been saved.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add new epoque.'));
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

        if ($this->Epoque->delete($id)) {
            $this->Session->setFlash(
                __('Epoque (id: %s) was successfully deleted.', h($id)),
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
            throw new NotFoundException(__('Invalid epoque'));
        }

        $epoque = $this->Epoque->findById($id);
        if (!$epoque) {
            throw new NotFoundException(__('Invalid epoque'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->Epoque->id = $id;
            if ($this->Epoque->save($this->request->data)) {
                $this->Session->setFlash(__('Your epoque has been updated.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to update your epoque.'));
        }

        if (!$this->request->data) {
            $this->request->data = $epoque;
        }
    }
}

?>
