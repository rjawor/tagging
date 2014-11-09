<h1>Add word annotation type</h1>
<?php
echo $this->Form->create('WordAnnotationType');
echo $this->Form->input('name');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->input('strict_choices');
echo $this->Form->input('multiple_choices');

echo $this->Form->end('Save annotation type');
?>
