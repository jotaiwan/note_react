// General functions



window.addNewNote = function (ticket, text, status) {
    const data = { ticket, note: text, status };
    $.ajax({
        url: "/api/note/save",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (response) {
            let currentUrl = window.location.href;
            removeDiv("save_error");        // if need
            const message = `
              <div class="alert alert-success" id="save_ok">
                Saved. Auto refresh in 3 sec (<a href="${currentUrl}">manual refresh</a>)
              </div>
            `;
            addMessageBeforeField("note_id", "save_ok", message);

            // 3秒後關閉 & 可選擇刷新
            setTimeout(function () {
                $("#save_ok").fadeOut(function () {
                    location.reload();  // 自動刷新頁面
                });
            }, 3000);
        },
        error: function (xhr, status, error) {
            removeDiv("save_ok");        // if need
            addMessageBeforeField("note_id", "save_error", '<div class="alert alert-success" id="save_error">' +
                error + '</div>');
        }
    });
}

window.getRegexValue = function (pattern, text) {
    let match = text.match(pattern); // regex: capture after "group-"
    if (match) {
        return match[1];
    }
    return "";
}


window.removeDiv = function (divId) {
    divId = "#" + divId;
    if ($(divId).length) {
        $(divId).remove();
    }
}

window.addMessageBeforeField = function (beforeField, messageDivId, message) {
    removeDiv(messageDivId);
    const field = $("#" + beforeField);
    field.before(message);
}


window.copyToClipboard = function (data) {
    var target = document.createElement('textarea');
    target.value = data;
    target.id = "_hiddenCopyText_";
    target.setAttribute('readonly', '');
    target.style.position = 'absolute';
    target.style.left = '-9999px';
    document.body.appendChild(target);
    target.select();
    document.execCommand("copy", false, null);
    target.remove();
}

window.goToLink = function (e, key = '') {
    if ($.trim(key).length > 0) {
        copyToClipboard(key);
    }
    // get link from title
    var link = e.title;
    window.open(link, "_blank");
}

window.sortTable = function (order) {
    const rowsArray = Array.from(rows);

    // Group rows by ticket, based on rowspan
    const groupedRows = {};

    rowsArray.forEach(row => {
        const ticket = row.querySelector('td').innerText.trim();
        if (!groupedRows[ticket]) {
            groupedRows[ticket] = [];
        }
        groupedRows[ticket].push(row);
    });

    // Sort the group based on ticket name
    const sortedGroups = Object.keys(groupedRows).sort((a, b) => {
        if (order === 'asc') {
            return a.localeCompare(b);
        } else {
            return b.localeCompare(a);
        }
    });

    // Clear table body
    const tbody = table.querySelector('tbody');
    tbody.innerHTML = '';

    // Append rows back in sorted order
    sortedGroups.forEach(ticket => {
        const group = groupedRows[ticket];
        const firstRow = group[0];
        const rowspan = group.length;

        // Set rowspan on the first row
        const ticketCell = firstRow.querySelector('td');
        ticketCell.rowSpan = rowspan;

        // Append all rows in the group
        group.forEach(row => tbody.appendChild(row));
    });
}

window.emojiToHtmlEntity = function (emoji) {
    // return Array.from(emoji).map(char => `&#\${char.codePointAt(0)};`).join('');

    // If it's already a tag like {code}, return it as-is
    if (emoji.startsWith('{')) return emoji;

    // Otherwise convert emoji string to HTML entities
    return Array.from(emoji).map(char => `&#\${char.codePointAt(0)};`).join('');
}


window.updatePreview = function () {
    let text = raw.value;

    // Replace {code} blocks
    text = text.replace(/\{code\}([\s\S]*?)\{\/code\}/g, (_, content) => {
        return `<div class="code-block">\${escapeHtml(content.trim())}</div>`;
    });

    // Replace {warning-note} blocks
    text = text.replace(/\{warning-note\}([\s\S]*?)\{\/warning-note\}/g, (_, content) => {
        return `<div class="warning-note">\${escapeHtml(content.trim())}</div>`;
    });

    preview.innerHTML = text;
}

