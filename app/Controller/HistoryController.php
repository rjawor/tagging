<?php

App::uses('AppController', 'Controller');
App::uses('History', 'Lib');

class HistoryController extends AppController {
    
    public function storeOperation() {
        $this->autoRender = false;
        if ($this->request->is('post')) {            
            History::storeOperation($this->Session, $this->request->data);
        }
    }

    public function undo() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            return json_encode(History::undo($this->Session));
        }        
    }
    
    public function redo() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            return json_encode(History::redo($this->Session));
        }        
    }

    public function listOperations() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            return json_encode(History::listOperations($this->Session));
        }        
    }

    public function clear() {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            return json_encode(History::clear($this->Session));
        }        
    }

}

?>
