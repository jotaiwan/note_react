import React from 'react';
import tripIcon from "../../assets/img/tripadvisor-4.png"; // Import image

export const TaWork = () => {
    return (
        <li className="nav-item d-flex align-items-center bg-secondary p-2 rounded">
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
                    href="https://www.myworkday.com/tripadvisor/d/home.htmld"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Workday
                </a>

                {/* Separator */}
                <span className="separator mx-2">·</span>

                <a
                    className="nav-link py-1 text-white small font-weight-bold"
                    href="https://us-east-1.signin.aws.amazon.com/platform/login?workflowStateHandle=ab9985cc-6293-47fc-beeb-a3036763a40f"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    AWS
                </a>

                {/* Separator */}
                <span className="separator mx-2">·</span>

                <a
                    className="nav-link py-1 text-white small font-weight-bold"
                    href="https://myapplications.microsoft.com/"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    SSO
                </a>
            </div>
        </li>
    );
}
