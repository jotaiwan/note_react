import React from 'react';
import jiraIcon from "../../assets/img/jira_cloud.png";  // Import image

export const JiraWork = () => {
    return (
        <li className="nav-item d-flex align-items-center bg-primary p-2 rounded">

            <img
                className="img-thumbnail me-3"
                src={jiraIcon}  // Use imported image
                alt="Atlassian. Logo"
                style={{ maxWidth: '55px', height: '35px' }}
            />
            {/* Image + Bug Section */}
            <div className="image-bug-section d-flex align-items-center me-3 bg-info p-2 rounded">
                {/* Image */}


                {/* Bug Section */}
                <div className="bug-section ms-3 d-flex align-items-center">
                    <span className="small py-1 font-weight-bold text-white">Bug:</span>

                    {/* Links for Bug */}
                    <div className="link-group ms-2 d-flex align-items-center">
                        <a
                            className="nav-link nav-dot py-1 text-white small font-weight-bold"
                            href="https://viatorinc.atlassian..net/secure/CreateIssueDetails!init.jspa?pid=10089&issuetype=10040&reporter=712020%3A54173f2a-ea63-495a-8431-1cdae42a7b45&priority=10002&customfield_10390=12279&labels=vs-tools"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            📌 New
                        </a>

                        <span className="separator mx-2">·</span> {/* Separator */}

                        <a
                            className="nav-link nav-dot py-1 text-white small font-weight-bold"
                            href="https://viatorinc.atlassian..net/secure/CreateIssueDetails!init.jspa?pid=10089&issuetype=10040&parent=APPSUP-7761&reporter=712020%3A54173f2a-ea63-495a-8431-1cdae42a7b45&priority=10002&customfield_10390=12279&labels=vs-tools"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            📌 KTLO
                        </a>
                    </div>
                </div>
            </div>

            {/* Task Section */}
            <div className="task-section d-flex align-items-center bg-info p-2 rounded ms-3">
                <span className="small py-1 font-weight-bold text-white">Task:</span>

                {/* Links for Task */}
                <div className="link-group ms-2 d-flex align-items-center">
                    <a
                        className="nav-link nav-dot py-1 text-white small font-weight-bold"
                        href="https://viatorinc.atlassian..net/secure/CreateIssueDetails!init.jspa?pid=10089&issuetype=10037&reporter=712020%3A54173f2a-ea63-495a-8431-1cdae42a7b45&priority=10002&customfield_10390=12279&labels=vs-tools"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        📌 New
                    </a>

                    <span className="separator mx-2">·</span> {/* Separator */}

                    <a
                        className="nav-link nav-dot py-1 text-white small font-weight-bold"
                        href="https://viatorinc.atlassian..net/secure/CreateIssueDetails!init.jspa?pid=10089&issuetype=10037&parent=APPSUP-7761&reporter=712020%3A54173f2a-ea63-495a-8431-1cdae42a7b45&priority=10002&customfield_10390=12279&labels=vs-tools"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        📌 KTLO
                    </a>

                    <span className="separator mx-2">·</span> {/* Separator */}

                    <a
                        className="nav-link nav-dot py-1 text-white small font-weight-bold"
                        href="https://viatorinc-sandbox-973.atlassian.net"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        🔴 RC
                    </a>
                </div>
            </div>
        </li>
    );
}
