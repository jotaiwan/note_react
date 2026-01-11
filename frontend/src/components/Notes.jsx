// src/components/Notes.jsx
import React, { useEffect, useState } from "react";
import { listNotes } from "../services/noteService";
import { statusData } from "../services/noteStatus";
import NoteEditable from "./NoteEditable";
import "../assets/note/noteBuilder.css";

export default function Notes({ reloadTrigger }) {
    const [notes, setNotes] = useState({});
    const [collapsedTickets, setCollapsedTickets] = useState({}); // 折疊狀態
    const [noteStatusData, setNoteStatusData] = useState({}); // ⚠️ 這裡改成物件

    const loadNotes = async () => {
        try {
            const data = await listNotes();
            setNotes(data || {});
        } catch (err) {
            console.error("Failed to load notes:", err);
            setNotes({});
        }
    };

    const loadStatus = async () => {
        try {
            const data = await statusData(); // data 是物件
            setNoteStatusData(data || {});
        } catch (err) {
            console.error("Failed to load note status:", err);
            setNoteStatusData({});
        }
    };

    useEffect(() => {
        loadNotes();
        loadStatus();
    }, [reloadTrigger]);

    const toggleTicket = (ticket) => {
        setCollapsedTickets((prev) => ({
            ...prev,
            [ticket]: !prev[ticket],
        }));
    };

    const copyTicket = (ticket) => {
        navigator.clipboard.writeText(ticket).then(() => console.log("Copied:", ticket));
    };

    // ⚡ 根據 status 名稱取得 icon
    const getStatusIcon = (status) => {
        const s = noteStatusData[status]; // 直接用 key 取
        if (!s) return status;
        return <i className={`fa ${s.icon}`} style={{ color: s.color }} title={s.text}></i>;
    };

    return (
        <table className="table table-bordered table-sm table-hover mt-3">
            <thead>
                <tr>
                    <th style={{ width: "180px" }}>Ticket</th>
                    <th style={{ width: "80px" }}>Status</th>
                    <th style={{ width: "110px" }}>Note_Count</th>
                    <th style={{ width: "220px" }}>Note_Date</th>
                    <th>Note (click to edit)</th>
                </tr>
            </thead>
            <tbody>
                {notes &&
                    Object.entries(notes).map(([ticket, ticketNotes]) => {
                        const collapsed = collapsedTickets[ticket];
                        const visibleRows = collapsed ? ticketNotes.slice(0, 1) : ticketNotes;
                        const rowspan = visibleRows.length;

                        return visibleRows.map((note, index) => (
                            <tr
                                key={
                                    note.rowId +
                                    "-" +
                                    (typeof note.date === "object" ? note.date.SYD : note.date)
                                }
                                className={`group-${ticket}`}
                                data-row-id={note.rowId}
                            >
                                {index === 0 && (
                                    <td rowSpan={rowspan} className="fixed-date-col align-top">
                                        {ticket === "NOTE_ONLY" || ticket === "MEETING" ? (
                                            // Render plain text for these special tickets
                                            <span>{ticket}</span>
                                        ) : (
                                            // Otherwise, render the clickable link
                                            <a
                                                href={note.link || "#"}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                {ticket}
                                            </a>
                                        )}

                                        <span className="ms-2">
                                            <i
                                                className="fa fa-copy"
                                                style={{ cursor: "pointer" }}
                                                onClick={() => copyTicket(ticket)}
                                            ></i>
                                        </span>
                                        <span className="ms-2">
                                            <i
                                                className={`fas ${collapsed ? "fa-plus" : "fa-minus"}`}
                                                style={{ cursor: "pointer" }}
                                                onClick={() => toggleTicket(ticket)}
                                            ></i>
                                        </span>
                                    </td>
                                )}
                                {index === 0 && (
                                    <td rowSpan={rowspan} className="text-center">
                                        {getStatusIcon(note.status)}
                                    </td>
                                )}
                                {index === 0 && (
                                    <td rowSpan={rowspan} className="align-top text-center">
                                        {ticketNotes.length}
                                    </td>
                                )}
                                <td className="fixed-date-col date-cell small">
                                    <div>🌐 {note.date.ETC_GMT7}</div>
                                    <div>🦘 {note.date.SYD}</div>
                                </td>

                                <td className="editable note">
                                    <NoteEditable rowId={note.rowId} content={note.note} />
                                </td>
                            </tr>
                        ));
                    })}
            </tbody>
        </table>
    );
}
