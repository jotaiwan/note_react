// src/assets/helpers/noteApi.js
export async function saveNote(ticket, note, status) {
    const data = { ticket, note, status };

    try {
        const res = await fetch('/api/notes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });

        // 🔹 直接解析 JSON
        const response = await res.json();

        // 🔹 不丟 500，即使失敗也返回 success=false
        return response;

    } catch (err) {
        console.error('Save note error:', err);
        return { success: false, savedTicket: null };
    }
}
