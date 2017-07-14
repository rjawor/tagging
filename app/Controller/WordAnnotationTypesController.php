<?php

class WordAnnotationTypesController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $this->WordAnnotationType->recursive = 0;
        $this->set('wordAnnotationTypes',
                   $this->WordAnnotationType->find('all', array('order' => 'position'))
                  );
    }

    public function move($position, $offset) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $this->WordAnnotationType->recursive = 0;
        $current = $this->WordAnnotationType->find('first', array('conditions'=> array('position'=>$position)));
        $neighbour = $this->WordAnnotationType->find('first', array('conditions'=> array('position'=>$position+$offset)));
        $current['WordAnnotationType']['position'] = $position + $offset;
        $neighbour['WordAnnotationType']['position'] = $position;
        if ($this->WordAnnotationType->save($current) && $this->WordAnnotationType->save($neighbour)) {
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
            $this->WordAnnotationType->create();
            $data = $this->request->data;
            $maxPosition = $this->WordAnnotationType->find('first', array('fields' =>  array('max(position) AS max_pos', 'count(*) AS total_count')));
            if ($maxPosition[0]['total_count'] == 0) {
                $data['WordAnnotationType']['position'] = 0;                
            } else {
                $data['WordAnnotationType']['position'] = $maxPosition[0]['max_pos']+1;
            }
            if ($this->WordAnnotationType->save($data)) {
                $this->Session->setFlash(__('New word annotation type has been saved.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add new word annotation type.'));
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

        if ($this->WordAnnotationType->delete($id)) {
            $this->Session->setFlash(
                __('Word annotation type (id: %s) was successfully deleted.', h($id)),
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
            throw new NotFoundException(__('Invalid word annotation type'));
        }

        $wordAnnotationType = $this->WordAnnotationType->findById($id);
        if (!$wordAnnotationType) {
            throw new NotFoundException(__('Invalid word annotation type'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->WordAnnotationType->id = $id;
            if ($this->WordAnnotationType->save($this->request->data)) {
                $this->Session->setFlash(__('Your word annotation type has been updated.'), 'flashes/success');
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to update your word annotation type.'));
        }

        if (!$this->request->data) {
            $this->request->data = $wordAnnotationType;
        }
    }
}

?>
