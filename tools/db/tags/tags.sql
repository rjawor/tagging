select tag, count(*) as tagCount from 
    (select group_concat(word_annotation_type_choice_id order by word_annotation_type_choice_id asc) as tag from
        word_annotations inner join
        word_annotation_type_choices_word_annotations on word_annotations.id = word_annotation_type_choices_word_annotations.word_annotation_id and word_annotation_type_choices_word_annotations.word_annotation_type_choice_id <= 86
         group by word_id
     ) as tags
     group by tag having tagCount >=3 order by tagCount;