window.escapeHtml = function (str) {
    return str.replace(/[&<>"']/g, function (m) {
        return ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[m];
    });
}

window.checkStringContainsMoreThan3SpaceCharacters = function (str) {
    // Check if the string contains more than 3 consecutive spaces
    return /\s{3,}/.test(str);
}

window.isExcludedCookieName = function (cookieName) {
    var excluded = ["PHPSESSID", "_ga=", "GA1"];
    this.cookieName = cookieName;
    var found = false;
    $.each(excluded, function (i, excl) {
        if (cookieName.indexOf(excl) !== -1) {
            found = true;
            return;
        }
    });
    return found;
}

window.readAllCookies = function () {
    var localCookies = document.cookie.split(';');
    // exclude PHPSESSID
    var _cookies = [];
    if ($.trim(localCookies).length > 0) {
        $.each(localCookies, function (idx, localCookie) {
            if (!isExcludedCookieName(localCookie)) {
                _cookies.push($.trim(localCookie));
            }
        });
        return _cookies.reverse();
    }
    return _cookies;
}

window.isStringContainBrowserAnalytics = function (_str) {
    var browserAnalytics = ["_ga_"];
    return browserAnalytics.some(sub => _str.includes(sub));
}

window.updateSearchFromCookie = function () {
    var _cookies = readAllCookies();
    var searchText = "";
    var maximum = 20;
    if (!$.isEmptyObject(_cookies)) {
        let count = 0;
        $.each(_cookies, function (idx, _cookie) {
            if (count > maximum) {
                return;
            }
            var map = _cookie.split('=');
            if (!isStringContainBrowserAnalytics(map[0])) {
                const $parsed = $(
                    $.parseHTML(decodeURIComponent(map[1]))
                );
                const spanHtml = $parsed.filter('span').html() || '';
                const htmlWithBreaks = spanHtml.replace(/<br\s*\/?>/gi, '\\n');
                const text = $('<div>').html(htmlWithBreaks).text();

                if (!checkStringContainsMoreThan3SpaceCharacters(text) && !(text.length > 20)) {
                    searchText += "<strong>&nbsp;&middot;&nbsp;</strong>";
                    searchText += decodeURIComponent(map[1]);
                    count++;
                }

                // add search text with remove cookie
                searchText += "<span class=\"clear-button session-delete-small ml-1\" style='cursor: pointer;' " +
                    "onmouseover=\"this.style.color='blue';\"" + "  onmouseout=\"this.style.color='';\" " +
                    "onclick='deleteCookie(\"" + map[0] + "\")'> ✗ </span>";
            }
        });

        $("#search-from-cookie").html(searchText);
    } else {
        $("#search-from-cookie").html('');
    }
}

window.createCookie = function (name, value, days) {
    // remove requested cookie before create
    deleteCookie(name);

    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    console.log("New cookie ('" + name + "') is added: " + document.cookie);

}

window.deleteCookie = function (name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    updateSearchFromCookie();
}

window.getCookieValue = function (searchText) {
    // Get the current URL
    const jiraPattern = /^[A-Z]+-\d+$/;

    if (jiraPattern.test(searchText)) {
        // If searchText is a valid JIRA ticket, return it directly
        return addLinkToText(searchText, getJiraLink(searchText));
    }
    return addLinkToText(searchText, updateSearchText(searchText));
}

window.addLinkToText = function (text, link) {
    if (text && link) {
        return `<a href="${link}" target="_blank">${text}</a>`;
    }
    return text;
}

window.getJiraLink = function (searchText) {
    // Assuming the JIRA link is constructed like this
    const baseJiraUrl = "https://viatorinc.atlassian.net/browse/searchText";
    return baseJiraUrl + encodeURIComponent(searchText);
}

window.updateSearchText = function (newSearchText) {
    const url = new URL(window.location.href);
    url.searchParams.set('search', newSearchText);
    return url.toString();
}

