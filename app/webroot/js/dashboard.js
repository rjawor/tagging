function prevSentence() {
    var offsetElement = document.getElementById('offset');
    var documentId = getDocumentId();
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        setEditMode(offset, false);
        if (offset > 0) {
            offset--;
            offsetElement.value = offset;
            $.ajax({async:true, url:"/tagging/dashboard/setCurrentDocument/"+documentId+"/"+offset});
            document.location.href = "/tagging/dashboard/index/"+documentId+"/"+offset;
        }
    }
}

function nextSentence(documentId) {
    var offsetElement = document.getElementById('offset');
    var documentId = getDocumentId();
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        var sentencesCount = document.getElementById('document-sentences-count').value;
        setEditMode(offset, false);
        if (offset < sentencesCount - 1) {
            offset++;
            offsetElement.value = offset;
            $.ajax({async:true, url:"/tagging/dashboard/setCurrentDocument/"+documentId+"/"+offset});
            document.location.href = "/tagging/dashboard/index/"+documentId+"/"+offset;            
        }
    }
}

function updateSentence() {
    var sentenceNumber = getSentenceNumber();
    var sentenceCells = document.querySelectorAll('#sentence'+sentenceNumber+" td.selected,td.edited");
    for (var i=0; i<sentenceCells.length; i++) {
        sentenceCells[i].className = 'normal-cell';
    }
    
    var cellId = 'cell-'+sentenceNumber+'-'+getGridY(sentenceNumber)+'-'+getGridX(sentenceNumber);
    var activeCell = document.getElementById(cellId);
    if (activeCell != null) {
        if (getEditMode(sentenceNumber)) {
            activeCell.className = 'edited';
            
            var selector = '.edit-field input';
            var splitElement = document.getElementById(cellId+'-split');
            if (splitElement != null && splitElement.value == '0') {
                selector = '.edit-field .word-unsplit-field input'
            }
            var editField = activeCell.querySelector(selector);
            if (editField != null) {
                editField.focus();
            }
        } else {
            activeCell.className = 'selected';        
        }
        var offset = $('#'+activeCell.id).offset();
        var width = $(window).width();
        var margin = 50;
        if (offset.left > width - margin) {
            $('div#content').animate({
                scrollTop: 0,
                scrollLeft: offset.left - 150 + $('div#content').scrollLeft()
            });
        } else if (offset.left < margin) {
            $('div#content').animate({
                scrollTop: 0,
                scrollLeft: offset.left + 150 + $('div#content').scrollLeft() - width
            });
        }
    }
}

function setGrid(sentenceNumber, gridY, gridX) {
    var elementY = document.getElementById('sentence'+sentenceNumber+'-grid-y');
    if (elementY != null ) {
        elementY.value = gridY
    }

    var elementX = document.getElementById('sentence'+sentenceNumber+'-grid-x');
    if (elementX != null ) {
        elementX.value = gridX
    }
}

function setSelected(sentenceNumber, gridY, gridX) {
    setEditMode(sentenceNumber, false);
    setGrid(sentenceNumber, gridY, gridX);
    updateSentence(sentenceNumber);
}

function checkUserPrivileges() {
    var roleId = document.getElementById('userRoleId');
    if (roleId != null) {
        return roleId.value < 3;
    } else {
        return false;
    }
}

function setEdited(sentenceNumber, gridY, gridX) {
    if (!checkUserPrivileges()) {
        return;
    }
    if (!(getEditMode(sentenceNumber) &&
          getGridX(sentenceNumber) == gridX &&
          getGridX(sentenceNumber) == gridX)) {
        
        setEditMode(sentenceNumber, false); //switching off editing of current cell
        setEditMode(sentenceNumber, true);
        setGrid(sentenceNumber, gridY, gridX);
        updateSentence(sentenceNumber);
    }
}

function getSentenceNumber() {
    var element = document.getElementById('offset');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}

function getDocumentId() {
    var element = document.getElementById('document-id');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return -1;
    }
}

