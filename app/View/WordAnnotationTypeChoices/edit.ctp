<h1>Edit word annotation type choice</h1>
<?php
echo $this->Form->create('WordAnnotationTypeChoice');
echo $this->Form->input('value');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save choice');
?>
