<?php
echo $this->Html->image('left.png', array(
                                      'alt' => 'back to documents list',
                                      'title' => 'back to documents list',
                                      'url' => array('action'=>'index')
                                  )
                       );
?>
<br/><br/>

<h3><?php echo h($document['Document']['name']); ?></h3>

<?php
$sentenceCounter = 0;
foreach ($document['Sentence'] as $sentence): ?>

<p>
    <?php echo ($sentenceCounter+1); ?>.&nbsp;
    <?php echo $this->Html->image("edit.png", array(
                "alt" => "edit",
                'url' => array('controller' => 'dashboard', 'action' => 'setCurrentDocument', $document['Document']['id'], $sentenceCounter)
                     ));
    $sentenceCounter++;
    ?>
    &nbsp;          
    <?php foreach ($sentence['Word'] as $word): ?>
        <?php
            if ($word['split'] == 1) {
                echo $word['stem']."&nbsp;&#124;&nbsp;".$word['suffix'];
            } else {
                echo $word['text'];
            }
        
        ?>&nbsp;
    <?php endforeach; ?>

</p>


<?php endforeach; ?>