function getWordCount(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-word-count');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}

function getAnnotationCount(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-annotation-count');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}

function getGridX(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-grid-x');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}


function setGridX(sentenceNumber, newValue) {
    var element = document.getElementById('sentence'+sentenceNumber+'-grid-x');
    if (element != null) {
        element.value = newValue;
    }
}

function setGridY(sentenceNumber, newValue) {
    var element = document.getElementById('sentence'+sentenceNumber+'-grid-y');
    if (element != null) {
        element.value = newValue;
    }
}

function getGridY(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-grid-y');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}

function getEditMode(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-edit-mode');
    if (element != null) {
        return element.value == "1";
    } else {
        return false;
    }
}

function updateAndSaveCell(sentenceNumber) {
    updateCellValue(sentenceNumber);
    saveCell(sentenceNumber);
    updateCellDisplay(sentenceNumber);
}

function setEditMode(sentenceNumber, editMode, preventSave) {
    if (!checkUserPrivileges()) {
        return;
    }
    preventSave = preventSave || false;
    var element = document.getElementById('sentence'+sentenceNumber+'-edit-mode');
    if (element != null) {
        if (editMode) {
            element.value = "1";
        } else {
            if (element.value == "1" && !preventSave) { //edit mode was switched off, but not by ESC
                updateAndSaveCell(sentenceNumber);
            }
            element.value = "0";        
        }
    }
}

function toggleEditMode(sentenceNumber) {
    if (!checkUserPrivileges()) {
        return;
    }
    var element = document.getElementById('sentence'+sentenceNumber+'-edit-mode');
    if (element != null) {
        if (element.value == "1") {
            updateAndSaveCell(sentenceNumber);
            element.value = "0";
        } else {
            element.value = "1";        
        }
    }
}

function normalizeText(text) {
    return text.replace(/^\s+/, '').replace(/\s+$/, '');
}

function escapeHTML(text) {
    return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;');
}

function deEscapeHTML(text) {
    return text.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"');
}

function updateCellValue(sentenceNumber, gridX, gridY) {
    if (gridX == null || gridY == null) {
        gridX = getGridX(sentenceNumber);
        gridY = getGridY(sentenceNumber);    
    }
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cell = document.getElementById(cellId);
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);
    var valueElement = document.getElementById(cellId+'-value');
    
    var editSpan = cell.querySelector('.edit-field');

    if (cellTypeElement.value == 'word-text' || cellTypeElement.value == 'sentence-text') {
        var textInputElement = editSpan.querySelector('input[type=text]');
        valueElement.value = normalizeText(escapeHTML(textInputElement.value));
        
    } else if (cellTypeElement.value == 'word') {
        var splitElement = document.getElementById(cellId+'-split');
        if (splitElement.value == '0') {
            var wordTextElement = cell.querySelector('.edit-field .word-unsplit-field input');
            valueElement.value = normalizeText(escapeHTML(wordTextElement.value)); 
        } else {
            var wordTextElements = cell.querySelectorAll('.edit-field .word-split-field input');
            var stem = normalizeText(escapeHTML(wordTextElements[0].value));
            var suffix = normalizeText(escapeHTML(wordTextElements[1].value));   
            valueElement.value = stem+','+suffix;
        }
        
        
    } else if (cellTypeElement.value == 'choices' || cellTypeElement.value == 'multiple-choices') {
        var selectedChoices = editSpan.querySelectorAll('input.choice-selected');
        var selectedChoicesIds=[];
        if (selectedChoices != null) {
            for (var i=0; i< selectedChoices.length; i++) {
                var choiceValueArr = selectedChoices[i].value.match(/^(\w+).*/);
                var choiceValue = choiceValueArr[1];
                var selectedChoiceId = document.getElementById(selectedChoices[i].id+'-type-id').value;
                selectedChoicesIds[selectedChoicesIds.length] = selectedChoiceId;
            }
            var selectedChoicesIdsString = selectedChoicesIds.join();
            valueElement.value = selectedChoicesIdsString;
        } else {
            valueElement.value = '';
        }
    }

}


function modifyValue(sentenceNumber, gridX, gridY, value) {
    var valueElement = document.getElementById('cell-'+sentenceNumber+'-'+gridY+'-'+gridX+'-value');
    valueElement.value = value;
    updateCellDisplay(sentenceNumber, gridX, gridY); 
}

function updateCellDisplay(sentenceNumber, gridX, gridY) {
    if (gridX == null || gridY == null) {
        gridX = getGridX(sentenceNumber);
        gridY = getGridY(sentenceNumber);    
    }
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cell = document.getElementById(cellId);
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);
    var valueElement = document.getElementById(cellId+'-value');

    var displaySpan = cell.querySelector('.ro-display');
    var editSpan = cell.querySelector('.edit-field');

    if (cellTypeElement.value == 'word-text' || cellTypeElement.value == 'sentence-text') {
        displaySpan.innerHTML = valueElement.value;
        var textInputElement = editSpan.querySelector('input[type=text]');
        textInputElement.value = deEscapeHTML(valueElement.value);        
    } else if (cellTypeElement.value == 'word') {
        var splitElement = document.getElementById(cellId+'-split');
        if (splitElement.value == '0') {
            displaySpan.innerHTML = valueElement.value;            
            var wordTextElement = cell.querySelector('.edit-field .word-unsplit-field input');
            wordTextElement.value = deEscapeHTML(valueElement.value);
        } else {
            var stemAndSuffix = valueElement.value.split(",");
            var stem = stemAndSuffix[0];
            var suffix = stemAndSuffix[1];

            var splitDisplaySpan = cell.querySelector('.ro-display .word-split-field');
            if (splitDisplaySpan != null) {
                splitDisplaySpan.innerHTML = stem+'&#124;'+suffix;
            }
            
            var wordTextElements = cell.querySelectorAll('.edit-field .word-split-field input');
            wordTextElements[0].value = deEscapeHTML(stem);
            wordTextElements[1].value = deEscapeHTML(suffix);
        }
    
    } else if (cellTypeElement.value == 'choices' || cellTypeElement.value == 'multiple-choices') {
        displaySpan.innerHTML = '';
        if (valueElement.value != '') {
            var selectedChoicesIds=valueElement.value.split(",");
        } else {
            var selectedChoicesIds=[];        
        }
        
        for (var i=0; i< selectedChoicesIds.length; i++) {
            var id = selectedChoicesIds[i];
            displaySpan.innerHTML = displaySpan.innerHTML + '<input type="button" class="choice-selected" value="'+choicesObject['choice'+id+'value']+'" title="'+choicesObject['choice'+id+'description']+'"/>';
        }
        
        var elements = editSpan.querySelectorAll('input[type="button"]');
        for (var i = 0; i< elements.length; i++) {
	    if (cellTypeElement.value == 'choices') {
                elements[i].className = 'choice-available';
	    } else {
		elements[i].className = 'choice-inactive';
            }
        }
        
        for (var i=0; i< selectedChoicesIds.length; i++) {
            var id = selectedChoicesIds[i];
            var idElement = editSpan.querySelector('input[type="hidden"][value="'+id+'"]');
            var matches = idElement.id.match(/cell-\d+-\d+-\d+-choice-(\d+)-type-id/);
            var choiceNumber = matches[1];
            var element = document.getElementById(cellId+'-choice-'+choiceNumber);
            if (element != null) {
                element.className = 'choice-selected';
            }
        } 


    }


}

function saveCell(sentenceNumber, gridX, gridY) {
    //based on value field
    if (gridX == null || gridY == null) {
        gridX = getGridX(sentenceNumber);
        gridY = getGridY(sentenceNumber);    
    }
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cell = document.getElementById(cellId);
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);
    var valueElement = document.getElementById(cellId+'-value');
    
        
    if (cellTypeElement.value == 'word-text') {
        var wordAnnotationTypeElement = document.getElementById(cellId+'-word-annotation-type-id');
        var wordAnnotationTypeId = wordAnnotationTypeElement.value;
        var wordId = document.getElementById(cellId+'-word-id').value;
        $.post( "/tagging/wordAnnotations/saveWordTextAnnotation", { wordId: wordId, wordAnnotationTypeId: wordAnnotationTypeId, text: valueElement.value } );
    } else if (cellTypeElement.value == 'sentence-text') {
        var sentenceAnnotationTypeId = document.getElementById(cellId+'-sentence-annotation-type-id').value;
        var sentenceId = document.getElementById(cellId+'-sentence-id').value;
        $.post( "/tagging/sentenceAnnotations/saveSentenceAnnotation", { sentenceId: sentenceId, sentenceAnnotationTypeId: sentenceAnnotationTypeId, text: valueElement.value} );        
     } else if (cellTypeElement.value == 'word') {
        var splitElement = document.getElementById(cellId+'-split');
        var wordId = document.getElementById(cellId+'-word-id').value;
        
        if (splitElement.value == '0') {
            $.post( "/tagging/words/saveWord", { wordId: wordId, text: valueElement.value, wordSplit:0} );        
        } else {
            var wordTextElements = cell.querySelectorAll('.edit-field .word-split-field input');
            var stemAndSuffix = valueElement.value.split(",");
            var stem = stemAndSuffix[0];
            var suffix = stemAndSuffix[1];
            $.post( "/tagging/words/saveWord", { wordId: wordId, text: valueElement.value, wordSplit:1, stem: stem, suffix:suffix} );        
        }

      } else if (cellTypeElement.value == 'choices' || cellTypeElement.value == 'multiple-choices') {
        var wordAnnotationTypeId = document.getElementById(cellId+'-word-annotation-type-id').value;
        var wordId = document.getElementById(cellId+'-word-id').value;
        var selectedChoicesIdsString = valueElement.value;
        if (selectedChoicesIdsString == '') {
            selectedChoicesIdsString = 'none';
        }
        $.ajax({async:true, url:"/tagging/wordAnnotations/saveWordChoicesAnnotation/"+wordId+"/"+wordAnnotationTypeId+"/"+selectedChoicesIdsString});
    }
    
}

function getWordCount(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-word-count');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}

function getWordAnnotationCount(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-word-annotation-count');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}

function getSentenceAnnotationCount(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-sentence-annotation-count');
    if (element != null) {
        return parseInt(element.value);
    } else {
        return 0;
    }
}

function switchSelectionLeft() {
    var sentenceNumber = getSentenceNumber();
    var gridX = getGridX(sentenceNumber);
    if (gridX > 0) {
        setGridX(sentenceNumber, gridX - 1);
        setEditMode(sentenceNumber, false);
        updateSentence(sentenceNumber);
    }
}

function switchSelectionRight() {
    var sentenceNumber = getSentenceNumber();
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var wordAnnotationCount = getWordAnnotationCount(sentenceNumber);
    var gridXMax = getWordCount(sentenceNumber);
    if (gridY < wordAnnotationCount && gridX < gridXMax - 1) {
        setGridX(sentenceNumber, gridX + 1);
        setEditMode(sentenceNumber, false);
        updateSentence(sentenceNumber);
    }
}

function switchSelectionUp() {
    var sentenceNumber = getSentenceNumber();
    var gridY = getGridY(sentenceNumber);
    if (gridY > 0) {
        setGridY(sentenceNumber, gridY - 1);
        setEditMode(sentenceNumber, false);
        updateSentence(sentenceNumber);
    }
}

function switchSelectionDown() {
    var sentenceNumber = getSentenceNumber();
    var gridY = getGridY(sentenceNumber);
    var wordAnnotationCount = getWordAnnotationCount(sentenceNumber);
    var sentenceAnnotationCount = getSentenceAnnotationCount(sentenceNumber);
    
    
    if (gridY < wordAnnotationCount + sentenceAnnotationCount - 1) {
        setGridY(sentenceNumber, gridY + 1);
        if (gridY >= wordAnnotationCount - 1) {
            setGridX(sentenceNumber, 0);        
        }
        setEditMode(sentenceNumber, false);
        updateSentence(sentenceNumber);
    }
}

function getCellType(sentenceNumber) {
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cell = document.getElementById(cellId);
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);
    return cellTypeElement.value;
}

function enterHandle(e) {
    var sentenceNumber = getSentenceNumber();
    if(getCellType(sentenceNumber) == 'multiple-choices') {
        if (!getEditMode(sentenceNumber)) {
            setEditMode(sentenceNumber, true);
            updateSentence(sentenceNumber);
        } else {
            handleEnterInMultipleChoices(sentenceNumber);
        }
    } else {
        toggleEditMode(sentenceNumber);
        updateSentence(sentenceNumber);
    }
    e.preventDefault();
}

function handleEnterInMultipleChoices(sentenceNumber) {
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var inputId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX+'-input';
    var input = document.getElementById(inputId);
    
    if (input.value != '') {
        var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
        var cell = document.getElementById(cellId);
        
        
        var selector = 'input[value="'+input.value+' x"]';
        var enteredElement = cell.querySelector(selector);
        input.value = '';
        if (enteredElement != null) {
            enteredElement.className = 'choice-selected';
            updateAndSaveCell(sentenceNumber);
        }
    } else {
        toggleEditMode(sentenceNumber);
        updateSentence(sentenceNumber);
    }
}

function escapeHandle() {
    var sentenceNumber = getSentenceNumber();
    setEditMode(sentenceNumber, false, true);
    updateSentence(sentenceNumber);
}

function leftArrowHandle() {
    var sentenceNumber = getSentenceNumber();
    if (!getEditMode(sentenceNumber)) {
        switchSelectionLeft();
    }
}


function rightArrowHandle() {
    var sentenceNumber = getSentenceNumber();
    if (!getEditMode(sentenceNumber)) {
        switchSelectionRight();
    }
}

function upArrowHandle() {
    var sentenceNumber = getSentenceNumber();
    if (!getEditMode(sentenceNumber)) {
        switchSelectionUp();
    }
}

function downArrowHandle() {
    var sentenceNumber = getSentenceNumber();
    if (!getEditMode(sentenceNumber)) {
        switchSelectionDown();
    }
}

function ctrlUpArrowHandle() {
    var sentenceNumber = getSentenceNumber();
    prevSentence();
}

function ctrlDownArrowHandle() {
    var sentenceNumber = getSentenceNumber();
    nextSentence();
}

function toggleSelectedChoice(element) {
    if (element.className == 'choice-selected') {
        element.className = 'choice-available';
    } else {
        element.className = 'choice-selected';    
    }
}

function deselectChoice(element) {
    element.className = 'choice-inactive';
}

function hotKeyHandle(number) {
    var sentenceNumber = getSentenceNumber();
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);
    if (cellTypeElement.value == 'choices') {
        var choiceId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX+'-choice-'+number;
        var choiceElement = document.getElementById(choiceId);
        if (choiceElement != null) {
            toggleSelectedChoice(choiceElement);
            updateAndSaveCell(sentenceNumber);
        }
    }
}

function handleWordOperation(e, operation) {
    if (getEditMode(getSentenceNumber())) {
        $('#'+operation+getGridX(getSentenceNumber())).click();
        e.preventDefault();    
    }
}

function splitWord(e) {
    var sentenceNumber = getSentenceNumber();
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var splitSpan = document.getElementById(cellId+'-split-span');
    if (splitSpan != null && splitSpan.className == "word-unsplit" && getEditMode(sentenceNumber)) {
        splitSpan.className="word-split"; 
        var splitElement = document.getElementById(cellId+'-split');
        splitElement.value = '1';
           
        var inputUnsplit = splitSpan.querySelector('.word-unsplit-field input');
        var splitInputs = splitSpan.querySelectorAll('.word-split-field input');
        var stemInput = splitInputs[0];
        var suffixInput = splitInputs[1];
        
        pos = inputUnsplit.selectionStart;
        
        stemInput.value = inputUnsplit.value.substring(0,pos);
        suffixInput.value = inputUnsplit.value.substring(pos, inputUnsplit.value.length);        
        stemInput.focus();

        e.preventDefault();
    }
    
}

function unsplitWord(e) {
    var sentenceNumber = getSentenceNumber();
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var splitSpan = document.getElementById(cellId+'-split-span');
    if (splitSpan != null && splitSpan.className == "word-split" && getEditMode(sentenceNumber)) {
        splitSpan.className="word-unsplit"; 
        var splitElement = document.getElementById(cellId+'-split');
        splitElement.value = '0';
           
        var inputUnsplit = splitSpan.querySelector('.word-unsplit-field input');
        var splitInputs = splitSpan.querySelectorAll('.word-split-field input');
        var stemInput = splitInputs[0];
        var suffixInput = splitInputs[1];
        
        inputUnsplit.value = stemInput.value+suffixInput.value;
        inputUnsplit.focus();

        e.preventDefault();
    }
    
}



$(document).keydown(function(e) {
    if (e.ctrlKey) {
        switch(e.which) {
            case 38:
                ctrlUpArrowHandle();
            break;

            case 40:
                ctrlDownArrowHandle();
            break;

            case 73: //i
                handleWordOperation(e, 'insertWord');
            break;
            
            case 74: //j
                splitWord(e);
            break;
            
            case 75: //k
                unsplitWord(e);
            break;
            
            case 76: //l
                handleWordOperation(e, 'deleteWord');
            break;
            
            case 79: //o
                handleWordOperation(e, 'insertAfterWord');
            break;
            
            case 85: //u
                handleWordOperation(e, 'unmarkPostposition');
            break;

            case 89: //y
                handleWordOperation(e, 'markPostposition');
            break;

            default: return; // exit this handler for other keys
        }
    } else {
        switch(e.which) {
            case 37:
                leftArrowHandle();
            break;

            case 38:
                upArrowHandle();
            break;

            case 39:
                rightArrowHandle();
            break;

            case 40:
                downArrowHandle();
            break;

            case 13:
            enterHandle(e);
            break;

            case 27:
            escapeHandle();
            break;
            
            case 81:  //q
            hotKeyHandle(0);
            break;

            case 87:  //w
            hotKeyHandle(1);
            break;

            case 69:  //e
            hotKeyHandle(2);
            break;

            case 82:  //r
            hotKeyHandle(3);
            break;

            case 65:  //a
            hotKeyHandle(4);
            break;

            case 83:  //s
            hotKeyHandle(5);
            break;

            case 68:  //d
            hotKeyHandle(6);
            break;

            case 70:  //f
            hotKeyHandle(7);
            break;

            case 90:  //z
            hotKeyHandle(8);
            break;

            case 88:  //x
            hotKeyHandle(9);
            break;

            case 67:  //c
            hotKeyHandle(10);
            break;

            case 86:  //v
            hotKeyHandle(11);
            break;

            default: return; // exit this handler for other keys
        }
    }

});

