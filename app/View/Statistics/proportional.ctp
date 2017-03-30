<h3>Proportional statistics</h3>

<?php
    echo $this->element('statistics_header',
        array(
            'mainValue' => $mainValue,
            'collocationValue' => 0,
            'specificValue' => $specificValue,
            'immediate' => 0,
            'page' => 0,
            'totalPages' => 0,
            'itemName' => '',
            'itemTotalCount' => 0,
            'selectedLanguages' => $selectedLanguages,
            'selectedEpoques' => $selectedEpoques,
            'enablePagination' => false

        )
    );

    $mainTags = array();
    $mainParams = explode(',',$mainValue);
    foreach ($mainParams as $param) {
        array_push($mainTags, $tags[$param]['description']);
    }

    $specificTags = $mainTags;
    $specificParams = explode(',',$specificValue);
    foreach ($specificParams as $param) {
        array_push($specificTags, $tags[$param]['description']);
    }


?>
<p>
Number of words tagged as: <i><?= implode(', ', $mainTags)?></i><br/>
<b><?= $mainCount ?></b>
</p>
<p>
Number of words more specifically tagged as: <i><?= implode(', ', $specificTags)?></i><br/>
<b><?= $specificCount ?> (<?= number_format($specificCount * 100 / $mainCount, 2) ?>%)</b>
</p>