window.updateEnvironmentClass = function (environmentId) {
    $.each($("span[id^='env-']"), function () {
        $(this).removeClass("badge-danger");
    });
    $("#env-" + environmentId).addClass("badge-danger");
}

window.bindKebabHover = function () {
    $('.dropdown').each(function () {
        const $dropdown = $(this);
        const $toggle = $dropdown.find('.dropdown-toggle');
        let hideTimeout;

        function showDropdown() {
            clearTimeout(hideTimeout);
            $dropdown.addClass('show');
            $dropdown.find('.dropdown-menu').addClass('show');
        }

        function hideDropdown() {
            $dropdown.removeClass('show');
            $dropdown.find('.dropdown-menu').removeClass('show');
        }

        // Show on hover
        $toggle.on('mouseenter', function () {
            showDropdown();

            setTimeout(() => {
                const popoverTip = document.querySelector('.popover');
                if (!popoverTip) return;

                // Keep dropdown open while inside popover
                $(popoverTip).on('mouseenter', function () {
                    clearTimeout(hideTimeout);
                }).on('mouseleave', function (e) {
                    if (!$dropdown[0].contains(e.relatedTarget)) {
                        hideTimeout = setTimeout(hideDropdown, 300);
                    }
                });
            }, 1000);
        });

        // Handle leaving dropdown
        $dropdown.on('mouseleave', function (e) {
            const popoverTip = document.querySelector('.popover');
            if (popoverTip && popoverTip.contains(e.relatedTarget)) {
                clearTimeout(hideTimeout); // cancel hide if moving into popover
                return;
            }

            hideTimeout = setTimeout(hideDropdown, 1000);
        });

        // Also cancel hide if coming back into dropdown quickly
        $dropdown.on('mouseenter', function () {
            clearTimeout(hideTimeout);
        });
    });
}

/** ****************************************************************************************************
 // git change : 開始 #3
 ***************************************************************************************************** */
// call gitlab and update the php-common head
// function loadPhpCommonGitInfo(branch) {
//     $.getJSON("/api/gitlab/branch-head?branch=" + branch, function (data) {
//         let headInfo = data["command_head"]
//         var branch = headInfo["Branch"];
//         var head = headInfo["Head"];
//         $("#php-common_" + branch).text(head);
//     }).fail(function () {
//         console.error("Could not load commit info.");
//     });
// }
/** ****************************************************************************************************
 // git change : 結束 #3
 ***************************************************************************************************** */


/** ****************************************************************************************************
 // 這裡都是有關 update link per environment 的 function (開始！！！！！）
 ***************************************************************************************************** */

window.updateHostIfNeed = function (environmentId, update) {
    // Loop through all hrefs, get current href host, find and replace to env host
    // var currentEnvironment = $("#current-environment").text();
    if (update) {
        $("#current-environment").text(environmentId);
        // update all links
        $.each($('a'), function () {
            var href = $(this).attr("href");
            if ($.trim(href) != "" && $.trim(href) !== "#") {

                var isScapiRequest = href.indexOf("/externalapi/") >= 0;
                // let's update
                var existing = getHostName(href);
                if ($.trim(existing) != "") {
                    var project = hostMapToProject(existing);

                    addEnvironmentNextToHomeTextIfNeed(this, project, environmentId);
                    // TODO: 注意，如果是 apache.. 應該重新 call..
                    if ($(this).text() == "apache" && environmentId == "local") {
                        // this must be hollywood-repos.. use apache link instead
                        var _new = getHostByProject(project, "apache", isScapiRequest);
                    } else {
                        // 正常！！！
                        var _new = getHostByProject(project, environmentId, isScapiRequest);
                    }

                    if ($.trim(_new) != "") {
                        // replace existing to new
                        var isForceProtocolToHttps = forcedProtocolToHttps(_new, project);
                        var _newHref = $.trim(href.replace(existing, _new));
                        if (environmentId == "local" && !isForceProtocolToHttps) {
                            _newHref = updateHostProtocol(_newHref, environmentId);
                        }
                        $(this).attr("href", _newHref);
                    }
                }
            }
        });
    }
}

