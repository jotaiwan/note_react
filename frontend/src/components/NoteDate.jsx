// frontend/src/components/NoteDate.jsx
import React from "react";

export default function NoteDate({ dateObj, defaultTimezone = "SYD" }) {
    if (!dateObj) return null;

    // 如果只想显示 Sydney
    if (dateObj[defaultTimezone]) {
        return <>{dateObj[defaultTimezone]}</>;
    }

    // 如果想显示所有时区
    return (
        <>
            {Object.entries(dateObj).map(([tz, val]) => (
                <div key={tz}>
                    <span>{val}</span>
                </div>
            ))}
        </>
    );
}
