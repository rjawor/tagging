<?php

App::uses('AppController', 'Controller');
App::uses('PHPWord', 'Lib');
App::uses('PHPExcel', 'Lib');
App::uses('Utils', 'Lib');
App::uses('Document', 'Model');

class GeneratorController extends AppController {
    
    public function generatedocx() {
        if ($this->request->is('post')) {
            $sentenceId = $this->request['data']['sentenceId'];
            $startIndex = $this->request['data']['startIndex'];
            $endIndex = $this->request['data']['endIndex'];
            $maxLevel = $this->request['data']['maxLevel'];
            
            $tmpDocumentPath = '/tmp/IAtagger_generated.docx';
            
            $sentenceData = Utils::getSentenceData($sentenceId);
            //die(print_r($sentenceData, true));

            // New Word Document
            $PHPWord = new PHPWord();

            // New portrait section
            $section = $PHPWord->createSection();
            
            $PHPWord->addParagraphStyle('centering', array('align'=>'center'));
            $PHPWord->addFontStyle('wordsRowTextStyle', array('bold'=>true));
            $PHPWord->addFontStyle('tagsTextStyle', array('bold'=>true, 'color'=>'000066', 'align'=>'center'));
            $PHPWord->addFontStyle('defaultTextStyle', array('bold'=>false));
            
            $wordsRowCellStyle = array('borderTopSize'=>6,
                                       'borderTopColor'=>'006699', 
                                       'borderLeftSize'=>6,
                                       'borderLeftColor'=>'006699',
                                       'borderRightSize'=>6,
                                       'borderRightColor'=>'006699',
                                       'borderBottomSize'=>18,
                                       'borderBottomColor'=>'000066',
                                       'bgColor'=>'E2F0FF',
                                       'cellMargin'=>30,
                                       'valign'=>'center');
            $cellStyle = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>30, 'valign'=>'center');
            $table = $section->addTable();

            // Bracket row
            $table->addRow();
            $table->addCell(900);
            $wordIndex = 0;
            foreach ($sentenceData['sentence']['Word'] as $word) {
                if ($wordIndex >= $startIndex && $wordIndex < $endIndex) {
                    $cell = $table->addCell(2000);
                    if ($word['postposition_id']) {
                        $cell->addImage('/var/www/html/tagging/app/webroot/img/leftBracket.png', array('width'=>40, 'height'=>15, 'align'=>'right'));        
                    }
                    if ($word['is_postposition']) {
                        $cell->addImage('/var/www/html/tagging/app/webroot/img/rightBracket.png', array('width'=>40, 'height'=>15, 'align'=>'left'));        
                    }
                }
                $wordIndex++;
            }

            // Words row
            $table->addRow(900);
            $table->addCell(900, $wordsRowCellStyle);
            $wordIndex = 0;
            foreach ($sentenceData['sentence']['Word'] as $word) {
                if ($wordIndex >= $startIndex && $wordIndex < $endIndex) {
                    $cell = $table->addCell(2000, $wordsRowCellStyle);
                    if ($word['split']) {
                        $wordText = $word['stem'].'-'.$word['suffix'];
                    } else {            
                        $wordText = $word['text'];
                    }
                    $cell->addText($wordText, 'wordsRowTextStyle', 'centering');
                }
                $wordIndex++;
            }

            // Annotation rows
            $legend = array();
            $levelIndex = 0;
            foreach ($sentenceData['sentence']['WordAnnotations'] as $annotationData) {
                if ($levelIndex < $maxLevel) {
                    $table->addRow(900);
                    
                    $wordAnnotationType = $annotationData['type']['WordAnnotationType'];
                    // annotation name cell
                    $cell = $table->addCell(900, $cellStyle);
                    $cell->addText($wordAnnotationType['name'], 'defaultTextStyle', 'centering');
                    
                    $wordIndex = 0;
                    foreach ($annotationData['annotations'] as $annotation) {
                        if ($wordIndex >= $startIndex && $wordIndex < $endIndex) {
                            $cell = $table->addCell(900, $cellStyle);
                            if (!empty($annotation)) {
                                if ($wordAnnotationType['strict_choices']) {
                                    foreach ($annotation['WordAnnotationTypeChoice'] as $choice) {
                                        $cell->addText($choice['value'], 'tagsTextStyle', 'centering');
                                        $legend[$choice['value']]=$choice['description'];
                                    }
                                } else {
                                    $cell->addText($annotation['text_value'], 'defaultTextStyle', 'centering');                    
                                }
                            }
                        }
                        $wordIndex++;
                    }
                }
                $levelIndex++;
            }
            
            foreach ($sentenceData['sentence']['SentenceAnnotations'] as $annotationData) {
                if ($levelIndex < $maxLevel) {
                    $table->addRow(900);
                    $sentenceAnnotationType = $annotationData['type']['SentenceAnnotationType'];
                    // annotation name cell
                    $cell = $table->addCell(900, $cellStyle);
                    $cell->addText($sentenceAnnotationType['name'], 'defaultTextStyle', 'centering');


                    $spanningCellStyle = $cellStyle;
                    $spanningCellStyle['gridSpan'] = $endIndex - $startIndex;
                    $cell = $table->addCell(900, $spanningCellStyle);
                    $text = isset($annotationData['annotation']['text']) ? $annotationData['annotation']['text'] : '';
                    $cell->addText($text);
                }            
                $levelIndex++;
            }
            

            // Legend
            ksort($legend);
            $section->addTextBreak(2);
            $section->addText("Legend:", array('bold'=>true));
            foreach ($legend as $value => $description) {
                $section->addListItem($value.' - '.$description);
            }
            

            // Save File
            $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
            $objWriter->save($tmpDocumentPath);
            
            $this->response->file(
                $tmpDocumentPath,
                array('download' => true, 'name' => 'IAtagger_table.docx')
            );
            // Return response object to prevent controller from trying to render
            // a view
            return $this->response;
        
        }
        
    }
    
    public function generatexlsx() {
        if ($this->request->is('post')) {
            $sentenceId = $this->request['data']['sentenceId'];
            $startIndex = $this->request['data']['startIndex'];
            $endIndex = $this->request['data']['endIndex'];
            $maxLevel = $this->request['data']['maxLevel'];
            
            $tmpDocumentPath = '/tmp/IAtagger_generated.xlsx';
            
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("IA tagger system")
							             ->setLastModifiedBy("IA tagger system")
							             ->setTitle("Sentence exported from IA tagger")
							             ->setSubject("Sentence table")
							             ->setDescription("Document generated automatically from the IA tagger system, containing exhaustive information about a single sentence in one of the Indo-Aryan languages.")
							             ->setKeywords("IAtagger sentence generated")
							             ->setCategory("Automatically generated file");


            $objPHPExcel->setActiveSheetIndex(0);

            $legend = array();
            $this->addSentenceToExcel($objPHPExcel, $legend, $sentenceId, $startIndex, $endIndex, $maxLevel, 0);

            // Legend
            $levelIndex = $maxLevel + 2;
            
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $levelIndex+3)->setValue("Legend");
            ksort($legend);
            $levelIndex++;
            
            foreach ($legend as $value => $description) {
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $levelIndex+3)->setValue($value);
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $levelIndex+3)->setValue($description);
                $levelIndex++;
            }

            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Sentence');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Save Excel 2007 file
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($tmpDocumentPath);

            $this->response->file(
                $tmpDocumentPath,
                array('download' => true, 'name' => 'IAtagger_table.xlsx')
            );
            // Return response object to prevent controller from trying to render
            // a view
            return $this->response;
        
        }
        
    }
    
    private function addSentenceToExcel(&$objPHPExcel, &$legend, $sentenceId, $startIndex, $endIndex, $maxLevel, $baseLine) {
    
        // Add sentence data
        $sentenceData = Utils::getSentenceData($sentenceId);

        // Bracket row
        $wordIndex = 0;
        foreach ($sentenceData['sentence']['Word'] as $word) {
            if ($wordIndex >= $startIndex && $wordIndex < $endIndex) {
                if ($word['postposition_id']) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($wordIndex-$startIndex+1, 1+$baseLine)->setValue("{----");
                }
                if ($word['is_postposition']) {
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($wordIndex-$startIndex+1, 1+$baseLine)->setValue("----}");
                    $objPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($wordIndex-$startIndex+1).(1+$baseLine))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                }   
            }
            $wordIndex++;
        }
        
        // Words row
        $wordIndex = 0;
        foreach ($sentenceData['sentence']['Word'] as $word) {
            if ($wordIndex >= $startIndex && $wordIndex < $endIndex) {
                if ($word['split']) {
                    $wordText = $word['stem'].'-'.$word['suffix'];
                } else {            
                    $wordText = $word['text'];
                }
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($wordIndex-$startIndex+1, 2+$baseLine)->setValue($wordText);
                $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($wordIndex-$startIndex+1)->setAutoSize(true);
            }
            $wordIndex++;
        }
        
        $objPHPExcel->getActiveSheet()->getStyle("A".(2+$baseLine).":ZZ".(2+$baseLine))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A".(1+$baseLine).":A".(100+$baseLine))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A".(2+$baseLine).":ZZ".(100+$baseLine))->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle("A".(2+$baseLine).":ZZ".(100+$baseLine))->setQuotePrefix(true);

        // Annotation rows

        $levelIndex = 0;
        foreach ($sentenceData['sentence']['WordAnnotations'] as $annotationData) {
            if ($levelIndex < $maxLevel) {
                
                $wordAnnotationType = $annotationData['type']['WordAnnotationType'];
                // annotation name cell
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $levelIndex+3+$baseLine)->setValue($wordAnnotationType['name']);
                
                $wordIndex = 0;
                foreach ($annotationData['annotations'] as $annotation) {
                    if ($wordIndex >= $startIndex && $wordIndex < $endIndex) {
                        $cellText = "";
                        if (!empty($annotation)) {
                            if ($wordAnnotationType['strict_choices']) {
                                foreach ($annotation['WordAnnotationTypeChoice'] as $choice) {
                                    $cellText = $cellText.$choice['value']."\n";
                                    $legend[$choice['value']]=$choice['description'];
                                }
                            } else {
                                $cellText = $annotation['text_value'];
                            }
                        }
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($wordIndex-$startIndex+1, $levelIndex+3+$baseLine)->setValue(trim($cellText));
                    }
                    $wordIndex++;
                }
            }
            $levelIndex++;
        }
        
        foreach ($sentenceData['sentence']['SentenceAnnotations'] as $annotationData) {
            if ($levelIndex < $maxLevel) {
                $objPHPExcel->getActiveSheet()->mergeCells('B'.($levelIndex+3+$baseLine).':'.PHPExcel_Cell::stringFromColumnIndex($endIndex-$startIndex).($levelIndex+3+$baseLine));

                $sentenceAnnotationType = $annotationData['type']['SentenceAnnotationType'];
                // annotation name cell
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $levelIndex+3+$baseLine)->setValue($sentenceAnnotationType['name']);

                $text = array_key_exists('text', $annotationData['annotation']) ? $annotationData['annotation']['text'] : '';
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $levelIndex+3+$baseLine)->setValue($text);
            }            
            $levelIndex++;
        }
        
        unset($sentenceData);

    } 
    
    public function generatedocxlsx($documentId) {
        $tmpDocumentPath = '/tmp/IAtagger_generated.xlsx';
        shell_exec("/var/www/test/tagging/tools/scripts/generate-doc-xlsx.py ".$tmpDocumentPath." ".$documentId);
        /*
        set_time_limit(3600);
        ini_set('max_execution_time', 3600);
        
        
        
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("IA tagger system")
						             ->setLastModifiedBy("IA tagger system")
						             ->setTitle("Document exported from IA tagger")
						             ->setSubject("Document table")
						             ->setDescription("Document generated automatically from the IA tagger system, containing exhaustive information about a document in one of the Indo-Aryan languages.")
						             ->setKeywords("IAtagger document generated")
						             ->setCategory("Automatically generated file");


        $objPHPExcel->setActiveSheetIndex(0);

        $legend = array();

        $documentModel = ClassRegistry::init('Document');
        $document = $documentModel->findById($documentId);
        
        $baseLine = 0;
        foreach ($document['Sentence'] as $sentence) {
            $this->addSentenceToExcel($objPHPExcel, $legend, $sentence['id'], 0, 300, 8, $baseLine);        
            $baseLine += 11;
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Sentence');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Save Excel 2007 file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($tmpDocumentPath);
        */

        $this->response->file(
            $tmpDocumentPath,
            array('download' => true, 'name' => 'IAtagger_table.xlsx')
        );
        // Return response object to prevent controller from trying to render
        // a view
        return $this->response;
    }

}

?>
