# Dynamic Class Management Application - Starter Template

A starter template for building a web-based class management system for educational institutions.

## Project Overview

Build a comprehensive class management system that enables:
- **Lecturers**: Create and manage classes, track attendance, assign grades
- **Students**: Enroll in classes, view schedules, track progress

## Your Mission

Complete this starter template by implementing the required features outlined below. This is your capstone project - demonstrate your PHP, MySQL, and web development skills!

## What's Provided

- Basic project structure
- Database schema outline
- Minimal configuration file
- Placeholder pages
- Basic CSS starter

## What You Need to Build

### Phase 1: Authentication System
- [x] Implement user login with username/password
- [x] Create session management
- [x] Add role-based access control (lecturer vs student)
- [x] Implement logout functionality
- [x] Add password hashing for security

### Phase 2: Lecturer Features
- [x] Create class creation form
- [x] Display lecturer's classes
- [x] Show enrolled students per class
- [x] Implement grade assignment system
- [x] Build attendance tracking feature
- [x] Add class deletion capability

### Phase 3: Student Features
- [x] Display available classes
- [x] Implement class enrollment
- [x] Show enrolled classes
- [x] Display class schedules
- [x] Show attendance records
- [x] Display grades

### Phase 4: UI/UX Enhancement
- [x] Style the login page
- [x] Create responsive dashboards
- [x] Design class cards
- [x] Add form validation
- [x] Implement error/success messages
- [x] Make it mobile-friendly

## Database Requirements

Create tables for:
- **users**: Store student and lecturer accounts
- **classes**: Course information and schedules
- **enrollments**: Link students to classes
- **attendance**: Track student attendance

## Technical Requirements

- PHP 7.4+
- MySQL 5.7+
- Secure coding practices (prevent SQL injection, XSS)
- Session-based authentication
- Responsive design

## Getting Started

1. Set up your local server (XAMPP/WAMP)
2. Create the database using `database/dcma_template.sql`
3. Configure database connection in `config/config.php`
4. Start building features incrementally
5. Test thoroughly with different user roles

## Evaluation Criteria

Your project will be evaluated on:
- **Functionality**: All features work as expected
- **Code Quality**: Clean, organized, well-commented code
- **Security**: Proper input validation and sanitization
- **UI/UX**: Professional, user-friendly interface
- **Database Design**: Normalized, efficient schema
- **Documentation**: Clear README and code comments

## Tips for Success

1. Start with the database schema - get it right first
2. Build authentication before other features
3. Test each feature thoroughly before moving on
4. Use prepared statements or proper escaping for SQL
5. Keep your code DRY (Don't Repeat Yourself)
6. Comment your code for clarity
7. Handle errors gracefully
8. Make the UI intuitive and attractive

## Demo Credentials (After Implementation)

You should create these test accounts:
- Lecturer: john_lecturer / password123
- Student: alice_student / password123

## Resources

- PHP Documentation: https://www.php.net/docs.php
- MySQL Documentation: https://dev.mysql.com/doc/
- W3Schools PHP Tutorial: https://www.w3schools.com/php/
- MDN Web Docs: https://developer.mozilla.org/

## Submission Guidelines

1. Complete all required features
2. Test with multiple user accounts
3. Document any additional features you added
4. Include setup instructions
5. Prepare a demo presentation

## Support

Consult with your instructor or peers if you encounter challenges. Remember, this is a learning experience!

Good luck with your capstone project! 🚀
