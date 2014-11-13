<h3>Changing password for user: <?php echo $currentUsername?></h3>
<?php echo $this->Form->create('User'); ?>
<?php echo $this->Form->input('newPassword', array('type' => 'password', 'label' => 'New password', 'style'=>'width:200px')); ?>
<?php echo $this->Form->input('newPasswordRepeat', array('type' => 'password', 'label' => 'Repeat new password', 'style'=>'width:200px')); ?>
<?php echo $this->Form->end(__('Change password')); ?>

