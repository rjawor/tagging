<form method="post" id="filter_form">
    <input type="hidden" name="data[mainValue]" value="<?php echo $mainValue; ?>" />
    <input type="hidden" name="data[collocationValue]" value="<?php echo $collocationValue; ?>" />
    <input type="hidden" name="data[specificValue]" value="<?php echo $specificValue; ?>" />
    <input type="hidden" name="data[immediate]" value="<?php echo $immediate; ?>" />
    <input id="page_number" type="hidden" name="data[page]" value="<?php echo $page; ?>" />
    <input id="total_pages" type="hidden"value="<?php echo $totalPages; ?>" />
    <h4>Narrow by:</h4>
    <table>
        <tr>
            <th>Language</th>
            <th>Epoque</th>
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
