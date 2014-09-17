<p><strong><?php echo h($document['Document']['name']); ?></strong></p>

<?php foreach ($document['Sentence'] as $sentence): ?>

<p>
    <?php foreach ($sentence['Word'] as $word): ?>
        <?php echo $word['text'] ?>&nbsp;
    <?php endforeach; ?>

</p>


<?php endforeach; ?>

