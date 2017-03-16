<?php
    include('predefined_stats.php');
?>

<h3>Statistics generator</h3>
<p>If you want to generate custom statistics, use the <a href="<?= Configure::read('SystemInstallationPath') ?>/statistics/generator">statistics generator</a>.</p>

<h3>Predefined statistics</h3>
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
            <?php foreach($documentIds as $documentId) {
            ?>
                <input type="hidden" name="documentIds[]" value="<?php echo $documentId;?>" />
            <?php
            }?>
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
                    <?php foreach($documentIds as $documentId) {
                    ?>
                        <input type="hidden" name="documentIds[]" value="<?php echo $documentId;?>" />
                    <?php
                    }?>
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
                        <?php foreach($documentIds as $documentId) {
                        ?>
                            <input type="hidden" name="documentIds[]" value="<?php echo $documentId;?>" />
                        <?php
                        }?>
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
	    </li>
        <?php
        }

    ?>

</ul>