window.updateHostProtocol = function (href, environmentId) {
    if (environmentId == "local") {
        href = href.replace("https", "http");
    } else if (href.indexOf("https") == -1) {
        href = href.replace("http", "https");
    }
    return href;
}

window.addEnvironmentNextToHomeTextIfNeed = function (element, project, environmentId) {
    if (requireChangeHomeUrlText(project) && ($(element).text().indexOf("Home") >= 0)) {
        $(element).html("<span class='badge ml-1'>Home (" + environmentId.toUpperCase() + ")</span>");
    }
}

window.getHostByProject = function (project, environmentId, isScapiRequest) {
    var projectMapToHosts = {
        "nsp": {
            "prod": "supplier.viator.com",
            "rc": "supplier.live.rc.viator.com",
            "int": "supplier.live.int.viator.com",
            "zelda": "supplier.live.zelda.viator.com",
            "local": "localhost:8435"
        },
        "app-support": {
            "prod": "app-support.prod.viatorsystems.com",
            "rc": "app-support.rc.viatorsystems.com",
            "int": "app-support.int.viatorsystems.com",
            "zelda": "app-support.zelda.viatorsystems.com",
            "apache": "app-support.local.viatorsystems.com",
            "local": "localhost:8178"
        },
        "adhoc-reports": {
            "prod": "adhoc-reports.prod.viatorsystems.com",
            "rc": "adhoc-reports.rc.viatorsystems.com",
            "int": "adhoc-reports.int.viatorsystems.com",
            "zelda": "adhoc-reports.zelda.viatorsystems.com",
            "apache": "adhoc-reports.local.viatorsystems.com",
            "local": "localhost:8177"
        },
        "competitive-analysis": {
            "prod": "competitive-analysis.prod.viatorsystems.com",
            "rc": "competitive-analysis.rc.viatorsystems.com",
            "int": "competitive-analysis.int.viator.com",
            "zelda": "competitive-analysis.zelda.viator.com",
            "apache": "competitive-analysis.local.viator.com",
            "local": "localhost:8215"
        },
        "staff": {
            "prod": "staff.viator.com",
            "rc": "staff.rc.viator.com",
            "int": "staff.int.viator.com",
            "zelda": "staff.zelda.viator.com",
            "apache": "staff.local.viator.com",
            "local": "localhost:8179"
        },
        "stingray": {
            "prod": "stingray.viator.com",
            "rc": "stingray.rc.viator.com",
            "int": "stingray.int.viator.com",
            "zelda": "stingray.zelda.viator.com",
            "apache": "stingray.local.viator.com",
            "local": "localhost:8176"
        },
        "demandproduct": {
            "prod": "demandproduct.prod.vkp.viatorsystems.com",
            "rc": "demandproduct.rc.vkp.viatorsystems.com",
            "int": "demandproduct.int.vkp.viatorsystems.com",
            "zelda": "demandproduct.zelda.vkp.viatorsystems.com",
            "local": "localhost:8455"
        },
        "productservice": {
            "prod": "productservice.prod.vkp.viatorsystems.com",
            "rc": "productservice.rc.vkp.viatorsystems.com",
            "int": "productservice.int.vkp.viatorsystems.com",
            "zelda": "productservice.zelda.vkp.viatorsystems.com",
            "local": "localhost:8441"
        },
        "orion": {
            "prod": "viator.com",
            "rc": "shop.live.rc.viator.com",
            "int": "shop.live.int.viator.com",
            "zelda": "shop.live.zelda.viator.com",
            "local": "shop.live.local.viator.com:8432"
        },
        "bookings-monitoring": {
            "prod": "bookings-monitoring.prod.viatorsystems.com/",
            "rc": "bookings-monitoring.rc.viatorsystems.com/",
            "int": "bookings-monitoring.int.viatorsystems.com/",
            "zelda": "bookings-monitoring.zelda.viatorsystems.com/",
            "local": "bookings-monitoring.local.viatorsystems.com/"
        },
        "directus": {
            "prod": "directus.prod.vkp.viatorsystems.com",
            "rc": "directus.rc.vkp.viatorsystems.com",
            "int": "directus.int.vkp.viatorsystems.com",
            "zelda": "directus.zelda.vkp.viatorsystems.com",
            "local": "directus.local.viatorsystems.com:8432"
        },
    }
    return (typeof projectMapToHosts[project] == "undefined")
        ? "" : getProjectHostIfScapiRequest(project, environmentId, projectMapToHosts[project][environmentId], isScapiRequest);
}

