<p><strong><?php echo h($document['Document']['name']); ?></strong></p>

<?php
$sentenceCounter = 0;
foreach ($document['Sentence'] as $sentence): ?>

<p>
    <?php echo $this->Html->image("edit.png", array(
                "alt" => "edit",
                'url' => array('controller' => 'dashboard', 'action' => 'setCurrentDocument', $document['Document']['id'], $sentenceCounter)
                     ));
    $sentenceCounter++;
    ?>
    &nbsp;          
    <?php foreach ($sentence['Word'] as $word): ?>
        <?php echo $word['text'] ?>&nbsp;
    <?php endforeach; ?>

</p>


<?php endforeach; ?>

