# GPIMS (Government Polytechnic Inventory Management System)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](#license)  
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](#tech-stack)  
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](#tech-stack)  

> A role‑based inventory management system built for Government Polytechnic Dehradun.  

---

## Table of Contents

1. [Overview](#overview)  
2. [Features](#features)  
3. [User Roles & Permissions](#user-roles--permissions)  
4. [Tech Stack](#tech-stack)  
5. [Getting Started](#getting-started)  
   - [Prerequisites](#prerequisites)  
   - [Installation](#installation)  
   - [Database Setup](#database-setup)  
   - [Running the App](#running-the-app)  
6. [Usage](#usage)  
8. [Data Flow Diagram (DFD)](#data-flow-diagram-dfd)  

---

## Overview

GPIMS is a web‑based inventory management system designed to streamline how Government Polytechnic Dehradun tracks and manages its equipment, consumables, and lab assets. It enforces a four‑level role hierarchy to ensure proper authorization and accountability.

---

## Features

- **Role‑based Access Control**  
- **CRUD operations** for inventory items  
- **Real‑time stock updates**  
- **Audit trail** of all inventory transactions  
- **Responsive UI** built with Bootstrap  
- **Secure authentication** and session management  

---

## User Roles & Permissions

| Role                | Description                                                                                  | Permissions                                |
|---------------------|----------------------------------------------------------------------------------------------|--------------------------------------------|
| **Inventory Manager** | Entry‑level staff who add/update items at the departmental level.                            | Create, Read, Update, Delete own entries. |
| **Inventory Officer** | Mid‑level officer whose approval is required for critical operations (e.g., bulk delete).    | All Manager permissions + approve changes.|
| **Admin (Principal)** | Full authority over the system—can manage users, override approvals, and view all reports.   | All permissions.                           |
| **Lab In‑Charge**     | Manages inventory for a specific lab (e.g., Computer Lab, Electronics Lab).                 | CRUD within assigned lab only.            |

---

## Tech Stack

- **Server-side:** PHP (via XAMPP)  
- **Database:** MySQL  
- **Front-end:** HTML5, CSS3, Bootstrap 4, JavaScript (ES6)  
- **Development Environment:** XAMPP (Apache + PHP + MySQL)  
- **Version Control:** Git & GitHub  

---

### Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) installed  
- PHP ≥ 7.4  
- MySQL ≥ 5.7  
- Git  

### Installation

1. **Clone the repo**  
   ```bash
   git clone https://github.com/Suraj-1309/GPIMS.git
   cd GPIMS










## 📦Goverment Polytechnic Inventory Management System – Data Flow Diagram (DFD)

This repository includes a detailed **Data Flow Diagram (DFD)** for the Inventory Management System.

### 🧭 Visual Flow (DFD Preview)

![GPIMS Workflow DFD](./images/diagram-export-6-4-2025-10_13_57-am.png)

> 📝 This image represents the end-to-end flow of stock management, approvals, allotment, returns, and lab handling.

---

### 📁 Diagram Contents

- 🧑‍💼 **Users**: Stock Manager, Admin, Officer, Lab Incharge
- 📥 **Inputs**: Add Items, RR Received
- 🗄️ **Databases**: Inventory Items Table, Allotment, RR Received Items Table
- 🌀 **Processes**: Accept, Reject, Return Item, Deprecate
- 🖥️ **Web Pages**: Return Req Cancel Page, Lab Use

---
