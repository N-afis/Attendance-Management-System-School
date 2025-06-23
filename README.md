# 📚 Attendance Management System

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive full-stack attendance management system designed for educational institutions. Built with modern web technologies including PHP (OOP & PDO), MySQL, JavaScript, and Bootstrap for a responsive and intuitive user experience.

![Attendance System Dashboard](assets/images/Screenshot%202025-06-23%20175029.png)
![Attendance System Dashboard](assets/images/Screenshot%202025-06-23%20191014.png)
![Attendance System Dashboard](assets/images/Screenshot%202025-06-23%20191026.png)
![Attendance System Dashboard](assets/images/Screenshot%202025-06-23%20191038.png)
![Attendance System Dashboard](assets/images/Screenshot%202025-06-23%20191047.png)
![Attendance System Dashboard](assets/images/Screenshot%202025-06-23%20191053.png)
![Attendance System Dashboard](assets/images/Screenshot%202025-06-23%20191100.png)

## ✨ Features

### 🎯 Core Functionality
- **Student & Teacher Attendance Tracking** - Real-time attendance monitoring
- **Absence Justification System** - Upload and manage PDF justification documents
- **Admin Profile Management** - Complete user profile control
- **Dynamic Dashboard** - Interactive statistics and analytics
- **Excel Bulk Upload** - Import students and teachers via Excel files
- **Advanced Search & Filtering** - Smart filtering with pagination
- **Responsive Design** - Mobile-friendly interface

### 📊 Dashboard Analytics
- Real-time attendance statistics
- Monthly/weekly attendance trends
- Student and teacher performance metrics
- Absence rate analysis

### 📱 User Experience
- Clean, modern Bootstrap 5 interface
- Mobile-responsive design
- Intuitive navigation and user flows
- Toast notifications for user feedback

## 🚀 Quick Start

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Sa3d-Ka/Attendance-Management-System-School.git
   cd Attendance-Management-System-School
   ```

2. **Database Setup**
   - Create a new MySQL database (e.g., `attendancedb`)
   - Import the SQL schema:
     ```bash
     mysql -u your_username -p attendancedb < database/attendance_system.sql
     ```

3. **Environment Configuration**
   - Copy the environment template:
     ```bash
     cp .env.example .env
     ```
   - Update `.env` with your database credentials:
     ```env
     DB_HOST=localhost
     DB_NAME=attendancedb
     DB_USER=your_username
     DB_PASS=your_password
     ```

4. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/justifications/
   chmod 755 uploads/students/
   chmod 755 uploads/teachers/
   ```

5. **Launch the Application**
   - For XAMPP: Place in `htdocs` folder
   - Access via: `http://localhost/Attendance-Management-System-School/`
   - Default login: Check `database/attendance_system.sql` for default credentials

## 📁 Project Structure

```
Attendance-Management-System-School/
├── 📄 index.php                 # Main entry point
├── 🔐 login.php                 # Authentication page
├── 🚪 logout.php                # Session termination
├── ⚙️ .env                      # Environment configuration
├── 📋 README.md                 # Project documentation
│
├── 🔌 api/                      # REST API endpoints
│   ├── 📊 dashboard/            # Dashboard data endpoints
│   ├── 👥 attendance/           # Attendance management APIs
│   ├── 🎓 student/              # Student management APIs
│   ├── 👨‍🏫 teacher/              # Teacher management APIs
│   └── 📝 records/              # Record management APIs
│
├── 🎨 assets/                   # Static resources
│   ├── 🎭 css/                  # Stylesheets
│   ├── 🖼️ images/               # Images and icons
│   └── ⚡ js/                   # JavaScript files
│       └── 🛠️ utils/            # Utility scripts
│
├── 🏗️ classes/                  # PHP OOP Classes
│   ├── Students.php             # Student management class
│   ├── Teachers.php             # Teacher management class
│   ├── StudentAttendance.php    # Student attendance logic
│   ├── TeacherAttendance.php    # Teacher attendance logic
│   └── Profile.php              # Profile management class
│
├── ⚙️ config/                   # Configuration files
│   ├── config.php               # Application config
│   └── database.php             # Database connection
│
├── 📦 includes/                 # Reusable components
│   ├── header.php               # Common header
│   ├── footer.php               # Common footer
│   ├── toast.php                # Notification component
│   ├── 🎓 student/              # Student-specific includes
│   └── 👨‍🏫 teacher/              # Teacher-specific includes
│
├── 📄 pages/                    # Main application pages
│   ├── dashboard.php            # Main dashboard
│   ├── attendance.php           # Attendance tracking
│   ├── students.php             # Student management
│   ├── teachers.php             # Teacher management
│   ├── records.php              # Attendance records
│   └── admin-profile.php        # Admin profile page
│
└── 📁 uploads/                  # File upload directory
    ├── justifications/          # Absence justification PDFs
    ├── students/               # Student-related uploads
    └── teachers/               # Teacher-related uploads
```

## 🛠️ Technology Stack

### Backend
- **PHP 8.0+** - Server-side logic with OOP principles
- **MySQL** - Relational database management
- **PDO** - Secure database operations
- **Apache/Nginx** - Web server

### Frontend
- **HTML5 & CSS3** - Modern markup and styling
- **Bootstrap 5.3** - Responsive UI framework
- **JavaScript (Vanilla)** - Client-side interactivity
- **SheetJS** - Excel file processing

### Tools & Libraries
- **Font Awesome** - Icon library
- **Chart.js** - Data visualization
- **DataTables** - Advanced table functionality

## 🔧 Configuration

### Database Configuration
Edit `config/database.php` to match your database settings:

```php
<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    
    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'attendancedb';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }
    
    // ... connection logic
}
```

### Environment Variables
Configure your `.env` file:

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=attendancedb
DB_USER=root
DB_PASS=
```

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Make your changes**
4. **Commit your changes**
   ```bash
   git commit -m 'Add some amazing feature'
   ```
5. **Push to the branch**
   ```bash
   git push origin feature/amazing-feature
   ```
6. **Open a Pull Request**

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Saad Kanani** 🇲🇦
- Instagram: [@learntodev.ka](https://www.instagram.com/learntodev.ka/)
- LinkedIn: [Saad Kanani](https://linkedin.com/in/saad-kanani)
- Email: saad.kanani.off@gmail.com

---

<div align="center">
  <p>⭐ Star this repository if you found it helpful!</p>
  <p>Made with ❤️ in Morocco 🇲🇦</p>
</div>