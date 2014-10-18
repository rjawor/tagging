<div id="document-list">
    <table>
        <tr>
            <th>Id</th>
            <th></th>
            <th width="50%">Name</th>
            <th>Language</th>
            <th>Owner</th>
            <!--<th>Akcje</th>-->
        </tr>

        <?php foreach ($documents as $document): ?>
        <tr>
            <td><?php echo $document['Document']['id']; ?></td>
            <td><?php echo $this->Html->image("edit.png", array(
                                    "alt" => "edit",
                                    'url' => array('controller' => 'dashboard', 'action' => 'setCurrentDocument', $document['Document']['id'], 0)
                                     ));
                ?>
            </td>
            <td>
                <?php
                    echo $this->Html->link(
                        $document['Document']['name'],
                        array('action' => 'view', $document['Document']['id'])
                    );
                ?>
            </td>
            <td><?php echo $document['Language']['description']; ?> (<?php echo $document['Language']['code']; ?>)</td>
            <td><?php echo $document['User']['username']; ?></td>
            <!-- <td>
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $document['Document']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                ?>
                <?php
                    echo $this->Html->link(
                        'Edit', array('action' => 'edit', $document['Document']['id'])
                    );
                ?>
            </td> -->
        </tr>
        <?php endforeach; ?>

    </table>
</div>

<div id="import-box">
    <h3>Import document from a text file</h3>
    <?php 
    echo $this->Form->create('Documents', array('type' => 'file', 'action' => 'add'));
    echo $this->Form->input('file',array( 'type' => 'file'));
    echo $this->Form->input('language', array('type' => 'select', 'options' => $languageOptions,'empty' => false));
    echo $this->Form->end('Submit');
    ?>
</div>

