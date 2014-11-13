<h3>Edit sentence annotation type</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to sentence annotations list',
                                      'title' => 'back to sentence annotations list',
                                      'url' => array('controller'=>'sentenceAnnotationTypes','action'=>'index')
                                  )
                       );
?>
<br/><br/>
<?php
echo $this->Form->create('SentenceAnnotationType');
echo $this->Form->input('name');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save annotation type');
?>
