<?php

class HelpSectionsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow everyone to read help.
        $this->Auth->allow('index');
    }

    public function index() {
        $this->HelpSection->recursive = 0;
        $this->set('helpSections', $this->HelpSection->find('all', array('order' => 'position')));
        $this->set('roleId', $this->Auth->user()['role_id']);
    }


    public function delete($id) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrator privileges.');
            $this->redirect('/');
        }
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        $deletedSection = $this->HelpSection->findById($id);
        if ($this->HelpSection->delete($id)) {
            $this->HelpSection->updateAll(
                        array(
                            'position' => 'position-1'
                        ),
                        array(
                            'position >' => $deletedSection['HelpSection']['position']
                        )
                    );

            $this->Session->setFlash(
                __('HelpSection (id: %s) was successfully deleted.', h($id)),
                'flashes/success'
            );
            return $this->redirect(array('action' => 'index'));
        }
    }
    
    public function add($position) {
        $this->helpers = array('TinyMCE.TinyMCE');

        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrator privileges.');
            $this->redirect('/');
        }
        
        if ($this->request->is('post')) {
            $this->HelpSection->updateAll(
                        array(
                            'position' => 'position+1'
                        ),
                        array(
                            'position >=' => $position
                        )
                    );

            $this->HelpSection->create();
            $data = $this->request->data;
            $data['HelpSection']['position'] = $position;
            if ($this->HelpSection->save($data)) {
                $this->Session->setFlash(__('New section has been saved.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add new section.'));
        }

    }
    
    public function edit($id = null) {
        $this->helpers = array('TinyMCE.TinyMCE');

        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrator privileges.');
            $this->redirect('/');
        }
        if (!$id) {
            throw new NotFoundException(__('Invalid section'));
        }
                
        $helpSection = $this->HelpSection->findById($id);
        if (!$helpSection) {
            throw new NotFoundException(__('Invalid section'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->HelpSection->id = $id;
            if ($this->HelpSection->save($this->request->data)) {
                $this->Session->setFlash(__('The section has been updated.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to update the section.'));
        }

        if (!$this->request->data) {
            $this->request->data = $helpSection;
        }

    }
}

?>
