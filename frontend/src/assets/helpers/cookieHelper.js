// helper functions you need (replace your old jQuery helpers)
function readAllCookies() {
    // Returns an array of cookies like ["name=value", ...]
    return document.cookie.split(';').map(c => c.trim()).filter(Boolean);
}

function isStringContainBrowserAnalytics(str) {
    // your logic to ignore analytics cookies
    return str.toLowerCase().includes('analytics');
}

function checkStringContainsMoreThan3SpaceCharacters(str) {
    return (str.match(/\s/g) || []).length > 3;
}

function deleteCookie(name) {
    document.cookie = name + '=; Max-Age=0; path=/';
}
// frontend/src/assets/helpers/cookieHelper.js

export function updateSearchFromCookie() {
    console.log("updateSearchFromCookie called");

    const _cookies = readAllCookies();
    let searchText = '';
    const maximum = 20;
    let count = 0;

    if (_cookies.length > 0) {
        for (const cookie of _cookies) {
            if (count >= maximum) break;

            const [key, value] = cookie.split('=');
            if (!isStringContainBrowserAnalytics(key)) {
                const decoded = decodeURIComponent(value);

                // convert HTML to text safely
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = decoded.replace(/<br\s*\/?>/gi, '\n');
                const text = tempDiv.textContent || tempDiv.innerText || '';

                if (!checkStringContainsMoreThan3SpaceCharacters(text) && text.length <= 20) {
                    searchText += `<strong>&nbsp;&middot;&nbsp;</strong>`;
                    searchText += decoded;

                    searchText += `<span class="clear-button session-delete-small ml-1" style="cursor:pointer;" 
            onmouseover="this.style.color='blue';" 
            onmouseout="this.style.color='';" 
            onclick='(${deleteCookie.toString()})("${key}")'> ✗ </span>`;

                    count++;
                }
            }
        }
    }

    // set HTML
    const container = document.querySelector('#search-from-cookie');
    if (container) {
        container.innerHTML = searchText;
    }
}
