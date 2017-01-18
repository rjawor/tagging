select documents.id,
       documents.name,
       sentences.id,
       words.id,
       words.text,
       words.position,
       if (sum(
           case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id
               when 47 then 1
               when 51 then 2
               when 78 then 4
               else 0
           end
       ) = 7 or
       sum(
           case word_annotation_type_choices_word_annotations.word_annotation_type_choice_id
               when 46 then 1
               when 78 then 2
               else 0
           end
       ) = 3, group_concat(word_annotation_type_choices.value), NULL) as tags

from
    documents
    inner join sentences on documents.id = sentences.document_id and sentences.id in (47,53)
    inner join languages on languages.id = documents.language_id
    inner join words on sentences.id = words.sentence_id
    inner join word_annotations on words.id = word_annotations.word_id
    inner join word_annotation_type_choices_word_annotations on word_annotation_type_choices_word_annotations.word_annotation_id = word_annotations.id
    inner join word_annotation_type_choices on word_annotation_type_choices.id = word_annotation_type_choices_word_annotations.word_annotation_type_choice_id

group by words.id

order by documents.id, sentences.id, words.position;
