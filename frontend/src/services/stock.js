import axios from "axios";
const API_BASE = "/api";

// stockData 可以接收 source 参数，默认 AlpacaMarkets
export const stockData = (source = "Alpacamarkets") => {
    return axios.get(`${API_BASE}/stocks/TRIP/json`, { params: { source } })
        .then(res => res.data);
};
