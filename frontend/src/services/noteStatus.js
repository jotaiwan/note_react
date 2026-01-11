// frontend/src/services/noteService.js
import axios from "axios";

const API_BASE = "/api";

// ✅ 统一调试 header
const debugHeaders = {
    "XDEBUG_SESSION": "1"
};

// 获取 notes 状态列表
export const statusData = () =>
    axios.get(`${API_BASE}/notes/statuses`, { headers: debugHeaders }).then(res => res.data);
