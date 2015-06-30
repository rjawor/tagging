<?php

App::uses('AppController', 'Controller');
App::uses('Word', 'Model');
App::uses('PHPWord', 'Lib');


class GeneratorController extends AppController {
    
    public function generatedocx() {
        $wordModel = ClassRegistry::init('Word');
        $exampleWord = $wordModel->findById(381388);

        // New Word Document
        $PHPWord = new PHPWord();

        // New portrait section
        $section = $PHPWord->createSection();

        // Add text elements
        $section->addText('Hello World! zażółć gęślą jaźńZAŻÓŁĆ GĘŚLĄ JAŹŃ');
        $section->addText($exampleWord['Word']['text']);
        $section->addTextBreak(2);

        $section->addText('I am inline styled.', array('name'=>'Verdana', 'color'=>'006699'));
        $section->addTextBreak(2);

        $PHPWord->addFontStyle('rStyle', array('bold'=>true, 'italic'=>true, 'size'=>16));
        $PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));
        $section->addText('I am styled by two style definitions.', 'rStyle', 'pStyle');
        $section->addText('I have only a paragraph style definition.', null, 'pStyle');



        // Save File
        $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $objWriter->save('/tmp/MyText.docx');
        
        //now mess with CakePHP send file
        $this->response->file(
            '/tmp/MyText.docx',
            array('download' => true, 'name' => 'generated.docx')
        );
        // Return response object to prevent controller from trying to render
        // a view
        return $this->response;
        
    }
    
}

?>
