// frontend/src/components/menu/MenuIcons.jsx
import React from 'react';

const icons = [
    { id: 'icon-user', label: '👤', href: 'https://viatorinc.atlassian.net/jira/people/712020:54173f2a-ea63-495a-8431-1cdae42a7b45/work', size: 20, top: 3 },
    { id: 'icon-group', label: '👥', href: 'https://docs.google.com/spreadsheets/d/166y6wL-nuuf7ukNbA4lgmdQVsQbgZ4P_bZfhUjRJe1Y/edit#gid=0', size: 24, top: -2 },
];

const MenuIcons = () => {
    return (
        <div className="d-flex">
            {icons.map(icon => (
                <div className="me-2" key={icon.id}>
                    <a href={icon.href} target="_blank" rel="noopener noreferrer">
                        <span style={{ fontSize: `${icon.size}px`, position: 'relative', top: `${icon.top}px` }}>
                            {icon.label}
                        </span>
                    </a>
                </div>
            ))}
        </div>
    );
};

export default MenuIcons;
