/**
 * Auto-refresh stock data every 30 seconds
 * Requires an element with id="stock-info"
 */

function refreshStock() {
    $.getJSON('/api/checker/stock?format=html', function (response) {

        if (!response || !response.stockPrice) {
            $('#stock-info').html('<span style="color:red">Stock data unavailable</span>');
            return;
        }

        let stockHtml = response.stockPrice;
        $("#stock-info").html(stockHtml);

    }).fail(function () {
        $("#stock-info").html("<span style='color:red'>API error</span>");
    });
}

function refreshStockSource(source) {
    // API URL with dynamic provider
    // const apiUrl = `/api/checker/stock?source=${encodeURIComponent(source)}&format=html`;
    const apiUrl = window.location.origin + '/api/stocks/TRIP/html?source=' + source;

    console.log("🔄 Refreshing stock using source:", source);

    fetch(apiUrl)
        .then(response => {
            debugger;
            if (!response.ok) {
                throw new Error("API error: " + response.status);
            }
            return response.json(); // because your API returns HTML snip
        })
        .then(data => {
            if (!data.stockInfo) {
                throw new Error("Missing stockPrice in API response");
            }

            const container = document.getElementById('stock-info');
            if (container) {
                container.innerHTML = data.stockInfo.content;
            } else {
                console.error("❌ Element #stock-info not found.");
            }
        })
        .catch(error => {
            console.error("❌ Failed loading stock:", error);
            alert("Stock refresh failed — check console.");
        });
}

// Helper: convert browser time → Sydney time
function getSydneyTime() {
    return new Date(
        new Date().toLocaleString("en-US", { timeZone: "Australia/Sydney" })
    );
}


// Auto-refresh only during Sydney stock-watching hours (1:30 AM → 8:00 AM AEDT)
setInterval(function () {
    const syd = getSydneyTime();
    const hour = syd.getHours();
    const minute = syd.getMinutes();
    const totalMins = hour * 60 + minute;

    // Detect if Sydney is currently in DST (UTC+11)
    const offset = syd.getTimezoneOffset();
    const isDST = offset === -660;  // Sydney DST offset = -660 min (UTC+11)
    // Standard Time (UTC+10) = -600

    let start, end;

    if (isDST) {
        // -------------------------
        // ✔ Sydney DST (Nov–Mar)
        // 1:30 AM → 8:00 AM
        // -------------------------
        start = 1 * 60 + 30;   // 01:30
        end = 8 * 60;        // 08:00

    } else {
        // -------------------------
        // ✔ Sydney non-DST (Apr–Oct)
        // 11:30 PM → 6:00 AM
        // -------------------------
        start = 23 * 60 + 30;  // 23:30 (previous day)
        end = 6 * 60;        // 06:00
    }

    // Refresh logic
    if (isDST) {
        // Normal same-day range
        if (totalMins >= start && totalMins < end) {
            refreshStockSource();
        }
    } else {
        // Non-DST range crosses midnight
        if (
            totalMins >= start ||    // 23:30 → 23:59
            totalMins < end          // 00:00 → 06:00
        ) {
            refreshStockSource();
        }
    }
}, 180000); // 180,000 ms = 3 minutes
