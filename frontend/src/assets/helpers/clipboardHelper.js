// frontend/src/assets/helpers/clipboardHelper.js
export const copyToClipboard = (text) => {
    if (!text) return Promise.reject("No text to copy");

    if (navigator.clipboard && navigator.clipboard.writeText) {
        return navigator.clipboard.writeText(text); // 标准方式
    } else {
        // 兼容旧浏览器
        return new Promise((resolve, reject) => {
            const textarea = document.createElement("textarea");
            textarea.value = text;
            textarea.style.position = "fixed";  // 避免滚动
            textarea.style.top = "-9999px";
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();

            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textarea);
                if (successful) resolve();
                else reject("Fallback: copy command failed");
            } catch (err) {
                document.body.removeChild(textarea);
                reject(err);
            }
        });
    }
};
