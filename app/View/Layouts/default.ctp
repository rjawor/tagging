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

$systemDescription = __d('cake_dev', 'IA tagger');
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
		echo $this->Html->css('menu');

        echo $this->Html->script('jquery');
        echo $this->Html->script('jquery-ui');
		echo $this->Html->script('dashboard');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		
		
	?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
</head>
<body onLoad="updateDashboard()">
	<div id="container">
		<div id="header">
			<div style="float:right">
			<?php
			if (AuthComponent::user()) {
                // The user is logged in, show the logout link
                echo "Logged in as <b>".AuthComponent::user()['username']."</b> | ".$this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout'));
            } else {
                // The user is not logged in, show login link
                echo "anonymous | ".$this->Html->link('Log in', array('controller' => 'users', 'action' => 'login'));
            }
            ?>
            </div>
			<h1><?php echo $this->Html->link($systemDescription, '../'); ?></h1>
			<?php if (Configure::check('test-version')) { ?>
			        <h2>Test version</h2>
			<?php } ?>
			<?php echo $this->fetch('meta'); ?>
			 
            <div id='cssmenu'>
                <ul>
	            <li><a href='/tagging/dashboard'><span>Dashboard</span></a></li>
                    <li><a href='/tagging/documents'><span>Documents</span></a></li>
                    <li class='last'><a href='#'><span>Configuration</span></a></li>
                </ul>
            </div>            
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
