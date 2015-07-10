<?php if(isset($sentence)) { ?>

<br/><br/><br/>
<input id="userRoleId" type="hidden" value="<?php echo $userRoleId ?>" />
<input id="offset" type="hidden" value="<?php echo $offset ?>" />
<input id="document-id" type="hidden" value="<?php echo $documentId ?>" />
<input id="first-reload" type="hidden" value="1" />

<script>
<?php 
    foreach ($wordAnnotationTypes as $wordAnnotationType) {
        if ($wordAnnotationType['WordAnnotationType']['multiple_choices'] == 1) {
            ?>

            $(function() {
                $( ".multiple-suggestions-input-<?php echo $wordAnnotationType['WordAnnotationType']['id'];?>" ).autocomplete({
                    source: [
                    <?php
                        $counter = 0;
                        foreach ($wordAnnotationType['WordAnnotationTypeChoice'] as $choice) {
                            echo "{ value: \"".$choice['value']."\", label: \"".$choice['value']." - ".$choice['description']."\"}";
                            if ($counter < count($wordAnnotationType['WordAnnotationTypeChoice']) - 1) {
                                echo ",";
                            }
                            $counter++;
                        }
                    ?>
                    ]
                });
            });

            <?php
        }
    }
    ?>
    
    var choicesObject = {

    <?php    
    foreach ($wordAnnotationTypes as $wordAnnotationType) {
        foreach ($wordAnnotationType['WordAnnotationTypeChoice'] as $choice) {
            echo "\"choice".$choice['id']."value\":\"".$choice['value']."\",";       
            echo "\"choice".$choice['id']."description\":\"".$choice['description']."\",";       
        }
    }
    
    ?>
    
    "none":"none"};
</script>

<?php
    function normalizeText($text) {
        return preg_replace('/ /', '%20', $text);
    }
?>

<?php
     $sentenceNumber = $offset - $currentSentenceIndex + 1;

     for($i = 0; $i < $currentSentenceIndex; $i++) { ?>
<p>
    <?php
        echo $sentenceNumber.".&nbsp;";
        $sentenceNumber++;   
        foreach ($sentencesWindow[$i]['Word'] as $word) {
            if ($word['split'] == 1) {
                echo $word['stem']."&#124;".$word['suffix'];
            } else {
                echo $word['text'];
            }
            if (isset($word['postposition_id'])) {
                echo "&ndash;";
            } else {
                echo "&nbsp;";
            }
        }?>
</p>
<?php } ?>

<div name="sentence" id="sentence<?php echo $offset; ?>">
    <input type="hidden" id="document-sentences-count" value="<?php echo $sentencesCount ?>" />
    <input type="hidden" id="sentence<?php echo $offset; ?>-word-count" value="<?php echo count($sentence['Word']) ?>" />
    <input type="hidden" id="sentence<?php echo $offset; ?>-word-annotation-count" value="<?php echo $wordAnnotationCount + 1 ?>" />
    <input type="hidden" id="sentence<?php echo $offset; ?>-sentence-annotation-count" value="<?php echo $sentenceAnnotationCount ?>" />
    <input type="hidden" id="sentence<?php echo $offset; ?>-grid-x" value="<?php echo $gridX ?>" />
    <input type="hidden" id="sentence<?php echo $offset; ?>-grid-y" value="0" />
    <input type="hidden" id="sentence<?php echo $offset; ?>-edit-mode" value="<?php echo $editMode;?>" />
    <div>
        <?php 
            $options = array("alt" => "previous sentence",
                            "title" => "previous sentence",
                            "onClick" => "prevSentence();");
            if ($offset > 0) {
                $options['class'] = 'clickable-image';
            } else {
                $options['class'] = 'disabled-image';                
            }
            echo $this->Html->image("up.png", $options);

            $options = array("alt" => "next sentence",
                            "title" => "next sentence",
                            "onClick" => "nextSentence();");
                            
            if($offset < $sentencesCount - 1) {
                $options['class'] = 'clickable-image';
            } else {
                $options['class'] = 'disabled-image';                
            }
            echo $this->Html->image("down.png", $options);
            
            $options = array("alt" => "export sentence to Word",
                            "title" => "export sentence to Word",
                            "style" => "cursor:pointer",
                            "onClick" => "toggleVisibility('exportOptions');");
            echo $this->Html->image("word.png", $options);
        ?>
        <span id="exportOptions" style="display:none">
            <?php
                echo $this->Form->create(false, array('url' => array('controller' => 'generator', 'action' => 'generatedocx')
                                                ));
                #echo "<pre>".print_r($sentence, true)."</pre>";                                
                $startIndexOptions = array();
                $endIndexOptions = array();
                $wordIndex = 0;
                foreach ($sentence['Word'] as $word) {
                    if ($word['split']) {
                        $wordText = $word['stem'].'-'.$word['suffix'];
                    } else {            
                        $wordText = $word['text'];
                    }
                    $startIndexOptions[$wordIndex] = $wordText;
                    $endIndexOptions[$wordIndex+1] = $wordText;
                    
                    $wordIndex++;
                }
                $lastWordIndex = $wordIndex;
                
                $maxLevelOptions = array();
                $levelIndex = 1;
                foreach ($sentence['WordAnnotations'] as $annotationData) {
                    $wordAnnotationType = $annotationData['type']['WordAnnotationType'];
                    $maxLevelOptions[$levelIndex] = $wordAnnotationType['name'];
                    $levelIndex++;
                }
                $lastLevelIndex = $levelIndex - 1;
                
                echo $this->Form->input('sentenceId', array('type' => 'hidden', 'value'=>$sentence['Sentence']['id']));
                echo $this->Form->input('startIndex', array('label'=>'From word:','options'=>$startIndexOptions, 'empty' => false));
                echo $this->Form->input('endIndex', array('label'=>'To word:','options'=>$endIndexOptions, 'default'=>$lastWordIndex, 'empty' => false));
                echo $this->Form->input('maxLevel', array('label'=>'Up to level:','options'=>$maxLevelOptions, 'default'=>$lastLevelIndex, 'empty' => false));
                echo $this->Form->end('Export');
            ?>
        </span>
        <hr/>
        <table>
            <tr>
                <td></td>
                <?php 
                foreach ($sentence['Word'] as $word) {
                    $className = "bracket-cell";
                    if($word['is_postposition']) {
                        $className .= " right-bracket";
                    }
                    if(isset($word['postposition_id'])) {
                        $className .= " left-bracket";
                    }
                ?>
                <td class="<?php echo $className; ?>" >
					<?php
						echo $this->Html->image("preloader.gif", array("id" => 'cell-'.$offset.'-0-'.$word['position'].'-preloader',
   				                                                       "title" => "loading suggestions...",
                                                                       "class" => "preloader-inactive"
																	
																 )
									           );
					?>
                    <div id="cell-<?php echo $offset.'-0-'.$word['position'].'-suggestion-box'; ?>" class="suggestion-box-inactive">
                    </div>
                </td>    
                
                <?php } ?>
                
            </tr>
            <tr class="words-row">
                <td class="annotation-column"><?php echo $sentenceNumber; $sentenceNumber++;?>.</td>
                <?php
                    $wordIndex = 0;
                    foreach ($sentence['Word'] as $word): ?>
                    <td onClick="setEdited(<?php echo $offset.',0,'.$wordIndex; ?>)"
                        id="cell-<?php echo $offset.'-0-'.$wordIndex; ?>"
                        class="normal-cell">
                        <input type="hidden" id="cell-<?php echo $offset.'-0-'.$wordIndex.'-type'; ?>" value="word" />
                        <input type="hidden" id="cell-<?php echo $offset.'-0-'.$wordIndex.'-value'; ?>" value="<?php echo normalizeText($word['text']);?>" />
                        <input type="hidden" id="cell-<?php echo $offset.'-0-'.$wordIndex.'-split'; ?>" value="<?php echo $word['split']?"1":"0";?>" />
                        <input type="hidden" id="cell-<?php echo $offset.'-0-'.$wordIndex.'-word-id'; ?>" value="<?php echo $word['id']; ?>" />
                        <input type="hidden" id="cell-<?php echo $offset.'-0-'.$wordIndex.'-word-annotation-type-id'; ?>" value="0" />
                        <span id="cell-<?php echo $offset.'-0-'.$wordIndex.'-split-span'; ?>" class="<?php if ($word['split']) { echo "word-split"; } else {echo "word-unsplit";} ?>">
                            <span class="ro-display">
                                <?php
                                if ($word['split'] == 1) {
                                    echo $word['stem']."&#124;".$word['suffix'];
                                } else {
                                    echo $word['text'];
                                }
                                ?>
                            </span>
                            <span class="edit-field">
                                <span class="word-split-field">
                                    <input type="text" value="<?php echo $word['stem'] ?>" />&#124;<input type="text" value="<?php echo $word['suffix'] ?>" />                                    
                                </span>
                                <span class="word-unsplit-field">
                                   <input type="text" value="<?php echo $word['text'] ?>" />
                                </span>
                                <?php
                                   if ($wordIndex == 0) {
                                       echo $this->Html->image("plus.png", array("id" => "insertWord".$wordIndex,
                                                                                 "alt" => "insert word before current (ctrl + i)",
                                                                                 "title" => "insert word before current (ctrl + i)",
                                                                                 "url" => array("controller" => "words", "action"=>"insertWord", $documentId, $offset, $sentence['Sentence']['id'], $word['position'])
                                                                           )
                                                              );
                                       echo "&nbsp;";
                                   }
                                   if (!isset($word['postposition_id'])) {                       
                                       echo $this->Html->image("plusNext.png", array("id" => "insertAfterWord".$wordIndex,
                                                                                 "alt" => "insert word after current (ctrl + o)",
                                                                                 "title" => "insert word after current (ctrl + o)",
                                                                                 "url" => array("controller" => "words", "action"=>"insertAfterWord", $documentId, $offset, $sentence['Sentence']['id'], $word['position'])
                                                                           )
                                                              );
                                       echo "&nbsp;";
                                   }
                                   if (!isset($word['postposition_id'])) {                       
                                       echo $this->Html->image("delete.png", array("id" => "deleteWord".$wordIndex,
                                                                                 "alt" => "delete word (ctrl + l)",
                                                                                 "title" => "delete word (ctrl + l)",
                                                                                 "onClick" => "return confirm('You are about to remove this word with all its annotations. This operation can not be undone (no ctrl+z!) Are you sure?');",
                                                                                 "url" => array("controller" => "words", "action"=>"deleteWord", $documentId, $offset, $sentence['Sentence']['id'], $word['position'])
                                                                           )
                                                              );
                                       echo "&nbsp;";
                                   }                       
                                   if (!isset($word['postposition_id']) && $wordIndex < count($sentence['Word']) - 1 && !$word['is_postposition'] && !isset($sentence['Word'][$wordIndex+1]['postposition_id'])) {                       
                                       echo $this->Html->image("curlyBracket.png", array("id" => "markPostposition".$wordIndex,
                                                                                 "alt" => "mark the next word as postposition of current (ctrl + y)",
                                                                                 "title" => "mark the next word as postposition of current (ctrl + y)",
                                                                                 "url" => array("controller" => "words", "action"=>"markPostposition", $documentId, $offset, $sentence['Sentence']['id'], $word['position'])
                                                                           )
                                                              );
                                       echo "&nbsp;";
                                   }
                                   
                                   if (isset($word['postposition_id']) || $word['is_postposition']) {
                                   echo $this->Html->image("curlyBracketX.png", array("id" => "unmarkPostposition".$wordIndex,
                                                                             "alt" => "unmark postposition binding (ctrl + u)",
                                                                             "title" => "unmark postposition binding (ctrl + u)",
                                                                             "url" => array("controller" => "words", "action"=>"unmarkPostposition", $documentId, $offset, $sentence['Sentence']['id'], $word['position'])
                                                                       )
                                                          );
                                   }
                                   if ($wordIndex > 0) {
                                   echo $this->Html->image("copyFromPrev.png", array("id" => "copyFromPrev".$wordIndex,
                                                                             "alt" => "copy annotations from previous word",
                                                                             "title" => "copy annotations from previous word",
                                                                             "style" => "cursor:pointer",
                                                                             "onclick" => "copyAnnotations(".($word['position']-1).",".$word['position'].");"
                                                                       )
                                                          );
                                   }
                                   if ($wordIndex < count($sentence['Word']) - 1) {
                                   echo $this->Html->image("copyFromNext.png", array("id" => "copyFromNext".$wordIndex,
                                                                             "alt" => "copy annotations from next word",
                                                                             "title" => "copy annotations from next word",
                                                                             "style" => "cursor:pointer",
                                                                             "onclick" => "copyAnnotations(".($word['position']+1).",".$word['position'].");"
                                                                       )
                                                          );
                                   }
                               ?>
                            </span>
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
                <td class="annotation-column" title="<?php echo $wordAnnotations['type']['WordAnnotationType']['description'] ?>"><?php echo $wordAnnotations['type']['WordAnnotationType']['name'] ?></td>
                <?php
                    $wordIndex = 0;
                    foreach ($wordAnnotations['annotations'] as $annotation): ?>
                    <td onClick="setEdited(<?php echo $offset.','.$annotationTypeCount.','.$wordIndex; ?>)"
                        class="normal-cell"
                        id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex; ?>">
                        <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-word-id'; ?>" value="<?php echo $sentence['Word'][$wordIndex]['id']; ?>" />
                        <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-word-annotation-type-id'; ?>" value="<?php echo $wordAnnotations['type']['WordAnnotationType']['id']; ?>" />
                        <?php
                            if ($wordAnnotations['type']['WordAnnotationType']['strict_choices']) {
                            $selectedChoices = array(); ?>
                            <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-type'; ?>" value="<?php echo $wordAnnotations['type']['WordAnnotationType']['multiple_choices'] ? 'multiple-choices': 'choices' ?>" />
                            <span class="ro-display">
                                <?php
                                if (count($annotation) > 0) {
                                    foreach ($annotation['WordAnnotationTypeChoice'] as $selectedChoice) {
                                        array_push($selectedChoices, $selectedChoice['id']); ?>
                                        <input type="button" class="choice-selected" title="<?php echo $selectedChoice['description']; ?>" value="<?php echo $selectedChoice['value']; ?>" />
                                <?php        
                                    }
                                } ?>
                            </span>
                            <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-value'; ?>" value="<?php echo implode(",", $selectedChoices); ?>" />

                            <span class="edit-field">
                            <?php                                    
                                if ($wordAnnotations['type']['WordAnnotationType']['multiple_choices']) {
                                ?>
                                
                                <input onfocus="this.value='';" class="multiple-suggestions-input-<?php echo $wordAnnotations['type']['WordAnnotationType']['id']?>" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-input';?>"/>                                    
                                <?php


                                    $choiceIndex = 0;
                                    foreach ($wordAnnotations['type']['WordAnnotationTypeChoice'] as $choice) {
                                ?>
                                        <input id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex; ?>" onfocus="this.blur()" type="button" onclick="deselectChoice(this)" class="<?php echo in_array($choice['id'], $selectedChoices) ? 'choice-selected' : 'choice-inactive' ?>" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value'];?> x"/>
                                        <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex.'-type-id'; ?>" value="<?php echo $choice['id'] ?>" />
                                <?php
                                        $choiceIndex++;
                                    }
                                
                                } else { // regular strict choices
                                    $choiceIndex = 0;
                                    foreach ($wordAnnotations['type']['WordAnnotationTypeChoice'] as $choice) {
                                ?>
                                        <input id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex; ?>" onfocus="this.blur()" type="button" onclick="toggleSelectedChoice(this)" class="<?php echo in_array($choice['id'], $selectedChoices) ? 'choice-selected' : 'choice-available' ?>" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value'];if ($choiceIndex < count($hotKeys)) { echo '&nbsp;['.$hotKeys[$choiceIndex].']'; ?>"/>
                                        <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex.'-type-id'; ?>" value="<?php echo $choice['id'] ?>" />
                                <?php       }
                                        
                                        $choiceIndex++;
                                    }
                                }
                            ?>
                               </span>
                                
                        <?php } else { #text field ?>
                            <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-type'; ?>" value="word-text" />
                            <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-'.$wordIndex.'-value'; ?>" value="<?php echo isset($annotation['text_value']) ? normalizeText($annotation['text_value']) : '';  ?>" />

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
                <td class="annotation-column" title="<?php echo $sentenceAnnotations['type']['SentenceAnnotationType']['description'] ?>"><?php echo $sentenceAnnotations['type']['SentenceAnnotationType']['name'] ?></td>
                <td class="normal-cell" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-0'; ?>"
                    onClick="setEdited(<?php echo $offset.','.$annotationTypeCount.',0'; ?>)"
                    colspan="<?php echo count($sentence['Word']) ?>">
                    <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-0-type'; ?>" value="sentence-text" />
                    <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-0-value'; ?>" value="<?php echo isset($sentenceAnnotations['annotation']['text']) ? normalizeText($sentenceAnnotations['annotation']['text']) : '';  ?>" />
                    <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-0-sentence-id'; ?>" value="<?php echo $sentence['Sentence']['id']; ?>" />
                    <input type="hidden" id="cell-<?php echo $offset.'-'.$annotationTypeCount.'-0-sentence-annotation-type-id'; ?>" value="<?php echo $sentenceAnnotations['type']['SentenceAnnotationType']['id']; ?>" />
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
</div>

<?php for($i = $currentSentenceIndex + 1; $i < count($sentencesWindow); $i++) { ?>
<p>
    <?php
        echo $sentenceNumber.".&nbsp;";
        $sentenceNumber++;   
        foreach ($sentencesWindow[$i]['Word'] as $word) {
            if ($word['split'] == 1) {
                echo $word['stem']."&#124;".$word['suffix'];
            } else {
                echo $word['text'];
            }
            if (isset($word['postposition_id'])) {
                echo "&ndash;";
            } else {
                echo "&nbsp;";
            }
        } ?>
</p>
<?php } ?>


<?php } ?>
