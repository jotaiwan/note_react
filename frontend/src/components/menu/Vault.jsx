import React, { useEffect, useRef } from 'react';
import { getResouceKeyValue } from "../../services/credential";
import { copyToClipboard } from "../../assets/helpers/clipboardHelper";
import vaultIcon from "../../assets/img/vault.png";

/* ---------------- VaultLink ---------------- */
const VaultLink = ({ label, href, onClick }) => {
    return (
        <a
            className="nav-link py-1 small text-white"
            href={href}
            target="_blank"
            rel="noopener noreferrer"
            onClick={onClick}
        >
            {label}
        </a>
    );
};

/* ---------------- VaultNav ---------------- */
export function VaultNav() {

    // Store secret in memory (no re-render)
    const secretRef = useRef(null);

    // Fetch secret once
    useEffect(() => {
        getResouceKeyValue("vault", "int")
            .then(secretFromApi => {
                secretRef.current = secretFromApi;
            })
            .catch(err => console.error(err));
    }, []);

    // Copy + open Vault
    const handleVaultClick = async (e) => {
        e.preventDefault();

        if (!secretRef.current) {
            console.error("Vault token not loaded");
            return;
        }

        console.log("Copying token...", secretRef.current);


        try {
            await copyToClipboard(secretRef.current);
            window.open(
                "https://vault.common.int.viator.com/ui/vault/auth?with=token",
                "_blank",
                "noopener,noreferrer"
            );
        } catch (err) {
            console.error("Failed to copy token:", err);
        }
    };

    return (
        <li className="nav-item">
            <div className="nav-heading bg-danger p-2 mb-1 rounded small">
                <div className="d-flex align-items-center">
                    <img
                        className="img-thumbnail me-3"
                        src={vaultIcon}
                        width="35"
                        height="35"
                        alt="Vault Icon"
                    />

                    <VaultLink
                        label="INT"
                        href="https://vault.com/ui/vault/auth?with=token"
                        onClick={handleVaultClick}
                    />

                    <span className="separator mx-2 text-white">·</span>

                    <VaultLink
                        label="Confluence"
                        href="https://confluence.viator.com/pages/viewpage.action?spaceKey=TO&title=Adding+secrets+into+Vault"
                    />
                </div>
            </div>
        </li>
    );
}
