<h3>Edit tag</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to tags list',
                                      'title' => 'back to tags list',
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
