<?php

App::uses('AppController', 'Controller');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('Role', 'Model');

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to logout.
        $this->Auth->allow('logout');
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
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $this->User->recursive = 1;
        $this->set('users', $this->User->find('all'));
        $this->set('currentUserId', $this->Auth->user('id'));
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Unknown user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    private function getRoleOptions() {
        $roleModel = ClassRegistry::init('Role');
        $roles = $roleModel->find('all');
        $roleOptions = array();
        foreach ($roles as $role) {
            $roleOptions[$role['Role']['id']] = $role['Role']['name'];
        }
        return $roleOptions;    
    }


    public function add() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        if ($this->request->is('post')) {
            $this->User->create();
            $passwordHasher = new BlowfishPasswordHasher();
            $this->request->data['User']['password'] = $passwordHasher->hash("tagger");
            if ($this->User->save($this->request->data)) {

                $this->Session->setFlash(__('The new user is registered.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('Error registering the user.')
            );
        }
        $this->set('roleOptions', $this->getRoleOptions());

    }

    public function resetPassword($userId) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $passwordHasher = new BlowfishPasswordHasher();
        
        $user = $this->User->findById($userId);
        $user['User']['password'] = $passwordHasher->hash("tagger");
        if ($this->User->save($user)) {
            $this->Session->setFlash(
                __('Password changed.'), 'flashes/success'
            );
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('Error reseting password.'));
            return $this->redirect(array('action' => 'index'));            
        }
    }

    public function changePassword() {
        if ($this->request->is('post')) {
            
            if ($this->request->data['User']['newPassword'] == $this->request->data['User']['newPasswordRepeat']) {
                $passwordHasher = new BlowfishPasswordHasher();
                
                $user = $this->User->findById($this->Auth->user('id'));
                $user['User']['password'] = $passwordHasher->hash($this->request->data['User']['newPassword']);
                if ($this->User->save($user)) {
                    $this->Session->setFlash(
                        __('Password changed.'), 'flashes/success'
                    );
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Error changing password.'));
                    return $this->redirect(array('action' => 'index'));            
                }
            } else {
                $this->Session->setFlash(
                    __('Passwords do not match.')
                );
                return $this->redirect(array('action' => 'changePassword'));            
            }
        }
        $this->set('currentUsername', $this->Auth->user('username'));

    }
    
    

    public function edit($id = null) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user.'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('User data saved.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('Error editing user data.')
            );
        } else {
            $this->request->data = $this->User->read(null, $id);
            $this->set('roleOptions', $this->getRoleOptions());
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
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
