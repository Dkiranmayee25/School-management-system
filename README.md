# 🎓 School Management System

A comprehensive **School Management System** built using **PHP, MySQL, HTML5, CSS3, JavaScript, and XAMPP**. The project provides a centralized platform for managing academic and administrative activities through dedicated dashboards for **Students**, **Teachers**, and **Administrators**.

In addition to the management system, the project includes a **Management Portal** that serves as a business website where educational institutions can explore the platform, request customized solutions, and submit project enquiries.

---

## 📌 Project Overview

The application is divided into two major modules:

### 🌐 Management Portal

The Management Portal serves as the public-facing website for the platform.

It includes the following sections:

- 🏠 Home
- ℹ️ About
- 💻 Demo
- 📩 Contact

The **Demo** section redirects visitors to the complete School Management System.

The **Contact** section allows schools and educational institutions to submit enquiries regarding customized website or management system development. All enquiries are securely stored in the database, enabling the development team to review client requirements and respond through appropriate communication channels.

---

### 🏫 School Management System

The School Management System provides secure **role-based authentication** with dedicated dashboards for different users.

Supported roles:

- 👨‍🎓 Student
- 👨‍🏫 Teacher
- 👨‍💼 Administrator

Each role has access only to the features relevant to its responsibilities.

---

# 🚀 System Workflow

```text
Management Portal
│
├── Home
├── About
├── Contact
│      │
│      ▼
│  Enquiry Database
│
└── Demo
       │
       ▼
School Management System
       │
 ┌─────┼─────────────┐
 ▼     ▼             ▼
Student Teacher     Admin
 │      │             │
 └──────┼─────────────┘
        │
        ▼
 Centralized MySQL Database
```

---

# ✨ Features

## 🌐 Management Portal

- Responsive landing page
- About section
- Demo section
- Contact page
- Database-driven enquiry management system

---

## 👨‍🎓 Student Dashboard

Students can:

- View examination results
- Track attendance
- View notices
- Monitor their academic performance

---

## 👨‍🏫 Teacher Dashboard

Teachers can:

- View school notices
- Access timetable
- Upload student marks
- Access teaching resources and study materials
- Monitor assigned students

---

## 👨‍💼 Administrator Dashboard

Administrators can:

- Add, update, and remove students
- Add, update, and remove teachers
- Publish examination results
- Upload attendance records
- Monitor attendance reports
- Publish, edit, and delete notices
- Manage overall school information

---

# 🔄 Centralized Data Management

The application uses a centralized MySQL database to ensure that information is shared efficiently across different user roles.

Examples include:

- Notices published by the administrator are visible to students and teachers.
- Attendance uploaded by the administrator is reflected in student dashboards.
- Results published by the administrator become available to students.
- Marks uploaded by teachers are managed through the centralized system.

This architecture ensures consistency and efficient information sharing throughout the application.

---

# 📊 Student Performance Monitoring

Students can monitor their academic progress through a dedicated dashboard by accessing:

- Examination Results
- Attendance Records
- Academic Performance
- School Notices

---

# 💻 Technology Stack

### Frontend

- HTML5
- CSS3
- JavaScript

### Backend

- PHP

### Database

- MySQL

### Development Environment

- XAMPP

---

# 🛠️ Core Concepts

- Role-Based Access Control (RBAC)
- User Authentication
- Session Management
- CRUD Operations
- Relational Database Design
- Dynamic Content Rendering
- Responsive Web Design
- Multi-user Dashboard Architecture

---

# 📂 Project Structure

```text
school-management-system/
│
├── admin/
├── teacher/
├── student/
├── css/
├── js/
├── images/
├── includes/
├── database/
│   └── school_management.sql
├── index.php
├── login.php
├── connect.php
├── README.md
└── ...
```

---

# ⚙️ Installation

## Prerequisites

- XAMPP
- PHP
- MySQL
- Web Browser

## Steps

1. Clone the repository

```bash
git clone https://github.com/yourusername/school-management-system.git
```

2. Move the project folder to:

```text
C:\xampp\htdocs\
```

3. Start **Apache** and **MySQL** using XAMPP.

4. Open **phpMyAdmin**.

5. Create a new database.

6. Import the SQL file located inside the `database` folder.

7. Update the database connection settings if required.

8. Launch the application:

```text
http://localhost/school-management-system/
```

---

# 🎯 Learning Outcomes

This project strengthened my understanding of:

- Full-stack web development
- PHP backend development
- MySQL database integration
- Authentication and authorization
- Session handling
- CRUD operations
- Responsive web design
- Developing role-based web applications

---

# 🚀 Future Enhancements

- Parent Portal
- Online Fee Management
- Assignment Submission
- Library Management
- Online Examination Module
- Email Notifications
- Password Recovery
- Analytics Dashboard
- Mobile Optimization

---

# 👩‍💻 Author

**Derangula Kiranmayee**

B.Tech – Computer Science Engineering  
Presidency University, Bengaluru

- GitHub: https://github.com/your-github-username
- LinkedIn: https://linkedin.com/in/your-linkedin-profile

---

## ⭐ Support

If you found this project useful, consider giving it a ⭐ on GitHub.
