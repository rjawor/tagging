<div id="word<?= $id ?>-div" <?php if ($visible == false) { echo "class=\"hidden\"";} ?> >
    <label for="word<?= $id ?>"><?= $numeral ?> word:</label>
    <input type="hidden" id="word<?= $id ?>Value" name="data[wordValues][]" />
    <input id="word<?= $id ?>" type="text" class="multiple-suggestions-input" value=""/>
    Selected criteria: (clicking on a criterion deletes it) <br/><br/>
    <?php foreach ($choices as $choice) { ?>
        <input name="word<?= $id ?>Button" id="<?php echo $choice['id'];?>" type="button" class="choice-inactive" title="<?php echo $choice['description']; ?>" value="<?php echo $choice['value']; ?>" onclick="this.className='choice-inactive';"/>
    <?php } ?>
    <?php
    if ($id > 1 && $id < 5) {
    ?>
        <span id="showNext<?= $id ?>" style="cursor:pointer" onclick="showWordCriteria(<?= $id ?>)"><br/><br/><img src="<?= Configure::read('SystemInstallationPath') ?>/img/plus.png" >Add another word</span>
    <?php
    }
    ?>

</div>
