<h3>Rename folder</h3>
<?php
echo $this->Form->create('Catalogue');
echo $this->Form->input('name', array('label' => 'New name for the folder'));
echo $this->Form->end('Save changes');
?>
