<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

App::uses('Debugger', 'Utility');
?>

<?php echo $this->Html->image("iatagger.png", array("alt" => "IA tagger")); ?>

<p>
    Welcome to the IA tagger. Use the menu bar to navigate:
    <ul>
        <li><b>Dashboard</b> - work on current document,</li>
        <li><b>Documents</b> - import and browsing of the documents,</li>
        <?php
        $user = AuthComponent::user();
        if ($user['role_id'] == 1) { ?>
        <li><b>Configuration</b> - changing the system parameters, including annotation types.</li>
        <?php }
        ?>
    </ul>
</p>
