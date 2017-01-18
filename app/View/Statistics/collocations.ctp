<form method="post" id="filter_form">
    <input type="hidden" name="data[mainValue]" value="<?php echo $mainValue; ?>" />
    <input type="hidden" name="data[collocationValue]" value="<?php echo $collocationValue; ?>" />
    <input type="hidden" name="data[immediate]" value="<?php echo $immediate; ?>" />
    <input id="page_number" type="hidden" name="data[page]" value="<?php echo $page; ?>" />
    <input id="total_pages" type="hidden"value="<?php echo $totalPages; ?>" />
    <input id="documentFilterInput" type="hidden" name="data[documentFilter]" value="<?php echo $documentFilter; ?>" />
    <a href="#" onclick="toggleDocumentFilter();">Show/hide document filtering</a>
    <span id="documentFilter" style="display:<?= !empty($documentFilter) && $documentFilter ? "inline":"none" ?>">
        <h3>Show collocations only from the following documents:</h3>
        <input type="button" value="select all" onclick="$('#page_number').val(0);selectAll();" />&nbsp;<input type="button" value="select none" onclick="$('#page_number').val(0);selectNone();"/>&nbsp;Select by language:&nbsp;
        <?php foreach ($languages as $language) { ?>
        <a href="#" onclick="$('#page_number').val(0);selectByLang('<?= $language['Language']['code'] ?>');"><?= $language['Language']['description'] ?></a>
        <?php } ?>
        <table>
            <tr>
                <th></th>
                <th>Document name</th>
                <th>Document language</th>
            </tr>
        <?php
        foreach ($documents as $document) {
            ?>
            <tr>
                <td style="vertical-align:middle"><input class="checkboxDoc<?= $document['Language']['code'] ?>" onclick="$('#page_number').val(0);document.getElementById('filter_form').submit()" type="checkbox" name="data[documentIds][]" value="<?php echo $document['Document']['id']; ?>" <?php if (in_array($document['Document']['id'], $documentIds)){echo "checked='checked'";}?>/></td>
                <td><?php echo $document['Document']['name']; ?></td>
                <td><?php echo $document['Language']['code']; ?></td>
            </tr>
            <?php
        }
        ?>
        </table>
    </span>
</form>

<br/><br/>


<h3>Collocations</h3>
<p>Total number of sentences found: <strong><?php echo $sentencesTotalCount; ?></strong></p>
<a href="#" onclick="decreasePage();document.getElementById('filter_form').submit()">Previous</a> Page <?= ($page + 1)?> of <?= $totalPages?> <a href="#" onclick="increasePage();document.getElementById('filter_form').submit()">Next</a><br/><br/>



<table>
    <tr>
        <th>No.</th>
        <th>Document</th>
        <th>Language</th>
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
        <td>
        <?php
        foreach ($words as $word) {
            if (isset($word[0]['tags'])) {

            ?>

            <a href="/tagging/dashboard/viewWord/<?=$word['words']['id']?>" target="_blank"><img src="/tagging/img/edit.png" alt="edit"></a>
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
