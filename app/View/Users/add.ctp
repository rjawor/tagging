<h3>Add new user</h3>
<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to users list',
                                      'title' => 'back to users list',
                                      'url' => array('action'=>'index')
                                  )
                       );
?>
<br/><br/>
<?php echo $this->Form->create('User'); ?>
<?php echo $this->Form->input('username', array('label' => 'Username', 'style'=>'width:150px')); ?>
<?php echo $this->Form->input('role_id', array('label' => 'Role', 'type' => 'select', 'options' => $roleOptions,'selected' => 2, 'empty' => false)); ?>

<p><strong>Notice!</strong> The default password for the newly added user is "tagger". It can be changed by the user himself.</p>
<?php echo $this->Form->end(__('Add user')); ?>

