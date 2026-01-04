import React from 'react';
import jenkinsIcon from "../../assets/img/jenkins.png"; // Import image

const JenkinsLink = ({ label, href }) => (
    <a className="nav-link py-1 small text-black" href={href} target="_blank" rel="noopener noreferrer">
        {label}
    </a>
);

export const JenkinsNav = () => {
    return (
        <li className="nav-item">
            {/* Background container with a single row layout */}
            <div className="nav-heading bg-warning p-2 mb-1 rounded small">
                {/* Row for image and links */}
                <div className="d-flex align-items-center">
                    {/* Jenkins icon */}
                    <img
                        className="img-thumbnail me-3"  // Adds a margin to the right of the image
                        src={jenkinsIcon}  // Use the imported image variable
                        width="35px"
                        height="35px"
                        alt="Jenkins Icon"
                    />

                    {/* Links in a row */}
                    <JenkinsLink label="Home" href="https://jenkins.prod.viatorsystems.com/" />
                    <span className="separator mx-2">·</span>
                    <JenkinsLink label="Dev" href="https://avrdevjenkins00n.ndmad2.tripadvisor.com/" />
                    <span className="separator mx-2">·</span>
                    <JenkinsLink label="Self-Prod" href="https://jenkins.prod.viatorsystems.com/job/Infrastructure/job/Vault/job/vault-self-service-prod/" />
                    <span className="separator mx-2">·</span>
                    <JenkinsLink label="Self-RC" href="https://jenkins.prod.viatorsystems.com/job/Infrastructure/job/Vault/job/vault-self-service-rc/" />
                    <span className="separator mx-2">·</span>
                    <JenkinsLink label="Restart-Prod" href="https://jenkins.prod.viatorsystems.com/job/Apps/job/rolling-restart/" />
                    <span className="separator mx-2">·</span>
                    <JenkinsLink label="Restart-Dev" href="https://avrdevjenkins00n.ndmad2.tripadvisor.com/user/scorreia/search/?q" />
                </div>
            </div>
        </li>
    );
};
