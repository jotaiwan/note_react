// emojiHelper.js

export function copyToClipboard(emoji) {
    const textArea = document.createElement('textarea');
    textArea.value = emoji;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
}

export function insertEmojiIntoTextarea(emoji, textarea, selectionStart, selectionEnd) {
    const text = textarea.value;
    const start = selectionStart;
    const end = selectionEnd;
    const selectedText = text.slice(start, end);

    // Emoji tag matching logic
    const tagMatch = emoji.replace(/\s+/g, "").match(/^\{([^}]+)\}\{\/\1\}$/);
    let newValue;

    if (tagMatch) {
        const tagName = tagMatch[1];
        const openTag = `{${tagName}}`;
        const closeTag = `{/${tagName}}`;

        if (start === end) {
            newValue = `${text.slice(0, start)}${openTag}${closeTag}${text.slice(end)}`;
            textarea.value = newValue;
            const cursor = start + openTag.length + 1;
            textarea.setSelectionRange(cursor, cursor);
        } else {
            newValue = `${text.slice(0, start)}${openTag}${selectedText}${closeTag}${text.slice(end)}`;
            textarea.value = newValue;
            const cursor = start + openTag.length + 1 + selectedText.length + closeTag.length;
            textarea.setSelectionRange(cursor, cursor);
        }
    } else {
        const before = text.slice(0, start);
        const after = text.slice(end);

        let emojiToInsert = emoji;
        if (before && !/\s$/.test(before)) emojiToInsert = " " + emojiToInsert;
        if (after && !/[\s.,!?]/.test(after)) emojiToInsert += " ";

        newValue = before + emojiToInsert + after;
        textarea.value = newValue;

        const newCursor = before.length + emojiToInsert.length;
        textarea.setSelectionRange(newCursor, newCursor);
    }

    textarea.focus();
    return {
        selectionStart: textarea.selectionStart,
        selectionEnd: textarea.selectionEnd,
    };
}
