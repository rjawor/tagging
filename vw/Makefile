SHELL=/bin/bash
CROSSVALID_GRAN = 10
PASSES = 10


LEARNING_RATE = 8
CONTEXT = 1
SUFFIXES = 3
FL = 0
CLASS = 0
CLASS_CONTEXT = 2
LOSS_FUNCTION = "squared"
ENDING = 0
HYPHEN = 0
NGRAM = 0



CONFIG = learning_rate=$(LEARNING_RATE),context=$(CONTEXT),suffixes=$(SUFFIXES),fl=$(FL),class=$(CLASS),class_context=$(CLASS_CONTEXT),loss_function=$(LOSS_FUNCTION),ending=$(ENDING),hyphen=$(HYPHEN),ngram=$(NGRAM)

all: clean-preserve-data test collect-results

dictionary-predict: test-dictionary
	./collectResults.py $(CROSSVALID_GRAN) dictionary-annotation

clean-preserve-data:
	rm -f data_vw.txt
	rm -f train/*
	rm -f test/*
	rm -f model/*
	rm -f prediction/*

clean:
	rm -f data.txt
	rm -f converbs_dictionary.txt
	rm -f data_vw.txt
	rm -f train/*
	rm -f test/*
	rm -f model/*
	rm -f prediction/*
	rm -f dictionary/*
	rm -f prod/experiment91-170/jayasiP_91-170_data.txt
	rm -f prod/experiment91-170/jayasiP_91-170_data_vw.txt
	rm -f prod/experiment91-170/jayasiP_91-170_predictions.txt
	rm -f prod/experiment90-220/jayasiP_90-220_data.txt
	rm -f prod/experiment90-220/jayasiP_90-220_data_vw.txt
	rm -f prod/experiment90-220/jayasiP_90-220_predictions.txt
	rm -f prod/jayasi_full/jayasiP_data.txt
	rm -f prod/jayasi_full/jayasiP_data_vw.txt
	rm -f prod/jayasi_full/jayasiP_predictions.txt
	rm -f prod/jayasi.model
	rm -f prod/jayasi_predictions.txt
	rm -f prod/jayasi_vw.txt
	rm -f prod/jayasi_contexts.txt
	rm -f prod/jayasi_converbs.html

data.txt:
	./getData.py

data_vw.txt: data.txt
	./extract_features.py data.txt data_vw.txt $(CONFIG)

split-data: data_vw.txt
	./split_data.py data_vw.txt $(CROSSVALID_GRAN)

train: split-data
	rm -f model/*
	for i in `seq 0 $(shell expr $(CROSSVALID_GRAN) - 1 )`; do \
		vw train/train$$i.txt -f model/model$$i.model --cache_file model/model$$i.cache -l $(LEARNING_RATE) --binary --loss_function=$(LOSS_FUNCTION) --passes $(PASSES) --quiet; \
	done

test: train
	rm -f prediction/*
	for i in `seq 0 $(shell expr $(CROSSVALID_GRAN) - 1 )`; do \
		vw -i model/model$$i.model -p prediction/prediction$$i.txt -t test/test$$i.txt --quiet --binary; \
	done

get-dictionaries: split-data
	for i in `seq 0 $(shell expr $(CROSSVALID_GRAN) - 1 )`; do \
		./get_converbs_dictionary.py train/train$$i.txt dictionary/dictionary$$i.txt; \
	done


test-dictionary: get-dictionaries
	for i in `seq 0 $(shell expr $(CROSSVALID_GRAN) - 1 )`; do \
		./predict_by_dictionary.py test/test$$i.txt dictionary/dictionary$$i.txt prediction/prediction$$i.txt; \
	done


collect-results:
	./collectResults.py $(CROSSVALID_GRAN) $(CONFIG)

prod: publish-prod

publish-prod: prod/jayasi_converbs.html
	scp prod/jayasi_converbs.html rjawor@rjawor.vm.wmi.amu.edu.pl:/var/www/html/converbs



# jayasi full


jayasi_full: prod/jayasi_full/jayasiP_annotated.txt

prod/jayasi_full/jayasiP_annotated.txt: prod/jayasi_full/jayasiP_data.txt prod/jayasi_full/jayasiP_predictions.txt
	./merge_annotations.py prod/jayasi_full/jayasiP_data.txt prod/jayasi_full/jayasiP_predictions.txt > $@

prod/jayasi_full/jayasiP_predictions.txt: prod/jayasi.model prod/jayasi_full/jayasiP_data_vw.txt
	vw -i $< -p $@ -t prod/jayasi_full/jayasiP_data_vw.txt --quiet --binary

prod/jayasi_full/jayasiP_data_vw.txt: prod/jayasi_full/jayasiP_data.txt
	./extract_features.py $< $@ $(CONFIG)

prod/jayasi_full/jayasiP_data.txt: prod/jayasi_full/jayasiP.txt
	./to_data_format.py $< $@ verse



# experiment91-170

experiment91-170: prod/experiment91-170/jayasiP_91-170_report.txt

prod/experiment91-170/jayasiP_91-170_report.txt: prod/experiment91-170/jayasiP_91-170_predictions.txt prod/experiment91-170/jayasiP_91-170_data_vw.txt
	./collectResultsSingle.py prod/experiment91-170/jayasiP_91-170_data_vw.txt prod/experiment91-170/jayasiP_91-170_predictions.txt > $@

prod/experiment91-170/jayasiP_91-170_predictions.txt: prod/jayasi.model prod/experiment91-170/jayasiP_91-170_data_vw.txt
	vw -i $< -p $@ -t prod/experiment91-170/jayasiP_91-170_data_vw.txt --quiet --binary


prod/experiment91-170/jayasiP_91-170_data_vw.txt: prod/experiment91-170/jayasiP_91-170_data.txt
	./extract_features.py $< $@ $(CONFIG)

prod/experiment91-170/jayasiP_91-170_data.txt: prod/experiment91-170/jayasiP_91-170.txt
	./to_data_format.py $< $@

# experiment90-220

experiment90-220: prod/experiment90-220/jayasiP_90-220_report.txt

prod/experiment90-220/jayasiP_90-220_report.txt: prod/experiment90-220/jayasiP_90-220_predictions.txt prod/experiment90-220/jayasiP_90-220_data_vw.txt
	./collectResultsSingle.py prod/experiment90-220/jayasiP_90-220_data_vw.txt prod/experiment90-220/jayasiP_90-220_predictions.txt > $@

prod/experiment90-220/jayasiP_90-220_predictions.txt: prod/jayasi.model prod/experiment90-220/jayasiP_90-220_data_vw.txt
	vw -i $< -p $@ -t prod/experiment90-220/jayasiP_90-220_data_vw.txt --quiet --binary


prod/experiment90-220/jayasiP_90-220_data_vw.txt: prod/experiment90-220/jayasiP_90-220_data.txt
	./extract_features.py $< $@ $(CONFIG)

prod/experiment90-220/jayasiP_90-220_data.txt: prod/experiment90-220/jayasiP_90-220.txt
	./to_data_format.py $< $@


# other experiments

prod/jayasi_converbs.html: prod/jayasi_predictions.txt prod/jayasi_contexts.txt
	./create_converbs_list.py prod/jayasi_predictions.txt prod/jayasi_contexts.txt $@

prod/jayasi.model: data_vw.txt
	vw $< -f $@ --cache_file prod/jayasi.cache -l $(LEARNING_RATE) --binary --loss_function=$(LOSS_FUNCTION) --passes $(PASSES) --quiet

prod/jayasi_predictions.txt: prod/jayasi.model prod/jayasi_vw.txt
	vw -i $< -p $@ -t prod/jayasi_vw.txt --quiet --binary

prod/jayasi_vw.txt: prod/jayasi.txt
	./extract_features.py $< $@ $(CONFIG) no-labels

prod/jayasi_contexts.txt prod/jayasi.txt: prod/jayasi.xml
	./extract_text.py $< prod/jayasi_contexts.txt prod/jayasi.txt
