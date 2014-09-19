<?php if(isset($documentWindow)) { ?>

<h1><strong><?php echo $documentWindow['Document']['name']; ?></strong></h1>
<br/><br/>

<input id="offset" type="hidden" value="<?php echo $offset ?>" />

<?php $sentenceCount = 0; ?>
<?php foreach ($documentWindow['Sentence'] as $sentence): ?>
    <span name="sentence" id="sentence<?php echo $sentenceCount; ?>">
        <p>
            <?php foreach ($sentence['Word'] as $word): ?>
                <?php echo $word['text'] ?>&nbsp;
            
            <?php endforeach; ?>
        </p>
        <div>
            <?php 
                $options = array("alt" => "poprzednie zdanie",
                                "title" => "poprzednie zdanie");
                if($sentenceCount > 0) {
                    $options['onClick'] = "prevSentence();".$this->Js->request(
                                array('action' => 'setCurrentDocument', $documentWindow['Document']['id'], $sentenceCount - 1),
                                array('async' => true));
                    $options['class'] = 'clickable-image';
                } else {
                    $options['class'] = 'disabled-image';                
                }
                echo $this->Html->image("up.png", $options);

                $options = array("alt" => "następne zdanie",
                                "title" => "następne zdanie");
                                
                if($sentenceCount < count($documentWindow['Sentence'])-1) {
                    $options['onClick'] = "nextSentence();".$this->Js->request(
                                array('action' => 'setCurrentDocument', $documentWindow['Document']['id'], $sentenceCount + 1),
                                array('async' => true));
                    $options['class'] = 'clickable-image';
                } else {
                    $options['class'] = 'disabled-image';                
                }
                echo $this->Html->image("down.png", $options);
            ?>

            <table>
                <tr class="words-row">
                    <td class="annotation-column"></td>
                    <?php foreach ($sentence['Word'] as $word): ?>
                        <td><?php echo $word['text'] ?></td>
                    <?php endforeach; ?>
                </tr>

                <?php foreach ($wordAnnotationTypes as $wordAnnotationType): ?>
                <tr>
                    <td class="annotation-column"><?php echo $wordAnnotationType['WordAnnotationType']['name'] ?></td>
                    <?php foreach ($sentence['Word'] as $word): ?>
                        <td></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>

                <?php foreach ($sentenceAnnotationTypes as $sentenceAnnotationType): ?>
                <tr>
                    <td class="annotation-column"><?php echo $sentenceAnnotationType['SentenceAnnotationType']['name'] ?></td>
                    <td colspan="<?php echo count($sentence['Word']) ?>"></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php $sentenceCount++; ?>
    </span>
<?php endforeach; ?>

<?php } ?>
