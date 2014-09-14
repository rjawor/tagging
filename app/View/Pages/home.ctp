<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

App::uses('Debugger', 'Utility');
?>

<div>
<?php
if (AuthComponent::user()) {
    // The user is logged in, show the logout link
    echo $this->Html->link('Log out', array('controller' => 'users', 'action' => 'logout'));
} else {
    // The user is not logged in, show login link
    echo $this->Html->link('Log in', array('controller' => 'users', 'action' => 'login'));
}
?>
</div>
