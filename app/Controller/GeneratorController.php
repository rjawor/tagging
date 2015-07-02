<?php

App::uses('AppController', 'Controller');
App::uses('PHPWord', 'Lib');
App::uses('Utils', 'Lib');

class GeneratorController extends AppController {
    
    public function generatedocx() {
        if ($this->request->is('post')) {
            $sentenceId = $this->request['data']['sentenceId'];
            $startIndex = $this->request['data']['startIndex'];
            $endIndex = $this->request['data']['endIndex'];
            $maxLevel = $this->request['data']['maxLevel'];
            
            $tmpDocumentPath = '/tmp/IAtagger_generated.docx';
            
            $sentenceData = Utils::getSentenceData($sentenceId);
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
    
}

?>
