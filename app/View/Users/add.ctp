<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Register'); ?></legend>
        <?php echo $this->Form->input('username', array('label' => 'Username'));
        echo $this->Form->input('password', array('label' => 'Password'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Register')); ?>
</div>
