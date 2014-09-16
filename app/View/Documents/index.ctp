<p><?php echo $this->Html->link('Dodaj dokument', array('action' => 'add')); ?></p>
<table>
    <tr>
        <th>Id</th>
        <th>Nazwa</th>
        <th>Język</th>
        <th>Właściciel</th>
        <th>Akcje</th>
    </tr>

    <?php foreach ($documents as $document): ?>
    <tr>
        <td><?php echo $document['Document']['id']; ?></td>
        <td>
            <?php
                echo $this->Html->link(
                    $document['Document']['name'],
                    array('action' => 'view', $document['Document']['id'])
                );
            ?>
        </td>
        <td><?php echo $document['Language']['code']; ?> (<?php echo $document['Language']['description']; ?>)</td>
        <td>
        </td>
        <td>
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
        </td>
    </tr>
    <?php endforeach; ?>

</table>
