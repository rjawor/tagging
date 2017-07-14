<?php

class CataloguesController extends AppController {
    public function add() {
        if ($this->request->is('post')) {
            $this->Catalogue->create();
            if ($this->Catalogue->save($this->request->data)) {
                $this->Session->setFlash(__('New folder has been created.'), 'flashes/success');
            } else {
	            $this->Session->setFlash(__('Unable to create a new folder.'));
            }
            return $this->redirect(array('controller'=>'documents',  'action' => 'index'));

        }
    }

    public function edit($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid folder'));
        }

        $folder = $this->Catalogue->findById($id);
        if (!$folder) {
            throw new NotFoundException(__('Invalid folder'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->Catalogue->id = $id;
            if ($this->Catalogue->save($this->request->data)) {
                $this->Session->setFlash(__('Your folder has been updated.'), 'flashes/success');
            } else {
                $this->Session->setFlash(__('Unable to update your language.'));
            }
            return $this->redirect(array('controller'=>'documents','action' => 'index'));
        }

        if (!$this->request->data) {
            $this->request->data = $folder;
        }
    }

    public function delete($id) {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        if ($this->Catalogue->delete($id)) {
            $this->Session->setFlash(
                __('Folder (id: %s) was successfully deleted.', h($id)),
                'flashes/success'
            );
            return $this->redirect(array('controller'=>'documents','action' => 'index'));
        }
    }

}

?>
