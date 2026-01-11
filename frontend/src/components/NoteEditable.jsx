import React, { useState, useEffect, useRef } from "react";
import { updateNote, readNote } from "../services/noteService";
import "../assets/note/noteBuilder.css";
import parse from "html-react-parser";

let clickCount = 0;
let clickTimer;

export default function NoteEditable({ rowId, content }) {
    const [noteText, setNoteText] = useState(content || "");
    const [editing, setEditing] = useState(false);
    const [hint, setHint] = useState(false);
    const textareaRef = useRef();
    const wrapperRef = useRef();  // Wrapper ref to monitor click events.
    const originalNoteRef = useRef(""); // 🔹 存原始 note，编辑前就存

    const handleClick = () => {
        clickCount++;
        clearTimeout(clickTimer);

        if (clickCount === 1) setHint(true);

        clickTimer = setTimeout(() => {
            if (clickCount === 4) {
                setEditing(true);
                fetchOriginalPlainText(); // 🔹 進入編輯時拿原始純文字
            }
            clickCount = 0;
            setHint(false);
        }, 300);
    };

    // Fetch original plain text for editing
    const fetchOriginalPlainText = async () => {
        try {
            const resp = await readNote(rowId);
            if (resp && resp.note) {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = resp.note;
                const plain = tempDiv.textContent || tempDiv.innerText || "";
                setNoteText(plain);
                originalNoteRef.current = plain; // 🔹 保存原始值
            }
        } catch (err) {
            console.error("Failed to fetch original note:", err);
        }
    };


    const handleBlur = async (e) => {
        e.persist();
        console.log("handleBlur triggered"); // 🔹 先确认事件触发

        // 🔹 If clicked inside emoji picker, don't lose focus
        if (e.relatedTarget && e.relatedTarget.closest(".emoji-container")) {
            return;
        }

        // 🔹 Move setEditing(false) & setHint(false) here to ensure blur always runs first
        setEditing(false);
        setHint(false);

        // 🔹 Change: compare with current noteText instead of content
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = noteText; // ⚡ was `content` before

        const originalPlain = originalNoteRef.current;
        console.log("Original Plain Text: " + originalPlain);
        console.log("Note Text: " + noteText);

        if (noteText !== originalPlain) {
            const confirmed = window.confirm("Content changed! Save?");
            if (confirmed) {
                try {
                    const resp = await updateNote(rowId, { note: noteText });
                    setNoteText(resp.note || noteText);
                } catch (err) {
                    console.error("Save failed:", err);
                    setNoteText(originalPlain); // revert on error
                }
            } else {
                setNoteText(originalPlain); // revert if not saved
            }
        }
    };

    // Event listener to detect click outside textarea/emoji to end editing
    useEffect(() => {
        const handleClickOutside = (event) => {
            // 🔹 Change: if click outside wrapper, trigger textarea blur instead of directly setEditing(false)
            if (wrapperRef.current && wrapperRef.current.contains(event.target)) {
                return;
            }
            if (textareaRef.current) {
                textareaRef.current.blur(); // ⚡ triggers handleBlur properly
            }
        };

        document.addEventListener("mousedown", handleClickOutside);

        // Cleanup event listener
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, []);

    useEffect(() => {
        if (editing && textareaRef.current) {
            textareaRef.current.focus();
            textareaRef.current.style.height = "auto";
            textareaRef.current.style.height = textareaRef.current.scrollHeight + "px";
        }
    }, [editing]);

    // enable for debugging
    // console.log("Source note text: " + noteText);

    const cleaned = noteText
        .replace(/<p>/gi, "")
        .replace(/<\/p>/gi, "")
        .replace(/\r\n/g, "\n")
        .replace(/\r/g, "\n")
        .replace(/\\n/g, "<br />");

    return (
        <div
            ref={wrapperRef}  // The wrapper around the entire editable section
            onClick={!editing ? handleClick : undefined}
            style={{ position: "relative", cursor: "pointer" }}
        >
            {editing ? (
                <textarea
                    ref={textareaRef}
                    className="table-textarea"
                    value={noteText}
                    onChange={(e) => setNoteText(e.target.value)}
                    onBlur={handleBlur}  // Handle blur logic
                />
            ) : (

                // <div>{cleaned}</div>
                <div dangerouslySetInnerHTML={{ __html: cleaned }} />
            )}

            {/* Hint overlay */}
            {!editing && hint && (
                <span
                    style={{
                        position: "absolute",
                        top: 0,
                        right: 0,
                        background: "rgba(255, 255, 0, 0.8)",
                        padding: "2px 6px",
                        fontSize: "10px",
                        borderRadius: "4px",
                    }}
                >
                    Click 4× to edit
                </span>
            )}
        </div>
    );
}
