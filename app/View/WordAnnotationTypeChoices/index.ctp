<div id="word-annotation-type-choices-list">
    <h3>Choices list for word annotation type: <?php echo $wordAnnotationType['WordAnnotationType']['name'];?></h3>
    <?php
    echo $this->Html->image('left.png', array(
                                          'alt' => 'back to word annotations list',
                                          'title' => 'back to word annotations list',
                                          'url' => array('controller'=>'wordAnnotationTypes','action'=>'index')
                                      )
                           );
    ?>
    <br/><br/>
    <p>
        <?php
            echo $this->Html->link(
                '+ Add word annotation type choice', array('action' => 'add', $wordAnnotationType['WordAnnotationType']['id'])
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
            <td><?php echo $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['value']; ?></td>
            <td><?php echo $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['description']; ?></td>
            <td>
                <?php 
                    if ($wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'] > 0) {
                        echo $this->Html->image('up.png', array(
                                                              'alt' => 'Move up',
                                                              'title' => 'Move up',
                                                              'url' => array('action' => 'move', $wordAnnotationType['WordAnnotationType']['id'], $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'], -1)
                                                          )
                                               );
                    }
                    if ($wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'] < count($wordAnnotationTypeChoices) - 1 ) {
                        echo $this->Html->image('down.png', array(
                                                              'alt' => 'Move down',
                                                              'title' => 'Move down',
                                                              'url' => array('action' => 'move', $wordAnnotationType['WordAnnotationType']['id'], $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['position'], 1)
                                                          )
                                               );
                    }
                ?>
            </td>
            <td style="max-width:300px;">
                <?php
                    echo $this->Html->link(
                        'Edit',
                        array('action' => 'edit', $wordAnnotationType['WordAnnotationType']['id'], $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $wordAnnotationType['WordAnnotationType']['id'], $wordAnnotationTypeChoice['WordAnnotationTypeChoice']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                ?>
        </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

