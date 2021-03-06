function showWordCriteria(id) {
    var nextId = id + 1;
    $('#showNext'+id).addClass('hidden');
    $('#word'+nextId+'-div').removeClass('hidden');
}

function enterCriterionHandle(e) {
    selectedValue = e.target.value;
    e.target.value='';
    buttonName = e.target.id+'Button';
    valueName = e.target.id+'Value';
    e.preventDefault();
    addSearchCriterion(selectedValue, buttonName, valueName);
}

function addSearchCriterion(selectedValue, buttonName, valueName) {
    $("[name=\""+buttonName+"\"][type=\"button\"][value=\""+selectedValue+"\"]").attr('class', 'choice-selected');
    var selected = $("[name=\""+buttonName+"\"][class=\"choice-selected\"]");
    var ids = [];
    for (var i=0;i<selected.length;i++) {
        ids.push(selected[i].id);
    }
    $('#'+valueName).val(ids.join());
}

function addCriterionBeforeSubmit(elementId) {
    addSearchCriterion($('#'+elementId).val(), elementId+'Button', elementId+'Value');

}


function documentCheckboxAnyClicked() {
    $('.documentCheckbox').prop('checked', false);
    setPage(0);
    submitFilterForm();
}


function documentCheckboxClicked() {
    $('#documentAny').prop('checked', false);
    setPage(0);
}


function epoqueCheckboxAnyClicked() {
    $('.epoqueCheckbox').prop('checked', false);
    setPage(0);
    submitFilterForm();
}


function epoqueCheckboxClicked() {
    $('#epoqueAny').prop('checked', false);
    setPage(0);
    // Narrowing epoque filter may cause some selected documents
    // to become invisible, yet active in the sql conditions.
    // It is necessary to clear the document filter.
    $('#documentAny').prop('checked', true);
    documentCheckboxAnyClicked();
}

function langCheckboxAnyClicked() {
    $('.langCheckbox').prop('checked', false);
    setPage(0);
    submitFilterForm();
}


function langCheckboxClicked() {
    $('#langAny').prop('checked', false);
    setPage(0);
    // Narrowing language filter may cause some selected documents
    // to become invisible, yet active in the sql conditions.
    // It is necessary to clear the document filter.
    $('#documentAny').prop('checked', true);
    documentCheckboxAnyClicked();
}

function setPage(pageNumber) {
    $('#page_number').val(pageNumber);
    submitFilterForm();
}

function decreasePage() {
    $('#page_number').val( function(i, oldval) {
        if (parseInt(oldval) > 0) {
            return --oldval;
        } else {
            return oldval;
        }
    });
    submitFilterForm();
}
function increasePage() {
    $('#page_number').val( function(i, oldval) {
        max = parseInt($('#total_pages').val());
        if (parseInt(oldval) + 1 < max) {
            return ++oldval;
        } else {
            return oldval;
        }
    });
    submitFilterForm();
}

function submitFilterForm() {
    $('#documentsScrollTop').val($('#documentFilterArea').scrollTop());
    document.getElementById('filter_form').submit();
}

function moveToFolder(selectElement, documentId) {
    var folderId = selectElement.options[ selectElement.selectedIndex ].value;
    if (folderId >= 0) {
        location.href=systemInstallationPath+'/documents/moveToFolder/'+documentId+'/'+folderId;
    }
}

function folderAddForm() {
    $('#folder_add_form').toggleClass('hidden');
}

function selectByLang(lang) {
    $('input:checked').prop('checked', false);
    $('input.checkboxDoc'+lang).prop('checked', 'true');
    document.getElementById('filter_form').submit();
}

function toggleDocumentFilter() {
    toggleVisibility('documentFilter');
    $('#documentFilterInput').val(1-$('#documentFilterInput').val());
}

function selectAll() {
    $('input[type=checkbox]').prop('checked', true);
    document.getElementById('filter_form').submit();
}

