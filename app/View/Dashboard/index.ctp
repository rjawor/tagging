<?php if(isset($documentWindow)) { ?>

<h1><strong><?php echo $documentWindow['Document']['name']; ?></strong></h1>
<br/><br/>

<input id="offset" type="hidden" value="<?php echo $offset ?>" />
<input id="document-id" type="hidden" value="<?php echo $documentWindow['Document']['id'] ?>" />

<?php $sentenceCount = 0; ?>
<?php foreach ($documentWindow['Sentence'] as $sentence): ?>
    <div name="sentence" id="sentence<?php echo $sentenceCount; ?>">
        <input type="hidden" id="sentence<?php echo $sentenceCount; ?>-word-count" value="<?php echo count($sentence['Word']) ?>" />
        <input type="hidden" id="sentence<?php echo $sentenceCount; ?>-word-annotation-count" value="<?php echo count($wordAnnotationTypes) + 1 ?>" />
        <input type="hidden" id="sentence<?php echo $sentenceCount; ?>-sentence-annotation-count" value="<?php echo count($sentenceAnnotationTypes) ?>" />
        <input type="hidden" id="sentence<?php echo $sentenceCount; ?>-grid-x" value="0" />
        <input type="hidden" id="sentence<?php echo $sentenceCount; ?>-grid-y" value="0" />
        <input type="hidden" id="sentence<?php echo $sentenceCount; ?>-edit-mode" value="0" />
        <p>
            <?php echo ($sentenceCount+1); ?>.&nbsp;
            <?php foreach ($sentence['Word'] as $word): ?>
                <?php echo $word['text'] ?>&nbsp;
            
            <?php endforeach; ?>
        </p>
        <div>
            <?php 
                $options = array("alt" => "poprzednie zdanie",
                                "title" => "poprzednie zdanie",
                                "onClick" => "prevSentence();");
                if ($sentenceCount > 0) {
                    $options['class'] = 'clickable-image';
                } else {
                    $options['class'] = 'disabled-image';                
                }
                echo $this->Html->image("up.png", $options);

                $options = array("alt" => "następne zdanie",
                                "title" => "następne zdanie",
                                "onClick" => "nextSentence();");
                                
                if($sentenceCount < count($documentWindow['Sentence'])-1) {
                    $options['class'] = 'clickable-image';
                } else {
                    $options['class'] = 'disabled-image';                
                }
                echo $this->Html->image("down.png", $options);
            ?>
            <hr/>
            <table>
                
                <tr class="words-row">
                    <td class="annotation-column"><?php echo ($sentenceCount + 1)?>.</td>
                    <?php
                        $wordCount = 0;
                        foreach ($sentence['Word'] as $word): ?>
                        <td onClick="setEdited(<?php echo $sentenceCount.',0,'.$wordCount; ?>)" id="cell:<?php echo $sentenceCount.':0:'.$wordCount; ?>" class="normal-cell">
                            <span class="ro-display">
                                <?php echo $word['text'] ?>
                            </span>
                            <span class="edit-field">
                                <input type="text" value="<?php echo $word['text'] ?>" />
                            </span>                            
                        </td>
                    <?php
                            $wordCount++;
                        endforeach; ?>
                </tr>

                <?php
                    $annotationTypeCount = 1;
                    foreach ($wordAnnotationTypes as $wordAnnotationType): ?>
                <tr>
                    <td class="annotation-column"><?php echo $wordAnnotationType['WordAnnotationType']['name'] ?></td>
                    <?php
                        $wordCount = 0;
                        foreach ($sentence['Word'] as $word): ?>
                        <td onClick="setEdited(<?php echo $sentenceCount.','.$annotationTypeCount.','.$wordCount; ?>)" class="normal-cell" id="cell:<?php echo $sentenceCount.':'.$annotationTypeCount.':'.$wordCount; ?>"></td>
                    <?php
                        $wordCount++;
                        endforeach; ?>
                </tr>
                <?php 
                    $annotationTypeCount++;
                    endforeach; ?>

                <?php foreach ($sentenceAnnotationTypes as $sentenceAnnotationType): ?>
                <tr>
                    <td class="annotation-column"><?php echo $sentenceAnnotationType['SentenceAnnotationType']['name'] ?></td>
                    <td id="cell:<?php echo $sentenceCount.':'.$annotationTypeCount.':0'; ?>" onClick="setEdited(<?php echo $sentenceCount.','.$annotationTypeCount.',0'; ?>)" colspan="<?php echo count($sentence['Word']) ?>"></td>
                </tr>
                <?php
                    $annotationTypeCount++;
                    endforeach; ?>
            </table>
            <hr/>
        </div>
        <?php $sentenceCount++; ?>
    </div>
<?php endforeach; ?>

<?php } ?>
