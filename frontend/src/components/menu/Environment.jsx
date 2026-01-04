import React, { useEffect, useState, useRef } from "react";
import "../../assets/note/noteBuilder.css";
import { TaWork } from "./TaWork";
import { JiraWork } from "./Jira";
import { Gitlab } from "./Gitlab";
import { JenkinsNav } from "./Jenkins";
import { SalesforceNav } from "./Salesforce";
import { VaultNav } from "./Vault";



const ENVIRONMENTS = [
    { id: "env-prod", label: "Production", bg: "bg-primary", text: "text-white" },
    { id: "env-rc", label: "RC/Staging", bg: "bg-secondary", text: "text-white" },
    { id: "env-int", label: "INT", bg: "bg-info", text: "text-white" },
    { id: "env-zelda", label: "ZELDA", bg: "bg-warning", text: "text-dark" },
    { id: "env-local", label: "Local", bg: "bg-danger", text: "text-white" },
];

const Environment = () => {
    const [menuData, setMenuData] = useState([]); // API 数据
    const [openDropdown, setOpenDropdown] = useState(false);
    const [currentEnv, setCurrentEnv] = useState("local");
    const [hoveredProject, setHoveredProject] = useState(null);
    const [popupPosition, setPopupPosition] = useState({ top: 0, left: 0 });
    const hoverTimeoutRef = useRef(null);

    // 获取 API 数据
    useEffect(() => {
        const fetchMenuData = async () => {
            try {
                const res = await fetch("/api/links/projects");
                if (!res.ok) throw new Error("Failed to fetch menu data");
                const data = await res.json(); // 解析返回的JSON数据
                console.log("Fetching menu data...:", data); // 打印返回的menu data
                setMenuData(Object.keys(data).map(key => ({
                    key,
                    text: key.replace(/-/g, " "),
                    links: data[key]
                })));
            } catch (err) {
                console.error(err);
            }
        };
        fetchMenuData();
    }, []);

    // 延迟隐藏 Popover
    const handleMouseLeaveProject = () => {
        hoverTimeoutRef.current = setTimeout(() => setHoveredProject(null), 2000);
    };

    // 获取项目的位置
    const handleMouseEnterProject = (e, itemKey, itemText) => {
        // 获取项目元素的位置
        const rect = e.target.getBoundingClientRect();

        // 输出调试信息
        console.log("Hovering over text:", itemText);
        console.log("Element's rect:", rect);
        console.log("react.bottom - react.top: ", rect.bottom - rect.top);
        console.log("window.scrollY: ", window.scrollY);
        console.log("rect.bottom + window.scrollY: ", rect.bottom + window.scrollY);

        // 设置弹出框的位置
        setPopupPosition({
            top: rect.bottom + window.scrollY, // 直接使用 rect.bottom 来确保弹出框位置紧贴目标项目
            left: rect.left + window.scrollX,  // 设置弹出框的左侧位置
        });

        setHoveredProject(itemKey);
    };



    return (
        <div
            className="dropdown"
            onMouseEnter={() => setOpenDropdown(true)}
            onMouseLeave={() => setOpenDropdown(false)}
        >
            {/* 三点按钮 */}
            <button className="btn btn-link p-1">
                <i className="fa fa-ellipsis-v" />
            </button>

            {/* Dropdown 内容 */}
            <ul className={`dropdown-menu dropdown-menu-end shadow flex-column ${openDropdown ? "show" : ""}`}>
                {/* 环境行 */}
                <li>
                    <div className="d-flex flex-wrap ms-1 me-1 mb-3 mt-2">
                        {ENVIRONMENTS.map((env, i) => (
                            <React.Fragment key={env.id}>
                                <span
                                    role="button"
                                    id={env.id}
                                    className={`badge ${env.bg} ${env.text} mt-1 ms-2`}
                                    onClick={() => setCurrentEnv(env.id.replace("env-", ""))}
                                >
                                    {env.label}
                                </span>
                                {i < ENVIRONMENTS.length - 1 && <span>&nbsp;·&nbsp;</span>}
                            </React.Fragment>
                        ))}
                    </div>
                </li>

                <li><hr className="dropdown-divider" /></li>

                {/* 项目行 */}
                <li className="px-2 small">
                    {menuData.map((item, index, arr) => {
                        return (
                            <React.Fragment key={item.key}>
                                <span
                                    role="button"
                                    className="hover-cursor ms-2 me-1"
                                    onMouseEnter={(e) => handleMouseEnterProject(e, item.key, item.text)} // 添加动态位置调整
                                    onMouseLeave={handleMouseLeaveProject}
                                >
                                    <a
                                        href={
                                            currentEnv === "local"
                                                ? item.links.site["docker"] || "#"
                                                : item.links.site[currentEnv] || "#"
                                        }
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {item.text.replace(/(^\w{1})|(\s+\w{1})/g, (match) => match.toUpperCase())}
                                    </a>
                                </span>

                                {/* Popover */}
                                {hoveredProject === item.key && (
                                    <div
                                        className="position-absolute bg-white border shadow p-2"
                                        style={{
                                            zIndex: 1000,
                                            minWidth: "200px",
                                            top: popupPosition.top,  // 使用动态位置
                                            left: popupPosition.left, // 使用动态位置
                                        }}
                                        onMouseEnter={() => {
                                            if (hoverTimeoutRef.current) clearTimeout(hoverTimeoutRef.current);
                                        }}
                                        onMouseLeave={handleMouseLeaveProject}
                                    >
                                        {/* Apache */}
                                        {item.links.site && (currentEnv === "local" ? item.links.site["apache"] : item.links.site[currentEnv]) && (
                                            <div>
                                                <a
                                                    href={currentEnv === "local" ? item.links.site["apache"] : item.links.site[currentEnv]}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    Apache
                                                </a>
                                            </div>
                                        )}
                                        {/* Kibana */}
                                        {item.links.kibana && (currentEnv === "local" ? item.links.kibana["int"] : item.links.kibana[currentEnv]) && (
                                            <div>
                                                <a
                                                    href={currentEnv === "local" ? item.links.kibana["int"] : item.links.kibana[currentEnv]}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    Kibana
                                                </a>
                                            </div>
                                        )}
                                        {/* Pipeline */}
                                        {item.links.pipeline && (
                                            <div>
                                                <a
                                                    href={item.links.pipeline}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    Pipeline
                                                </a>
                                            </div>
                                        )}
                                    </div>
                                )}

                                {index < arr.length - 1 && <span>&nbsp;·&nbsp;</span>}
                            </React.Fragment>
                        );
                    })}
                </li>

                <li><hr className="dropdown-divider" /></li>
                <TaWork />
                <li><hr className="dropdown-divider" /></li>
                <JiraWork />
                <li><hr className="dropdown-divider" /></li>

                <VaultNav />
                <li><hr className="dropdown-divider" /></li>

                <Gitlab />
                <li><hr className="dropdown-divider" /></li>

                <JenkinsNav />
                <li><hr className="dropdown-divider" /></li>

                <SalesforceNav />
                <li><hr className="dropdown-divider" /></li>

                {/* Vault 链接 */}
                <li className="nav-item">
                    <div className="nav-heading bg-danger text-white fw-bold p-2 rounded small">
                        VAULT ·{" "}
                        <a
                            className="text-white"
                            href="https://vault.common.int.test.net"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            INT
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    );
};

export default Environment;