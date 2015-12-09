<?php
$suggestions = array();
$choices = array();
foreach ($wordAnnotationTypes as $wordAnnotationType) {
    if ($wordAnnotationType['WordAnnotationType']['strict_choices'] == 1) {
        foreach ($wordAnnotationType['WordAnnotationTypeChoice'] as $choice) {
            $text = $choice['value']." (".$wordAnnotationType['WordAnnotationType']['name'].")";
            $description = $choice['description']." on the level: ".$wordAnnotationType['WordAnnotationType']['description'];
            array_push($suggestions, "{ value: \"".$text."\", label: \"".$text."\"}");            
            array_push($choices, array('id' => $choice['id'], 'value' => $text, 'description'=>$description));            
        }
    }
}

?>

<script>
$(function() {
    $( ".multiple-suggestions-input" ).autocomplete({
        source: [<?php echo join(',', $suggestions); ?>]
    });
});

$(document).keydown(function(e) {
    switch(e.which) {
        case 13:
            enterHandle(e);
        break;
    }
});

function enterHandle(e) {
    selectedValue = e.target.value;
    e.target.value='';
    buttonName = e.target.id+'Button';
    valueName = e.target.id+'Value';
    $("[name=\""+buttonName+"\"][type=\"button\"][value=\""+selectedValue+"\"]").attr('class', 'choice-selected');
    var selected = $("[name=\""+buttonName+"\"][class=\"choice-selected\"]");
    var ids = [];
    for (var i=0;i<selected.length;i++) {
        ids.push(selected[i].id);
    }
    $('#'+valueName).val(ids.join());
}

</script>

<h2>Find annotated words</h2>

<label for="singleWord">Add annotation criterion:</label>
<input id="singleWord" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input id="<?php echo $choice['id'];?>" name="singleWordButton" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br /><br />
<?php
    echo $this->Form->create(false, array('url' => array('controller' => 'statistics', 'action' => 'singleWords')));
    ?>
    <input type="hidden" id="singleWordValue" name="data[mainValue]" />
    <?php
    foreach($documentIds as $documentId) {
        echo '<input type="hidden" name="documentIds[]" value="'.$documentId.'" />';
    }
    echo $this->Form->end('Find words');
?>

<br/>
<br/>
<br/>
<hr/>
<br/>
<h2>Find 2-word collocations</h2>

<label for="mainWord">First word:</label>
<input id="mainWord" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input name="mainWordButton" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br/><br/>
<label for="collocationWord">Second word:</label>
<input id="collocationWord" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input name="collocationWordButton" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br /><br />
<?php
    echo $this->Form->create(false, array('url' => array('controller' => 'statistics', 'action' => 'collocations')));
    ?>
    <input type="hidden" id="mainWordValue" name="data[mainValue]" />
    <input type="hidden" id="collocationWordValue" name="data[collocationValue]" />
    <input type="hidden" name="immediate" value="false" />

    <?php
    foreach($documentIds as $documentId) {
        echo '<input type="hidden" name="documentIds[]" value="'.$documentId.'" />';
    }

    echo $this->Form->end('Find 2-word collocations');
?>

<br/>
<br/>
<br/>
<hr/>
<br/>
<h2>Find 3-word collocations</h2>

<label for="multiWord1">First word:</label>
<input id="multiWord1" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input name="multiWord1Button" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br/><br/>
<label for="multiWord2">Second word:</label>
<input id="multiWord2" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input name="multiWord2Button" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br /><br />
<label for="multiWord3">Third word:</label>
<input id="multiWord3" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input name="multiWord3Button" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br /><br />
<?php
    echo $this->Form->create(false, array('url' => array('controller' => 'statistics', 'action' => 'multicollocations')));
    ?>
    <input type="hidden" id="multiWord1Value" name="data[multiWord1Value]" />
    <input type="hidden" id="multiWord2Value" name="data[multiWord2Value]" />
    <input type="hidden" id="multiWord3Value" name="data[multiWord3Value]" />
    <input type="hidden" name="immediate" value="false" />

    <?php
    foreach($documentIds as $documentId) {
        echo '<input type="hidden" name="documentIds[]" value="'.$documentId.'" />';
    }

    echo $this->Form->end('Find 3-word collocations');
?>

