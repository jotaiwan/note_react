// frontend/src/services/noteService.js
import axios from "axios";

const API_BASE = "/api";

// ✅ 全局调试用 header，可选
const debugHeaders = {
    "XDEBUG_SESSION": "1"
};

// GET 所有 notes
export const listNotes = () =>
    axios.get(`${API_BASE}/notes`, { headers: debugHeaders })
        .then(res => res.data);

// GET 单个 note
export const readNote = (id) =>
    axios.get(`${API_BASE}/notes/${id}`, { headers: debugHeaders })
        .then(res => res.data);

// POST 创建 note
export const createNote = async (data) => {
    try {
        const response = await axios.post(`${API_BASE}/notes`, data, { headers: debugHeaders });

        // 如果 success = false 就直接 throw
        if (!response.data.success) {
            throw new Error(`Save failed: ${JSON.stringify(response.data)}`);
        }

        return response.data;
    } catch (err) {
        console.error("Save note error:", err);
        throw err;
    }
};

// PUT 更新 note
export const updateNote = (id, data) =>
    axios.put(`${API_BASE}/notes/${id}`, data, { headers: debugHeaders })
        .then(res => res.data);
