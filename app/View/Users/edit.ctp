<h3>Editing user: <?php $this->request->data['User']['username'] ?></h3>
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
<?php echo $this->Form->input('role_id', array('label' => 'Role', 'type' => 'select', 'options' => $roleOptions, 'empty' => false)); ?>
<?php echo $this->Form->end(__('Change role')); ?>

