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
                          'desc' => 'Converbs (CVB on the level "Grammar")',
                          'short' => 'CVB',
                          'value' => '21'
                      ),
            'collocations' => $standardCollocations
        ),
        array(
            'main' => array(
                          'desc' => 'Converbs...',
                          'short' => 'CVB',
                          'value' => '21'
                      ),
            'collocations' => array(
                                  array(
                                      'desc' => 'immediately VRB',
                                      'value' => '80',
                                      'immediate' => true
                                  )
                              )
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
                          'desc' => 'Infinitives (INF on the level "POS")',
                          'short' => 'INF',
                          'value' => '41'
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

<h3>Statistics generator</h3>
<p>If you want to generate custom statistics, use the <a href="/tagging/statistics/generator">statistics generator</a>.</p>

<h3>Predefined statistics</h3>
</p>You can also view one of the following predefined statistics:</p>

<ul>
	    <li style="margin:1em 0">
            <form method="post" name="sentences" action="/tagging/statistics/sentences"></form>
    	    <a href="#" onclick="document.forms['sentences'].submit()">Sentences with "add info"</a>
   	    </li>
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
