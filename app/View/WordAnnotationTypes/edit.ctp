<h3>Edit word annotation type</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to word annotation types list',
                                      'title' => 'back to word annotation types list',
                                      'url' => array('action'=>'index')
                                  )
                       );
?>
<br/><br/>
<?php
echo $this->Form->create('WordAnnotationType');
echo $this->Form->input('name');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->input('strict_choices');
echo $this->Form->input('multiple_choices');
echo $this->Form->end('Save annotation type');
?>
