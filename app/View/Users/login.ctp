<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend>
            <?php echo __('Podaj nazwę użytkownika i hasło'); ?>
        </legend>
        <?php echo $this->Form->input('username',  array('label' => 'Nazwa użytkownika'));
        echo $this->Form->input('password',  array('label' => 'Hasło'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Zaloguj')); ?>

Nie masz konta? <?php echo $this->Html->link('Zarejestruj się', array('controller' => 'users', 'action' => 'add')); ?>

</div>
