# 🎓 College Events Portal - MCE Hassan

A modern, responsive web application for discovering, organizing, and registering for campus events at **Malnad College of Engineering (MCE), Hassan**.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/CSS3-Modern_Responsive-1572B6?logo=css3&logoColor=white)

---

## 🌟 Overview

The **College Events Portal** is built to streamline student participation and administrative management for major college fests and student club initiatives. It provides dedicated role-based workflows for **Students**, **Event Organizers**, and **Teachers / Faculty Advisors**.

### 🏛️ Featured Campus Activities & Clubs
- **Aavishkar Manthan (Edition 4.0):** Organized by the **IUCEE-EWB MCE Student Chapter** (National Ideathon, National Elocution, Mega Social Awareness Walkathon, and Cultural Bonfire Night).
- **ENIGMA:** State-Level Technical Symposium by the **Malnad Technical Club (MTC)** featuring *Robokombat* and *MCE Dollars (Virtual Stock Trading)*.
- **The Malnad Fest:** Cultural extravaganza hosted by **The Literary Club** featuring *Darpan (Campus Beats Dance Battle)*, *Kalakrithi*, and *Buddhimaan*.
- **Hack-MCE:** 24-Hour Smart Campus Hackathon organized by the **FOSS Club** & CSE Department.
- **Community Initiatives:** Hands-on science and tech workshops conducted by **SoUL (Student Organisation for Upliftment)**.

---

## ✨ Features

- **Role-Based Authentication:**
  - **Student:** Register for solo and team events, view registration status in "My Registrations", cancel signups.
  - **Organizer:** Create, update, and delete campus events, configure solo or team registration formats (with custom team sizes), and manage rosters.
  - **Teacher / Faculty:** Review attendee rosters with real-time filters by department (CSE, ISE, ECE, EEE, ME, CE, AIML) and academic year (1st to 4th year).
- **Team Event Support:** Captains register their squad with teammate USN validation; members can manage their participation.
- **Modern Responsive Design:** Crafted with *Plus Jakarta Sans*, subtle gradients, glassmorphism, event calendar badges, search filtering, and clean data tables.
- **Print & Export Support:** One-click roster printing for event coordinators and faculty.

---

## 🗂️ Project Directory Structure

```plaintext
college-events/
├── app/
│   ├── auth.php              # Authentication functions (signup, login, sessions, roles)
│   ├── config.php            # Database credentials and base URL constants
│   ├── db.php                # PDO database connection wrapper
│   ├── EventModel.php        # Event CRUD operations and database queries
│   ├── Registration.php      # Registration helper methods
│   └── AttendanceModel.php   # Attendance and tracking model
├── assets/
│   ├── css/
│   │   └── style.css         # Modern design system stylesheet
│   └── js/
│       └── app.js            # Front-end interactions
├── public/
│   ├── api/
│   │   ├── auth.php          # Organizer event creation API endpoint
│   │   ├── event.php         # Event actions endpoint
│   │   ├── register.php      # Individual registration handler
│   │   ├── unregister.php    # Individual unregistration handler
│   │   ├── team_register.php # Team registration handler
│   │   └── team_unregister.php # Leave team handler
│   ├── dashboard.php         # Organizer dashboard (Create / Edit event)
│   ├── events.php            # Browse events catalog with search & category filters
│   ├── event_view.php        # Detailed event view and registration modal/form
│   ├── index.php             # Home landing page with hero and featured events
│   ├── login.php             # Account login page
│   ├── participants.php      # Participant roster with Department & Year filters
│   ├── register.php          # New user account registration
│   ├── registered.php        # Student personal dashboard ("My Registrations")
│   └── remove_participant.php# Participant removal handler
├── uploads/
│   └── certificates/         # Directory for generated event certificates
├── views/
│   └── partials/
│       └── nav.php           # Global responsive navigation header
├── index.php                 # Root entry router (redirects to public/)
├── schema.sql                # Complete database structure and schema
└── README.md
```

---

## 🚀 Getting Started

### Prerequisites
- **PHP** 8.0 or newer
- **MySQL / MariaDB** (via XAMPP, WAMP, Laragon, or standalone)
- **Git**

### Installation Steps

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yashums-05/college-events.git
   cd college-events
   ```

2. **Configure Database:**
   - Start your MySQL / MariaDB service.
   - Open phpMyAdmin or your MySQL CLI and create the database:
     ```sql
     CREATE DATABASE college_events;
     ```
   - Import the database schema from `schema.sql`:
     ```bash
     mysql -u root -p college_events < schema.sql
     ```

3. **Configure Environment Settings:**
   - Open [`app/config.php`](app/config.php) and verify your MySQL host, port, and credentials:
     ```php
     define('DB_HOST', 'localhost'); // or '127.0.0.1'
     define('DB_PORT', 3306);        // adjust if using a custom port (e.g. 3307)
     define('DB_NAME', 'college_events');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('BASE_URL', '/college-events/public/');
     ```

4. **Run the Application:**
   - **Using PHP Built-in Server:**
     ```bash
     php -S localhost:8085
     ```
     Access the application in your browser at:
     **[http://localhost:8085](http://localhost:8085)**

   - **Using XAMPP Apache:**
     - Copy or move the project folder into your `htdocs` directory (e.g., `C:/xampp/htdocs/college-events`).
     - Access via: **[http://localhost/college-events/public/](http://localhost/college-events/public/)**

---

## 👥 User Roles

| Role | Permissions & Capabilities |
| :--- | :--- |
| **Student** | Browse upcoming events, filter by solo/team format, register individually or invite teammates, view registered events. |
| **Organizer** | Publish new events, set team minimum/maximum rules, edit/delete managed events, review attendee rosters. |
| **Teacher** | Review and filter participant lists across departments (CSE, ISE, ECE, EEE, ME, CE, AIML) and academic years; export rosters. |

---

## 📱 Connected Student Chapters & Handles

- **IUCEE-EWB MCE Student Chapter:**
  - Instagram: [@mce_iucee_ewb](https://www.instagram.com/mce_iucee_ewb/)
  - Email: `iuceeewb.mce@gmail.com`
- **Institution:** Malnad College of Engineering (MCE), Hassan, Karnataka, India

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
