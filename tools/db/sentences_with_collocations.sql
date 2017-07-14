select count(*)

from (

select document_id,
       sentence_id,
       group_concat(position1 order by position1) as positions1,
       group_concat(position2 order by position2) as positions2,
       within_distance( group_concat(position1 order by position1) , group_concat(position2 order by position2), 0, false) as distance_ok

from (
    select documents.id as document_id,
           sentences.id as sentence_id,
           words.id as word_id,

           if (sum(
               case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id
                   when 47 then 1
                   when 51 then 2
                   when 78 then 4
                   else 0
               end
           ) = 7, words.position, NULL) as position1,

           if (sum(
               case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id
                   when 46 then 1
                   when 78 then 2
                   else 0
               end
           ) = 3, words.position, NULL) as position2

    from
        documents
        inner join sentences on documents.id in (3,9) and documents.id = sentences.document_id
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

) as sub2;
