<h3>Sentences with "add info"</h3>

<?php
    echo $this->element('statistics_header',
        array(
            'mainValue' => 0,
            'initial' => 0,
            'collocationValue' => 0,
            'specificValue' => 0,
            'initialSpecific' => 0,
            'immediate' => 0,
            'page' => 0,
            'totalPages' => 0,
            'itemName' => 'sentences',
            'itemTotalCount' => 0,
            'selectedLanguages' => $selectedLanguages,
            'selectedEpoques' => $selectedEpoques,
            'enablePagination' => false

        )
    );
?>


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
