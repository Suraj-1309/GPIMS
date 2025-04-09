# GPIMS (Government Polytechnic Inventory Management System)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](#license)  
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](#tech-stack)  
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](#tech-stack)  



> **Government Polytechnic Dehradun Inventory Management System**  
> A role‑based inventory management system built for Government Polytechnic Dehradun.  
> An initiative to modernize and optimize the resource management of technical educational institutes.

---
<br>

## Table of Contents
1. [Overview](#overview)  
2. [Features](#features)  
3. [User Roles & Permissions](#user-roles--permissions)  
4. [Tech Stack](#tech-stack)  
5. [Getting Started](#getting-started)  
   - [Prerequisites](#prerequisites)  
   - [Installation](#installation)
6. [Data Flow Diagram (DFD)](#data-flow-diagram-dfd)  

---
<br>

## Overview

**GPIMS (Government Polytechnic Inventory Management System)** is a comprehensive, web-based inventory management platform developed for **Government Polytechnic Dehradun**. It is designed to streamline how the institute tracks, manages, and regulates its wide range of assets including **equipment**, **consumables**, and **lab resources** across different departments and branches.

### Project Overview

GPIMS replaces traditional paper-based workflows with a **centralized digital system**, offering enhanced **transparency**, **accountability**, and **efficiency** in inventory management. It supports real-time data updates, structured reporting, and seamless coordination between different institutional roles and responsibilities.

### Role-Based Access Control

The system is built with a **four-tier role hierarchy** to ensure structured operations and secure access to data and functionality:

#### 1. Inventory Manager
- Manages the overall inventory system.
- Handles item entries and updates.
- Coordinates transfers between departments.
- Ensures accuracy and integrity of stock records.

#### 2. Inventory Officer
- Verifies requests and stock updates.
- Generates and reviews inventory reports.
- Monitors daily inventory operations.

#### 3. Admin (Principal)
- Holds the highest level of authorization.
- Approves critical reports and update requests.
- Oversees departmental inventory data and compliance.

#### 4. Lab Incharge (Branch Level)
- Manages department-specific inventory.
- Requests new items and consumables.
- Tracks usage, availability, and condition of assets.

<br>

##  Features

-  Centralized item and inventory tracking
-  Role-based login and authorization
-  Department-wise stock management
-  Report generation and analytics
-  Real-time updates and usage logs
-  Request, approval, and transfer system
-  Easy-to-use and responsive interface
- **Real‑time stock updates**  
- **Audit trail** of all inventory transactions  
- **Responsive UI** built with Bootstrap  
- **Secure authentication** and session management  

## 🏫 Target Institution

> **Government Polytechnic Dehradun**  
> An initiative to modernize and optimize the resource management of technical educational institutes.

---
<br>

## User Roles & Permissions

| Role                | Description                                                                                  | Permissions                                |
|---------------------|----------------------------------------------------------------------------------------------|--------------------------------------------|
| **Inventory Manager** | Entry‑level staff who add/update items at the departmental level.                            | Create, Read, Update, Delete own entries. |
| **Inventory Officer** | Mid‑level officer whose approval is required for critical operations (e.g., bulk delete).    | All Manager permissions + approve changes.|
| **Admin (Principal)** | Full authority over the system—can manage users, override approvals, and view all reports.   | All permissions.                           |
| **Lab In‑Charge**     | Manages inventory for a specific lab (e.g., Computer Lab, Electronics Lab).                 | CRUD within assigned lab only.            |

---
<br>

## Tech Stack

- **Server-side:** PHP (via XAMPP)  
- **Database:** MySQL  
- **Front-end:** HTML5, CSS3, Bootstrap 4, JavaScript (ES6)  
- **Development Environment:** XAMPP (Apache + PHP + MySQL)  
- **Version Control:** Git & GitHub  

---
<br>

## Getting-Started

Follow the instructions below to set up and run the GPIMS project on your local machine using **XAMPP**.

####  Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html)
- Web browser (Google Chrome, Firefox, etc.)
- PHP ≥ 7.4  
- MySQL ≥ 5.7  
- Git  
- Code editor (e.g., VS Code) - optional but helpful
- Basic understanding of PHP & MySQL

#### Installation

1. Download and install **XAMPP** from the [official website](https://www.apachefriends.org/index.html).
2. Clone or download this repository:
   ```bash
   git clone https://github.com/Suraj-1309/GPIMS.git

<br>

## Data Flow Diagram (DFD)

This repository includes a detailed **Data Flow Diagram (DFD)** for the Inventory Management System.

###  Visual Flow (DFD Preview)

![GPIMS Workflow DFD](./images/diagram-export-6-4-2025-10_13_57-am.png)

>  This image represents the end-to-end flow of stock management, approvals, allotment, returns, and lab handling.

---

####  Diagram Contents

- **Users**: Stock Manager, Admin, Officer, Lab Incharge
- **Inputs**: Add Items, RR Received
- **Databases**: Inventory Items Table, Allotment, RR Received Items Table
- **Processes**: Accept, Reject, Return Item, Deprecate
- **Web Features**: Return Req Cancel Page, Lab Use

---
