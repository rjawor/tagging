<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$systemDescription = __d('cake_dev', 'Tagowanie tekstów hinduskich');
$cakeDescription = __d('cake_dev', 'CakePHP');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $systemDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<div style="float:right">
			<?php
			if (AuthComponent::user()) {
                // The user is logged in, show the logout link
                echo "Zalogowany jako <b>".AuthComponent::user()['username']."</b> | ".$this->Html->link('Wyloguj się', array('controller' => 'users', 'action' => 'logout'));
            } else {
                // The user is not logged in, show login link
                echo "niezalogowany | ".$this->Html->link('Zaloguj się', array('controller' => 'users', 'action' => 'login'));
            }
            ?>
            </div>
			<h1><?php echo $this->Html->link($systemDescription, 'http://rjawor.vm.wmi.amu.edu.pl/tagging/'); ?></h1>
		</div>
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false, 'id' => 'cake-powered')
				);
			?>
		</div>
	</div>
</body>
</html>
