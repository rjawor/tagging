<h1>Edit sentence annotation type</h1>
<?php
echo $this->Form->create('SentenceAnnotationType');
echo $this->Form->input('name');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save annotation type');
?>
