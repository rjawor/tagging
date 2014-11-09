<div id="sentetnce-annotation-types-list">
    <p>
        <?php
            echo $this->Html->link(
                'Add sentence annotation type', array('action' => 'add')
            );
        ?>
    </p>
    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Order</th>
            <th>Actions</th>
        </tr>


        <?php foreach ($sentenceAnnotationTypes as $sentenceAnnotationType): ?>
        <tr>
            <td><?php echo $sentenceAnnotationType['SentenceAnnotationType']['name']; ?></td>
            <td><?php echo $sentenceAnnotationType['SentenceAnnotationType']['description']; ?></td>
            <td>
                <?php 
                    if ($sentenceAnnotationType['SentenceAnnotationType']['position'] > 0) {
                        echo $this->Html->image('up.png', array(
                                                              'alt' => 'Move up',
                                                              'title' => 'Move up',
                                                              'url' => array('action' => 'move', $sentenceAnnotationType['SentenceAnnotationType']['position'], -1)
                                                          )
                                               );
                    }
                    if ($sentenceAnnotationType['SentenceAnnotationType']['position'] < count($sentenceAnnotationTypes) - 1 ) {
                        echo $this->Html->image('down.png', array(
                                                              'alt' => 'Move down',
                                                              'title' => 'Move down',
                                                              'url' => array('action' => 'move', $sentenceAnnotationType['SentenceAnnotationType']['position'], 1)
                                                          )
                                               );
                    }
                ?>
            
            </td>
            <td>
                <?php
                    echo $this->Html->link(
                        'Edit',
                        array('action' => 'edit', $sentenceAnnotationType['SentenceAnnotationType']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $sentenceAnnotationType['SentenceAnnotationType']['id']),
                        array('confirm' => 'Are you sure?')
                    );
                ?>
        </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

