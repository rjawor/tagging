<h3>Add language</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to language list',
                                      'title' => 'back to language list',
                                      'url' => array('action'=>'index')
                                  )
                       );
?>
<br/><br/>
<?php
echo $this->Form->create('Language');
echo $this->Form->input('code');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save language');
?>
