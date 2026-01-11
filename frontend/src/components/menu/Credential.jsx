import { useEffect, useRef } from "react";
import { getCredential } from "../../services/credential";
import { copyToClipboard } from "../../assets/helpers/clipboardHelper";

export default function Credential() {

    // 用 useRef 保存 secret
    // ✅ 存在内存里
    // ❌ 不触发重新渲染
    // ❌ 不显示在页面上
    const secretRef = useRef(null);

    // 组件“第一次加载到页面上”时执行一次
    useEffect(() => {
        getCredential()
            .then(secretFromApi => {
                // 把后端返回的字符串存进 ref
                secretRef.current = secretFromApi;
            })
            .catch(err => console.error(err));
    }, []);

    // 点击按钮时执行
    const handleCopy = () => {
        // ⚠️ 这里不是 secret
        // ✅ 正确的是 secretRef.current
        if (!secretRef.current) return;

        copyToClipboard(secretRef.current);
    };

    return (
        <button
            className="badge bg-danger even-larger-badge small-badge ms-2"
            type="button"
            onClick={handleCopy}
            title="Copy credential"
        >
            <i className="fas fa-key"></i>
        </button>
    );
}
