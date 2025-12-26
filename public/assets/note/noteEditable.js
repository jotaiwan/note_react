let clickCount = 0;
let clickTimer;

$(document).on("click", ".editable", function () {
    const $cell = $(this);

    clickCount++;
    clearTimeout(clickTimer);

    clickTimer = setTimeout(function () {
        if (clickCount === 4) {
            debugger;
            const row = $cell.closest('tr');
            const rowId = $cell.closest('tr').data('row-id');
            const htmlNote = $cell.closest('tr').find('.editable').html();
            debugger;
            $.ajax({
                url: '/api/note/read/' + rowId,
                method: 'GET',
                success: function (response) {
                    let originalText = response.note.replace(/\\"/g, '"');
                    originalText = originalText.replace(/\\n/g, "\n");

                    const $textarea = $('<textarea>')
                        .val(originalText)
                        .addClass('table-textarea')
                        .on('input', function () {
                            this.style.height = 'auto';
                            this.style.height = this.scrollHeight + 'px';
                        });

                    $cell.empty().append($textarea);
                    $textarea[0].style.height = 'auto';
                    $textarea[0].style.height = $textarea[0].scrollHeight + 'px';
                    $textarea.focus();

                    // save on blur
                    $textarea.on("blur", function () {
                        const newText = $(this).val();

                        if (newText !== originalText) {
                            const confirmed = confirm("Content changed! Do you want to save it?");
                            if (confirmed) {
                                // update the cell visually
                                $cell.html('<pre>' + $('<div>').text(newText).html() + '</pre>');

                                // send AJAX POST to save
                                $.ajax({
                                    url: '/api/note/update/' + rowId,
                                    method: 'POST',
                                    contentType: 'application/json',
                                    data: JSON.stringify({
                                        rowId: rowId,
                                        note: newText
                                    }),
                                    success: function (resp) {
                                        $cell.html(resp["note"]);
                                        console.log("Saved:", resp);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error("Save failed:", error);
                                    }
                                });
                            } else {
                                // revert
                                $cell.html('<pre>' + $('<div>').text(originalText).html() + '</pre>');
                            }
                        } else {
                            // no change, just restore pre
                            $cell.html(htmlNote);
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Failed to load raw note:", error);
                }
            });
        }


        clickCount = 0;
    }, 400);
});
