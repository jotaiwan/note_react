// src/components/AddNewNoteForm.jsx
import React, { useState } from "react";
import EmojiSelector from './menu/EmojiSelector';
import { createNote } from '../services/noteService'; // 🔹 用 service 取代舊的 noteApi.js

export default function AddNewNoteForm({ onSave, onCancel }) {
    const [noteId, setNoteId] = useState("");
    const [noteText, setNoteText] = useState("");
    const [status, setStatus] = useState("Open");

    const appendEmoji = (emoji) => {
        setNoteText(prev => prev + emoji);
    };

    const handleSave = async () => {
        try {
            console.log('noteText before save:', noteText);

            const response = await createNote({
                ticket: noteId,
                note: noteText,
                status: status
            });

            if (response.success) {
                alert('Saved!');

                // 🔹 回傳給父組件
                if (onSave) onSave({ id: noteId, text: noteText, status });

                // 🔹 清空表單
                setNoteId("");
                setNoteText("");
                setStatus("Open");
            } else {
                alert('Save failed: ' + JSON.stringify(response));
            }
        } catch (err) {
            alert('Save failed: ' + err.message);
            console.error(err);
        }
    };

    return (
        <div className="mb-5" id="addNewNoteForm">
            <div className="row">
                {/* 左邊欄位 */}
                <div className="col-md-2 d-flex flex-column">
                    <div id="note_div_id" className="mb-3">
                        <input
                            className="form-control text-uppercase"
                            type="search"
                            placeholder="Add new note id here"
                            aria-label="Search"
                            id="note_id"
                            value={noteId}
                            onChange={(e) => setNoteId(e.target.value)}
                            style={{ position: "sticky", top: 0, zIndex: 10 }}
                        />
                    </div>

                    {/* Status Radio buttons in same row */}
                    <div className="mb-2 d-flex flex-wrap align-items-center">
                        {[
                            { id: "statusOpen", value: "Open", color: "blue", icon: "fa-hourglass-half" },
                            { id: "statusResolved", value: "Resolved", color: "green", icon: "fa-check-circle" },
                            { id: "statusProcessing", value: "Processing", color: "darkcyan", icon: "fa-spinner fa-spin" },
                            { id: "statusWaiting", value: "Follow", color: "indigo", icon: "fa-eye" }
                        ].map((s) => (
                            <div className="form-check form-check-inline me-3 d-flex align-items-center" key={s.id}>
                                <input
                                    className="form-check-input"
                                    type="radio"
                                    name="note_status"
                                    id={s.id}
                                    value={s.value}
                                    checked={status === s.value}
                                    onChange={() => setStatus(s.value)}
                                />
                                <label
                                    className="form-check-label small ms-1 d-flex align-items-center"
                                    htmlFor={s.id}
                                    style={{ lineHeight: "2" }} // 避免 label 高度影響
                                >
                                    <i className={`fa ${s.icon}`} style={{ color: s.color }} title={`Status::${s.value}`}></i>
                                </label>
                            </div>
                        ))}
                    </div>


                    <div id="actions">
                        <div className="d-flex align-items-center">
                            <button
                                className="btn btn-dotted btn-sm"
                                id="saveNote"
                                style={{ border: "1px dotted red" }}
                                type="button"
                                onClick={handleSave}
                            >
                                Save Note
                            </button>

                            {/* Cancel Button (小紅圓形) */}
                            {onCancel && (
                                <button
                                    className="badge bg-danger rounded-circle ms-2"
                                    type="button"
                                    onClick={onCancel}
                                    style={{ width: "24px", height: "24px", padding: 0, display: "flex", alignItems: "center", justifyContent: "center", fontSize: "1rem" }}
                                    title="Cancel"
                                >
                                    <i className="fas fa-times"></i>
                                </button>
                            )}


                        </div>
                    </div>

                </div>

                {/* 右邊欄位 */}
                <div className="col-md-10">
                    <textarea
                        id="note_text"
                        className="form-control pr-5"
                        rows="20"
                        placeholder="Your note..."
                        value={noteText}
                        onChange={(e) => setNoteText(e.target.value)}
                    />
                    <EmojiSelector appendToTextarea={appendEmoji} />
                </div>
            </div>
        </div>
    );
}
