<form method="post" id="filter_form">
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

<h3>Words with "add info"</h3>
Total number of words found: <strong><?php echo $word_count; ?></strong><br/><br/>
<a href="#" onclick="decreasePage();document.getElementById('filter_form').submit()">Previous</a> Page <?= ($page + 1)?> of <?= $totalPages?> <a href="#" onclick="increasePage();document.getElementById('filter_form').submit()">Next</a><br/><br/>

<?php foreach($wordsAddInfo as $wordAddInfo): ?>

<table>
	<tr>
		<?php
		    $image = $this->Html->image("edit.png", array("alt" => "edit"));
		?>
		<td width="10%">Document</td><td><?= $wordAddInfo['documents']['name']?></td>
	</tr>
	<tr>
		<td>Word <?=
					$this->Html->link(
                        $image,
                        array('controller' => 'dashboard', 'action' => 'viewWord', $wordAddInfo['words']['id']),
                        array(
                            'target'=>'_blank',
                            'escape' => false
                        ))

		 ?></td><td width="auto"><?= $wordAddInfo['words']['text'] ?></td>
	</tr>
	<tr>
		<td>Add info</td><td><b><?= $wordAddInfo['word_annotations']['text_value'] ?></b></td>
	</tr>
</table>
<br/>
<br/>
<br/>


<?php endforeach; ?>
