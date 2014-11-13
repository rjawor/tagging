<h3>Edit word annotation type choice</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to choices list',
                                      'title' => 'back to choices list',
                                      'url' => array('action'=>'index', $wordAnnotationTypeId)
                                  )
                       );
?>
<?php
echo $this->Form->create('WordAnnotationTypeChoice');
echo $this->Form->input('value');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save choice');
?>
