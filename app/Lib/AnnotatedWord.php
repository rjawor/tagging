<?php

class AnnotatedWord {
    private $text;
    
    private $stem;

    private $suffix;
    
    private $split;
    
    private $annotations;

    public function __construct($wordData) {
        $this->text = $wordData['Word']['text'];
        $this->stem = $wordData['Word']['stem'];
        $this->suffix = $wordData['Word']['suffix'];
        if ($wordData['Word']['split']) {
            $this->split = 1;
        } else {
            $this->split = 0;        
        }
        $this->annotations = $this->cleanEmptyAnnotations($wordData['WordAnnotation']);
    }
    
    private function cleanEmptyAnnotations($annotationsArray) {
   	    $count = count($annotationsArray);
        for($i = 0; $i < $count; $i++) {
            $annotation = $annotationsArray[$i];
            if (empty($annotation['text_value']) && count($annotation['WordAnnotationTypeChoice']) == 0) {
        	    unset($annotationsArray[$i]);
            }
        }
        return array_values($annotationsArray);
    }

    public function containsAnnotation($anotherWord) {
        foreach ($anotherWord->annotations as $smallAnnotation) {
            $largeAnnotation = $this->findAnnotationByType($this->annotations, $smallAnnotation['type_id']);
            if (is_null($largeAnnotation)) {
                return false;
            } else {
                if (empty($smallAnnotation['text_value'])) {
                    $largeChoicesIds = array();
                    foreach ($largeAnnotation['WordAnnotationTypeChoice'] as $choice) {
                        array_push($largeChoicesIds, $choice['id']);
                    }
                    foreach ($smallAnnotation['WordAnnotationTypeChoice'] as $choice) {
                        if (!in_array($choice['id'], $largeChoicesIds)) {
                            return false;
                        }
                    }
                    
                } else {
                    if ($smallAnnotation['text_value'] != $largeAnnotation['text_value']) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
    
    private function findAnnotationByType($annotationList, $typeId) {
        foreach ($annotationList as $annotation) {
            if ($annotation['type_id'] == $typeId) {
                return $annotation;
            }
        }
        return NULL;
    }

    private function getWordText() {
        if ($this->split) {
            return $this->stem."|".$this->suffix;
        } else {
            return $this->text;
        }
    }

    private function compareAnnotations($a, $b) {
        if ($a['position'] == $b['position']) {
           return 0;
        }

        return ($a['position'] < $b['position']) ? -1 : 1;
    }

    public function getSuggestionData($wordAnnotationTypes) {
        $data = array();
        $data['text'] = $this->getWordText();
        $annotationsArray = array();
        foreach($this->annotations as $annotation) {
 
            $annotationType = $this->getAnnotationType($wordAnnotationTypes, $annotation['type_id']);

            $annotationData = array();
            $annotationData['position'] = $annotationType['WordAnnotationType']['position'];
            if ($annotationType['WordAnnotationType']['strict_choices']) {
                $annotationData['type'] = 'choices';
                $choicesIds = array();
                $choices = array();
                foreach($annotation['WordAnnotationTypeChoice'] as $choice) {
                    array_push($choicesIds, $choice['id']);
                    array_push($choices, array('value'=>$choice['value'], 'description' => $choice['description']));
                }
                $annotationData['value'] = join(',', $choicesIds);
                $annotationData['choices'] = $choices;
            } else {
                $annotationData['type'] = 'text';
                $annotationData['value'] = $annotation['text_value'];
            }
            array_push($annotationsArray, $annotationData);
        }
        usort($annotationsArray, array($this, 'compareAnnotations'));
        $data['annotations'] = $annotationsArray;
        return $data;
    }

    private function getAnnotationType($wordAnnotationTypes, $id) {
        foreach ($wordAnnotationTypes as $type) {
            if ($type['WordAnnotationType']['id'] == $id) {
                return $type;
            }
        }
        return null;
    }
    
    public function hasAnnotations() {
        return count($this->annotations) > 0;
    }
}

?>
