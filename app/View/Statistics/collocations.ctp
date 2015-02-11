<form method="post" id="filter_form">
    <input type="hidden" name="data[mainValue]" value="<?php echo $mainValue; ?>" />
    <input type="hidden" name="data[collocationValue]" value="<?php echo $collocationValue; ?>" />
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

Total number of collocations found: <strong><?php echo count($collocations); ?></strong>


<table>
    <tr>
        <th>No.</th>
        <th></th>
        <th>First word</th>
        <th>Separating words</th>
        <th>Second word</th>
    </tr>

<?php
$number = 0;
foreach ($collocations as $collocation) {
    $number++;
    echo "<tr>";
    echo "<td>".$number."</td>";
    
    $image = $this->Html->image("edit.png", array(
                    "alt" => "edit"                    
                     ));
                     
    echo "<td>".$this->Html->link(
                        $image,
                        array('controller' => 'dashboard', 'action' => 'viewWord', $collocation['mwId']),
                        array(
                            'target'=>'_blank', 
                            'escape' => false
                        ))."</td>";
    echo "<td>".$collocation['mwText']."</td>";
    echo "<td>".$collocation['sepWords']."</td>";
    echo "<td>".$collocation['cwText']."</td>";
    echo "</tr>\n";    
}
?>
</table>

