<?php if(isset($documentWindow)) { ?>

<h1><strong><?php echo $documentWindow['Document']['name']; ?></strong></h1>

<input id="offset" type="hidden" value="<?php echo $offset ?>" />

<?php $sentenceCount = 0; ?>
<?php foreach ($documentWindow['Sentence'] as $sentence): ?>
    <span id="sentence<?php echo $sentenceCount; ?>">
        <p>
            <?php foreach ($sentence['Word'] as $word): ?>
                <?php echo $word['text'] ?>&nbsp;
            
            <?php endforeach; ?>
        </p>
        <table>
            <tr>
                <td class="annotation_column"></td>
                <?php foreach ($sentence['Word'] as $word): ?>
                    <td><?php echo $word['text'] ?></td>
                <?php endforeach; ?>
            </tr>

            <?php foreach ($wordAnnotationTypes as $wordAnnotationType): ?>
            <tr>
                <td class="annotation_column"><?php echo $wordAnnotationType['WordAnnotationType']['name'] ?></td>
                <?php foreach ($sentence['Word'] as $word): ?>
                    <td></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>

            <?php foreach ($sentenceAnnotationTypes as $sentenceAnnotationType): ?>
            <tr>
                <td class="annotation_column"><?php echo $sentenceAnnotationType['SentenceAnnotationType']['name'] ?></td>
                <td colspan="<?php echo count($sentence['Word']) ?>"></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </span>
<?php endforeach; ?>

<pre>
<?php print_r($wordAnnotationTypes) ?>
<?php print_r($documentWindow) ?>
</pre>

<?php } ?>
