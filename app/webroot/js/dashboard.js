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

function prevSentence(documentId) {
    var offsetElement = document.getElementById('offset');
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
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
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        var sentencesCount = document.getElementsByName('sentence').length;
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
    
    var activeCell = document.getElementById('cell:'+sentenceNumber+':'+getGridY(sentenceNumber)+':'+getGridX(sentenceNumber));
    if (activeCell != null) {
        if (getEditMode(sentenceNumber)) {
            activeCell.className = 'edited';
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
    setEditMode(sentenceNumber, true);
    setGrid(sentenceNumber, gridY, gridX);
    updateSentence(sentenceNumber);
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

function setEditMode(sentenceNumber, editMode) {
    var element = document.getElementById('sentence'+sentenceNumber+'-edit-mode');
    if (element != null) {
        if (editMode) {
            element.value = "1";
        } else {
            element.value = "0";        
        }
    }
}

$(document).keydown(function(e) {
    switch(e.which) {
        case 37: // left
        alert('left');
        break;

        case 38: // up
        alert('up');
        break;

        case 39: // right
        alert('right');
        break;

        case 40: // down
        alert('down');
        break;

        default: return; // exit this handler for other keys
    }
    e.preventDefault(); // prevent the default action (scroll / move caret)
});
