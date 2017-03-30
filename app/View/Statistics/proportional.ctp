<h3>Proportional statistics</h3>

<?php
    echo $this->element('statistics_header',
        array(
            'mainValue' => $mainValue,
            'initial' => $initial,
            'collocationValue' => 0,
            'specificValue' => $specificValue,
            'initialSpecific' => $initialSpecific,
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

    if ($initial == 0) {
        $mainSentencePos = 'Any';
    } else if ($initial == 1) {
        $mainSentencePos = 'Initial';
    } else if ($initial == 2) {
        $mainSentencePos = 'Non-initial';
    }

    if ($initialSpecific == 0) {
        $specificSentencePos = 'Any';
    } else if ($initialSpecific == 1) {
        $specificSentencePos = 'Initial';
    } else if ($initialSpecific == 2) {
        $specificSentencePos = 'Non-initial';
    }
?>
<p>
Number of words tagged as: <i><?= implode(', ', $mainTags)?></i>, position in sentence: <i><?= $mainSentencePos ?></i><br/>
<b><?= $mainCount ?></b>
</p>
<p>
Number of words more specifically tagged as: <i><?= implode(', ', $specificTags)?></i>, position in sentence: <i><?= $specificSentencePos ?></i><br/>
<b><?= $specificCount ?> (<?= number_format($specificCount * 100 / $mainCount, 2) ?>%)</b>
</p>
