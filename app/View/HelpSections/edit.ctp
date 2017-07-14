<h3>Edit section</h3>

<?php
$this->TinyMCE->editor(array('theme' => 'advanced', 'mode' => 'textareas'));

echo $this->Form->create('HelpSection');
echo $this->Form->input('text', array('rows' => 12, 'escape' => false));  
echo $this->Form->end('Save changes');
?>
