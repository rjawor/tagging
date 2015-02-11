<form method="post" id="filter_form">
    <input type="hidden" name="data[mainValue]" value="<?php echo $mainValue; ?>" />
    Show results from documents:
    <table>
    <?php
    foreach ($documents as $document) {
        ?>
        <tr>
            <td style="vertical-align:middle"><input onclick="document.getElementById('filter_form').submit()" type="checkbox" name="data[documentIds][]" value="<?php echo $document['Document']['id']; ?>" <?php if (in_array($document['Document']['id'], $documentIds)){echo "checked='checked'";}?>/></td>
            <td><?php echo $document['Document']['name']; ?></td>
            <td><?php echo $document['Language']['code']; ?></td>
        </tr>
        <?php
    }
    ?>
    </table>
</form>

<br/><br/><br/>

Total number of words found: <strong><?php echo count($words); ?></strong>


<table>


<?php
$number = 0;
foreach ($words as $annotatedWord) {
    $number++;
    echo "<tr>";
    echo "<td>".$number."</td>";
    
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
    echo $annotatedWord->getHtml($wordAnnotationTypes);
    echo "</tr>\n";    
}
?>
</table>

