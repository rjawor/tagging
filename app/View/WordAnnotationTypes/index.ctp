<div id="word-annotation-types-list">
    <h3>Word annotation types list</h3>
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
                '+ Add word annotation type', array('action' => 'add')
            );
        ?>
    </p>
    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Strict choices</th>
            <th>Multiple choices</th>
            <th>Order</th>
            <th style="max-width:300px;">Actions</th>
        </tr>


        <?php foreach ($wordAnnotationTypes as $wordAnnotationType): ?>
        <tr>
            <td><?php echo $wordAnnotationType['WordAnnotationType']['name']; ?></td>
            <td><?php echo $wordAnnotationType['WordAnnotationType']['description']; ?></td>
            <td><?php echo $wordAnnotationType['WordAnnotationType']['strict_choices']?"+":"-"; ?></td>
            <td><?php echo $wordAnnotationType['WordAnnotationType']['multiple_choices']?"+":"-"; ?></td>
            <td>
                <?php 
                    if ($wordAnnotationType['WordAnnotationType']['position'] > 0) {
                        echo $this->Html->image('up.png', array(
                                                              'alt' => 'Move up',
                                                              'title' => 'Move up',
                                                              'url' => array('action' => 'move', $wordAnnotationType['WordAnnotationType']['position'], -1)
                                                          )
                                               );
                    }
                    if ($wordAnnotationType['WordAnnotationType']['position'] < count($wordAnnotationTypes) - 1 ) {
                        echo $this->Html->image('down.png', array(
                                                              'alt' => 'Move down',
                                                              'title' => 'Move down',
                                                              'url' => array('action' => 'move', $wordAnnotationType['WordAnnotationType']['position'], 1)
                                                          )
                                               );
                    }
                ?>
            </td>
            <td style="max-width:300px;">
                <?php
                    echo $this->Html->link(
                        'Edit',
                        array('action' => 'edit', $wordAnnotationType['WordAnnotationType']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $wordAnnotationType['WordAnnotationType']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                ?>
                
                <?php
                    if ($wordAnnotationType['WordAnnotationType']['strict_choices']) {
                        echo "&nbsp;&nbsp;";
                        echo $this->Html->link(
                            'Edit choices',
                            array('controller' => 'WordAnnotationTypeChoices', 'action' => 'index', $wordAnnotationType['WordAnnotationType']['id'])
                        );
                    }
                ?>
        </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

