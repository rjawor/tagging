<?php

App::uses('AppController', 'Controller');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('add', 'logout');
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('last_login', date(DATE_ATOM));
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Session->setFlash(__('Invalid username or password, try again.'));
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Unknown user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if (isset($this->request->data['User']['password'])) {
                $passwordHasher = new BlowfishPasswordHasher();
                $this->request->data['User']['password'] = $passwordHasher->hash($this->request->data['User']['password']);
            }
            if ($this->User->save($this->request->data)) {

                $this->Session->setFlash(__('The new user is registered. Use the "Log in" link.'), 'flashes/success');
                return $this->redirect($this->Auth->redirect());
            }
            $this->Session->setFlash(
                __('Error registering the user.')
            );
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user.'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('User data saved.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('Error editing user data.')
            );
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        $this->request->onlyAllow('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user.'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted.'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Error deleting the user.'));
        return $this->redirect(array('action' => 'index'));
    }

}

?>
