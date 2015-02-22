<?php

    $standardCollocations = array(
                                  array(
                                      'desc' => 'A(INS)',
                                      'value' => '87,42'
                                  ),
                                  array(
                                      'desc' => 'A(NOM)',
                                      'value' => '87,51'
                                  ),
                                  array(
                                      'desc' => 'A(OBL)',
                                      'value' => '87,52'
                                  ),
                                  array(
                                      'desc' => 'O(NOM)',
                                      'value' => '89,51'
                                  ),
                                  array(
                                      'desc' => 'O(ACC)',
                                      'value' => '89,6'
                                  ),
                                  array(
                                      'desc' => 'O(OBL)',
                                      'value' => '89,52'
                                  )
                              );

    $statistics = array(
        array(
            'main' => array(
                          'desc' => 'Converbs (CVB on the levels "Grammar" and "POS")',
                          'short' => 'CVB',
                          'value' => '21,85'
                      ),
            'collocations' => $standardCollocations
        ),
        array(
            'main' => array(
                          'desc' => 'Participles (PTCP on the levels "Grammar" and "POS")',
                          'short' => 'PTCP(PTCP)',
                          'value' => '65,81'
                      ),
            'collocations' => $standardCollocations
        ),
        array(
            'main' => array(
                          'desc' => 'Verb participles (PTCP on the level "Grammar", V on "SYNTAX")',
                          'short' => 'PTCP(V)',
                          'value' => '65,90'
                      ),
            'collocations' => $standardCollocations
        ),
        array(
            'main' => array(
                          'desc' => 'Infinitives (INF on the levels "Grammar" and "POS")',
                          'short' => 'INF',
                          'value' => '41,86'
                      ),
            'collocations' => $standardCollocations
        ),
        array(
            'main' => array(
                          'desc' => 'Verbs (VRB on the level "POS", V on the level "syntax")',
                          'short' => 'VRB(V)',
                          'value' => '80,90'
                      ),
            'including' => array(
                          'short' => 'PPP(V)',
                          'value' => '57,90'
                      ),
            'collocations' => $standardCollocations
        )
        
    );
?>

<!--Here you can download the <a href="/tagging/statistics/taggerdownload">complete tagged text</a>-->

Welcome to the statistics panel. Here you can view the following statistics:



<ul>
    <?php
        foreach($statistics as $position) {
        ?>
        <li style="margin:1em 0">
            <form method="post" name="<?php echo $position['main']['short'];?>" action="/tagging/statistics/singleWords">
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
                    <form method="post" name="<?php echo $position['main']['short'].'_'.$collocation['desc']; ?>" action="/tagging/statistics/collocations">
		            <input type="hidden" name="mainValue" value="<?php echo $position['main']['value'];?>"/>
		            <input type="hidden" name="collocationValue" value="<?php echo $collocation['value'];?>"/>
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
                        <form method="post" name="<?php echo $position['main']['short'].'_'.$collocation['desc'].'_including'; ?>" action="/tagging/statistics/collocations">
                        <input type="hidden" name="mainValue" value="<?php echo $position['main']['value'].','.$position['including']['value'];?>"/>
                        <input type="hidden" name="collocationValue" value="<?php echo $collocation['value'];?>"/>
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
