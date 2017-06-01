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
            enterCriterionHandle(e);
        break;
    }
});

</script>

<h2>Find collocations</h2>

<?php
echo $this->Form->create(false, array('url' => array('controller' => 'statistics', 'action' => 'collocations')));
?>

<?php
    echo $this->element('word_criteria',
        array(
            'id' => 1,
            'numeral' => 'First',
            'visible' => true,
            'choices' => $choices
        )
    );
    echo $this->element('word_criteria',
        array(
            'id' => 2,
            'numeral' => 'Second',
            'visible' => true,
            'choices' => $choices
        )
    );
    echo $this->element('word_criteria',
        array(
            'id' => 3,
            'numeral' => 'Third',
            'visible' => false,
            'choices' => $choices
        )
    );
    echo $this->element('word_criteria',
        array(
            'id' => 4,
            'numeral' => 'Fourth',
            'visible' => false,
            'choices' => $choices
        )
    );
    echo $this->element('word_criteria',
        array(
            'id' => 5,
            'numeral' => 'Fifth',
            'visible' => false,
            'choices' => $choices
        )
    );
?>



<br /><br />
    <input type="hidden" name="immediate" value="false" />

    <div class="submit"><input  type="submit" value="Find collocations" onclick="addCriterionBeforeSubmit('mainWord');addCriterionBeforeSubmit('collocationWord');"/></div></form>
