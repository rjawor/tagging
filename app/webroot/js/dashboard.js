function updateDashboard() {
    var offsetElement = document.getElementById('offset');
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        
        var sentences = document.getElementsByName('sentence');
        for (var i=0; i<sentences.length; i++) {
            if (i == offset) {
                sentences[i].className = 'active';
                updateSentence(i);
            } else if (Math.abs(i - offset) <= 2) {
                sentences[i].className = 'context';            
            } else {
                sentences[i].className = 'inactive';
            }
        }
    }
}

function prevSentence() {
    var offsetElement = document.getElementById('offset');
    var documentId = getDocumentId();
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        setEditMode(offset, false);
        if (offset > 0) {
            offset--;
            offsetElement.value = offset;
            updateDashboard();
            $.ajax({async:true, url:"/tagging/dashboard/setCurrentDocument/"+documentId+"/"+offset});
        }
    }
}

function nextSentence(documentId) {
    var offsetElement = document.getElementById('offset');
    var documentId = getDocumentId();
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        var sentencesCount = document.getElementsByName('sentence').length;
        setEditMode(offset, false);
        if (offset < sentencesCount - 1) {
            offset++;
            offsetElement.value = offset;
            updateDashboard();
            $.ajax({async:true, url:"/tagging/dashboard/setCurrentDocument/"+documentId+"/"+offset});
        }
    }
}

function updateSentence(sentenceNumber) {
    var sentenceCells = document.querySelectorAll('#sentence'+sentenceNumber+" td.selected,td.edited");
    for (var i=0; i<sentenceCells.length; i++) {
        sentenceCells[i].className = 'normal-cell';
    }
    
    var activeCell = document.getElementById('cell-'+sentenceNumber+'-'+getGridY(sentenceNumber)+'-'+getGridX(sentenceNumber));
    if (activeCell != null) {
        if (getEditMode(sentenceNumber)) {
            activeCell.className = 'edited';
            var editField = activeCell.querySelector('.edit-field>input');
            if (editField != null) {
                editField.focus();
            }
        } else {
            activeCell.className = 'selected';        
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

function setEdited(sentenceNumber, gridY, gridX) {
    setEditMode(sentenceNumber, false); //switching off editing of current cell
    setEditMode(sentenceNumber, true);
    setGrid(sentenceNumber, gridY, gridX);
    updateSentence(sentenceNumber);
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

function setEditMode(sentenceNumber, editMode, preventSave) {
    preventSave = preventSave || false;
    var element = document.getElementById('sentence'+sentenceNumber+'-edit-mode');
    if (element != null) {
        if (editMode) {
            element.value = "1";
        } else {
            if (element.value == "1" && !preventSave) { //edit mode was switched off, but not by ESC
                saveCell(sentenceNumber);
            }
            element.value = "0";        
        }
    }
}

function toggleEditMode(sentenceNumber) {
    var element = document.getElementById('sentence'+sentenceNumber+'-edit-mode');
    if (element != null) {
        if (element.value == "1") {
            saveCell(sentenceNumber);
            element.value = "0";
        } else {
            element.value = "1";        
        }
    }
}

function saveCell(sentenceNumber) {
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cell = document.getElementById(cellId);
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);

    var displaySpan = cell.querySelector('.ro-display');
    var editSpan = cell.querySelector('.edit-field');
    
    if (cellTypeElement.value == 'text') {
        var textInputElement = editSpan.querySelector('input[type=text]');
        displaySpan.innerHTML = textInputElement.value;
        
        var wordAnnotationTypeElement = document.getElementById(cellId+'-word-annotation-type-id');
        if (wordAnnotationTypeElement != null) { //word annotation
            var wordAnnotationTypeId = wordAnnotationTypeElement.value;
            var wordId = document.getElementById(cellId+'-word-id').value;
            $.ajax({async:true, url:"/tagging/wordAnnotations/saveWordTextAnnotation/"+wordId+"/"+wordAnnotationTypeId+"/"+textInputElement.value.replace(/ /g, '%20')});

        } else { //sentence annotation
            var sentenceAnnotationTypeId = document.getElementById(cellId+'-sentence-annotation-type-id').value;
            var sentenceId = document.getElementById(cellId+'-sentence-id').value;
            $.ajax({async:true, url:"/tagging/sentenceAnnotations/saveSentenceAnnotation/"+sentenceId+"/"+sentenceAnnotationTypeId+"/"+textInputElement.value.replace(/ /g, '%20')});
        }
    } else {
        var selectedChoices = editSpan.querySelectorAll('input.choice-selected');
        displaySpan.innerHTML = '';
        var selectedChoicesIds=[];
        if (selectedChoices != null) {
            for (var i=0; i< selectedChoices.length; i++) {
                var choiceValueArr = selectedChoices[i].value.match(/^(\w+).*/);
                var choiceValue = choiceValueArr[1];
                displaySpan.innerHTML = displaySpan.innerHTML + '<input type="button" class="choice-selected" value="'+choiceValue+'" />';

                var selectedChoiceId = document.getElementById(selectedChoices[i].id+'-type-id').value;
                selectedChoicesIds[selectedChoicesIds.length] = selectedChoiceId;
            }
        }
        
        var wordAnnotationTypeId = document.getElementById(cellId+'-word-annotation-type-id').value;
        var wordId = document.getElementById(cellId+'-word-id').value;
        
        var selectedChoicesIdsString = '';
        for(var i=0; i<selectedChoicesIds.length; i++) {
            selectedChoicesIdsString += selectedChoicesIds[i];
            if (i < selectedChoicesIds.length - 1) {
                selectedChoicesIdsString += ',';
            }   
        }
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

function enterHandle() {
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
}

function handleEnterInMultipleChoices(sentenceNumber) {
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cell = document.getElementById(cellId);
    
    alert('handle enter in multiple choices');    
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

function hotKeyHandle(number) {
    var sentenceNumber = getSentenceNumber();
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var choiceId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX+'-choice-'+number;
    var choiceElement = document.getElementById(choiceId);
    if (choiceElement != null) {
        toggleSelectedChoice(choiceElement);
        saveCell(sentenceNumber);
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
            enterHandle();
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