window.hostMapToProject = function (host) {
    // let's split dot '.' and pickup the first string
    var hostPrefix = host.split(".");
    var hostMapToProjects = {
        "supplier": "nsp",
        "app-support": "app-support",
        "adhoc-reports": "adhoc-reports",
        "competitive-analysis": "competitive-analysis",
        "staff": "staff",
        "stingray": "stingray",
        "localhost:8435": "nsp",
        "localhost:8445": "nsp",
        "localhost:8455": "demandproduct",
        "localhost:8441": "productservice",
        "localhost:8178": "app-support",
        "localhost:8177": "adhoc-reports",
        "localhost:8215": "competitive-analysis",
        "localhost:8179": "staff",
        "localhost:8176": "stingray",
        "shop": "orion",
        "viator": "orion",
        "demandproduct": "demandproduct",
        "productservice": "productservice",
        "bookings-monitoring": "bookings-monitoring",
        "directus": "directus"
    }
    if (hostPrefix.length == 0) {
        return "";
    }
    return (typeof hostMapToProjects[hostPrefix[0]] == "undefined") ? "" : hostMapToProjects[hostPrefix[0]];
}

window.requireChangeHomeUrlText = function (project) {
    return (project == "app-support" || project == "adhoc-reports" || project == "competitive-analysis" || project == "staff" || project == "stingray" || project == "bookings-monitoring");
}

window.getHostName = function (url) {
    var matches = url.match(/(http|https):\/\/(.[^/]+)/);
    if (matches != null && matches.length == 3) {
        return matches[2];
    }
    return "";
}

window.getProjectHostIfScapiRequest = function (project, environmentId, host, isScapiRequest) {
    if (environmentId == "local") {
        return (project == 'nsp' && isScapiRequest) ? "localhost:8445" : host;
    }
    return host;
}

window.forcedProtocolToHttps = function (host, project) {
    var protocols = ["localhost:8435", "shop.live.local.viator.com:8432", "localhost:8455", "localhost:8441"];
    return ($.inArray(host, protocols) >= 0) || (project == 'nsp') || (project == 'orion' || (project == 'demandproduct') || (project == 'productservice'));
}

window.updateKibanaLinkByTitle = function (environmentId) {
    this.environmentId = environmentId;
    $.each($("a[title^='kibana-']"), function (arg, link) {
        debugger;
        var project = link['title'].replace("kibana-", "")
        link["href"] = getKibanaLinkByProject(project, environmentId);
    });

    updateKibanaLinkFromMenu(environmentId);
}

