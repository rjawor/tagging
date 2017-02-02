<h3>Edit epoque</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to epoque list',
                                      'title' => 'back to epoque list',
                                      'url' => array('action'=>'index')
                                  )
                       );
?>
<br/><br/>
<?php
echo $this->Form->create('Epoque');
echo $this->Form->input('name');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save epoque');
?>
