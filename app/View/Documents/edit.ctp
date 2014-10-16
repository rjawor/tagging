
<h1>Edit document</h1>
<?php
echo $this->Form->create('Document');
echo $this->Form->input('name');
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Save dokument');
?>
