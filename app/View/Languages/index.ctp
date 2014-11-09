<div id="languages-list">
    <p>
        <?php
            echo $this->Html->link(
                'Add language', array('action' => 'add')
            );
        ?>
    </p>
    <table>
        <tr>
            <th>Description</th>
            <th>Code</th>
            <th>Actions</th>
        </tr>


        <?php foreach ($languages as $language): ?>
        <tr>
            <td><?php echo $language['Language']['description']; ?></td>
            <td><?php echo $language['Language']['code']; ?></td>
            <td>
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $language['Language']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                ?>
        </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

