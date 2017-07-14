select documents.id as document_id,
       sentences.id as sentence_id,
       words.id as word_id,
       words.position,
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
       ) = 3, words.position, NULL) as position2,

       sum(
           case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id
               when 47 then 1
               when 51 then 2
               when 78 then 4
               else 0
           end
       ) as criteria1_mask,

       sum(
           case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id
               when 46 then 1
               when 78 then 2
               else 0
           end
       ) as criteria2_mask

from
    documents
    inner join sentences on documents.id = sentences.document_id
    inner join words on sentences.id = words.sentence_id
    inner join word_annotations on words.id = word_annotations.word_id
    inner join word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id

group by
    documents.id,
    sentences.id,
    words.id

having
    criteria1_mask = 7 or criteria2_mask = 3

order by documents.id, sentences.id, words.position

limit 10;
