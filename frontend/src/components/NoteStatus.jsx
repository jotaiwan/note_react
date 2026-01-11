import { useEffect, useRef, useState } from "react";
import { statusData } from "../services/noteStatus";

export default function NoteStatus() {
    const [statuses, setStatuses] = useState([]);
    const [open, setOpen] = useState(false);
    const [selected, setSelected] = useState(null);
    const wrapperRef = useRef(null);

    useEffect(() => {
        console.log("Status API is being called");
        statusData().then(data => {
            const list = Object.entries(data).map(([key, v]) => ({
                key,
                ...v,
            }));
            setStatuses(list);
            setSelected(list[0]); // 默认 All
        });
    }, []);

    // 点击外部关闭
    useEffect(() => {
        const onClick = e => {
            if (!wrapperRef.current?.contains(e.target)) {
                setOpen(false);
            }
        };
        document.addEventListener("click", onClick);
        return () => document.removeEventListener("click", onClick);
    }, []);

    const select = status => {
        setSelected(status);
        setOpen(false);
        // window.location.href = status.url;
    };

    return (
        <div
            ref={wrapperRef}
            className="position-relative d-flex align-items-center"
            style={{ width: 140 }}
        >
            {/* selection */}
            <div
                className="form-control d-flex justify-content-between align-items-center"
                onClick={() => setOpen(o => !o)}
                style={{ cursor: "pointer" }}
            >
                {selected && (
                    <span>
                        <i
                            className={`fa ${selected.icon}`}
                            style={{ color: selected.color }}
                        />{" "}
                        {selected.text}
                    </span>
                )}
                <b className="ms-auto">▾</b>
            </div>

            {/* dropdown */}
            {open && (
                <div
                    className="position-absolute bg-white border shadow-sm mt-1 w-100 z-3"
                    style={{ top: "100%" }}
                >
                    {statuses.map(s => (
                        <div
                            key={s.key}
                            className="px-2 py-1 d-flex align-items-center gap-2"
                            style={{ cursor: "pointer" }}
                            onClick={() => select(s)}
                        >
                            <i
                                className={`fa ${s.icon}`}
                                style={{ color: s.color }}
                            />
                            {s.text}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