window.getKibanaLinkByProject = function (project, environmentId) {
    console.log("Passed projectKey: " + project);
    project = project.replace(/_/g, '-');
    var project = project.replace(/(_link|-link)$/, '');
    var projectMapToHosts = {
        "app-support": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/goto/10382db0-b184-11ed-9db1-9d3bca3e8809",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/goto/154f9f70-0a82-11ee-95e2-5fea4b6b16dd",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/goto/f6e39730-0a86-11ee-95e2-5fea4b6b16dd",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/goto/f6e39730-0a86-11ee-95e2-5fea4b6b16dd"
        },
        "adhoc-reports": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/FEDPB",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/mmPJC",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/5oZV9",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/jvz6Q"
        },
        "competitive-analysis": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/040XE",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/8Oi79",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/es2Oj",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/es2Oj"
        },
        "staff": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/LrLwI",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/9cRJI",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/YTknh",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/YTknh"
        },
        "stingray": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/MLr91",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/oC9e7",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/ZTP7F",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/ZTP7F"
        },
        "nsp": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/goto/d48795c0-45eb-11ed-ab81-b9c82d63e6f1",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/goto/b5ac41b0-0a84-11ee-95e2-5fea4b6b16dd",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/goto/a8fdeba0-0a87-11ee-95e2-5fea4b6b16dd",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/goto/a8fdeba0-0a87-11ee-95e2-5fea4b6b16dd"
        },
        "nsp-scapi": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/goto/53eba790-45ea-11ed-ab81-b9c82d63e6f1",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/goto/7d0897a0-0a84-11ee-95e2-5fea4b6b16dd",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/goto/8fd92720-0a87-11ee-95e2-5fea4b6b16dd",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/goto/8fd92720-0a87-11ee-95e2-5fea4b6b16dd"
        },
        "partner-ldap-access": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/goto/3e07de20-b7b5-11ed-9e4a-4317b558e49c",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/goto/e7676d60-0a84-11ee-95e2-5fea4b6b16dd",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/goto/d726c8d0-0a87-11ee-95e2-5fea4b6b16dd",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/goto/d726c8d0-0a87-11ee-95e2-5fea4b6b16dd"
        },
        "staff": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/FL3vG",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/w2Y6m",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/5fuOM",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/goto/d726c8d0-0a87-11ee-95e2-5fea4b6b16dd"
        },
        "stingray": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/VYxQZ",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/FLL7a",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/xhNJ1",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/xhNJ1"
        },
        "competitive-analysis": {
            "prod": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/3DxNN",
            "rc": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/1JPWw",
            "int": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/CDufB",
            "local": "https://kibana.prod.viatorsystems.com/s/application-support/app/r/s/CDufB"
        }
    };
    console.log("project: " + project + ", and environment: " + environmentId);
    return projectMapToHosts[project][environmentId];
}

window.updateKibanaLinkFromMenu = function (environmentId) {
    this.environmentId = environmentId;
    $.each($("a[id^='menu-']"), function (arg, link) {
        debugger;
        var project = link['id'].replace("menu-", "")
        var newLink = getKibanaLinkByProject(project, environmentId);

        var kibanaLinkId = "#kibana-" + project;
        // var content = $(link).attr('data-content')
        var content = $('<textarea/>').html($(link).attr('data-content')).text();

        // Parse content into a DOM wrapper
        var $wrapper = $('<div>').html(content);

        var $kibanaLink = $wrapper.find("a[title='kibana-" + project + "']");
        if ($kibanaLink.length) {
            $kibanaLink.attr('href', newLink);

            // Update back — no need to re-encode; browser will do it
            $(link).attr('data-content', $wrapper.html());
        }
    });
}



/** ****************************************************************************************************
 // 這裡都是有關 update link per environment 的 function (結束！！！！！）
 ***************************************************************************************************** */



/**
 * 這個很重要，他可以 handle 一個 url argument 可以有兩種 data..
 * 譬如這個 url: https://adhoc-reports.prod.viatorsystems.com/reports/supplier_listing?supplier_or_location_id={current-supplier-code|current-geo-id}&format=TABLE
 * 他的 supplier_or_location_id 對應到兩個不同的 input : current-supplier-code and current-geo-id
 *
 * @param _url
 * @param required: 如果這是 true.. 只要有一個 arguments 對應到的 input 是空值，就把這 argument 回傳並通知錯誤
 * */
