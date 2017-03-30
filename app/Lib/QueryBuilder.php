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


    public static function matchingWordsCount($choiceIds, $languageIds, $epoqueIds) {

        $result = "select count(*) as total_count from (";
        $result .= QueryBuilder::singleWordsInnerQuery($choiceIds, $languageIds, $epoqueIds);
        $result .= ") as sub2";

        return $result;
    }


    public static function matchingWordsIds($choiceIds, $languageIds, $epoqueIds, $limit, $offset) {

        $result = QueryBuilder::singleWordsInnerQuery($choiceIds, $languageIds, $epoqueIds);
        $result .= " limit $limit offset $offset";

        return $result;
    }

    public static function singleWordsInnerQuery($choiceIds, $languageIds, $epoqueIds) {
        $wMax = pow(2, count($choiceIds)) - 1;
        $result = "select documents.id as document_id,
               sentences.id as sentence_id,
               words.id as word_id,
               (case words.split when 1 then concat(words.stem, words.suffix) else words.text end) as word_text,
               words.position,
               sum(
                   case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id ";
                   $counter = 0;
                   foreach ($choiceIds as $choiceId) {
                       $power2 = pow(2,$counter);
                       $result .= "when $choiceId then $power2\n";
                       $counter++;
                   }
    $result.="
                       else 0
                   end
               ) as criteria_mask

        from
            documents
            inner join sentences on documents.id = sentences.document_id";
            if(!in_array('any', $languageIds)) {
                $result .= " and documents.language_id in (".implode(",", $languageIds).") ";
            }
            if(!in_array('any', $epoqueIds)) {
                $result .= " and documents.epoque_id in (".implode(",", $epoqueIds).") ";
            }
            $result.="
            inner join words on sentences.id = words.sentence_id
            inner join word_annotations on words.id = word_annotations.word_id
            inner join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id

        group by
            documents.id,
            sentences.id,
            words.id

        having
            criteria_mask = $wMax

        order by documents.id, sentences.id, words.position";
        return $result;
    }

    private static function matchingSentencesInnerQuery($mainChoiceIds, $collocationChoiceIds, $languageIds, $epoqueIds, $maxDist, $twoWay) {
        $w1Max = pow(2, count($mainChoiceIds)) - 1;
        $w2Max = pow(2, count($collocationChoiceIds)) - 1;

        $result = "select
               sentence_id,
               within_distance( group_concat(position1 order by position1) , group_concat(position2 order by position2), $maxDist, $twoWay) as distance_ok

        from (
            select documents.id as document_id,
                   sentences.id as sentence_id,
                   words.id as word_id,

                   if (sum(
                       case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id ";
                       $counter = 0;
                       foreach ($mainChoiceIds as $choiceId) {
                           $power2 = pow(2,$counter);
                           $result .= "when $choiceId then $power2\n";
                           $counter++;
                       }
        $result.="
                           else 0
                       end
                   ) = $w1Max, words.position, NULL) as position1,

                   if (sum(
                       case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id ";
                       $counter = 0;
                       foreach ($collocationChoiceIds as $choiceId) {
                           $power2 = pow(2,$counter);
                           $result .= "when $choiceId then $power2\n";
                           $counter++;
                       }
       $result .="
                           else 0
                       end
                   ) = $w2Max, words.position, NULL) as position2

            from
                documents
                inner join sentences on documents.id = sentences.document_id";
                if(!in_array('any', $languageIds)) {
                    $result .= " and documents.language_id in (".implode(",", $languageIds).") ";
                }
                if(!in_array('any', $epoqueIds)) {
                    $result .= " and documents.epoque_id in (".implode(",", $epoqueIds).") ";
                }
        $result .="
                inner join words on sentences.id = words.sentence_id
                inner join word_annotations on words.id = word_annotations.word_id
                inner join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id

            group by
                documents.id,
                sentences.id,
                words.id

            having
                position1 is not NULL or position2 is not NULL

        ) as sub

        group by
            document_id,
            sentence_id

        having
            distance_ok = 1
        ";

        return $result;

    }

    public static function sentenceWithCollocations($sentenceId, $mainChoiceIds, $collocationChoiceIds) {
        $w1Max = pow(2, count($mainChoiceIds)) - 1;
        $w2Max = pow(2, count($collocationChoiceIds)) - 1;
        $result = "
        select documents.id,
               documents.name,
               languages.code,
               epoques.name,
               sentences.id,
               words.id,
               case words.split when 1 then concat(words.stem, '|', words.suffix) else words.text end as word_text,
               words.position,
               if (sum(
                   case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id ";
                   $counter = 0;
                   foreach ($mainChoiceIds as $choiceId) {
                       $power2 = pow(2,$counter);
                       $result .= "when $choiceId then $power2\n";
                       $counter++;
                   }
        $result.="
                       else 0
                   end
               ) = $w1Max or
               sum(
                   case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id ";
                   $counter = 0;
                   foreach ($collocationChoiceIds as $choiceId) {
                       $power2 = pow(2,$counter);
                       $result .= "when $choiceId then $power2\n";
                       $counter++;
                   }
        $result .="
                       else 0
                   end
               ) = $w2Max, group_concat(word_annotation_type_choices.value), NULL) as tags

        from
            documents
            inner join sentences on documents.id = sentences.document_id and sentences.id = $sentenceId inner join languages on languages.id = documents.language_id
            left join epoques on epoques.id = documents.epoque_id
            inner join words on sentences.id = words.sentence_id
            left join word_annotations on words.id = word_annotations.word_id
            left join word_annotation_type_choices_word_annotations on word_annotation_type_choices_word_annotations.word_annotation_id = word_annotations.id
            left join word_annotation_type_choices on word_annotation_type_choices.id = word_annotation_type_choices_word_annotations.word_annotation_type_choice_id

        group by words.id

        order by documents.id, sentences.id, words.position;";

        return $result;
    }

    public static function matchingSentencesCount($mainChoiceIds, $collocationChoiceIds, $languageIds, $epoqueIds, $maxDist, $twoWay) {

        $result = "select count(*) as total_count from (";
        $result .= QueryBuilder::matchingSentencesInnerQuery($mainChoiceIds, $collocationChoiceIds, $languageIds, $epoqueIds, $maxDist, $twoWay);
        $result .= ") as sub2";

        return $result;
    }


    public static function matchingSentencesIds($mainChoiceIds, $collocationChoiceIds, $languageIds, $epoqueIds, $maxDist, $twoWay, $limit, $offset) {

        $result = QueryBuilder::matchingSentencesInnerQuery($mainChoiceIds, $collocationChoiceIds, $languageIds, $epoqueIds, $maxDist, $twoWay);
        $result .= " limit $limit offset $offset";

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

    public static function multicollocations($documentIds, $multiWord1ChoiceIds, $multiWord2ChoiceIds, $multiWord3ChoiceIds) {
        $result = "SELECT * FROM `sentences` INNER JOIN `words` AS MW1 ON `sentences`.`id` = MW1.`sentence_id` AND MW1.`id` IN (".self::singleWordChoices($multiWord1ChoiceIds).")";
        if (!empty($documentIds)) {
            $result .= " AND `sentences`.`document_id` IN (".implode(',', $documentIds).")";
        } else {
            $result .= " AND false";
        }

        $result .= " INNER JOIN `words` AS MW2 ON `sentences`.`id` = MW2.`sentence_id` AND MW2.`id` IN (".self::singleWordChoices($multiWord2ChoiceIds).")";

        $result .= " INNER JOIN `words` AS MW3 ON `sentences`.`id` = MW3.`sentence_id` AND MW3.`id` IN (".self::singleWordChoices($multiWord3ChoiceIds).") ORDER BY `sentences`.`id`, MW1.`id`";

        return $result;
    }

    private static function choiceCondition($number, $choiceId) {
        return " INNER JOIN `word_annotations` AS WA".$number." ON `words`.`id` = WA".$number.".`word_id` INNER JOIN `word_annotation_type_choices_word_annotations` AS CH".$number." ON WA".$number.".id = CH".$number.".`word_annotation_id` AND CH".$number.".`word_annotation_type_choice_id` = ".$choiceId;
    }
}
?>
