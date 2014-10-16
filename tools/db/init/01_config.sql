SET NAMES 'utf8' COLLATE 'utf8_general_ci';

INSERT INTO `word_annotation_types` VALUES (1,'lexeme',0,0,'glossing - lexical information'), (2,'grammar',1,1,'glossing - grammatical information'), (3,'POS',1,0,'part of speech'),(4,'syntax',1,0,'syntactic information'),(5,'semantics',1,0,'semantic information'),(6,'pragmatic',1,0,'pragmatic information');

INSERT INTO `word_annotation_type_choices` VALUES (1,'OBL',2),(2,'PPP',2),(3,'F',2),(4,'SG',2),(5,'PL',2),(6,'M',2),(7,'PRON',3),(8,'NOUN',3),(9,'PRNOUN',3),(10,'PREP',3),(11,'PART',3),(12,'SUBJ',4),(13,'V',4),(14,'AG',5),(15,'REC',5),(16,'TOP',6);

INSERT INTO `sentence_annotation_types` VALUES (1,'english','translation into English');

INSERT INTO `languages` VALUES (1,'hi','Hindi');

INSERT INTO `roles` VALUES (1,'administrator'), (2,'editor'), (3,'reader');
