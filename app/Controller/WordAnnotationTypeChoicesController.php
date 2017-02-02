<?php

App::uses('AppController', 'Controller', 'WordAnnotationType');

class WordAnnotationTypeChoicesController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index($wordAnnotationTypeId) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $wordAnnotationTypeModel = ClassRegistry::init('WordAnnotationType');
        $wordAnnotationTypeModel->recursive = 0;
        $wordAnnotationType = $wordAnnotationTypeModel->findById($wordAnnotationTypeId);

        $this->WordAnnotationTypeChoice->recursive = 0;
        $wordAnnotationTypeChoices = $this->WordAnnotationTypeChoice->find('all', array(
                                  'conditions' => array('word_annotation_type_id' => $wordAnnotationTypeId),
                                  'order' =>'position'));
        $this->set('wordAnnotationTypeChoices', $wordAnnotationTypeChoices);
        $this->set('wordAnnotationType', $wordAnnotationType);
    }

    public function add($wordAnnotationTypeId) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        if ($this->request->is('post')) {
            $this->WordAnnotationTypeChoice->create();
            $data = $this->request->data;
            $maxPosition = $this->WordAnnotationTypeChoice->find('first', array(
                                            'fields' =>  array('max(position) AS max_pos', 'count(*) AS total_count'),
                                            'conditions' => array('word_annotation_type_id' => $wordAnnotationTypeId)
                                        ));
            if ($maxPosition[0]['total_count'] == 0) {
                $data['WordAnnotationTypeChoice']['position'] = 0;
            } else {
                $data['WordAnnotationTypeChoice']['position'] = $maxPosition[0]['max_pos']+1;
            }
            $data['WordAnnotationTypeChoice']['word_annotation_type_id'] = $wordAnnotationTypeId;
            if ($this->WordAnnotationTypeChoice->save($data)) {
                $this->Session->setFlash(__('New word annotation type choice has been saved.'), 'flashes/success');
                return $this->redirect(array('action' => 'index', $wordAnnotationTypeId));
            }
            $this->Session->setFlash(__('Unable to add new word annotation type choice.'));
        }
        $this->set('wordAnnotationTypeId', $wordAnnotationTypeId);
    }

    public function move($wordAnnotationTypeId, $position, $offset) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        $this->WordAnnotationTypeChoice->recursive = 0;
        $current = $this->WordAnnotationTypeChoice->find('first', array('conditions'=> array('word_annotation_type_id'=>$wordAnnotationTypeId, 'position'=>$position)));
        $neighbour = $this->WordAnnotationTypeChoice->find('first', array('conditions'=> array('word_annotation_type_id'=>$wordAnnotationTypeId,'position'=>$position+$offset)));
        $current['WordAnnotationTypeChoice']['position'] = $position + $offset;
        $neighbour['WordAnnotationTypeChoice']['position'] = $position;
        if ($this->WordAnnotationTypeChoice->save($current) && $this->WordAnnotationTypeChoice->save($neighbour)) {
            $this->Session->setFlash(__('Successfully changed order.'), 'flashes/success');
            return $this->redirect(array('action' => 'index', $wordAnnotationTypeId));
        }
        $this->Session->setFlash(__('Unable to change order.'));

    }


    public function delete($wordAnnotationTypeId, $id) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        if ($this->WordAnnotationTypeChoice->delete($id)) {
            $this->Session->setFlash(
                __('Word annotation type choice (id: %s) was successfully deleted.', h($id)),
                'flashes/success'
            );
            return $this->redirect(array('action' => 'index', $wordAnnotationTypeId));
        }
    }

    public function edit($wordAnnotationTypeId, $id = null) {
        if ($this->Auth->user()['role_id'] != 1) {
            $this->Session->setFlash('This action needs administrative privileges.');
            $this->redirect('/');
        }
        if (!$id) {
            throw new NotFoundException(__('Invalid word annotation type choice'));
        }

        $wordAnnotationTypeChoice = $this->WordAnnotationTypeChoice->findById($id);
        if (!$wordAnnotationTypeChoice) {
            throw new NotFoundException(__('Invalid word annotation type choice'));
        }

        if ($this->request->is(array('post', 'put'))) {
            $this->WordAnnotationTypeChoice->id = $id;
            if ($this->WordAnnotationTypeChoice->save($this->request->data)) {
                $this->Session->setFlash(__('Your word annotation type choice has been updated.'), 'flashes/success');
                return $this->redirect(array('action' => 'index', $wordAnnotationTypeId));
            }
            $this->Session->setFlash(__('Unable to update your word annotation type choice.'));
        }

        if (!$this->request->data) {
            $this->request->data = $wordAnnotationTypeChoice;
        }
        $this->set('wordAnnotationTypeId', $wordAnnotationTypeId);
    }
}

?>
