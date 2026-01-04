// decode HTML entities (emoji, special chars)
export function decodeHtmlEntities(text = "") {
    const div = document.createElement("div");
    div.innerHTML = text;
    return div.textContent || "";
}

/**
 * 解析动态标签，比如 {code}, {blockquote}, {breakthrough}
 * 返回数组：{ type, content }
 */
export function parseDynamicTags(text = "", allowedTags = ["code", "blockquote", "breakthrough"]) {
    const result = [];
    let lastIndex = 0;

    // 构造正则，匹配 {tag}...{/tag}
    const tagsPattern = allowedTags.join("|"); // code|blockquote|breakthrough
    const regex = new RegExp(`\\{(${tagsPattern})\\}([\\s\\S]*?)\\{\\/\\1\\}`, "g");

    let match;
    while ((match = regex.exec(text)) !== null) {
        if (match.index > lastIndex) {
            result.push({ type: "text", content: text.slice(lastIndex, match.index) });
        }
        result.push({ type: match[1], content: match[2] });
        lastIndex = regex.lastIndex;
    }

    if (lastIndex < text.length) {
        result.push({ type: "text", content: text.slice(lastIndex) });
    }

    return result;
}
