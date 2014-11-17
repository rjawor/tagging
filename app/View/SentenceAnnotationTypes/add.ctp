<h3>Add sentence annotation level</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to sentence annotation levels list',
                                      'title' => 'back to sentence annotation levels list',
                                      'url' => array('controller'=>'sentenceAnnotationTypes','action'=>'index')
                                  )
                       );
?>
<br/><br/>
<?php
echo $this->Form->create('SentenceAnnotationType');
echo $this->Form->input('name');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save annotation level');
?>
