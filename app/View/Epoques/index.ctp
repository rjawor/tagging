<div id="epoques-list">
    <h3>Epoques list</h3>
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
                '+ Add epoque', array('action' => 'add')
            );
        ?>
    </p>
    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>


        <?php foreach ($epoques as $epoque): ?>
        <tr>
            <td><?php echo $epoque['Epoque']['name']; ?></td>
            <td><?php echo $epoque['Epoque']['description']; ?></td>
            <td>
                <?php
                    echo $this->Html->link(
                        'Edit',
                        array('action' => 'edit', $epoque['Epoque']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $epoque['Epoque']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                ?>
        </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>
