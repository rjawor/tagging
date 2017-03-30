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
    addSearchCriterion(selectedValue, buttonName, valueName);
}

function addSearchCriterion(selectedValue, buttonName, valueName) {
    $("[name=\""+buttonName+"\"][type=\"button\"][value=\""+selectedValue+"\"]").attr('class', 'choice-selected');
    var selected = $("[name=\""+buttonName+"\"][class=\"choice-selected\"]");
    var ids = [];
    for (var i=0;i<selected.length;i++) {
        ids.push(selected[i].id);
    }
    $('#'+valueName).val(ids.join());
}

function addCriterionBeforeSubmit(elementId) {
    addSearchCriterion($('#'+elementId).val(), elementId+'Button', elementId+'Value');

}

</script>

<h2>Find collocations</h2>

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

    <div class="submit"><input  type="submit" value="Find 2-word collocations" onclick="addCriterionBeforeSubmit('mainWord');addCriterionBeforeSubmit('collocationWord');"/></div></form>