function selectNone() {
    $('input[type=checkbox]').prop('checked', false);
    document.getElementById('filter_form').submit();
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
            $.ajax({async:true, url:systemInstallationPath+"/dashboard/setCurrentDocument/"+documentId+"/"+offset});
            $.post(systemInstallationPath+"/history/clear");
            document.location.href = systemInstallationPath+"/dashboard/index/"+documentId+"/"+offset;
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
            $.ajax({async:true, url:systemInstallationPath+"/dashboard/setCurrentDocument/"+documentId+"/"+offset});
            $.post(systemInstallationPath+"/history/clear");
            document.location.href = systemInstallationPath+"/dashboard/index/"+documentId+"/"+offset;
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

            if ($('#'+cellId+'-type').val() == 'word-reference') {

                var wordId = $('#'+cellId+'-word-id').val();

                $.ajax({
            		type: "POST",
            		url: systemInstallationPath+"/words/getWordReferences",
            		data: { wordId: wordId, gridX:getGridX(getSentenceNumber()), gridY:getGridY(getSentenceNumber()) },
                })
                .done(function( jsonString ) {
                    updateWordReferences(jsonString);
                });

            }
        } else {
            activeCell.className = 'selected';
        }

        var firstReloadElement = document.getElementById('first-reload');
        if (firstReloadElement.value == "1") {
            updateSuggestions();
            firstReloadElement.value = "0";
        }

        adjustScroll();
    }
}

function updateWordReferences(jsonString) {
    var sentenceNumber = getSentenceNumber();
    var wordReferences = jQuery.parseJSON(jsonString);
    var cellId = 'cell-'+sentenceNumber+'-'+wordReferences.gridY+'-'+wordReferences.gridX;

    var wordReferencePicker = $('#'+cellId+' .word-reference-picker');

    var referencesHtml = '<img src="'+systemInstallationPath+'/img/delete.png" title="delete anchor" onclick="setWordReference('+wordReferences.gridY+','+wordReferences.gridX+',0,\'\');if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;"/>';
    referencesHtml += '<table>';
    for(var i = 0;i<wordReferences.document.length;i++) {
        referencesHtml += '<tr>';
        for (var j=0;j<wordReferences.document[i].length;j++) {
            referencesHtml += '<td style="cursor:pointer" onclick="setWordReference('+wordReferences.gridY+','+wordReferences.gridX+','+wordReferences.document[i][j]['wordId']+',\''+wordReferences.document[i][j]['wordText']+'\');if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;">'+wordReferences.document[i][j]['wordText']+'</td>';
        }
        referencesHtml += '</tr>';
    }
    referencesHtml += '</table>'
    wordReferencePicker.html(referencesHtml);
    wordReferencePicker.scrollTop($(wordReferencePicker)[0].scrollHeight);

}

function setWordReference(gridY,gridX,wordId,wordText) {
    var sentenceNumber = getSentenceNumber();
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var oldValue = $('#'+cellId+'-value').val();
    var newValue = wordId+'@#@'+wordText;
    modifyValue(sentenceNumber, gridX, gridY, newValue);
    setEditMode(sentenceNumber, false, true);
    updateSentence();
    updateSuggestions();

    $.post( systemInstallationPath+"/history/storeOperation", {type: 'modifyCellValue', gridX:gridX, gridY:gridY, oldValue:oldValue, newValue:newValue} );

}

function adjustScroll() {
    var sentenceNumber = getSentenceNumber();
    var cellId = 'cell-'+sentenceNumber+'-'+getGridY(sentenceNumber)+'-'+getGridX(sentenceNumber);
    var activeCell = document.getElementById(cellId);
    var offset = $('#'+activeCell.id).offset();
    var width = $(window).width();

    $('div#content').animate({
        scrollTop: 0,
        scrollLeft: $('div#content').scrollLeft() + offset.left - 500
    }, 100);

}

function hideAllSuggestionBoxes() {
    var suggestionBoxes = document.querySelectorAll("div[id$='suggestion-box']");
    for (var i=0; i<suggestionBoxes.length; i++) {
        suggestionBoxes[i].className = 'suggestion-box-inactive';
    }
}

