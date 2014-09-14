<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Zarejestruj się'); ?></legend>
        <?php echo $this->Form->input('username', array('label' => 'Nazwa użytkownika'));
        echo $this->Form->input('password', array('label' => 'Hasło'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Zarejestruj')); ?>
</div>
