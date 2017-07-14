<?php

class SentenceAnnotationTypesController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
        $this->SentenceAnnotationType->recursive = 0;
        $this->set('sentenceAnnotationTypes',
                   $this->SentenceAnnotationType->find('all', array('order' => 'position'))
                  );
    }
    
    public function move($position, $offset) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
        $this->SentenceAnnotationType->recursive = 0;
        $current = $this->SentenceAnnotationType->find('first', array('conditions'=> array('position'=>$position)));
        $neighbour = $this->SentenceAnnotationType->find('first', array('conditions'=> array('position'=>$position+$offset)));
        $current['SentenceAnnotationType']['position'] = $position + $offset;
        $neighbour['SentenceAnnotationType']['position'] = $position;
        if ($this->SentenceAnnotationType->save($current) && $this->SentenceAnnotationType->save($neighbour)) {
            $this->Session->setFlash(__('Successfully changed order.'), 'flashes/success');
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Unable to change order.'));
        
    }

        
    public function add() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }    
        if ($this->request->is('post')) {
            $this->SentenceAnnotationType->create();
            $data = $this->request->data;
            $maxPosition = $this->SentenceAnnotationType->find('first', array('fields' =>  array('max(position) AS max_pos')));            
            $data['SentenceAnnotationType']['position'] = $maxPosition[0]['max_pos']+1;
            if ($this->SentenceAnnotationType->save($data)) {
                $this->Session->setFlash(__('New sentence annotation type has been saved.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add new sentence annotation type.'));
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

        if ($this->SentenceAnnotationType->delete($id)) {
            $this->Session->setFlash(
                __('Sentence annotation type (id: %s) was successfully deleted.', h($id)),
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
            throw new NotFoundException(__('Invalid sentence annotation type'));
        }

        $sentenceAnnotationType = $this->SentenceAnnotationType->findById($id);
        if (!$sentenceAnnotationType) {
            throw new NotFoundException(__('Invalid sentence annotation type'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->SentenceAnnotationType->id = $id;
            if ($this->SentenceAnnotationType->save($this->request->data)) {
                $this->Session->setFlash(__('Your sentence annotation type has been updated.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to update your sentence annotation type.'));
        }

        if (!$this->request->data) {
            $this->request->data = $sentenceAnnotationType;
        }
    }

}

?>
