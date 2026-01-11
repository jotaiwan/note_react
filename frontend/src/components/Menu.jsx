// frontend/src/components/Menu.jsx
import React, { useState } from "react";
import AddNewNoteForm from "./AddNewNoteForm";
import { Environment, MenuIcons, EmojiSelector, Clipboard, Credential } from './menu';
import NoteStatus from "./NoteStatus";
import Stock from "./Stock";
import chatgptIcon from "../assets/img/chatgpt-icon.png";

export default function Menu({ onSaveNote }) {
    const [showForm, setShowForm] = useState(false);

    const handleAddNew = () => setShowForm(true);
    const handleCancel = () => setShowForm(false);

    return (
        <div>
            {/* Menu Container */}
            <div
                id="menu"
                className="d-flex align-items-center gap-2 pt-2 pb-2"
                style={{ borderBottom: "1px solid #ccc", marginBottom: "1rem" }}
            >
                <Environment />
                <MenuIcons />
                <EmojiSelector />

                {/* Add New Button */}
                <button
                    className="badge bg-success even-larger-badge me-2"
                    style={{ fontSize: "1rem" }}
                    onClick={handleAddNew}
                >
                    Add New
                </button>

                <NoteStatus />

                <div>
                    <a href="https://chatgpt.com/" target="_blank" rel="noopener noreferrer">
                        <img
                            src={chatgptIcon}
                            width="35px"
                            height="35px"
                            alt="ChatGPT Icon"
                        />
                    </a>
                </div>

                <div className="ms-auto d-flex align-items-center">
                    <Stock />
                    <Clipboard />
                    <Credential />
                </div>
            </div>

            {/* Add New Form */}
            {showForm && (
                <AddNewNoteForm
                    onSave={async (data) => {
                        setShowForm(false);            // 隱藏表單
                        if (onSaveNote) await onSaveNote(data); // 🔹 呼叫 App.jsx 的 onSaveNote
                    }}
                    onCancel={handleCancel}
                />
            )}
        </div>
    );
}
