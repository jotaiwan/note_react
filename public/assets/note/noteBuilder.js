
import { Popover } from 'bootstrap';
import $ from 'jquery';

console.log("Jquery version: " + jQuery.fn.jquery + " and Popover version: " + Popover.VERSION);

var windows = [];

$(function () {

    const $link = $('a[id^=menu]');
    let hideTimeout;


    // 這是 variables for emoji insertion feature，譬如是包起來{code}...{/code}還是直接插入到 mouse cursor 位置
    let activeTextarea = null;
    let selectionStart = 0;
    let selectionEnd = 0;

    // Your jQuery DOM code here
    const rows = document.querySelectorAll('.table tbody tr');
    const table = document.querySelector('.table');
    const header = table.querySelector('th'); // First header (Ticket)
    const links = document.querySelectorAll('.table tbody a');
    const copies = document.querySelectorAll('.table tbody i.fa-copy');

    const today = new Date();
    const dayOfWeek = today.getDay(); // Sunday = 0, Monday = 1, ..., Tuesday = 2

    const jira_url = "https://viatorinc.atlassian.net/browse/";

    updateSearchFromCookie();

    header.addEventListener('click', function () {
        const isAsc = header.classList.contains('asc');
        sortTable(isAsc ? 'desc' : 'asc');
        header.classList.toggle('asc', !isAsc);
    });

    links.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default navigation if needed
            const href = this.getAttribute('href');
            const text = this.textContent.trim(); // or use this.innerText
            console.log('Link clicked:', href);

            // copyToClipboard(text);
            window.open(href, '_blank');
        });
    });

    copies.forEach(icon => {
        icon.addEventListener('click', () => {
            const text = icon.getAttribute('data-text');
            if (text) {
                copyToClipboard(text);
            }
        });
    });

    if (dayOfWeek === 2) {
        $("#securityVulnerability").show();
    }

    rows.forEach(row => {
        row.addEventListener('mouseenter', () => {
            const classList = Array.from(row.classList).filter(c => c.startsWith('group-'));
            if (classList.length > 0) {
                document.querySelectorAll('.' + classList[0]).forEach(el => el.classList.add('hover-group'));
            }
        });

        row.addEventListener('mouseleave', () => {
            const classList = Array.from(row.classList).filter(c => c.startsWith('group-'));
            if (classList.length > 0) {
                document.querySelectorAll('.' + classList[0]).forEach(el => el.classList.remove('hover-group'));
            }
        });
    });

    // Track which textarea is active for emojiSelect id
    $("textarea").on("focus", function () {
        activeTextarea = this;
    });

    // Continuously update selection when user selects text for emojiSelect id
    $("textarea").on("select keyup mouseup", function () {
        if (activeTextarea) {
            selectionStart = activeTextarea.selectionStart;
            selectionEnd = activeTextarea.selectionEnd;
        }
    });

    // Also store selection before blur (losing focus) for emojiSelect id
    $("textarea").on("blur", function () {
        if (activeTextarea) {
            selectionStart = activeTextarea.selectionStart;
            selectionEnd = activeTextarea.selectionEnd;
        }
    });


    let skipBlurConfirm = false;



    //
    // // Track which textarea is active and remember cursor position
    //     $(document)
    //         .on("focus", "textarea", function () {
    //             activeTextarea = this;
    //             selectionStart = this.selectionStart;
    //             selectionEnd = this.selectionEnd;
    //         })
    //         .on("keyup mouseup select", "textarea", function () {
    //             if (this === activeTextarea) {
    //                 selectionStart = this.selectionStart;
    //                 selectionEnd = this.selectionEnd;
    //             }
    //         });
    //
    // // 👇 Add this BEFORE your #emojiSelect change handler
    //     $("#emojiSelect").on("mousedown focus", function (e) {
    //         // Prevent the textarea from losing focus
    //         e.preventDefault();
    //     });




    $("#emojiSelect").on("change", function (e) {
        const emoji = $(this).val();
        // const htmlEntity = emojiToHtmlEntity(emoji);
        if (emoji) {
            copyToClipboard(emoji);
        }
        $(this).val("");

        // 這個是新加的，重要
        // 如果有 selected text in textarea, 就用包起來的方式 (譬如 {code}...{/code})
        // 如果沒有 selected text, 就直接在 cursor 位置插入 emoji

        // 進階版開始 -----------------------------------------------------------------------------------------
        if (!emoji || !activeTextarea) return;

        const textarea = activeTextarea;
        const text = textarea.value;
        const start = selectionStart;
        const end = selectionEnd;
        const selectedText = text.slice(start, end);

        e.stopImmediatePropagation();
        textarea.focus();

        // Detect {tag}{/tag} structure (ignore whitespace/newlines)
        const tagMatch = emoji.replace(/\s+/g, "").match(/^\{([^}]+)\}\{\/\1\}$/);

        if (tagMatch) {
            const tagName = tagMatch[1];
            const openTag = `{${tagName}}`;
            const closeTag = `{/${tagName}}`;

            let newValue;

            if (start === end) {
                // No selection → insert inline
                newValue = `${text.slice(0, start)}${openTag}${closeTag}${text.slice(end)}`;
                textarea.value = newValue;
                const cursor = start + openTag.length + 1;
                textarea.setSelectionRange(cursor, cursor);
            } else {
                // Inline wrapping
                newValue = `${text.slice(0, start)}${openTag}${selectedText}${closeTag}${text.slice(end)}`;
                newValue = `${text.slice(0, start)}${openTag}${selectedText}${closeTag}${text.slice(end)}`;
                textarea.value = newValue;
                const cursor = start + openTag.length + 1 + selectedText.length + closeTag.length;
                textarea.setSelectionRange(cursor, cursor);
            }
        } else {
            // ✅ Smart emoji insertion
            const before = text.slice(0, start);
            const after = text.slice(end);

            const prevChar = before.slice(-1);
            const nextChar = after.slice(0, 1);

            let emojiToInsert = emoji;

            // Add space before emoji if needed (not at start, not already space or newline)
            if (before && !/\s$/.test(before)) emojiToInsert = " " + emojiToInsert;

            // Add space after emoji if:
            // - not at end of text
            // - next char is not punctuation or space
            if (nextChar && !/[\s.,!?]/.test(nextChar)) emojiToInsert += " ";

            // Combine result
            const newValue = before + emojiToInsert + after;
            textarea.value = newValue;

            const newCursor = before.length + emojiToInsert.length;
            textarea.setSelectionRange(newCursor, newCursor);
        }

        // Update stored selection
        selectionStart = textarea.selectionStart;
        selectionEnd = textarea.selectionEnd;

        textarea.focus();


        // 進階版結束 -----------------------------------------------------------------------------------------

        // 保持舊的，依然 copy 到 clipboard
        if (emoji) {
            copyToClipboard(emoji);
        }
    });

    const formatWithIcon = (option) => {
        if (!option.id) return option.text; // skip the placeholder

        const icon = $(option.element).data('icon');  // get icon
        const color = $(option.element).data('color');  // get color

        return $(`<span><i class="fa \${icon}" style="color: ${color};"></i> ${option.text}</span>`);  // apply color to the icon
    };

    $('#statusOption').select2({
        width: '140px',
        templateResult: function (data) {
            if (!data.id) return data.text;

            const $el = $(data.element);
            const icon = $el.data('icon');
            const color = $el.data('color');
            const text = $el.text();

            return $('<span><i class="fa ' + icon + '"></i>' + text + '</span>');
            // return $('<span><i class="fa ' + icon + '" style="color:' + color + '; margin-right:5px;"></i>' + text + '</span>');
        },
        templateSelection: function (data) {
            if (!data.id) return data.text;
            const $el = $(data.element);
            const icon = $el.data('icon');
            const color = $el.data('color');
            const text = $el.text();

            return $('<span><i class="fa ' + icon + '"></i>' + text + '</span>');
            // return $('<span><i class="fa ' + icon + '" style="color:' + color + '; margin-right:5px;"></i>' + text + '</span>');
        },
        escapeMarkup: m => m,
        dropdownCssClass: 'status-dropdown'
    });

    // Trigger on option change
    $('#statusOption').on('change', function () {
        var selectedOption = $(this).find('option:selected'); // Get the selected option
        var newQuery = selectedOption.data('url'); // Get the query string from data-url

        if (newQuery) {
            // Get current URL
            var currentUrl = window.location.href.split('?')[0]; // Remove any existing query params
            window.location.href = currentUrl + newQuery; // Redirect to the same page with new query parameters
        }
    });

    $('input[type="checkbox"][name="include"]').on('click', function () {
        var url = $(this).data('url');
        if ($(this).is(':checked')) {
            url += "&include=all"
        }
        window.location.href = url
    });

    $('#searchBtn').on("click", function () {
        // if no text, clear url to make search=''
        // Get the current URL
        var searchText = $('#searchInput').val().trim();

        // 用 searchText 來判定 cookie value 是什麼
        createCookie(searchText, getCookieValue(searchText));

        const url = new URL(window.location.href);
        const params = url.searchParams;

        params.set('search', searchText);
        url.search = params.toString();
        window.location.href = url.toString();
    });

    $("#jira_search").on("click", function () {
        let searchInputText = $('#searchInput').val().trim();
        // Regex: letters (or any word chars) + "-" + digits
        const pattern = /^[A-Z]+-\d+$/i;
        if (pattern.test(searchInputText)) {
            window.open('https://viatorinc.atlassian.net/browse/' + searchInputText, '_blank')
        } else {
            window.open('https://viatorinc.atlassian.net/jira/software/c/projects/APPSUP/boards/89', '_blank')
        }
    });

    $("#add_new_note").on("click", function () {
        $("#addNewNoteForm").show();
    });

    $("#saveNote").on("click", function (e) {
        e.preventDefault();

        if ($(this).data("clicked")) {
            return;
        }

        $(this).data("clicked", true);
        $(this).prop('disabled', true);

        if ($('#note_id').val().trim() === '') {
            $('#note_id').val("NOTE_ONLY");
        }

        // save the Jira history and search / open Jira
        if (!(($('#note_id').val().trim() !== '') && ($('#note_id').val().trim() !== ''))) {
            return;
        }

        var noteId = $('#note_id').val();
        var noteText = $('#note_text').val();
        var noteStatus = $('input[name="note_status"]:checked').val();
        if (noteStatus == undefined) {
            noteStatus = '';
        }
        addNewNote(noteId, noteText, noteStatus)
    });


    $('#emojiSelect').select2({
        placeholder: "Select emoji",
        allowClear: true, // optional, adds 'x' to clear selection
        width: '200px',
        templateResult: function (data) {
            if (!data.id) return data.text || ' ';
            const $el = $(data.element);
            const display = $el.data('label') || data.text;
            const fontSize = $el.data('type') === 'tag' ? '12px' : '20px';
            return $('<span style="display:inline-block; width:24px; text-align:center; font-size:' + fontSize + ';">' + display + '</span>');
        },
        templateSelection: function (data) {
            if (!data.id) return data.text || ' ';
            const $el = $(data.element);
            const display = $el.data('label') || data.text;
            const fontSize = $el.data('type') === 'tag' ? '16px' : '20px';
            return $('<span style="display:inline-block; width:24px; text-align:center; font-size:' + fontSize + ';">' + display + '</span>');
        },
        escapeMarkup: m => m,
        dropdownCssClass: 'emoji-grid'
    });


    $(document).off("click", ".toggle-ticket").on("click", ".toggle-ticket", function (e) {
        e.stopPropagation();  // prevent bubbling
        e.preventDefault();

        let $icon = $(this);
        let $tr = $icon.closest("tr"); // main parent row
        let ticketId = getRegexValue(/group-([^\s]+)/, $tr.attr('class')); // 'APPSUP-7730'

        let clickedFontAwesome = $icon.hasClass("fa-plus") ? "fa-plus" : "fa-minus";
        if (clickedFontAwesome == "fa-plus") {
            $tr.remove();
            $(".group-" + ticketId).each(function () {
                $(this).show();
            });

        } else {
            let $clone = null;
            $(".group-" + ticketId).each(function () {
                if ($clone == null) {
                    $clone = $(this).clone(); // call clone()
                }
                $(this).hide();
            });

            $clone.addClass("clone-" + ticketId);       // add clone class with ticketId
            $clone.find("i.fa-minus").removeClass("fa-minus").addClass("fa-plus");

            // remove all <td> that have rowspan attribute
            $clone.find('td[rowspan]').removeAttr('rowspan');

            // change the editable note to be one line only
            let $noteCell = $clone.find(".note").first();
            let trimmedFirstLine = $noteCell.text().split("\n")[0].substring(0, 80);
            // add $noteCell back to $clone
            $clone.find(".note").first().text(trimmedFirstLine + " ....").removeClass("editable");

            $tr.before($clone);                // insert before current row
        }
    });

    // loadPhpCommonGitInfo("staging");
    // loadPhpCommonGitInfo("prod");

    // // refresh php-common head by branch
    // $("span[id^='php-common_']").on("click", function() {
    //     let branch = this.id.replace("php-common_", "");
    //     loadPhpCommonGitInfo(branch);
    // });

    // load dropdown kebab menu (DO NOT DELETE)
    bindKebabHover($('.dropdown.kebab'));

    $('.popover-link').each(function () {
        const $link = $(this);
        let hideTimeout;

        // ✅ 這裡就是模組 Popover
        const popover = new Popover(this, {
            trigger: 'manual',
            sanitize: false,
            customClass: 'my-popover'
        });

        $link.on('mouseenter', () => {
            clearTimeout(hideTimeout);
            popover.show();
        });

        $link.on('mouseleave', () => {
            hideTimeout = setTimeout(() => popover.hide(), 200);
        });

        this.addEventListener('shown.bs.popover', () => {
            const popoverEl = popover._getTipElement(); // 注意！模組版本有時候是 _getTipElement()
            popoverEl.addEventListener('mouseenter', () => clearTimeout(hideTimeout));
            popoverEl.addEventListener('mouseleave', () => {
                hideTimeout = setTimeout(() => popover.hide(), 200);
            });
        });
    });


    // Unbind first (safe way): to avoid multiple bindings (eg. show 2 times)
    $("a[href*='localhost:']").off("click.localhost").on("click.localhost", function (e) {
        if (!confirm("Does local is up and running?? Click OK to continue?")) {
            e.preventDefault();
            return;
        }
    });

    // NOTE: 這個是將來要弄 close all tab 的功能
    // $("a").on("click", function(e) {
    //     debugger;
    //     // if ($(this).hasClass("nav-item")) {
    //     if ($(this).hasClass("nav-item")) {
    //         openNewTab($(this).attr("href"));
    //         return false;
    //     }
    //
    //     if ($(this).attr("id") == "pageRefresh") {
    //         location.reload();
    //     }
    //
    //     // if ($(this).attr("href").indexOf("localhost") >= 0) {
    //     //     if( ! confirm("Does local is up and running?? Click OK to continue?") ) {
    //     //         e.preventDefault();
    //     //         return;
    //     //     }
    //     // }
    //
    //     var isRequired = isLinkRequireToCheckMissingValue(this);
    //     // 如果這個 a tag 裡頭有 "source", 那就得經過以下這段
    //     if (typeof $(this).attr("source") !== "undefined" && typeof $(this).attr("href") == "undefined") {
    //         var sourceUrl = getAttributeValue(this, "source");
    //         var elementHaveMissingValues = getElementHaveMissingValues(sourceUrl);
    //
    //         if (elementHaveMissingValues.length > 0 && isRequired) {
    //             alert("Missing element '" + elementHaveMissingValues.join(", ") + "' value.");
    //             return false;
    //         }
    //     }
    //
    //     if ($(this).attr("href") != "#") {
    //         var elementHaveMissingValues = getElementHaveMissingValues($(this).attr("href"))
    //         if (elementHaveMissingValues.length > 0 && isRequired) {
    //             alert("Missing element '" + elementHaveMissingValues.join(", ") + "' value.");
    //             return false;
    //         }
    //
    //         // 把最後的 url 更新掉
    //         $(this).attr("href", forceToSpecialDomain($(this).attr("href")));
    //     }
    // });

    $("#closeTab").on("click", function () {
        closeAllNewOpenedWindows();
    });

    $("span[id^='env-']").on("click", function () {
        var environmentId = this.id.replace("env-", "");
        // reset all environment class to warning
        updateEnvironmentClass(environmentId);
        var update = (environmentId == $("#current-environment")) ? false : true;
        updateHostIfNeed(environmentId, update);

        // update all kibana url per environment per project
        updateKibanaLinkByTitle(environmentId);
    });

    // Make note text to more nicer :: BEGIN
    const raw = document.getElementById('raw');
    const preview = document.getElementById('preview');

    if (raw && preview) {
        function updatePreview() {
            preview.innerHTML = raw.value;
        }
        updatePreview();
        raw.addEventListener('input', updatePreview);
    }







    // Keep track of the current clicked item
    let currentClickedItem = null;
    let tooltipTimeout = null; // To track the timeout for hiding the tooltip

    // Event listener for when the user hovers over the "Copy" text
    // document.querySelector('.hover-shortcut-text').addEventListener('mouseenter', function() {
    //     // Show the tooltip when hovering over the "Copy" text
    //     const tooltip = document.querySelector('.hover-shortcut-tooltip');
    //     tooltip.style.display = 'block'; // Adjust to your preferred way of showing the popup

    //     // Ensure copied icon is removed when the tooltip reappears
    //     document.querySelectorAll('.copied-icon').forEach(icon => {
    //         icon.remove(); // Remove the icon from any <li> element
    //     });

    //     // Clear the timeout if tooltip is manually hovered before it disappears
    //     if (tooltipTimeout) {
    //         clearTimeout(tooltipTimeout);
    //     }
    // });


    // 等待 DOM 加載完成後再執行
    document.addEventListener('DOMContentLoaded', function () {
        const hoverText = document.querySelector('.hover-shortcut-text');
        const tooltip = document.querySelector('.hover-shortcut-tooltip');
        let tooltipTimeout;  // 記錄 tooltip 隱藏的計時器

        // 確保元素存在再進行操作
        if (hoverText && tooltip) {
            // 進入 hover 時顯示 tooltip
            hoverText.addEventListener('mouseenter', function () {
                tooltip.style.display = 'block';  // 顯示 tooltip

                // 清除任何先前的 copied icon
                document.querySelectorAll('.copied-icon').forEach(icon => {
                    icon.remove();
                });

                // 如果 tooltip 被手動 hover，清除隱藏計時器
                if (tooltipTimeout) {
                    clearTimeout(tooltipTimeout);
                }
            });

            // 離開 hover 時設定計時器延遲隱藏 tooltip
            hoverText.addEventListener('mouseleave', function () {
                tooltipTimeout = setTimeout(function () {
                    tooltip.style.display = 'none'; // 2 秒後隱藏 tooltip
                }, 2000);
            });
        } else {
            console.log('元素未找到: .hover-shortcut-text 或 .hover-shortcut-tooltip');
        }

        // 處理 tooltip 內的 li 點擊事件，進行複製操作
        document.querySelectorAll('.hover-shortcut-tooltip li[data-copy]').forEach(item => {
            item.addEventListener('click', function () {
                const data = item.getAttribute('data-copy');

                // 進行複製到剪貼簿的操作
                copyToClipboard(data);

                // 移除所有 li 中的 "copied" 標誌
                document.querySelectorAll('li[data-copy]').forEach(li => {
                    const icon = li.querySelector('.copied-icon');
                    if (icon) {
                        icon.remove();
                    }
                });

                // 在點擊的 li 上加上複製成功的 icon
                const copiedIcon = document.createElement('i');
                copiedIcon.classList.add('fas', 'fa-check', 'copied-icon');
                copiedIcon.style.marginLeft = '8px';  // 增加間距
                item.appendChild(copiedIcon);
            });
        });

        // 處理 tooltip 顯示和隱藏的邏輯
        const shortcutText = document.querySelector('.hover-shortcut-text');
        let timeout;

        if (shortcutText) {
            shortcutText.addEventListener('mouseenter', () => {
                tooltip.style.visibility = 'visible';
                tooltip.style.opacity = '1';  // 顯示 tooltip
                clearTimeout(timeout);  // 清除任何先前的隱藏計時器
            });

            shortcutText.addEventListener('mouseleave', () => {
                timeout = setTimeout(() => {
                    tooltip.style.visibility = 'hidden';
                    tooltip.style.opacity = '0';  // 隱藏 tooltip，1 秒後
                }, 1000);
            });
        } else {
            console.log('元素未找到: .hover-shortcut-text');
        }
    });

    // 複製到剪貼簿的輔助函數
    function copyToClipboard(text) {
        const tempTextArea = document.createElement('textarea');
        tempTextArea.value = text;
        document.body.appendChild(tempTextArea);
        tempTextArea.select();
        document.execCommand('copy');
        document.body.removeChild(tempTextArea);
    }



    // ---------- HOVER ----------

    $(document).on("mouseenter", ".hover-stock-source-trigger", function () {
        const wrapper = $(this).closest(".hover-stock-source");
        const tooltip = wrapper.find(".hover-stock-source-tooltip");
        tooltip.css({ display: "block", visibility: "visible", opacity: 1 });
    });

    $(document).on("mouseleave", ".hover-stock-source", function () {
        const tooltip = $(this).find(".hover-stock-source-tooltip");
        setTimeout(() => {
            tooltip.css({ display: "none", visibility: "hidden", opacity: 0 });
        }, 3000);
    });

    $(document).on("mouseenter", ".hover-stock-highest-trigger", function () {
        const wrapper = $(this).closest(".hover-stock-highest");
        const tooltip = wrapper.find(".hover-stock-highest-tooltip");
        tooltip.css({ display: "block", visibility: "visible", opacity: 1 });
    });

    $(document).on("mouseleave", ".hover-stock-highest", function () {
        const tooltip = $(this).find(".hover-stock-highest-tooltip");
        setTimeout(() => {
            tooltip.css({ display: "none", visibility: "hidden", opacity: 0 });
        }, 1500);
    });

    // ---------- CLICK ----------

    $(document).on("click", ".hover-stock-source-tooltip li", function (e) {
        e.stopImmediatePropagation();       // <--- prevent duplicate handler
        const source = $(this).attr("data-source");
        console.log("JQUERY CLICK");
        refreshStockSource(source);

        $(this).siblings().removeClass("active");
        $(this).addClass("active");
    });



});

