SET NAMES 'utf8' COLLATE 'utf8_general_ci';

INSERT INTO `word_annotation_types` VALUES (1,'glossing',1,1,'na podstawie Leipzig glossing rules'),(2,'POS',1,0,'oznaczenie części mowy'),(3,'syntax',1,0,'informacja syntaktyczna'),(4,'semantics',1,0,'informacja semantyczna'),(5,'pragmatic',1,1,'informacja pragmatyczna');

INSERT INTO `sentence_annotation_types` VALUES (1,'english','Translation into English');

INSERT INTO `languages` VALUES (1,'hi','Hindi');

INSERT INTO `roles` VALUES (1,'administrator'),(2,'edytor'),(3,'czytelnik');
