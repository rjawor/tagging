<?php

App::uses('AppController', 'Controller');

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
                return $this->redirect($this->Auth->redirect());
            }
            $this->Session->setFlash(__('Błędna nazwa użytkownika lub hasło, spróbuj ponownie.'));
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
            throw new NotFoundException(__('Nieznany użytkownik'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Użytkownik został zarejestrowany. Użyj linku "Zaloguj się".'));
                return $this->redirect($this->Auth->redirect());
#                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('Błąd rejestrowania użytkownika.')
            );
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Nieprawidłowy użytkownik.'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Dane użytkownika zapisane.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('Błąd uaktualniania danych użytkownika.')
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
            throw new NotFoundException(__('Nieprawidłowy użytkownik.'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('Użytkownik skasowany.'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Uzytkownik nie został skasowany.'));
        return $this->redirect(array('action' => 'index'));
    }

}

?>
