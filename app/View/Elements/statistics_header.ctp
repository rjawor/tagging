<form method="post" id="filter_form">
    <input type="hidden" name="data[mainValue]" value="<?php echo $mainValue; ?>" />
    <input type="hidden" name="data[initial]" value="<?php echo $initial; ?>" />
    <input type="hidden" name="data[collocationValue]" value="<?php echo $collocationValue; ?>" />
    <input type="hidden" name="data[specificValue]" value="<?php echo $specificValue; ?>" />
    <input type="hidden" name="data[initialSpecific]" value="<?php echo $initialSpecific; ?>" />
    <input type="hidden" name="data[immediate]" value="<?php echo $immediate; ?>" />
    <input id="page_number" type="hidden" name="data[page]" value="<?php echo $page; ?>" />
    <input id="total_pages" type="hidden"value="<?php echo $totalPages; ?>" />
    <h4>Narrow by:</h4>
    <table>
        <tr>
            <th>Language</th>
            <th>Epoque</th>
            <th>Document</th>
        </tr>
        <tr>
            <td>
                <input <?php if(in_array('any', $selectedLanguages)) echo "checked";  ?> style="float:none" onclick="langCheckboxAnyClicked();" id="langAny" type="checkbox" name="data[languages][]" value="any"><i>Any</i><br>
                <?php foreach ($languages as $language) { ?>
                <input class="langCheckbox" <?php if(in_array($language['Language']['id'], $selectedLanguages)) echo "checked";  ?> style="float:none" type="checkbox" name="data[languages][]" onclick="langCheckboxClicked();" value="<?= $language['Language']['id'] ?>" ><?= $language['Language']['description'] ?><br>
                <?php } ?>
            </td>
            <td>
                <input <?php if(in_array('any', $selectedEpoques)) echo "checked";  ?> style="float:none" onclick="epoqueCheckboxAnyClicked();" id="epoqueAny" type="checkbox" name="data[epoques][]" value="any"><i>Any</i><br>
                <?php foreach ($epoques as $epoque) { ?>
                <input class="epoqueCheckbox" <?php if(in_array($epoque['Epoque']['id'], $selectedEpoques)) echo "checked";  ?> style="float:none" type="checkbox" name="data[epoques][]" onclick="epoqueCheckboxClicked();" value="<?= $epoque['Epoque']['id'] ?>" title="<?= $epoque['Epoque']['description'] ?>" ><?= $epoque['Epoque']['name'] ?><br>
                <?php } ?>
            </td>
            <td style="max-width:none">
                <input <?php if(in_array('any', $selectedDocuments)) echo "checked";  ?> style="float:none" onclick="documentCheckboxAnyClicked();" id="documentAny" type="checkbox" name="data[documents][]" value="any"><i>Any</i>
                <div id="documentFilterArea" style="max-height:300px;overflow-y:auto">
                    <table>
                    <?php
                        $docsPerRow = 5;
                        $index = 0;
                        foreach ($documents as $document) {
                            if ($index % $docsPerRow == 0) {
                                echo "<tr>";
                            }
                            ?>
                            <td><input class="documentCheckbox" <?php if(in_array($document['Document']['id'], $selectedDocuments)) echo "checked";  ?> style="float:none" type="checkbox" name="data[documents][]" onclick="documentCheckboxClicked();" value="<?= $document['Document']['id'] ?>" ><?= $document['Document']['name']?></td>
                            <?php
                            if ($index % $docsPerRow == $docsPerRow - 1) {
                                echo "</tr>";
                            }

                            $index++;
                        }

                        if ($index % $docsPerRow != 0) {
                            echo "</tr>";
                        }
                    ?>
                    </table>
                </div>
                <input id="documentsScrollTop" type="hidden" name="data[documentsScrollTop]" value="" />
                <script language="javascript">
                    $('#documentFilterArea').scrollTop(<?= $documentsScrollTop ?>);
                </script>
            </td>
        </tr>
    </table>
    <?php
    if ($enablePagination) {
    ?>
        Total number of <?= $itemName ?> found: <strong><?= $itemTotalCount ?></strong><br/><br/>
        Page:<br/>
        <a href="#" onclick="setPage(0);">First</a>&nbsp;<a href="#" onclick="decreasePage();">Previous</a> <b><?= ($page + 1)?></b> of <?= $totalPages?> <a href="#" onclick="increasePage();">Next</a>&nbsp;<a href="#" onclick="setPage(<?= $totalPages - 1?>);">Last</a><br/><br/>
    <?php
    }
    ?>
</form>
