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

