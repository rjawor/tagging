<div id="languages-list">
    <h3>Languages list</h3>
    <?php
    echo $this->Html->image('left.png', array(
                                          'alt' => 'back to configuration',
                                          'title' => 'back to configuration',
                                          'url' => array('controller'=>'configuration')
                                      )
                           );
    ?>
    <br/><br/>
    <p>
        <?php
            echo $this->Html->link(
                '+ Add language', array('action' => 'add')
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
                    echo $this->Html->link(
                        'Edit',
                        array('action' => 'edit', $language['Language']['id'])
                    );
                ?>
                &nbsp;&nbsp;
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

