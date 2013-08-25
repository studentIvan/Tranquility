function htmlCodeInsert(code, textAreaId) {
    var textArea = document.getElementById(textAreaId);
    textArea.focus();

    if (code == 'ul') {
        htmlCodeInsertStartStop(textArea, '<ul><li>', '</li></ul>');
    } else if (code == 'img') {
        var imageUri = htmlCodeInsertGetSelectedStringHelper(textArea);
        var imageSrc = prompt("Адрес изображения", imageUri);
        if (imageSrc != '' && imageSrc != null) {
            htmlCodeInsertString(textArea, '<img src="' + imageSrc + '" alt="">');
        }
    } else if (code == 'a') {
        var linkUri = htmlCodeInsertGetSelectedStringHelper(textArea);
        var linkHref = prompt("Адрес ссылки", linkUri);
        if (linkHref != '' && linkHref != null) {
            var linkText = prompt("Название ссылки", linkUri);
            if (linkText != '' && linkText != null) {
                htmlCodeInsertString(textArea, '<a href="' + linkHref + '">' + linkText + '</a>');
            }
        }
    } else {
        htmlCodeInsertStartStop(textArea, '<' + code + '>', '</' + code + '>');
    }

    textArea.focus();
}

function htmlCodeInsertGetSelectedStringHelper(textArea) {
    if (document.selection && document.selection.createRange) {
        var selected = document.selection.createRange();
        if (selected.parentElement() == textArea) {
            return selected.text;
        } else {
            return '';
        }
    } else if (typeof(textArea) != "undefined") {
        var selStart = textArea.selectionStart;
        var selEnd = textArea.selectionEnd;
        return textArea.value.substring(selStart, selEnd)
    } else {
        return '';
    }
}

function htmlCodeInsertString(textArea, string) {
    if (document.selection && document.selection.createRange) {
        var selected = document.selection.createRange();
        if (selected.parentElement() == textArea) {
            selected.text = string;
        }
    } else if (typeof(textArea) != "undefined") {
        var longueur = parseInt(textArea.value.length);
        var selStart = textArea.selectionStart;
        var selEnd = textArea.selectionEnd;
        var scrollTop = textArea.scrollTop;
        var scrollLeft = textArea.scrollLeft;
        textArea.value = textArea.value.substring(0, selStart) +
            string + textArea.value.substring(selEnd, longueur);
        textArea.scrollTop = scrollTop;
        textArea.scrollLeft = scrollLeft;
        textArea.selectionStart = selStart;
        textArea.selectionEnd = selStart + string.length;
    } else {
        textArea.value += string;
    }
}

function htmlCodeInsertStartStop(textArea, stringStart, stringStop) {
    if (document.selection && document.selection.createRange) {
        var selected = document.selection.createRange();
        if (selected.parentElement() == textArea) {
            selected.text = stringStart + selected.text + stringStop;
        }
    } else if (typeof(textArea) != "undefined") {
        var longueur = parseInt(textArea.value.length);
        var selStart = textArea.selectionStart;
        var selEnd = textArea.selectionEnd;
        var scrollTop = textArea.scrollTop;
        var scrollLeft = textArea.scrollLeft;
        textArea.value = textArea.value.substring(0, selStart) +
            stringStart + textArea.value.substring(selStart, selEnd) +
            stringStop + textArea.value.substring(selEnd, longueur);
        textArea.scrollTop = scrollTop;
        textArea.scrollLeft = scrollLeft;
        textArea.selectionStart = selStart;
        textArea.selectionEnd = selEnd + stringStart.length + stringStop.length;
    } else {
        textArea.value += stringStart + stringStop;
    }
}