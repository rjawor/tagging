Welcome to the configuration panel. Here you can edit the following system parameters:

<ul>
    <li style="margin:1em 0"><?= $this->Html->link('Users', array('controller' => 'users', 'action' => 'index'))?></li>
    <li style="margin:1em 0"><?= $this->Html->link('Languages', array('controller' => 'languages', 'action' => 'index'))?></li>
    <li style="margin:1em 0"><?= $this->Html->link('Word annotation levels', array('controller' => 'wordAnnotationTypes', 'action' => 'index'))?></li>
    <li style="margin:1em 0"><?= $this->Html->link('Sentence annotation levels', array('controller' => 'sentenceAnnotationTypes', 'action' => 'index'))?></li>
</ul>
