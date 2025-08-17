
<p align="center"> <a href="https://www.testcosa.com" target="_blank"> <img src="https://www.testcosa.com/images/logo.png" alt="TESTCOSA" height="80"> </a> </p> <h3 align="center">TESTCOSA — Store (Inventory & Certification System)</h3> <p align="center"> Internal platform for inventory, work orders, inspections, and certification PDFs.<br/> Built with Laravel & Filament. </p>
Table of Contents

    About

    Key Features

    Tech Stack

    Screenshots

    Getting Started

        Requirements

        Setup

        Local Development

    Configuration

    Deployment

    Contributing

    Security

    License

    Contact

About

TESTCOSA Store is the company’s internal web application for:

    Managing inventory (tools, consumables, calibration, and stock).

    Handling work orders for third-party inspections.

    Recording checklists and generating professional certificates (PDF) for equipment (e.g., Crown Block, Traveling Block, Dead Line Anchor).

    Managing users, roles, and permissions across panels.

This repository is private and intended for TESTCOSA operational use.
Key Features

    Work Orders: Link inspections and certificates to specific WOs.

    Certification Modules: Crown Block (CB), Traveling Block (TB), Dead Line Anchor (DLA).

    Checklists: Structured accept/reject/NA with notes.

    PDF Certificates: Professional, branded output via mPDF (draft/final).

    Inventory & Stock: Categories, items, requests, and (optional) calibration tracking.

    Admin Panel: Filament-powered UI with roles/permissions (Filament Shield).

    Logs & Audit: Activity logging for key actions.

    Note: DLA follows the CB/TB UX but does not include readings.

Tech Stack

    Backend: PHP 8.3+, Laravel 10/11/12 (project dependent)

    Admin UI: FilamentPHP

    Auth/Permissions: Filament Shield / Spatie Permissions

    Views: Blade (and optional React/Inertia where used)

    PDF: mPDF

    DB: MySQL/MariaDB
