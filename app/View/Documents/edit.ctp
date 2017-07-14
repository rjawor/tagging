<h3>Edit document</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to documents list',
                                      'title' => 'back to documents list',
                                      'url' => array('action'=>'index')
                                  )
                       );
?>
<br/><br/>

<?php
echo $this->Form->create('Document');
echo $this->Form->input('name');
echo $this->Form->input('language_id', array('type' => 'select', 'options' => $languageOptions,'empty' => false));
echo $this->Form->input('epoque_id', array('type' => 'select', 'options' => $epoqueOptions,'empty' => true));
echo $this->Form->end('Save document');
?>
