<form method="post" id="filter_form">
    <input type="hidden" name="data[mainValue]" value="<?php echo $mainValue; ?>" />
    <input type="hidden" name="data[collocationValue]" value="<?php echo $collocationValue; ?>" />
    <input type="hidden" name="data[immediate]" value="<?php echo $immediate; ?>" />
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


<h3>Collocations</h3>
Total number of collocations found: <strong><?php echo count($collocations); ?></strong>


<table>
    <tr>
        <th>No.</th>
        <th>Document</th>
        <th>Language</th>
        <th>First word</th>
        <th>Second word</th>
        <th></th>
        <th width="60%" >Context</th>
    </tr>

<?php
for ($i=0;$i<count($collocations);$i++) {
    $collocation = $collocations[$i];
    $context = $contexts[$i];
    
    echo "<tr>";
    echo "<td>".($i+1)."</td>";
    echo "<td>".$context[0]["documents"]["name"]."</td>";
    echo "<td>".$context[0]["languages"]["code"]."</td>";
    echo "<td>".$collocation['mwText']."</td>";
    echo "<td>".$collocation['cwText']."</td>";
    
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
    echo "<td>";
    foreach ($context as $contextWord) {
        if ($contextWord['words']['id'] == $collocation['mwId'] ||
            $contextWord['words']['id'] == $collocation['cwId']) {
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
        if ($contextWord['words']['id'] == $collocation['mwId'] ||
            $contextWord['words']['id'] == $collocation['cwId']) {
            $text .= "</b>";
        }
        
        echo $text." ";
    }
  
    echo "</td>";
    echo "</tr>\n";    
}
?>
</table>

