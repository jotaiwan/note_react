import { useEffect, useState } from "react";
import { stockData } from "../services/stock";
import './menu/stock.css';

export default function Stock() {
    const [stockInfo, setStockInfo] = useState(null);
    const [showHighest, setShowHighest] = useState(false);
    const [showSource, setShowSource] = useState(false);

    useEffect(() => {
        // 初始加载默认 source
        loadStock("Alpacamarkets");
    }, []);

    // 封装 AJAX 请求
    const loadStock = (source) => {
        stockData(source)
            .then(data => setStockInfo(data.stockInfo))
            .catch(err => console.error("Failed to load stock info:", err));
    };

    if (!stockInfo) return <span>Loading...</span>;

    const trendUp = stockInfo.rise_or_drop.trend === "▲";

    // 点击 source 切换
    const handleSourceClick = (source) => {
        loadStock(source);
        setShowSource(false); // 点击后关闭 tooltip
    };

    return (
        <div id="stock-info" className="stock-container">
            {/* Hover: Highest */}
            <div
                className="hover-stock-highest hover-item"
                onMouseEnter={() => setShowHighest(true)}
                onMouseLeave={() => setShowHighest(false)}
            >
                <span className="hover-trigger">⏰</span>
                {showHighest && (
                    <div className="hover-tooltip highest-tooltip">
                        <ul>
                            <li>🌐 {stockInfo.daily_highest_timestamp}</li>
                            <li>🦘 {stockInfo.daily_highest_sydney_time}</li>
                        </ul>
                    </div>
                )}
            </div>

            {/* Hover: Source */}
            <div
                className="hover-stock-source hover-item"
                onMouseEnter={() => setShowSource(true)}
                onMouseLeave={() => setShowSource(false)}
            >
                <span className="hover-trigger">
                    {stockInfo.source[0]} : 🕰️ {stockInfo.earliest_open_days} d
                </span>
                {showSource && (
                    <div className="hover-tooltip source-tooltip">
                        <ul>
                            <li onClick={() => handleSourceClick("Alpacamarkets")}>Alpaca Markets (real-time)</li>
                            <li onClick={() => handleSourceClick("Finnhub")}>Finnhub</li>
                        </ul>
                    </div>
                )}
            </div>

            {/* Prices */}
            <PriceBadge color="lightgreen" icon="💰" value={stockInfo.rise_or_drop.opening_price} />
            <PriceBadge color="#FBFA69" icon="🏆" value={stockInfo.rise_or_drop.daily_highest} />
            <PriceBadge
                color={trendUp ? "green" : "red"}
                textColor="white"
                value={`${stockInfo.rise_or_drop.latest_close} ${stockInfo.rise_or_drop.trend}`}
            />
        </div>
    );
}

// Badge 子组件
function PriceBadge({ color, textColor, icon, value }) {
    return (
        <span
            className="price-badge"
            style={{
                backgroundColor: color,
                color: textColor || "black"
            }}
        >
            {icon && `${icon} `}{value}
        </span>
    );
}
