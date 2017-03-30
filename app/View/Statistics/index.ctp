<?php
    include('predefined_stats.php');
?>

<h2>Statistics generator</h2>
<p>
Welcome to the statistics generator. Here you can generate any kind of reports and statistics that suit your needs. Be sure to check the <a href="#">help section about statitics</a>.
</p>
<h3>Single words</h3>
<p>
In order to list words tagged in a specific way (i.e. singular nouns),<br/>
<a href="<?= Configure::read('SystemInstallationPath') ?>/statistics/single_generator">open single word statistics &gt;&gt;</a>
</p>
<h3>Collocations</h3>
<p>If you are interested in listing word collocations (such as singular nouns and definite articles) appearing in sentences,<br/>
<a href="<?= Configure::read('SystemInstallationPath') ?>/statistics/collocations_generator">open collocation statistics &gt;&gt;</a>
</p>
<h3>Proportional statistics</h3>
<p>Another option is generating number only proportional statistics, which let you count e.g. what is the percentage of singular nouns in all nouns.<br/>
<a href="<?= Configure::read('SystemInstallationPath') ?>/statistics/proportional_generator">open proportional statistics &gt;&gt;</a>
</p>
<h2>Predefined statistics</h2>
</p>You can also view one of the following predefined statistics:</p>

<ul>
        <li style="margin:1em 0">
            <form method="post" name="words_add_info" action="<?= Configure::read('SystemInstallationPath') ?>/statistics/words_add_info"></form>
            <a href="#" onclick="document.forms['words_add_info'].submit()">Words with "add info"</a>
        </li>
	    <li style="margin:1em 0">
            <form method="post" name="sentences" action="<?= Configure::read('SystemInstallationPath') ?>/statistics/sentences"></form>
    	    <a href="#" onclick="document.forms['sentences'].submit()">Sentences with "add info"</a>
   	    </li>
    <?php
        foreach($statistics as $position) {
        ?>
        <li style="margin:1em 0">
            <form method="post" name="<?php echo $position['main']['short'];?>" action="<?= Configure::read('SystemInstallationPath') ?>/statistics/singleWords">
		    <input type="hidden" name="mainValue" value="<?php echo $position['main']['value']; ?>"/>
	        </form>
    	    <a href="#" onclick="document.forms['<?php echo $position['main']['short'];?>'].submit()" ><?php echo $position['main']['desc'];?></a>
    	<?php if (count($position['collocations']) > 0) {
            echo "<ul>";
    	    foreach ($position['collocations'] as $collocation) {
    	        ?>
                <li style="margin:1em 0">
                    <form method="post" name="<?php echo $position['main']['short'].'_'.$collocation['desc']; ?>" action="<?= Configure::read('SystemInstallationPath') ?>/statistics/collocations">
		            <input type="hidden" name="mainValue" value="<?php echo $position['main']['value'];?>"/>
		            <input type="hidden" name="collocationValue" value="<?php echo $collocation['value'];?>"/>
		            <input type="hidden" name="immediate" value="<?php echo isset($collocation['immediate']);?>"/>
	                </form>
	                <a href="#" onclick="document.forms['<?php echo $position['main']['short'].'_'.$collocation['desc']; ?>'].submit()" ><?php echo $position['main']['short'].' + '.$collocation['desc']; ?></a>
	                <?php
	                    if(isset($position['including'])) {
	                ?>
                    <li>
                        <form method="post" name="<?php echo $position['main']['short'].'_'.$collocation['desc'].'_including'; ?>" action="<?= Configure::read('SystemInstallationPath') ?>/statistics/collocations">
                        <input type="hidden" name="mainValue" value="<?php echo $position['main']['value'].','.$position['including']['value'];?>"/>
                        <input type="hidden" name="collocationValue" value="<?php echo $collocation['value'];?>"/>
                        <input type="hidden" name="immediate" value="<?php echo isset($collocation['immediate']);?>"/>
                        </form>
                        including: <a href="#" onclick="document.forms['<?php echo $position['main']['short'].'_'.$collocation['desc'].'_including'; ?>'].submit()"><?php echo $position['including']['short'].' + '.$collocation['desc']; ?></a>

                    </li>
	                <?php
	                    }
	                ?>
                </li>
    	        <?php
    	    }
            echo "</ul>";
    	}?>
        <?php if (array_key_exists('proportions', $position) && count($position['proportions']) > 0) {
            echo "<ul>";
    	    foreach ($position['proportions'] as $proportion) {
    	        ?>
                <li style="margin:1em 0">
                    <form method="post" name="<?php echo $position['main']['short'].'_'.$proportion['desc']; ?>" action="<?= Configure::read('SystemInstallationPath') ?>/statistics/proportional">
		            <input type="hidden" name="mainValue" value="<?php echo $position['main']['value'];?>"/>
		            <input type="hidden" name="specificValue" value="<?php echo $proportion['value'];?>"/>
	                </form>
	                <a href="#" onclick="document.forms['<?php echo $position['main']['short'].'_'.$proportion['desc']; ?>'].submit()" ><?php echo $position['main']['short'].' including '.$proportion['desc']." (proportion)"; ?></a>
                </li>
    	        <?php
    	    }
            echo "</ul>";
    	}?>
	    </li>
        <?php
        }

    ?>

</ul>
