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


    public static function matchingWordsCount($choiceIds, $filter, $initial) {

        $result = "select count(*) as total_count from (";
        $result .= QueryBuilder::singleWordsInnerQuery($choiceIds, $filter, $initial);
        $result .= ") as sub2";

        return $result;
    }


    public static function matchingWordsIds($choiceIds, $filter, $initial, $limit, $offset) {

        $result = QueryBuilder::singleWordsInnerQuery($choiceIds, $filter, $initial);
        $result .= " limit $limit offset $offset";

        return $result;
    }

    public static function singleWordsInnerQuery($choiceIds, $filter, $initial) {
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
            if(!in_array('any', $filter['selectedLanguages'])) {
                $result .= " and documents.language_id in (".implode(",", $filter['selectedLanguages']).") ";
            }
            if(!in_array('any', $filter['selectedEpoques'])) {
                $result .= " and documents.epoque_id in (".implode(",", $filter['selectedEpoques']).") ";
            }
            if(!in_array('any', $filter['selectedDocuments'])) {
                $result .= " and documents.id in (".implode(",", $filter['selectedDocuments']).") ";
            }
            $result.="
            inner join words on sentences.id = words.sentence_id";
            if ($initial == 1) {
                $result.=" and words.position = 0 ";
            } else  if ($initial == 2) {
                $result.=" and words.position > 0 ";
            }
    $result.="
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

    private static function valuesToParams($wordValues) {
        $wordParams = array();
        foreach ($wordValues as $wordValue) {
            if (!empty($wordValue)) {
                $params = explode(',', $wordValue);
                array_push($wordParams,
                    array(
                        'params' => $params,
                        'max' => (pow(2, count($params)) - 1)
                    )
                );
            }

        }
        return $wordParams;
    }

    private static function tagsCondition($word) {
        $result = "sum(
            case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id ";
        $counter = 0;
        foreach ($word['params'] as $choiceId) {
            $power2 = pow(2,$counter);
            $result .= "when $choiceId then $power2\n";
            $counter++;
        }
        $result.="
                else 0
            end
        ) = ".$word['max'];

        return $result;
    }

    private static function matchingSentencesInnerQuery($wordValues, $filter, $maxDist, $twoWay) {
        $wordParams = self::valuesToParams($wordValues);
        $result = "select
               sentence_id,";
        for($i=1;$i<=count($wordParams);$i++) {
            $result .= "group_concat(position".$i." order by position".$i.") as positions".$i.",";
        }
        $result .= "
               within_distance( group_concat(position1 order by position1) , group_concat(position2 order by position2), $maxDist, $twoWay) as distance_ok";

        $result .= "
        from (
            select documents.id as document_id,
                   sentences.id as sentence_id,
                   words.id as word_id,";
        $wordCounter = 0;
        foreach($wordParams as $word) {
            $wordCounter++;
            $result .= "if (".self::tagsCondition($word).", words.position, NULL) as position".$wordCounter;
            if ($wordCounter < count($wordParams)) {
                $result .= ",";
            }
        }

       $result .="
            from
                documents
                inner join sentences on documents.id = sentences.document_id";
                if(!in_array('any', $filter['selectedLanguages'])) {
                    $result .= " and documents.language_id in (".implode(",", $filter['selectedLanguages']).") ";
                }
                if(!in_array('any', $filter['selectedEpoques'])) {
                    $result .= " and documents.epoque_id in (".implode(",", $filter['selectedEpoques']).") ";
                }
                if(!in_array('any', $filter['selectedDocuments'])) {
                    $result .= " and documents.id in (".implode(",", $filter['selectedDocuments']).") ";
                }
        $result .="
                inner join words on sentences.id = words.sentence_id
                inner join word_annotations on words.id = word_annotations.word_id
                inner join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id

            group by
                documents.id,
                sentences.id,
                words.id

            having";
        for($i=1;$i<=count($wordParams);$i++) {
            $result .= " position".$i." is not NULL";
            if ($i < count($wordParams)) {
                $result .= " or";
            }
        }
        $result .="
        ) as sub

        group by
            document_id,
            sentence_id

        having";
        for($i=1;$i<=count($wordParams);$i++) {
            $result .= " positions".$i." is not NULL and";
        }

        $result .= " distance_ok = 1";

        return $result;

    }

    public static function sentenceWithCollocations($sentenceId, $wordValues) {
        $wordParams = self::valuesToParams($wordValues);
        $result = "
        select documents.id,
               documents.name,
               languages.code,
               epoques.name,
               sentences.id,
               words.id,
               case words.split when 1 then concat(words.stem, '|', words.suffix) else words.text end as word_text,
               words.position,
               if (";
        $counter = 0;
        foreach ($wordParams as $word) {
            $counter++;
            $result .= self::tagsCondition($word);
            if ($counter < count($wordParams)) {
                $result .= " or ";
            }
        }
        $result .= "
               ,group_concat(word_annotation_type_choices.value),
               NULL) as tags

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

    public static function matchingSentencesCount($wordValues, $filter, $maxDist, $twoWay) {

        $result = "select count(*) as total_count from (";
        $result .= QueryBuilder::matchingSentencesInnerQuery($wordValues, $filter, $maxDist, $twoWay);
        $result .= ") as sub2";

        return $result;
    }


    public static function matchingSentencesIds($wordValues, $filter, $maxDist, $twoWay, $limit, $offset) {

        $result = QueryBuilder::matchingSentencesInnerQuery($wordValues, $filter, $maxDist, $twoWay);
        $result .= " limit $limit offset $offset";

        return $result;
    }

    private static function choiceCondition($number, $choiceId) {
        return " INNER JOIN `word_annotations` AS WA".$number." ON `words`.`id` = WA".$number.".`word_id` INNER JOIN `word_annotation_type_choices_word_annotations` AS CH".$number." ON WA".$number.".id = CH".$number.".`word_annotation_id` AND CH".$number.".`word_annotation_type_choice_id` = ".$choiceId;
    }
}
?>
