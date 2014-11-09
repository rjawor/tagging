<div id="word-annotation-type-choices-list">
    <p>
        <?php
            echo $this->Html->link(
                'Add word annotation type choice', array('action' => 'add', $wordAnnotationTypeId)
            );
        ?>
    </p>
    <table>
        <tr>
            <th>Value</th>
            <th>Description</th>
            <th>Order</th>
            <th style="max-width:300px;">Actions</th>
        </tr>


        <?php foreach ($wordAnnotationTypeChoices as $wordAnnotationTypeChoice): ?>
        <tr>
            <td><?php echo $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['value']; ?> - <?php echo $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position']; ?></td>
            <td><?php echo $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['description']; ?></td>
            <td>
                <?php 
                    if ($wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'] > 1) {
                        echo $this->Html->image('up.png', array(
                                                              'alt' => 'Move up',
                                                              'title' => 'Move up',
                                                              'url' => array('action' => 'move', $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'], -1)
                                                          )
                                               );
                    }
                    if ($wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'] < count($wordAnnotationTypeChoices) ) {
                        echo $this->Html->image('down.png', array(
                                                              'alt' => 'Move down',
                                                              'title' => 'Move down',
                                                              'url' => array('action' => 'move', $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'], 1)
                                                          )
                                               );
                    }
                ?>
            </td>
            <td style="max-width:300px;">
                <?php
                    echo $this->Html->link(
                        'Edit',
                        array('action' => 'edit', $wordAnnotationTypeId, $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $wordAnnotationTypeId, $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                ?>
        </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

