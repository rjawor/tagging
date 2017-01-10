<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

App::uses('Debugger', 'Utility');
?>

<?php echo $this->Html->image(Configure::read('SystemLogo'), array("alt" => Configure::read('SystemDescription'))); ?>

<p>
    Welcome to <?= Configure::read('SystemDescription') ?>. Use the menu bar to navigate:
    <ul>
        <li><b>Dashboard</b> - work on current document,</li>
        <li><b>Documents</b> - import and browse the documents,</li>
        <li><b>Statistics</b> - compute statistics of words and collocations,</li>
        <?php
        $user = AuthComponent::user();
        if ($user['role_id'] == 1) { ?>
        <li><b>Configuration</b> - change the system parameters, including annotation types,</li>
        <?php }
        ?>
        <li><b>Help</b> - access the system wiki.</li>
    </ul>
</p>
