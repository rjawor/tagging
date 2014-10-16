<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend>
            <?php echo __('Input username and password'); ?>
        </legend>
        <?php echo $this->Form->input('username',  array('label' => 'Username'));
        echo $this->Form->input('password',  array('label' => 'Password'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Log in')); ?>

Don't have an account? <?php echo $this->Html->link('Register', array('controller' => 'users', 'action' => 'add')); ?>

</div>
