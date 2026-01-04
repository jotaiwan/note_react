// frontend/src/services/clipboard.js
import axios from "axios";

const API_BASE = "/api";

export const getCredential = () => {
    return axios.get(`${API_BASE}/credential`)  // Symfony Controller 返回数组
        .then(res => res.data);
};

export const getResouceKeyValue = (resource, key) => {
    return axios
        .get(`${API_BASE}/credential/${resource}/${key}`)
        .then(res => res.data);
};