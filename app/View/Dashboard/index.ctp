<?php if(isset($documentWindow)) { ?>

<h1><strong><?php echo $documentWindow['Document']['name']; ?></strong></h1>
<br/><br/>

<input id="offset" type="hidden" value="<?php echo $offset ?>" />
<input id="document-id" type="hidden" value="<?php echo $documentWindow['Document']['id'] ?>" />

<?php $sentenceIndex = 0; ?>
<?php foreach ($documentWindow['Sentence'] as $sentence): ?>
    <div name="sentence" id="sentence<?php echo $sentenceIndex; ?>">
        <input type="hidden" id="sentence<?php echo $sentenceIndex; ?>-word-count" value="<?php echo count($sentence['Word']) ?>" />
        <input type="hidden" id="sentence<?php echo $sentenceIndex; ?>-word-annotation-count" value="<?php echo $wordAnnotationCount + 1 ?>" />
        <input type="hidden" id="sentence<?php echo $sentenceIndex; ?>-sentence-annotation-count" value="<?php echo $sentenceAnnotationCount ?>" />
        <input type="hidden" id="sentence<?php echo $sentenceIndex; ?>-grid-x" value="0" />
        <input type="hidden" id="sentence<?php echo $sentenceIndex; ?>-grid-y" value="0" />
        <input type="hidden" id="sentence<?php echo $sentenceIndex; ?>-edit-mode" value="0" />
        <p>
            <?php echo ($sentenceIndex+1); ?>.&nbsp;
            <?php foreach ($sentence['Word'] as $word): ?>
                <?php echo $word['text'] ?>&nbsp;
            
            <?php endforeach; ?>
        </p>
        <div>
            <?php 
                $options = array("alt" => "poprzednie zdanie",
                                "title" => "poprzednie zdanie",
                                "onClick" => "prevSentence();");
                if ($sentenceIndex > 0) {
                    $options['class'] = 'clickable-image';
                } else {
                    $options['class'] = 'disabled-image';                
                }
                echo $this->Html->image("up.png", $options);

                $options = array("alt" => "następne zdanie",
                                "title" => "następne zdanie",
                                "onClick" => "nextSentence();");
                                
                if($sentenceIndex < count($documentWindow['Sentence'])-1) {
                    $options['class'] = 'clickable-image';
                } else {
                    $options['class'] = 'disabled-image';                
                }
                echo $this->Html->image("down.png", $options);
            ?>
            <hr/>
            <table>
                
                <tr class="words-row">
                    <td class="annotation-column"><?php echo ($sentenceIndex + 1)?>.</td>
                    <?php
                        $wordIndex = 0;
                        foreach ($sentence['Word'] as $word): ?>
                        <td onClick="setEdited(<?php echo $sentenceIndex.',0,'.$wordIndex; ?>)"
                            id="cell:<?php echo $sentenceIndex.':0:'.$wordIndex; ?>"
                            class="normal-cell">
                            <span class="ro-display">
                                <?php echo $word['text'] ?>
                            </span>
                            <span class="edit-field">
                                <input type="text" value="<?php echo $word['text'] ?>" />
                            </span>                            
                        </td>
                    <?php
                            $wordIndex++;
                        endforeach; ?>
                </tr>

                <?php
                    $annotationTypeCount = 1;
                    foreach ($sentence['WordAnnotations'] as $wordAnnotations): ?>
                <tr>
                    <td class="annotation-column"><?php echo $wordAnnotations['type']['WordAnnotationType']['name'] ?></td>
                    <?php
                        $wordIndex = 0;
                        foreach ($wordAnnotations['annotations'] as $annotation): ?>
                        <td onClick="setEdited(<?php echo $sentenceIndex.','.$annotationTypeCount.','.$wordIndex; ?>)"
                            class="normal-cell"
                            id="cell:<?php echo $sentenceIndex.':'.$annotationTypeCount.':'.$wordIndex; ?>">
                            <?php
                                if ($wordAnnotations['type']['WordAnnotationType']['strict_choices']) { ?>    
                                <span class="ro-display">
                                    <!-- ro combo -->
                                </span>
                                <span class="edit-field">
                                    <!-- editable combo -->
                                </span>                            
                            <?php } else { ?>
                                <span class="ro-display">
                                    <?php echo isset($annotation['text_value']) ? $annotation['text_value'] : '';  ?>
                                </span>
                                <span class="edit-field">
                                    <input type="text" value="<?php echo isset($annotation['text_value']) ? $annotation['text_value'] : '';  ?>" />
                                </span>                            
                            <?php } ?>
                        </td>
                    <?php
                        $wordIndex++;
                        endforeach; ?>
                </tr>
                <?php 
                    $annotationTypeCount++;
                    endforeach; ?>

                <?php foreach ($sentence['SentenceAnnotations'] as $sentenceAnnotations): ?>
                <tr>
                    <td class="annotation-column"><?php echo $sentenceAnnotations['type']['SentenceAnnotationType']['name'] ?></td>
                    <td class="normal-cell" id="cell:<?php echo $sentenceIndex.':'.$annotationTypeCount.':0'; ?>"
                        onClick="setEdited(<?php echo $sentenceIndex.','.$annotationTypeCount.',0'; ?>)"
                        colspan="<?php echo count($sentence['Word']) ?>">
                        <span class="ro-display">
                            <?php echo isset($sentenceAnnotations['annotation']['text']) ? $sentenceAnnotations['annotation']['text'] : '';  ?>
                        </span>
                        <span class="edit-field">
                            <input type="text" value="<?php echo isset($sentenceAnnotations['annotation']['text']) ? $sentenceAnnotations['annotation']['text'] : '';  ?>" />
                        </span>                            
                    </td>
                </tr>
                <?php
                    $annotationTypeCount++;
                    endforeach; ?>
            </table>
            <hr/>
        </div>
        <?php $sentenceIndex++; ?>
    </div>
<?php endforeach; ?>

<?php } ?>
