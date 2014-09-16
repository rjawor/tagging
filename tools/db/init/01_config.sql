SET NAMES 'utf8' COLLATE 'utf8_general_ci';

INSERT INTO `word_annotation_types` VALUES (1,'glossing',1,1,'na podstawie Leipzig glossing rules'),(2,'POS',1,0,'oznaczenie części mowy'),(3,'syntax',1,0,'informacja syntaktyczna'),(4,'semantics',1,0,'informacja semantyczna'),(5,'pragmatic',1,1,'informacja pragmatyczna');

INSERT INTO `word_annotation_type_choices` VALUES (1,'OBL',1),(2,'PPP',1),(3,'F',1),(4,'SG',1),(5,'PL',1),(6,'M',1),(7,'PRON',2),(8,'NOUN',2),(9,'PRNOUN',2),(10,'PREP',2),(11,'PART',2),(12,'SUBJ',3),(13,'V',3),(14,'AG',4),(15,'REC',4),(16,'TOP',5);

INSERT INTO `sentence_annotation_types` VALUES (1,'english','Translation into English');

INSERT INTO `languages` VALUES (1,'hi','Hindi');

INSERT INTO `roles` VALUES (1,'administrator'),(2,'edytor'),(3,'czytelnik');
