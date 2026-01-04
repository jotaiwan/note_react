import React from 'react'; // Required for React components
import salesforceIcon from "../../assets/img/salesforce.png";  // Import image


const SalesforceLink = ({ label, href }) => (
    <a className="nav-link py-1 small text-black" href={href} target="_blank" rel="noopener noreferrer">
        {label}
    </a>
);

export const SalesforceNav = () => {
    return (
        <li className="nav-item">
            {/* Background container with a single row layout */}
            <div className="nav-heading bg-light p-2 mb-1 rounded small">
                {/* Row for image and links */}
                <div className="d-flex align-items-center">
                    {/* Jenkins icon */}
                    <img
                        className="img-thumbnail me-3"  // Adds a margin to the right of the image
                        src={salesforceIcon}  // Use the imported image variable
                        width="35px"
                        height="35px"
                        alt="Jenkins Icon"
                    />

                    <SalesforceLink label="Home" href="https://attractions.my.salesforce.com/" />
                    <span className="separator mx-2">·</span>
                    <SalesforceLink label="Test" href="https://test.salesforce.com/" />
                    <span className="separator mx-2">·</span>

                </div>
            </div>
        </li>

    );
};
