<h3>Collocations</h3>
<?php
    echo $this->element('statistics_header',
        array(
            'mainValue' => $mainValue,
            'initial' => 0,
            'collocationValue' => $collocationValue,
            'specificValue' => 0,
            'initialSpecific' => 0,
            'immediate' => $immediate,
            'page' => $page,
            'totalPages' => $totalPages,
            'itemName' => 'sentences',
            'itemTotalCount' => $sentencesTotalCount,
            'selectedLanguages' => $selectedLanguages,
            'selectedEpoques' => $selectedEpoques,
            'enablePagination' => true

        )
    );
?>

<table>
    <tr>
        <th>No.</th>
        <th>Document</th>
        <th>Language</th>
        <th>Epoque</th>
        <th width="80%" >Sentence</th>
    </tr>

<?php

for ($i=0;$i<count($sentencesWithCollocations);$i++) {
    $words = $sentencesWithCollocations[$i];
?>
    <tr>
        <td><?= $i+1+$offset ?></td>
        <td><?= $words[0]['documents']['name'] ?></td>
        <td><?= $words[0]['languages']['code'] ?></td>
        <td><?= $words[0]['epoques']['name'] ?></td>
        <td>
        <?php
        foreach ($words as $word) {
            if (isset($word[0]['tags'])) {

            ?>

            <a href="<?= Configure::read('SystemInstallationPath') ?>/dashboard/viewWord/<?=$word['words']['id']?>" target="_blank"><img src="<?= Configure::read('SystemInstallationPath') ?>/img/edit.png" alt="edit"></a>
            <b title="<?=$word[0]['tags']?>"><?=$word[0]['word_text']?></b>

            <?php
            } else {
                echo $word[0]['word_text']." ";
            }
        }
        ?>
        </td>
    </tr>
<?php

}
?>
</table>