// NOTE: 這個是將來要弄 close all tab 的功能
// function getElementHaveMissingValues(_url, required) {
//     // find element like {current-geo-id|current-product-code}
//     var elementOrConditions = getElementOrConditions(_url);
//     // find all elements from url
//     var matches = _url.match(/current-[(\w)-]+/g);
//
//     // find elements exclude or-condition, eg {current-geo-id}
//     var elementAndConditions = $(matches).not(elementOrConditions).get()
//     var missingElementValues = [];
//
//     // find missing element value form or-condition list
//     if (missingElementValues.length == 0 && elementOrConditions != null && elementOrConditions.length > 0) {
//         $.each(elementOrConditions, function(idx, match) {
//             if (getElementById(match).length > 0) {
//                 return missingElementValues;
//             } else {
//                 missingElementValues.push(match);
//             }
//         });
//     }
//     if (required && missingElementValues.length > 0) {
//         return missingElementValue;
//     }
//
//     // continue finding: missing element value form or-condition list
//     var doesAtLeastOneAndElementHaveBeenFound = false;
//     if (missingElementValues.length == 0 && elementAndConditions != null && elementAndConditions.length > 0) {
//         $.each(elementAndConditions, function(idx, match) {
//             if (getElementById(match).length == 0) {
//                 missingElementValues.push(match);
//             } else {
//                 doesAtLeastOneAndElementHaveBeenFound = true;
//             }
//         });
//     }
//
//     // 如果 required 是 true.. 得限制，否則其中一個有就行了
//     if (required || !doesAtLeastOneAndElementHaveBeenFound) {
//         return missingElementValues;
//     }
//     return [];
// }

// NOTE: 這個是將來要弄 close all tab 的功能
// function openNewTab(url) {
//     // 把 url 裡面 missing value 給它轉成 empty 字串，再發出去
//     var _url = url;
//     var missingValues = url = getElementHaveMissingValues(url);
//     if ($.trim(missingValues).length > 0) {
//         $.each(missingValues, function() {
//             _url = _url.replace("{" + missingValues + "}", "");
//         });
//     }
//
//     // 這裡是把 所有要 open 的 window 存起來，以方便以後 一次性關掉，（除 Bigquery 外）
//     if ((_url.indexOf("console.cloud.google.com") == -1) && (_url.indexOf("rapidView=464") == -1)) {
//         var tabCount = $("#tabCounter").text().replace("(", "").replace(")", "");
//         tabCount = ($.trim(tabCount).length == 0) ? 0 : parseInt(tabCount);
//         tabCount += 1;
//         $("#tabCounter").text("(" + tabCount + ")");
//         // $("#closeTab").removeClass("even-larger-badge");
//         windows.push(window.open(_url, '_blank'));
//     } else {
//         // 直接 open
//         window.open(_url, '_blank');
//     }
// }

// pick up element like this: {current-geo-id|current-product-code}
// NOTE: 這個是將來要弄 close all tab 的功能
// function getElementOrConditions(_url) {
//     var matches = _url.match(/current-[(\w)-]+/g);
//     var elementOrConditions = [];
//     $.each(matches, function(idx, match) {
//         if (_url.indexOf("|" + match) > 0 || _url.indexOf(match + "|") > 0) {
//             elementOrConditions.push(match);
//         }
//     });
//     return elementOrConditions;
// }

// NOTE: 這個是將來要弄 close all tab 的功能
// function forceToSpecialDomain(url) {
//     // 這是給一些沒辦法 call production, 必須透過 app-support curl 來搞的
//     var forceToSpecialSites = {
//         "https://demandproduct.prod.vkp.viatorsystems.com/product/" : "https://app-support.prod.viatorsystems.com/support-home/searchProduct.php?productCode="
//     };
//     this.targetUrl = url;
//     $.each(forceToSpecialSites, function(k, v) {
//         if (targetUrl.indexOf(k) >= 0) {
//             targetUrl = targetUrl.replace(k, v);
//             return;
//         }
//     });
//     return this.targetUrl;
// }

window.closeAllNewOpenedWindows = function () {
    for (var i = 0; i < windows.length; i++) {
        if (windows[i] == null) {
            continue;
        }
        windows[i].close()
    }
    $("#tabCounter").text("");
}

window.getAttributeValue = function (event, attribute) {
    return (typeof $(event).attr(attribute) == "undefined") ? "" : $(event).attr(attribute);
}

window.isLinkRequireToCheckMissingValue = function (event) {
    return getAttributeValue(event, "aria-required") == "" ? false : true;
}