function updateSuggestions() {
    var wordCellId = 'cell-'+getSentenceNumber()+'-0-'+getGridX(getSentenceNumber());
    var wordId = document.getElementById(wordCellId+'-word-id').value;

    document.getElementById(wordCellId+'-preloader').className="preloader-active";
    $.ajax({
		type: "POST",
		url: systemInstallationPath+"/words/getSuggestions",
		data: { wordId: wordId, gridX:getGridX(getSentenceNumber()) },
    })
    .done(function( jsonString ) {
        var sentenceNumber = getSentenceNumber();
		var suggestions = jQuery.parseJSON(jsonString);
	    var wordCellId = 'cell-'+getSentenceNumber()+'-0-'+suggestions.gridX;
        var suggestionBox = document.getElementById(wordCellId+'-suggestion-box');
        updateSuggestionBox(suggestionBox, suggestions);
        document.getElementById(wordCellId+'-preloader').className="preloader-inactive";
    });
}

function applySuggestion(gridX, suggestionIndex) {
    var wordCellId = 'cell-'+getSentenceNumber()+'-0-'+gridX;
    var suggestionBox = document.getElementById(wordCellId+'-suggestion-box');
    if (suggestionBox.className == 'suggestion-box') {
        var suggestions = jQuery.data(suggestionBox, "suggestions");
        if (suggestionIndex < suggestions.count) {
            var annotations = suggestions.data[suggestionIndex].suggestion.annotations;
            var modifications = [];
            for (var i = 0; i < annotations.length; i++) {
                //we add 1 to the position, because the first word annotation is at gridY = 1
                var gridY = parseInt(annotations[i].position)+1;
                var oldValue = getCellValue(gridX, gridY);
                var newValue = annotations[i].value;
                if (oldValue != newValue) {
                    modifications.push({type: 'modifyCellValue', gridX:gridX, gridY:gridY, oldValue:oldValue, newValue:newValue});
                    modifyValue(getSentenceNumber(), gridX, gridY, newValue);
                }
            }
            $.post( systemInstallationPath+"/history/storeOperation", {type: 'applySuggestion', modifications:modifications} );

        }
    }
}

function getCellValue(gridX, gridY) {
    var cellId = 'cell-'+getSentenceNumber()+'-'+gridY+'-'+gridX;
    var valueElement = document.getElementById(cellId+'-value');
    if (valueElement != null) {
        return valueElement.value;
    } else {
        return '';
    }
}

