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

<h2>Proportional statistics</h2>
<?php
echo $this->Form->create(false, array('url' => array('controller' => 'statistics', 'action' => 'proportional')));
?>

<label for="mainWord">Main search criteria:</label>
<input id="mainWord" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input name="mainWordButton" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br/>Position in sentence<br/>
<input style="float:none" type="radio" name="data[initial]" id="initial0" value="0" checked="true"/><i>Any</i><br/>
<input style="float:none" type="radio" name="data[initial]" id="initial1" value="1" />Initial<br/>
<input style="float:none" type="radio" name="data[initial]" id="initial2" value="2" />Non-initial<br/>

<br/><br/>
<label for="specificWord">Additional specific criteria:</label>
<input id="specificWord" type="text" class="multiple-suggestions-input" value=""/>
Selected criteria: (clicking on a criterion deletes it) <br/><br/>
<?php foreach ($choices as $choice) { ?>
    <input name="specificWordButton" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
<?php } ?>
<br/>Position in sentence<br/>
<input style="float:none" type="radio" name="data[initialSpecific]" id="initial0" value="0" checked="true"/><i>Any</i><br/>
<input style="float:none" type="radio" name="data[initialSpecific]" id="initial1" value="1" />Initial<br/>
<input style="float:none" type="radio" name="data[initialSpecific]" id="initial2" value="2" />Non-initial<br/>
<br /><br />
    <input type="hidden" id="mainWordValue" name="data[mainValue]" />
    <input type="hidden" id="specificWordValue" name="data[specificValue]" />
    <input type="hidden" name="immediate" value="false" />

    <div class="submit"><input  type="submit" value="Generate" onclick="addCriterionBeforeSubmit('mainWord');addCriterionBeforeSubmit('specificWord');"/></div></form>
