// frontend/src/services/clipboard.js
import axios from "axios";

const API_BASE = "/api";

export const getClipboardItems = () => {
    return axios.get(`${API_BASE}/clipboard`)  // Symfony Controller 返回数组
        .then(res => res.data);
};