function updateSuggestionBox(suggestionBox, suggestions) {
	if (suggestions.count == 0) {
        suggestionBox.className = 'suggestion-box-inactive';
	} else {
	    var suggestionsHtml = '<table>';
        suggestionBox.className = 'suggestion-box';
        jQuery.data(suggestionBox, "suggestions", suggestions);
        //alert(JSON.stringify(suggestions));
        for (var i = 0; i < suggestions.count; i++) {
            var index = i+1;
            suggestionsHtml += '<tr><td><img style="cursor:pointer" src="'+systemInstallationPath+'/img/apply.png" title="apply suggestion (ctrl + '+index+')" onclick="applySuggestion('+suggestions.gridX+','+i+')" alt="apply suggestion"></td>';
            if (suggestions.data[i].suggestionCount == 0) {
	            suggestionsHtml += '<td><input title="this suggestion comes from predefined rules" type="button" class="suggestion-count-box" value="R" /></td>';
            } else {
	            suggestionsHtml += '<td><a title="view one of the words with this annotation" href="'+systemInstallationPath+'/dashboard/viewWord/'+suggestions.data[i].wordId+'" target="_blank"><img src="'+systemInstallationPath+'/img/editSmall.png"/></a></td>';
		        suggestionsHtml += '<td><input title="suggestion frequency score" type="button" class="suggestion-count-box" value="'+suggestions.data[i].suggestionCount+'" /></td>';
            }
	        suggestionsHtml += '<td>'+suggestions.data[i].suggestion.text+'</td>';
            var annotations = suggestions.data[i].suggestion.annotations;
            for (var j = 0; j < annotations.length; j++) {
                suggestionsHtml += '<td>';
                var annotation = annotations[j];
                if (annotation.type == 'text') {
                    suggestionsHtml += annotation.value;
                } else if (annotation.type == 'choices') {
                    var choices = annotation.choices;
                    for(var k = 0; k < choices.length; k++) {
                        var choice = choices[k];
                        suggestionsHtml += '<input type="button" class="choice-selected" title="'+choice.description+'" value="'+choice.value+'" />';
                    }
                }
                suggestionsHtml += '</td>';
            }
            suggestionsHtml += '</tr>';
        }
        suggestionsHtml += '</table>';
        suggestionBox.innerHTML = suggestionsHtml;
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
          getGridY(sentenceNumber) == gridY &&
          getGridX(sentenceNumber) == gridX)) {

        setEditMode(sentenceNumber, false); //switching off editing of current cell
        setEditMode(sentenceNumber, true);
        setGrid(sentenceNumber, gridY, gridX);
        updateSentence(sentenceNumber);
        hideAllSuggestionBoxes();
        updateSuggestions();
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
    updateSuggestions();
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
            if (element.value == "1" && !preventSave) { //edit mode was switched off, but not by ESC or not in choices cell
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

// update the hidden input with the cell value, based on the edit field
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
    var oldValue = valueElement.value;
    var newValue = '';

    if (cellTypeElement.value == 'word-text' || cellTypeElement.value == 'sentence-text') {
        var textInputElement = editSpan.querySelector('input[type=text]');
        newValue = normalizeText(escapeHTML(textInputElement.value))
        valueElement.value = newValue;

    } else if (cellTypeElement.value == 'word') {
        var splitElement = document.getElementById(cellId+'-split');
        if (splitElement.value == '0') {
            var wordTextElement = cell.querySelector('.edit-field .word-unsplit-field input');
            newValue = normalizeText(escapeHTML(wordTextElement.value));
            valueElement.value = newValue;
        } else {
            var wordTextElements = cell.querySelectorAll('.edit-field .word-split-field input');
            var stem = normalizeText(escapeHTML(wordTextElements[0].value));
            var suffix = normalizeText(escapeHTML(wordTextElements[1].value));
            newValue = stem+','+suffix;
            valueElement.value = newValue;
        }


    } else if (cellTypeElement.value == 'choices' || cellTypeElement.value == 'multiple-choices') {
        var selectedChoices = editSpan.querySelectorAll('input.choice-selected');
        var selectedChoicesIds=[];
        if (selectedChoices != null) {
            for (var i=0; i< selectedChoices.length; i++) {
                var choiceValueArr = selectedChoices[i].value.match(/^([\w\-]+).*/);
                var choiceValue = choiceValueArr[1];
                var selectedChoiceId = document.getElementById(selectedChoices[i].id+'-type-id').value;
                selectedChoicesIds[selectedChoicesIds.length] = selectedChoiceId;
            }
            var selectedChoicesIdsString = selectedChoicesIds.join();
            newValue = selectedChoicesIdsString;
            valueElement.value = newValue;
        } else {
            newValue = '';
            valueElement.value = newValue;
        }
    }
    if (oldValue != newValue) {
        var opData =
        $.post( systemInstallationPath+"/history/storeOperation", {type: 'modifyCellValue', gridX:gridX, gridY:gridY, oldValue:oldValue, newValue:newValue} );
    }

}

function modifyValue(sentenceNumber, gridX, gridY, value) {
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var valueElement = document.getElementById(cellId+'-value');
    valueElement.value = value;
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);
    if (cellTypeElement != null && cellTypeElement.value == 'word') {
        var splitElement = document.getElementById(cellId+'-split');
        if (splitElement != null) {
            if (value.indexOf(',') > -1) {
                splitElement.value = '1';
            } else {
                splitElement.value = '0';
            }
        }
    }
    updateCellDisplay(sentenceNumber, gridX, gridY);
    saveCell(sentenceNumber, gridX, gridY);
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
        var splitSpan = document.getElementById(cellId+'-split-span');
        var splitElement = document.getElementById(cellId+'-split');
        if (splitElement.value == '0') {
            splitSpan.className='word-unsplit';
            displaySpan.innerHTML = deEscapeHTML(valueElement.value);
            var wordTextElement = cell.querySelector('.edit-field .word-unsplit-field input');
            wordTextElement.value = deEscapeHTML(valueElement.value);
        } else {
            splitSpan.className='word-split';
            var stemAndSuffix = valueElement.value.split(",");
            var stem = stemAndSuffix[0];
            var suffix = stemAndSuffix[1];
            displaySpan.innerHTML = deEscapeHTML(stem)+'&#124;'+deEscapeHTML(suffix);
            var wordTextElements = cell.querySelectorAll('.edit-field .word-split-field input');
            wordTextElements[0].value = deEscapeHTML(stem);
            wordTextElements[1].value = deEscapeHTML(suffix);
        }
    } else if (cellTypeElement.value == 'word-reference') {
        var arr = valueElement.value.split("@#@");
        wordId = arr[0];
        wordText = arr[1];

        if (wordId == 0) {
            displaySpan.innerHTML = '';
        } else {
            displaySpan.innerHTML = '<a href="'+systemInstallationPath+'/dashboard/viewWord/'+wordId+'" target="_blank"><img onclick="if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;" src="'+systemInstallationPath+'/img/anchor.png" alt="anchor"></a>&nbsp;'+wordText;
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
        $.ajax({
          type: 'POST',
          url: systemInstallationPath+"/wordAnnotations/saveWordTextAnnotation",
          data: { wordId: wordId, wordAnnotationTypeId: wordAnnotationTypeId, text: valueElement.value },
          async:false
        });
    } else if (cellTypeElement.value == 'word-reference') {
        var wordAnnotationTypeElement = document.getElementById(cellId+'-word-annotation-type-id');
        var wordAnnotationTypeId = wordAnnotationTypeElement.value;
        var wordId = document.getElementById(cellId+'-word-id').value;
        var arr = valueElement.value.split('@#@');
        referenceWordId = arr[0];
        referenceWordText = arr[1];
        $.ajax({
          type: 'POST',
          url: systemInstallationPath+"/wordAnnotations/saveWordTextAnnotation",
          data: { wordId: wordId, wordAnnotationTypeId: wordAnnotationTypeId, text: referenceWordText, numeric:referenceWordId },
          async:false
        });
    } else if (cellTypeElement.value == 'sentence-text') {
        var sentenceAnnotationTypeId = document.getElementById(cellId+'-sentence-annotation-type-id').value;
        var sentenceId = document.getElementById(cellId+'-sentence-id').value;
        $.post( systemInstallationPath+"/sentenceAnnotations/saveSentenceAnnotation", { sentenceId: sentenceId, sentenceAnnotationTypeId: sentenceAnnotationTypeId, text: valueElement.value} );
     } else if (cellTypeElement.value == 'word') {
        var splitElement = document.getElementById(cellId+'-split');
        var wordId = document.getElementById(cellId+'-word-id').value;

        if (splitElement.value == '0') {
            $.ajax({
              type: 'POST',
              url: systemInstallationPath+"/words/saveWord",
              data: { wordId: wordId, text: valueElement.value, wordSplit:0},
              async:false
            });
        } else {
            var wordTextElements = cell.querySelectorAll('.edit-field .word-split-field input');
            var stemAndSuffix = valueElement.value.split(",");
            var stem = stemAndSuffix[0];
            var suffix = stemAndSuffix[1];
            $.ajax({
              type: 'POST',
              url: systemInstallationPath+"/words/saveWord",
              data: { wordId: wordId, text: valueElement.value, wordSplit:1, stem: stem, suffix:suffix},
              async:false
            });
        }

      } else if (cellTypeElement.value == 'choices' || cellTypeElement.value == 'multiple-choices') {
        var wordAnnotationTypeId = document.getElementById(cellId+'-word-annotation-type-id').value;
        var wordId = document.getElementById(cellId+'-word-id').value;
        var selectedChoicesIdsString = valueElement.value;
        if (selectedChoicesIdsString == '') {
            selectedChoicesIdsString = 'none';
        }
        $.ajax({async:true, url:systemInstallationPath+"/wordAnnotations/saveWordChoicesAnnotation/"+wordId+"/"+wordAnnotationTypeId+"/"+selectedChoicesIdsString});
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
        hideAllSuggestionBoxes();
        updateSuggestions();
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
        hideAllSuggestionBoxes();
        updateSuggestions();
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
    } else if(getCellType(sentenceNumber) == 'choices') {
        if (!getEditMode(sentenceNumber)) {
            setEditMode(sentenceNumber, true);
        } else {
            setEditMode(sentenceNumber, false, true);
        }
        updateSentence(sentenceNumber);
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
        if (getEditMode(sentenceNumber)) {
            setEditMode(sentenceNumber, false, true);
        } else {
            setEditMode(sentenceNumber, true);
        }
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
    updateAndSaveCell(getSentenceNumber());
}

function handleChoiceClick(choiceElement) {
    toggleSelectedChoice(choiceElement);
    updateAndSaveCell(getSentenceNumber());
}

function hotKeyHandle(number) {
    var sentenceNumber = getSentenceNumber();
    var gridX = getGridX(sentenceNumber);
    var gridY = getGridY(sentenceNumber);
    var cellId = 'cell-'+sentenceNumber+'-'+gridY+'-'+gridX;
    var cellTypeId = cellId+'-type';
    var cellTypeElement = document.getElementById(cellTypeId);
    if (cellTypeElement != null && cellTypeElement.value == 'choices') {
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
    if (splitSpan != null && splitSpan.className == 'word-unsplit' && getEditMode(sentenceNumber)) {
        splitSpan.className='word-split';
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
    if (splitSpan != null && splitSpan.className == 'word-split' && getEditMode(sentenceNumber)) {
        splitSpan.className='word-unsplit';
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


function suggestionHandle(e, suggestionNumber) {
    applySuggestion(getGridX(getSentenceNumber()), suggestionNumber);
    e.preventDefault();
}

function toggleVisibility(id) {
   var e = document.getElementById(id);
   if(e.style.display == 'inline')
      e.style.display = 'none';
   else
      e.style.display = 'inline';
}

function undoHandle() {
    if (!getEditMode(getSentenceNumber())) {
        $.post(systemInstallationPath+"/history/undo", function(data) {
                               performOperation(data);
                            });
    }
}

function redoHandle() {
    if (!getEditMode(getSentenceNumber())) {
        $.post(systemInstallationPath+"/history/redo", function(data) {
                               performOperation(data);
                            });
    }
}

function performOperation(operationData) {
    var operation = jQuery.parseJSON(operationData);
    //alert('performing operation: '+operationData);
    if (operation.type == 'modifyCellValue') {
        modifyValue(getSentenceNumber(), operation.gridX, operation.gridY, operation.newValue);
    } else if (operation.type == 'applySuggestion') {
        var modificationsLength = operation.modifications.length;
        for (var i = 0; i < modificationsLength; i++) {
            var modification = operation.modifications[i];
            modifyValue(getSentenceNumber(), modification.gridX, modification.gridY, modification.newValue);
        }
    } else if (operation.type == 'unmarkPostposition' ||
               operation.type == 'markPostposition' ||
               operation.type == 'insertWord' ||
               operation.type == 'deleteWord'
               ) {
        window.location.href = systemInstallationPath+'/words/'+operation.type+'/'+operation.documentId+'/'
                                                                   +operation.documentOffset+'/'
                                                                   +operation.sentenceId+'/'
                                                                   +operation.position+'/1';
    }
}

function clearHistoryHandle(e) {
    $.post(systemInstallationPath+"/history/clear");
    e.preventDefault();
}

function listOperationsHandle(e) {
    $.post(systemInstallationPath+"/history/listOperations", function( data ) {
                           alert("operations: " + data );
                        });
    e.preventDefault();
}

function copyAnnotations(sourceX, targetX) {
    var annotationsCount = $('#sentence'+getSentenceNumber()+'-word-annotation-count').val();
    var modifications = [];

    for(var i=2;i<annotationsCount;i++) {
        var sourceValue = $('#cell-'+getSentenceNumber()+'-'+i+'-'+sourceX+'-value').val();
        var targetValue = $('#cell-'+getSentenceNumber()+'-'+i+'-'+targetX+'-value').val();
        if (sourceValue != '' && sourceValue != targetValue) {
            modifications.push({type: 'modifyCellValue', gridX:targetX, gridY:i, oldValue:targetValue, newValue:sourceValue});
            modifyValue(getSentenceNumber(), targetX, i, sourceValue);
        }
    }
    if (modifications.length > 0) {
        $.post( systemInstallationPath+"/history/storeOperation", {type: 'applySuggestion', modifications:modifications} );
    }
}
