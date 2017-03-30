<h3>Found words</h3>

<?php
    echo $this->element('statistics_header',
        array(
            'mainValue' => $mainValue,
            'collocationValue' => 0,
            'specificValue' => 0,
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

<table>
    <tr>
        <th>No.</th>
        <th>Document</th>
        <th>Language</th>
        <th>Epoque</th>
        <th>Word</th>
        <th width="60%" >Context</th>
    </tr>

<?php
for ($i=0;$i<count($words);$i++) {
    $wordText = $words[$i];
    $context = $contexts[$i];

    echo "<tr>";
    echo "<td>".($i+1+$offset)."</td>";
    echo "<td>".$context[0]["documents"]["name"]."</td>";
    echo "<td>".$context[0]["languages"]["code"]."</td>";
    echo "<td>".$context[0]["epoques"]["name"]."</td>";
    echo "<td>".$wordText['text']."&nbsp;";

    $image = $this->Html->image("edit.png", array(
                    "alt" => "edit"
                     ));

    echo $this->Html->link(
                        $image,
                        array('controller' => 'dashboard', 'action' => 'viewWord', $wordText['id']),
                        array(
                            'target'=>'_blank',
                            'escape' => false
                        ))."</td>";
    echo "<td>";
    foreach ($context as $contextWord) {
        if ($contextWord['words']['id'] == $wordText['id']) {
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
        if ($contextWord['words']['id'] == $wordText['id']) {
            $text .= "</b>";
        }

        echo $text." ";
    }

    echo "</td>";
    echo "</tr>\n";
}
?>
</table>
