<h1>Add language</h1>
<?php
echo $this->Form->create('Language');
echo $this->Form->input('code');
echo $this->Form->input('description', array('rows' => 1));
echo $this->Form->end('Save language');
?>
