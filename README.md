# 🏛️ Visitor Management System - Ministère de la Justice

A comprehensive web application for managing visitors at the Ministry of Justice. This system enables staff members (admins and agents) to register visitors, track their status, generate reports, and maintain a complete history of visits.

## 📋 Table of Contents

- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Project Structure](#-project-structure)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Endpoints](#-api-endpoints)
- [User Roles & Permissions](#-user-roles--permissions)
- [Features Overview](#-features-overview)

## ✨ Features

### 🔐 Authentication & Authorization
- User registration and login
- Role-based access control (Admin & Agent)
- Secure token-based authentication using Laravel Sanctum
- Protected routes with middleware

### 👥 Visitor Management
- **Add Visitors**: Register new visitors with CIN, name, phone, and reason for visit
- **View Visitors**: Display all visitors in a comprehensive dashboard
- **Edit Visitors**: Admins can modify visitor information
- **Delete Visitors**: Admins can remove visitors from the system
- **Status Tracking**: Track visitor status (En attente / Entré / Sorti)
- **Search & Filter**: Quickly find visitors by name or CIN

### 📊 Statistics & Reporting
- Total visitors count
- Visits today
- Visits this month
- Monthly visit trends (line chart visualization)
- Most frequent visitors
- Visits per agent performance metrics

### 📈 History & Analytics
- Complete visitor history with advanced filtering
- Filter by status (En attente, Entré, Sorti)
- Filter by date range (start date & end date)
- Search by name or CIN
- Chronological ordering of visits

### 📄 Data Export
- **PDF Export**: Generate PDF reports of visitor lists
- **CSV Export**: Export visitor data to CSV format for external analysis

### 🎨 User Interface
- Modern, responsive design using Tailwind CSS
- Intuitive navigation
- Real-time notifications with toast messages
- Interactive charts and visualizations using Recharts

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 12
- **PHP Version**: 8.2+
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/MariaDB
- **API**: RESTful API

### Frontend
- **Framework**: React 19
- **Build Tool**: Vite 6
- **Routing**: React Router DOM 7
- **HTTP Client**: Axios
- **Styling**: Tailwind CSS 4
- **Charts**: Recharts
- **Notifications**: React Toastify
- **PDF Generation**: jsPDF + jspdf-autotable
- **CSV Export**: PapaParse

## 📁 Project Structure

```
Project01/
├── backend/                 # Laravel backend application
│   ├── app/
│   │   ├── Http/
│   │   │   └── Middleware/
│   │   │       └── AdminMiddleware.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   └── Visitor.php
│   │   └── Providers/
│   ├── config/              # Configuration files
│   ├── database/
│   │   ├── migrations/      # Database migrations
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php          # API routes
│   ├── public/              # Public assets
│   └── composer.json
│
├── frontend/                # React frontend application
│   ├── src/
│   │   ├── components/      # Reusable components
│   │   │   ├── AddVisitorModal.jsx
│   │   │   └── ProtectedRoute.jsx
│   │   ├── pages/           # Page components
│   │   │   ├── Dashboard.jsx
│   │   │   ├── History.jsx
│   │   │   ├── Login.jsx
│   │   │   ├── Home.jsx
│   │   │   └── EditVisitor.jsx
│   │   ├── photos/          # Image assets
│   │   ├── App.jsx          # Main app component
│   │   └── main.jsx         # Entry point
│   ├── public/
│   └── package.json
│
├── rapports/                # Documentation files
├── visitors.sql             # Database dump (optional)
├── Procfile                 # Deployment configuration
└── README.md                # This file
```

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** (PHP dependency manager)
- **Node.js** >= 18.x and **npm**
- **MySQL** or **MariaDB**
- **Web Server** (Apache/Nginx) or use PHP built-in server

## 🚀 Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd Visitor/Project01
```

### 2. Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env file (see Configuration section)
# Then run migrations
php artisan migrate

# (Optional) Seed the database
php artisan db:seed
```

### 3. Frontend Setup

```bash
cd frontend

# Install Node dependencies
npm install
```

### 4. Configuration

#### Backend Configuration (.env)

Edit the `backend/.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_visiteurs
DB_USERNAME=your_username
DB_PASSWORD=your_password

APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
```

#### Frontend Configuration

Update the API base URL in frontend files if needed (currently set to `http://127.0.0.1:8000`).

## ▶️ Usage

### Development Mode

#### Start Backend Server

```bash
cd backend
php artisan serve
```

The backend API will be available at `http://localhost:8000`

#### Start Frontend Development Server

```bash
cd frontend
npm run dev
```

The frontend will be available at `http://localhost:5173`

### Production Build

#### Build Frontend

```bash
cd frontend
npm run build
```

The optimized build will be in the `frontend/dist` directory.

#### Serve Backend

For production, configure your web server (Apache/Nginx) to point to the `backend/public` directory.

## 🔌 API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/register` | Register new user | No |
| POST | `/api/auth/login` | User login | No |
| POST | `/api/auth/logout` | User logout | Yes |
| GET | `/api/auth/user` | Get authenticated user | Yes |

### Visitors

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/visitors` | Get all visitors | Yes |
| GET | `/api/visitors/{id}` | Get visitor by ID | Yes |
| POST | `/api/visitors` | Create new visitor | Yes |
| PUT | `/api/visitors/{id}` | Update visitor | Yes (Admin) |
| DELETE | `/api/visitors/{id}` | Delete visitor | Yes (Admin) |
| PUT | `/api/visitors/{id}/status` | Update visitor status | Yes |

### Statistics & History

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/visitor-stats` | Get visitor statistics | Yes |
| GET | `/api/visitor-history` | Get visitor history with filters | Yes |

**Query Parameters for `/api/visitor-history`:**
- `status`: Filter by status (En attente, Entré, Sorti)
- `start_date`: Start date for date range filter
- `end_date`: End date for date range filter
- `search`: Search by name or CIN

## 👤 User Roles & Permissions

### Admin
- ✅ Full CRUD operations on visitors
- ✅ Edit visitor information
- ✅ Delete visitors
- ✅ View statistics and reports
- ✅ Access history with all filters
- ✅ Export data (PDF/CSV)

### Agent
- ✅ Add new visitors
- ✅ View all visitors
- ✅ Update visitor status (En attente → Entré → Sorti)
- ✅ View statistics and reports
- ✅ Access history with all filters
- ✅ Export data (PDF/CSV)
- ❌ Cannot edit visitor information
- ❌ Cannot delete visitors

## 📖 Features Overview

### Dashboard
- Real-time statistics cards (Total visitors, Visits today, Visits this month)
- Interactive line chart showing monthly visit trends
- Complete visitor list with search functionality
- Quick actions based on user role (Edit/Delete for Admin, Status update for Agent)

### History Page
- Comprehensive visitor history
- Advanced filtering options:
  - Filter by status
  - Filter by date range
  - Search by name or CIN
- Chronological display of all visits

### Data Export
- **PDF Export**: Generates a formatted PDF with visitor information in a table
- **CSV Export**: Exports visitor data in CSV format compatible with Excel and other spreadsheet software

### Visitor Status Flow
1. **En attente** (Pending) - Initial status when visitor is registered
2. **Entré** (Entered) - Visitor has entered the premises
3. **Sorti** (Exited) - Visitor has left the premises

## 🔒 Security Features

- Password hashing using bcrypt
- Token-based authentication (Laravel Sanctum)
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection
- Role-based access control middleware

## 🧪 Testing

Run backend tests:
```bash
cd backend
php artisan test
```

## 📝 Database Schema

### Users Table
- `id` (Primary Key)
- `name`
- `email` (Unique)
- `password` (Hashed)
- `role` (admin/agent)
- `timestamps`

### Visitors Table
- `id` (Primary Key)
- `name`
- `cin` (Unique - National ID)
- `phone`
- `reason` (Reason for visit)
- `status` (En attente/Entré/Sorti)
- `user_id` (Foreign Key → users.id)
- `timestamps`

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is part of an internship/work project for the Ministry of Justice.

## 👨‍💻 Development Notes

- The backend uses Laravel 12 with PHP 8.2+
- The frontend uses React 19 with modern hooks and functional components
- API communication uses Axios with Bearer token authentication
- State management is handled via React hooks (useState, useEffect)
- The application follows RESTful API principles

## 📞 Support

For issues, questions, or contributions, please contact the development team or create an issue in the repository.

---

**Built with ❤️ for the Ministère de la Justice**
