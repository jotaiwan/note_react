// frontend/src/components/NoteForm.jsx
import { useState, useEffect } from "react";
import { createNote, updateNote, readNote } from "../services/noteService";

export default function NoteForm({ noteId = null, onSaved, onCancel }) {
    const [title, setTitle] = useState("");
    const [content, setContent] = useState("");

    // 如果是編輯，載入現有資料
    useEffect(() => {
        if (noteId !== null) {
            readNote(noteId).then(note => {
                setTitle(note.title || "");
                setContent(note.content || "");
            });
        } else {
            setTitle("");
            setContent("");
        }
    }, [noteId]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!title) return;

        if (noteId === null) {
            await createNote({ title, content });
        } else {
            await updateNote(noteId, { title, content });
        }

        setTitle("");
        setContent("");
        if (onSaved) onSaved();
    };

    const handleCancel = () => {
        setTitle("");
        setContent("");
        if (onCancel) onCancel();
    };

    return (
        <form onSubmit={handleSubmit}>
            <input
                placeholder="Title"
                value={title}
                onChange={e => setTitle(e.target.value)}
            />
            <input
                placeholder="Content"
                value={content}
                onChange={e => setContent(e.target.value)}
            />
            <button type="submit">{noteId === null ? "Add Note" : "Update Note"}</button>
            {noteId !== null && <button type="button" onClick={handleCancel}>Cancel</button>}
        </form>
    );
}
