<h3>Sentences with "add info"</h3>


<?php foreach($sentences as $sentence): ?>

<table>
	<tr>
		<?php		
		    $image = $this->Html->image("edit.png", array("alt" => "edit"));                     
		?>
		<td width="10%">Sentence <?=
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


