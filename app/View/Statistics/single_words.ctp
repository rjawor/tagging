<form method="post" id="filter_form">
    <input type="hidden" name="data[mainValue]" value="<?php echo $mainValue; ?>" />
    <input id="page_number" type="hidden" name="data[page]" value="<?php echo $page; ?>" />
    <input id="total_pages" type="hidden"value="<?php echo $totalPages; ?>" />
    <input id="documentFilterInput" type="hidden" name="data[documentFilter]" value="<?php echo $documentFilter; ?>" />
    <a href="#" onclick="toggleDocumentFilter();">Show/hide document filtering</a>
    <span id="documentFilter" style="display:<?= !empty($documentFilter) && $documentFilter ? "inline":"none" ?>">
        <h3>Show words only from the following documents:</h3>
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


<h3>Words</h3>

Total number of words found: <strong><?php echo $word_count; ?></strong><br/><br/>
<a href="#" onclick="decreasePage();document.getElementById('filter_form').submit()">Previous</a> Page <?= ($page + 1)?> of <?= $totalPages?> <a href="#" onclick="increasePage();document.getElementById('filter_form').submit()">Next</a><br/><br/>


<table>
    <tr>
        <th>No.</th>
        <th>Document</th>
        <th>Language</th>
        <th>Word</th>
        <th></th>
        <th width="60%" >Context</th>
    </tr>

<?php
for ($i=0;$i<count($words);$i++) {
    $annotatedWord = $words[$i];
    $context = $contexts[$i];

    echo "<tr>";
    echo "<td>".($i+1+$offset)."</td>";
    echo "<td>".$context[0]["documents"]["name"]."</td>";
    echo "<td>".$context[0]["languages"]["code"]."</td>";
    echo "<td>".$annotatedWord->getRawHtml($wordAnnotationTypes)."</td>";

    $image = $this->Html->image("edit.png", array(
                    "alt" => "edit"
                     ));

    echo "<td>".$this->Html->link(
                        $image,
                        array('controller' => 'dashboard', 'action' => 'viewWord', $annotatedWord->getId()),
                        array(
                            'target'=>'_blank',
                            'escape' => false
                        ))."</td>";
    echo "<td>";
    foreach ($context as $contextWord) {
        if ($contextWord['words']['id'] == $annotatedWord->getId()) {
            $text = "<b>";
        } else {
            $text = "";
        }

        if ($contextWord['words']['split']) {
            $text .=  $contextWord['words']['stem']."-".$contextWord['words']['suffix'];
        } else {
            $text .= $contextWord['words']['text'];
        }
        $text = trim($text);
        if ($contextWord['words']['id'] == $annotatedWord->getId()) {
            $text .= "</b>";
        }

        echo $text." ";
    }

    echo "</td>";
    echo "</tr>\n";
}
?>
</table>
