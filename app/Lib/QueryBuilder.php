<?php

class QueryBuilder {
    public static function singleWordDocuments($documentIds) {
        $result = "SELECT DISTINCT `words`.`id` FROM `sentences` INNER JOIN `words` ON `sentences.id` = `words.sentence_id` AND `sentences.document_id` IN (".implode(',', $documentIds).")";
        return $result;
    }


    public static function singleWordChoices($choiceIds) {
        $result = "SELECT DISTINCT `words`.`id` FROM `words`";
        $number = 0;
        foreach ($choiceIds as $choiceId) {
            $number++;
            $result .= self::choiceCondition($number, $choiceId);
        }
        return $result;
    }

    public static function collocations($documentIds, $mainChoiceIds, $collocationChoiceIds) {
        $result = "SELECT *, ABS(MW.`position`-CW.`position`) AS dist FROM `sentences` INNER JOIN `words` AS MW ON `sentences`.`id` = MW.`sentence_id` AND MW.`id` IN (".self::singleWordChoices($mainChoiceIds).")";
        if (!empty($documentIds)) {
            $result .= " AND `sentences`.`document_id` IN (".implode(',', $documentIds).")";
        } else {
            $result .= " AND false";
        }
        $result .= " INNER JOIN `words` AS CW ON `sentences`.`id` = CW.`sentence_id` AND CW.`id` IN (".self::singleWordChoices($collocationChoiceIds).") ORDER BY `sentences`.`id`, MW.`id`, dist";
        
        return $result;
    }
    
    private static function choiceCondition($number, $choiceId) {
        return " INNER JOIN `word_annotations` AS WA".$number." ON `words`.`id` = WA".$number.".`word_id` INNER JOIN `word_annotation_type_choices_word_annotations` AS CH".$number." ON WA".$number.".id = CH".$number.".`word_annotation_id` AND CH".$number.".`word_annotation_type_choice_id` = ".$choiceId;
    }
}
?>
