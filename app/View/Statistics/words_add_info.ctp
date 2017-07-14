<h3>Words with "add info"</h3>

<?php
    echo $this->element('statistics_header',
        array(
            'mainValue' => 0,
            'initial' => 0,
            'collocationValue' => 0,
            'specificValue' => 0,
            'initialSpecific' => 0,
            'immediate' => 0,
            'page' => $page,
            'totalPages' => $totalPages,
            'itemName' => 'words',
            'itemTotalCount' => $wordCount,
            'selectedLanguages' => $selectedLanguages,
            'selectedEpoques' => $selectedEpoques,
            'enablePagination' => true

        )
    );
?>


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
