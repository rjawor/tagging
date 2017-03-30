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

    Position in sentence<br/>
    <input style="float:none" type="radio" name="data[initial]" id="initial0" value="0" checked="true"/><i>Any</i><br/>
    <input style="float:none" type="radio" name="data[initial]" id="initial1" value="1" />Initial<br/>
    <input style="float:none" type="radio" name="data[initial]" id="initial2" value="2" />Non-initial<br/>

    <div class="submit"><input  type="submit" value="Find words" onclick="addCriterionBeforeSubmit('singleWord')"/></div></form>
