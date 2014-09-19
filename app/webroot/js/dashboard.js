function updateDashboard() {
    var offsetElement = document.getElementById('offset');
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        
        var sentences = document.getElementsByName('sentence');
        for (var i=0; i<sentences.length; i++) {
            if (i == offset) {
                sentences[i].className = 'active';
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
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        if (offset > 0) {
            offsetElement.value = offset - 1;
            updateDashboard();
        }
    }
}

function nextSentence() {
    var offsetElement = document.getElementById('offset');
    if (offsetElement != null) {
        var offset = parseInt(offsetElement.value);
        var sentencesCount = document.getElementsByName('sentence').length;
        if (offset < sentencesCount - 1) {
            offsetElement.value = offset + 1;
            updateDashboard();
        }
    }
}
