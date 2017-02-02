select lang, count(word_id) from (select words.id as word_id, languages.description as lang from documents inner join languages on documents.language_id  = languages.id inner join sentences on sentences.document_id = documents.id inner join words on words.sentence_id = sentences.id inner join word_annotations on words.id = word_annotations.word_id group by words.id) as sub group by lang;
