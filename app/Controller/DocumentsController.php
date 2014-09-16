<?php

class DocumentsController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        $this->set('documents', $this->Document->find('all'));
    }
    
    public function view($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Nieprawidłowy identyfikator dokumentu'));
        }

        $document = $this->Document->findById($id);
        if (!$document) {
            throw new NotFoundException(__('Nieprawidłowy dokument'));
        }
        $this->set('document', $document);
    }
    
    public function add() {
        if ($this->request->is('post')) {
            $this->Document->create();
            if ($this->Document->save($this->request->data)) {
                $this->Session->setFlash(__('Document zapisano.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Błąd zapisywania dokumentu'));
        }
    }
    
    public function delete($id) {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        if ($this->Document->delete($id)) {
            $this->Session->setFlash(
                __('Dokument o id: %s został usunięty.', h($id))
            );
            return $this->redirect(array('action' => 'index'));
        }
    }
}

?>
