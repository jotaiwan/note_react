import React from 'react';
import tripIcon from "../../assets/img/gitlab.png"; // Import image

export const Gitlab = () => {
    return (
        <li className="nav-item d-flex align-items-center bg-success p-2 rounded">
            {/* Image */}
            <img
                className="img-thumbnail"
                src={tripIcon}  // Use the imported image variable
                width="35px"
                height="35px"
                alt="Tripadvisor"
            />

            {/* Links Section */}
            <div className="nav-links ms-3 d-flex align-items-center">
                <a
                    className="nav-link py-1 text-white small font-weight-bold"
                    href="https://gitlab.com/viator/engineering/app-support"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Team
                </a>

                {/* Separator */}
                <span className="separator mx-2">·</span>

                <a
                    className="nav-link py-1 text-white small font-weight-bold"
                    href="https://gitlab.com/viator/engineering"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Engineering
                </a>

                {/* Separator */}
                <span className="separator mx-2">·</span>

                <a
                    className="nav-link py-1 text-white small font-weight-bold"
                    href="https://gitlab.com/viator/infrastructure"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Infrastrure
                </a>
            </div>
        </li>
    );
}
