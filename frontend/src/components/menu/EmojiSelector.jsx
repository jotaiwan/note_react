import React, { useState, useEffect, useRef } from 'react';
import { copyToClipboard } from '@/assets/helpers/emojiHelper';
import './emojiSelector.css';

export default function EmojiSelector({ appendToTextarea }) { // <- 接收 prop
    const [emojis, setEmojis] = useState([]);
    const [text, setText] = useState('');
    const [open, setOpen] = useState(false);
    const containerRef = useRef();

    useEffect(() => {
        const loadEmojis = async () => {
            try {
                const res = await fetch('/api/emojis');
                const data = await res.json();
                setEmojis(data);
            } catch (err) {
                console.error('Error fetching emojis:', err);
            }
        };
        loadEmojis();
    }, []);

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (containerRef.current && !containerRef.current.contains(e.target)) {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // in old jquery, this could be something like  $('.emoji-option').click(function ()
    const handleSelect = (emoji) => {
        // 获取笔记文本框
        const textarea = document.getElementById('note_text');
        if (!textarea) return;

        // 获取光标当前位置
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;

        // 在光标位置插入 emoji
        const newText =
            textarea.value.substring(0, start) +
            emoji.value + ' ' +
            textarea.value.substring(end);

        // 更新 textarea 内容
        textarea.value = newText;

        // 光标移动到刚插入的 emoji 后面，并保持焦点
        setTimeout(() => {
            textarea.selectionStart = textarea.selectionEnd = start + emoji.value.length;
            textarea.focus();
        }, 0);

        // 复制 emoji 到剪贴板
        copyToClipboard(emoji.value);

        // 关闭下拉框
        setOpen(false);
    };


    return (
        <div className="emoji-container" ref={containerRef}>
            <div className="emoji-selection" onClick={() => setOpen(!open)}>
                <span className='select2-selection__rendered'>{text || 'Select emoji'}</span>
                <span className="emoji-arrow">▾</span>
            </div>

            {open && (
                <div className="emoji-dropdown">
                    {emojis.map((emoji, index) => (
                        <button
                            key={index}
                            type="button"
                            onClick={() => handleSelect(emoji)}
                            title={emoji.label}
                            className="emoji-option"
                        >
                            {emoji.label}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
