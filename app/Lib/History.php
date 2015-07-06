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
            }
        }
        return $reversedOperation;
    }
}

?>
