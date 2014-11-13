<div id="users-list">
    <h3>Users list</h3>
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
                '+ Add user', array('action' => 'add')
            );
        ?>
    </p>
    <table>
        <tr>
            <th>Id</th>
            <th width="50%">Username</th>
            <th>Role</th>
            <th width="400px">Actions</th>
        </tr>

        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['User']['id']; ?></td>
            <td><?php echo $user['User']['username']; ?></td>
            <td><?php echo $user['Role']['name']; ?></td>
            <td>
                <?php if ($user['User']['role_id'] != 1) { ?>
                <?php
                    echo $this->Html->link(
                        'Change role',
                        array('action' => 'edit', $user['User']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Reset password',
                        array('action' => 'resetPassword', $user['User']['id']),
                        array('confirm' => 'This will reset the user\'s password to "tagger". Are you sure?')
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    if ($user['User']['id'] != $currentUserId) {
                        echo $this->Form->postLink(
                            'Delete',
                            array('action' => 'delete', $user['User']['id']),
                            array('confirm' => 'Are you sure?')
                        );
                    }
                ?>
                <?php } ?>

            </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>
