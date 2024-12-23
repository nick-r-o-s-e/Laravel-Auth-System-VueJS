# Laravel-VueJS


## Table of Contents
* [About](#about)
* [Getting Started](#getting-started)
* [Key Features](#key-faetures)
* [Project Status](#project-status)

## About

This project demonstrates the implementation of an Authentication and Authorization system integrated with a Vue.js 3 frontend. It also includes a dynamic registration form with dependent fields.

# Key Features
### Backend
- RESTful API for user management, authentication, and dependent data handling.
- Database migrations and seeders for a quick setup and testing environment.
- Validation for API requests.

### Authentication and Authorization
- User registration, login, and logout  with secure session management via Laravel Sanctum.
- Laravel Sanctum-based API authentication.
- Role-based access control different user permissions on both the frontend and backend.

### Dynamic Dependent Fields
- Country and Language Selection: The registration form dynamically updates the available languages based on the selected country.
- Countries and languages tables are seeded from a JSON file stored in private storage, ensuring quick and accurate data population.

### Frontend: Vue.js 3
- Form validation using yup for client-side validation.
- Protective routes and components that handle loading and error states, ensuring a smooth user experience during data fetching and form submissions.

# Getting started

## Follow these steps to set up and run the project locally:

### Prerequisites
- Ensure MySQL is installed and running locally.
- Install Node.js and npm.
- Install Composer for managing PHP dependencies.

## Backend Setup
#### Clone the repository:
```
git clone https://github.com/nick-r-o-s-e/Laravel-Auth-System-VueJS  
```
#### Navigate to the backend folder:
```
cd backend-laravel  
```

#### Install PHP dependencies:
```
composer install  
```

#### Set up the environment file:
- Create a .env file in the back folder.
- Add YOUR database connection settings (e.g., database name, username, and password).
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_crud
DB_USERNAME=root
DB_PASSWORD=
```
#### Run database migrations:
```
php artisan migrate  
```

#### Seed the database (optional):
```
php artisan db:seed  
```

#### Start the server:
```
php artisan serve  
```

The backend server will be available at http://127.0.0.1:8000.

## Frontend Setup
#### Navigate to the frontend folder:
```
cd frontend-vue  
```

#### Install npm modules:
```
npm install  
```

#### Start the development server:
```
npm run dev  
```
The frontend application will run on a local development server, usually available at http://localhost:5173.

#### Access the Application
 1. Open the frontend application in your browser at http://localhost:5173.
 2. The frontend will communicate with the backend API running at http://127.0.0.1:8000.

## Project Status
Project is in progress


