<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to documents list',
                                      'title' => 'back to documents list',
                                      'url' => array('action'=>'index')
                                  )
                       );
?>
<br/><br/>

<?php if ($roleId < 3) { ?>
Edit mode:
<?php

if ($editMode) {
    echo $this->Html->image('switchOn.png', array(
                                          'alt' => 'switch editing off',
                                          'title' => 'switch editing off',
                                          'url' => array('action'=>'view', $document['Document']['id'], 0),
                                          'style' => 'vertical-align:middle'
                                      )
                           );
} else {
    echo $this->Html->image('switchOff.png', array(
                                          'alt' => 'switch editing on',
                                          'title' => 'switch editing on',
                                          'url' => array('action'=>'view', $document['Document']['id'], 1),
                                          'style' => 'vertical-align:middle'
                                      )
                           );

}

?>
<br/><br/>

<?php } ?>
<h3><?php echo h($document['Document']['name']); ?></h3>

<?php
$sentenceCounter = 0;
$sentenceCount = count($document['Sentence']);
foreach ($document['Sentence'] as $sentence): ?>

<p id="sentence<?php echo $sentence['id'] ?>">
    <?php echo ($sentenceCounter+1); ?>.&nbsp;
    <?php echo $this->Html->image("edit.png", array(
                "alt" => "edit",
                'url' => array('controller' => 'dashboard', 'action' => 'setCurrentDocument', $document['Document']['id'], $sentenceCounter)
                     ));
    $sentenceCounter++;
    ?>
    &nbsp;          
    
    <?php
    $wordCount = count($sentence['Word']);
    $wordIndex = 0;
    ?>
    
    <?php
        foreach ($sentence['Word'] as $word) {
            echo $word['text'];
            if (isset($word['postposition_id'])) {
                echo "&ndash;";
            } else {
                if ($wordIndex < $wordCount - 1) {
                    if ($editMode) {
                        echo "&nbsp;";
                        echo $this->Html->image('split.png', array(
                                              'alt' => 'split here',
                                              'title' => 'split here',
                                              'url' => array('action'=>'split', $document['Document']['id'], $sentence['id'], $wordIndex + 1),
                                              'style' => 'vertical-align:middle'
                                          )
                               );
                        echo "&nbsp;";
                    } else {
                        echo "&nbsp;";
                    }
                }
            }
            $wordIndex++;
        }
    
    if ($editMode && $sentenceCounter < $sentenceCount) {
        echo $this->Html->image('joinRight.png', array(
                              'alt' => 'join with next sentence',
                              'title' => 'join with next sentence',
                              'url' => array('action'=>'joinNext', $document['Document']['id'], $sentence['id']),
                              'style' => 'vertical-align:middle'
                          )
               );
        
    }
    ?>

</p>


<?php endforeach; ?>

