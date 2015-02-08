<h3>IA tagger - user's manual</h3>

<?php foreach ($helpSections as $helpSection): ?>
<div class="help-section" id="<?php echo $helpSection['HelpSection']['position']?>">
    <?php
    if ($roleId == 1) {
    ?>
    <div>
    <?php
        echo $this->Html->image("plus.png", array("id" => "add".$helpSection['HelpSection']['position'],
                                                 "alt" => "add new section before current",
                                                 "title" => "add new section before current",
                                                 "url" => array("action"=>"add", $helpSection['HelpSection']['position'])
                                           )
                              )."&nbsp;";
        echo $this->Html->image("editLarge.png", array("id" => "edit".$helpSection['HelpSection']['id'],
                                                 "alt" => "edit this section",
                                                 "title" => "edit this section",
                                                 "url" => array("action"=>"edit", $helpSection['HelpSection']['id'])
                                           )
                              )."&nbsp;";
        echo $this->Form->postLink(
            $this->Html->image("delete.png", array("id" => "delete".$helpSection['HelpSection']['id'],
                                                   "alt" => "delete this section",
                                                   "title" => "delete this section",
                                           )
                              ),
            array('action' => 'delete', $helpSection['HelpSection']['id']),
            array('escape' => false, 'confirm' => 'Are you sure you want to delete this help section?')        
        );

    ?>
    </div>        
    <?php
    }
    ?>
    <?php echo $helpSection['HelpSection']['text']?>
</div>
<?php endforeach;?>

<?php
if ($roleId == 1) {
?>
<div>
<?php
   echo $this->Html->image("plusNext.png", array("id" => "add".count($helpSections),
                                             "alt" => "add new section",
                                             "title" => "add new section",
                                             "url" => array("action"=>"add", count($helpSections))
                                       )
                          );
?>
</div>        
<?php
}
?>

