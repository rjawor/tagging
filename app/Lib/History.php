<?php

class History {
    private static $operationsVarName = "IA_tagger_history";

    private static $cursorVarName = "IA_tagger_history_cursor";

    public static function storeOperation($session, $opData) {
        $operations = $session->read(History::$operationsVarName);
        $cursor = $session->read(History::$cursorVarName);
        
        if (isset($operations) && count($operations)>0) {
            $cursor++;
            array_splice($operations, $cursor);
            array_push($operations, $opData);
        } else {
            $operations = array($opData);
            $cursor = 0;
        }
        
        $session->write(History::$operationsVarName, $operations);
        $session->write(History::$cursorVarName, $cursor);
    }
    
    public static function undo($session) {
        $operations = $session->read(History::$operationsVarName);
        $cursor = $session->read(History::$cursorVarName);

        $returnedOperation = array();
        if (isset($cursor) && $cursor >= 0) {
            $returnedOperation = $operations[$cursor];
            $cursor--;
        }
        $session->write(History::$cursorVarName, $cursor);

        return History::reverseOperation($returnedOperation);    
    }
    
    public static function redo($session) {
        $operations = $session->read(History::$operationsVarName);
        $cursor = $session->read(History::$cursorVarName);

        $returnedOperation = array();
        if (isset($cursor) && isset($operations) && $cursor < count($operations)-1) {
            $cursor++;
            $returnedOperation = $operations[$cursor];
        }
        $session->write(History::$cursorVarName, $cursor);

        return $returnedOperation;
    
    }

    public static function offsetOperations($session, $startPos, $offset) {
        $operations = $session->read(History::$operationsVarName);
        for ($i=0;$i<count($operations);$i++) {
            if ($operations[$i]['type'] == 'modifyCellValue') {
                if($operations[$i]['gridX'] >= $startPos) {
                    $operations[$i]['gridX'] = $operations[$i]['gridX'] + $offset;
                }
            } else if ($operations[$i]['type'] == 'applySuggestion') {
                for($j=0; $j<count($operations[$i]['modifications']); $j++) {
                    if($operations[$i]['modifications'][$j]['gridX'] >= $startPos) {
                        $operations[$i]['modifications'][$j]['gridX'] = $operations[$i]['modifications'][$j]['gridX'] + $offset;
                    }
                }
            } else if ($operations[$i]['type'] == 'markPostposition' ||
                       $operations[$i]['type'] == 'unmarkPostposition' ||
                       $operations[$i]['type'] == 'insertWord' ||
                       $operations[$i]['type'] == 'deleteWord'                     
                      ) {
                if($operations[$i]['position'] >= $startPos) {
                    $operations[$i]['position'] = $operations[$i]['position'] + $offset;
                }
            }            
        }    
        $session->write(History::$operationsVarName, $operations);
    }

    public static function listOperations($session) {
        $operations = $session->read(History::$operationsVarName);
        $cursor = $session->read(History::$cursorVarName);

        return array('operations'=>$operations, 'cursor'=>$cursor);    
    }

    public static function clear($session) {
        $operations = $session->delete(History::$operationsVarName);
        $cursor = $session->delete(History::$cursorVarName);
    }
    
    private static function reverseOperation($operation) {
        $reversedOperation = array();
        if (count($operation) > 0) {
            if ($operation['type'] == 'modifyCellValue') {
                $reversedOperation['type'] = $operation['type'];
                $reversedOperation['gridX'] = $operation['gridX'];
                $reversedOperation['gridY'] = $operation['gridY'];
                $reversedOperation['oldValue'] = $operation['newValue'];
                $reversedOperation['newValue'] = $operation['oldValue'];
            } else if ($operation['type'] == 'applySuggestion') {
                $reversedOperation['type'] = $operation['type'];
                $modifications = array();
                foreach($operation['modifications'] as $modification) {
                    array_push($modifications, History::reverseOperation($modification));
                }
                $reversedOperation['modifications'] = $modifications;
            } else if ($operation['type'] == 'markPostposition') {
                $reversedOperation['type'] = 'unmarkPostposition';
                $reversedOperation['documentId'] = $operation['documentId'];
                $reversedOperation['documentOffset'] = $operation['documentOffset'];
                $reversedOperation['sentenceId'] = $operation['sentenceId'];
                $reversedOperation['position'] = $operation['position'];
            } else if ($operation['type'] == 'unmarkPostposition') {
                $reversedOperation['type'] = 'markPostposition';
                $reversedOperation['documentId'] = $operation['documentId'];
                $reversedOperation['documentOffset'] = $operation['documentOffset'];
                $reversedOperation['sentenceId'] = $operation['sentenceId'];
                $reversedOperation['position'] = $operation['position'];
            } else if ($operation['type'] == 'insertWord') {
                $reversedOperation['type'] = 'deleteWord';
                $reversedOperation['documentId'] = $operation['documentId'];
                $reversedOperation['documentOffset'] = $operation['documentOffset'];
                $reversedOperation['sentenceId'] = $operation['sentenceId'];
                $reversedOperation['position'] = $operation['position'];
            } else if ($operation['type'] == 'deleteWord') {
                $reversedOperation['type'] = 'insertWord';
                $reversedOperation['documentId'] = $operation['documentId'];
                $reversedOperation['documentOffset'] = $operation['documentOffset'];
                $reversedOperation['sentenceId'] = $operation['sentenceId'];
                $reversedOperation['position'] = $operation['position'];
            }

        }
        /*
        CakeLog::write('debug', 'operation: '.print_r($operation,true));
        CakeLog::write('debug', 'reversed: '.print_r($reversedOperation,true));
        */
        return $reversedOperation;
    }
}

?>
