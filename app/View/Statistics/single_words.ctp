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

