<?php if(isset($documentWindow)) { ?>

<h1><strong><?php echo $documentWindow['Document']['name']; ?></strong></h1>
<br/><br/>

<input id="offset" type="hidden" value="<?php echo $offset ?>" />
<input id="document-id" type="hidden" value="<?php echo $documentWindow['Document']['id'] ?>" />


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
            <input type="button" onclick="testInput();" value="set" />
            <table>
                
                <tr class="words-row">
                    <td class="annotation-column"><?php echo ($sentenceIndex + 1)?>.</td>
                    <?php
                        $wordIndex = 0;
                        foreach ($sentence['Word'] as $word): ?>
                        <td onClick="setEdited(<?php echo $sentenceIndex.',0,'.$wordIndex; ?>)"
                            id="cell-<?php echo $sentenceIndex.'-0-'.$wordIndex; ?>"
                            class="normal-cell">
                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-0-'.$wordIndex.'-type'; ?>" value="word" />
                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-0-'.$wordIndex.'-value'; ?>" value="<?php echo $word['split']?normalizeText($word['stem']).",".normalizeText($word['suffix']):normalizeText($word['text']);?>" />
                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-0-'.$wordIndex.'-split'; ?>" value="<?php echo $word['split']?"1":"0";?>" />
                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-0-'.$wordIndex.'-word-id'; ?>" value="<?php echo $word['id']; ?>" />
                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-0-'.$wordIndex.'-word-annotation-type-id'; ?>" value="0" />
                            <span id="cell-<?php echo $sentenceIndex.'-0-'.$wordIndex.'-split-span'; ?>" class="<?php if ($word['split']) { echo "word-split"; } else {echo "word-unsplit";} ?>">
                                <span class="ro-display">
                                    <span class="word-split-field">
                                        <?php echo $word['stem']."&nbsp;&#124;&nbsp;".$word['suffix']; ?>
                                    </span>
                                    <span class="word-unsplit-field">
                                        <?php echo $word['text']; ?>
                                    </span>
                                </span>
                                <span class="edit-field">
                                    <span class="word-split-field">
                                        <input type="text" value="<?php echo $word['stem'] ?>" />&nbsp;&#124;&nbsp;<input type="text" value="<?php echo $word['suffix'] ?>" />                                    
                                    </span>
                                    <span class="word-unsplit-field">
                                       <input type="text" value="<?php echo $word['text'] ?>" />
                                    </span>
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
                        <td onClick="setEdited(<?php echo $sentenceIndex.','.$annotationTypeCount.','.$wordIndex; ?>)"
                            class="normal-cell"
                            id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex; ?>">
                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-word-id'; ?>" value="<?php echo $sentence['Word'][$wordIndex]['id']; ?>" />
                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-word-annotation-type-id'; ?>" value="<?php echo $wordAnnotations['type']['WordAnnotationType']['id']; ?>" />
                            <?php
                                if ($wordAnnotations['type']['WordAnnotationType']['strict_choices']) {
                                $selectedChoices = array(); ?>
                                <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-type'; ?>" value="<?php echo $wordAnnotations['type']['WordAnnotationType']['multiple_choices'] ? 'multiple-choices': 'choices' ?>" />
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
                                <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-value'; ?>" value="<?php echo implode(",", $selectedChoices); ?>" />

                                <span class="edit-field">
                                <?php                                    
                                    if ($wordAnnotations['type']['WordAnnotationType']['multiple_choices']) {
                                    ?>
                                    
                                    <input onfocus="this.value='';" class="multiple-suggestions-input-<?php echo $wordAnnotations['type']['WordAnnotationType']['id']?>" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-input';?>"/>                                    
                                    <?php


                                        $choiceIndex = 0;
                                        foreach ($wordAnnotations['type']['WordAnnotationTypeChoice'] as $choice) {
                                    ?>
                                            <input id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex; ?>" onfocus="this.blur()" type="button" onclick="deselectChoice(this)" class="<?php echo in_array($choice['id'], $selectedChoices) ? 'choice-selected' : 'choice-inactive' ?>" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value'];?> x"/>
                                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex.'-type-id'; ?>" value="<?php echo $choice['id'] ?>" />
                                    <?php
                                            $choiceIndex++;
                                        }
                                    
                                    } else { // regular strict choices
                                        $choiceIndex = 0;
                                        foreach ($wordAnnotations['type']['WordAnnotationTypeChoice'] as $choice) {
                                    ?>
                                            <input id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex; ?>" onfocus="this.blur()" type="button" onclick="toggleSelectedChoice(this)" class="<?php echo in_array($choice['id'], $selectedChoices) ? 'choice-selected' : 'choice-available' ?>" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value'];if ($choiceIndex < count($hotKeys)) { echo '&nbsp;['.$hotKeys[$choiceIndex].']'; ?>"/>
                                            <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-choice-'.$choiceIndex.'-type-id'; ?>" value="<?php echo $choice['id'] ?>" />
                                    <?php       }
                                            
                                            $choiceIndex++;
                                        }
                                    }
                                ?>
                                   </span>
                                    
                            <?php } else { #text field ?>
                                <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-type'; ?>" value="word-text" />
                                <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-'.$wordIndex.'-value'; ?>" value="<?php echo isset($annotation['text_value']) ? normalizeText($annotation['text_value']) : '';  ?>" />

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
                    <td class="normal-cell" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-0'; ?>"
                        onClick="setEdited(<?php echo $sentenceIndex.','.$annotationTypeCount.',0'; ?>)"
                        colspan="<?php echo count($sentence['Word']) ?>">
                        <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-0-type'; ?>" value="sentence-text" />
                        <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-0-value'; ?>" value="<?php echo isset($sentenceAnnotations['annotation']['text']) ? normalizeText($sentenceAnnotations['annotation']['text']) : '';  ?>" />
                        <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-0-sentence-id'; ?>" value="<?php echo $sentence['id']; ?>" />
                        <input type="hidden" id="cell-<?php echo $sentenceIndex.'-'.$annotationTypeCount.'-0-sentence-annotation-type-id'; ?>" value="<?php echo $sentenceAnnotations['type']['SentenceAnnotationType']['id']; ?>" />
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
