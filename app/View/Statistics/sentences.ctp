<form method="post" id="filter_form">
    <input id="documentFilterInput" type="hidden" name="data[documentFilter]" value="<?php echo $documentFilter; ?>" />
    <a href="#" onclick="toggleDocumentFilter();">Show/hide document filtering</a>
    <span id="documentFilter" style="display:<?= !empty($documentFilter) && $documentFilter ? "inline":"none" ?>">
        <h3>Show collocations only from the following documents:</h3>
        <input type="button" value="select all" onclick="selectAll();" />&nbsp;<input type="button" value="select none" onclick="selectNone();"/>&nbsp;Select by language:&nbsp;
        <?php foreach ($languages as $language) { ?>
        <a href="#" onclick="selectByLang('<?= $language['Language']['code'] ?>');"><?= $language['Language']['description'] ?></a>
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
                <td style="vertical-align:middle"><input class="checkboxDoc<?= $document['Language']['code'] ?>" onclick="document.getElementById('filter_form').submit()" type="checkbox" name="data[documentIds][]" value="<?php echo $document['Document']['id']; ?>" <?php if (in_array($document['Document']['id'], $documentIds)){echo "checked='checked'";}?>/></td>
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


<h3>Sentences with "add info"</h3>


<?php foreach($sentences as $sentence): ?>

<table>
	<tr>
		<?php
		    $image = $this->Html->image("edit.png", array("alt" => "edit"));
		?>
		<td width="10%">Document</td><td><?= $sentence['documents']['name']?> (<?= $sentence['languages']['description'] ?>)</td>
	</tr>
	<tr>
		<td>Sentence <?=
					$this->Html->link(
                        $image,
                        array('controller' => 'dashboard', 'action' => 'index', $sentence['documents']['id'], $sentence['sentences']['position']),
                        array(
                            'target'=>'_blank',
                            'escape' => false
                        ))

		 ?></td><td width="auto"><?= $sentence[0]['sentence_text'] ?></td>
	</tr>
	<tr>
		<td>Add info</td><td><b><?= $sentence['sentence_annotations']['text'] ?></b></td>
	</tr>
</table>
<br/>
<br/>
<br/>


<?php endforeach; ?>
