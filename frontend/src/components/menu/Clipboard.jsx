import { useEffect, useState, useRef } from "react";
import { getClipboardItems } from "../../services/clipboardService";
import { copyToClipboard } from "../../assets/helpers/clipboardHelper";
import '../../assets/helpers/clipboard.css';

export default function Clipboard() {
    const [items, setItems] = useState([]);
    const [hovered, setHovered] = useState(false);
    const hideTimeout = useRef(null); // 存放延迟隐藏的定时器

    useEffect(() => {
        getClipboardItems()
            .then(data => setItems(data))
            .catch(err => console.error("Failed to load clipboard items:", err));
    }, []);

    const handleMouseEnter = () => {
        if (hideTimeout.current) {
            clearTimeout(hideTimeout.current); // 进入按钮或 tooltip 时取消隐藏
        }
        setHovered(true);
    };

    const handleMouseLeave = () => {
        hideTimeout.current = setTimeout(() => {
            setHovered(false); // 延迟 2 秒隐藏
        }, 2000);
    };

    return (
        <span className="hover-shortcut-text text-sm position-relative">
            {/* 按钮 */}
            <button
                className="badge bg-success even-larger-badge small-badge ms-2"
                type="button"
                onMouseEnter={handleMouseEnter}
                onMouseLeave={handleMouseLeave}
            >
                <i className="fas fa-copy"></i>
            </button>

            {/* Tooltip */}
            {hovered && (
                <div
                    className="hover-shortcut-tooltip position-absolute tooltip-left-align"
                    onMouseEnter={handleMouseEnter}   // 鼠标进入 tooltip 保持显示
                    onMouseLeave={handleMouseLeave}  // 鼠标离开 tooltip 延迟隐藏
                >
                    <ul className="mb-0 ps-2">
                        {items.map((item, idx) => (
                            <li
                                key={idx}
                                data-copy={item.copy}
                                onClick={() => copyToClipboard(item.copy)}
                            >
                                {item.text}
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </span>
    );
}
