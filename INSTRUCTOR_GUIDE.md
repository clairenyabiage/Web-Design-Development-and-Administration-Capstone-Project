# Instructor Guide - DCMA Starter Template

## Overview

This starter template provides students with a scaffolded structure to build a complete Dynamic Class Management Application. It's designed for a capstone project in web development courses covering PHP, MySQL, and full-stack development.

## Learning Objectives

By completing this project, students will demonstrate:
1. Database design and normalization
2. PHP backend development
3. Session-based authentication
4. CRUD operations
5. SQL query writing
6. Security best practices (SQL injection prevention, XSS protection)
7. Frontend development (HTML, CSS, JavaScript)
8. User experience design
9. Role-based access control
10. Project documentation

## Project Scope

### Estimated Time: 40-60 hours
- Database Design: 4-6 hours
- Authentication System: 6-8 hours
- Lecturer Features: 10-12 hours
- Student Features: 8-10 hours
- UI/UX Development: 8-10 hours
- Testing & Debugging: 4-6 hours

## What's Provided vs What Students Build

### Provided (Starter Template)
- Project folder structure
- Placeholder files with TODO comments
- Basic CSS skeleton
- README with requirements
- Database schema outline

### Students Must Build
- Complete database schema with all tables
- Full authentication system
- All CRUD operations
- Role-based dashboards
- Attendance tracking system
- Grade management
- Enrollment system
- Complete UI/UX styling
- Form validation
- Error handling

## Grading Rubric (Suggested)

### Functionality (40 points)
- [x] Authentication works correctly (8 pts)
- [x] Lecturer can create/manage classes (8 pts)
- [x] Lecturer can manage grades and attendance (8 pts)
- [x] Students can enroll/drop classes (8 pts)
- [x] Students can view their information (8 pts)

### Code Quality (20 points)
- [x] Clean, organized code structure (5 pts)
- [x] Proper commenting and documentation (5 pts)
- [x] Follows PHP best practices (5 pts)
- [x] DRY principle applied (5 pts)

### Security (15 points)
- [x] SQL injection prevention (5 pts)
- [x] XSS protection (5 pts)
- [x] Password hashing implemented (5 pts)

### Database Design (10 points)
- [x] Proper normalization (3 pts)
- [x] Appropriate data types (3 pts)
- [x] Foreign keys and constraints (4 pts)

### UI/UX (10 points)
- [x] Professional appearance (3 pts)
- [x] User-friendly interface (3 pts)
- [x] Responsive design (4 pts)

### Documentation (5 points)
- [x] Clear README with setup instructions (3 pts)
- [x] Code comments where needed (2 pts)

**Total: 100 points**

## Key Implementation Hints for Students

### Phase 1: Database Schema
Students should create:
```sql
users (id, username, password, full_name, email, role, created_at)
classes (id, class_code, class_name, description, lecturer_id, max_students, 
         schedule_day, schedule_time, room, status, created_at)
enrollments (id, student_id, class_id, enrollment_date, status, grade)
attendance (id, enrollment_id, attendance_date, status, notes, marked_at)
```

### Phase 2: Authentication
Key concepts:
- Use `password_hash()` and `password_verify()`
- Store user info in `$_SESSION`
- Create helper functions for access control
- Implement proper logout

### Phase 3: Security
Must implement:
- `mysqli_real_escape_string()` or prepared statements
- `htmlspecialchars()` for output
- Session validation on every protected page
- Role verification before actions

### Phase 4: Features
Core functionality:
- Lecturers: CRUD for classes, update grades, mark attendance
- Students: View available classes, enroll, view grades/attendance
- Both: View schedules, class details

## Common Challenges & Solutions

### Challenge 1: SQL Injection
**Solution**: Teach prepared statements or proper escaping
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
```

### Challenge 2: Session Management
**Solution**: Always start session at top of files that need it
```php
session_start();
```

### Challenge 3: Role-Based Access
**Solution**: Create helper functions
```php
function requireRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: login.php');
        exit();
    }
}
```

### Challenge 4: Complex Queries
**Solution**: Use JOINs for related data
```php
SELECT c.*, u.full_name as lecturer_name, COUNT(e.id) as enrolled
FROM classes c
JOIN users u ON c.lecturer_id = u.id
LEFT JOIN enrollments e ON c.id = e.class_id
GROUP BY c.id
```

## Testing Checklist for Students

### Authentication
- [x] Can login with valid credentials
- [x] Cannot login with invalid credentials
- [x] Redirects to correct dashboard based on role
- [x] Logout works properly
- [x] Cannot access pages without login

### Lecturer Features
- [x] Can create new class
- [x] Can view all their classes
- [x] Can see enrolled students
- [x] Can update grades
- [x] Can mark attendance
- [x] Can delete classes

### Student Features
- [x] Can view available classes
- [x] Can enroll in classes
- [x] Cannot enroll in full classes
- [x] Can view enrolled classes
- [x] Can see grades and attendance
- [x] Can drop classes

### Security
- [x] Passwords are hashed in database
- [x] SQL injection attempts fail
- [x] XSS attempts are escaped
- [x] Users cannot access other roles' pages

## Extension Ideas (Bonus Points)

For advanced students:
1. Assignment submission system
2. Email notifications
3. Class search/filter functionality
4. Export grades to CSV
5. Student profile pictures
6. Class announcements
7. Calendar view
8. Grade analytics/charts
9. Forgot password functionality
10. Admin role for system management

## Resources to Share with Students

### PHP
- PHP Manual: https://www.php.net/manual/
- PHP Security: https://www.php.net/manual/en/security.php

### MySQL
- MySQL Tutorial: https://dev.mysql.com/doc/mysql-tutorial-excerpt/8.0/en/
- SQL Joins: https://www.w3schools.com/sql/sql_join.asp

### Security
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- PHP Security Best Practices: https://www.php.net/manual/en/security.database.sql-injection.php

### Frontend
- MDN Web Docs: https://developer.mozilla.org/
- CSS Grid: https://css-tricks.com/snippets/css/complete-guide-grid/
- Flexbox: https://css-tricks.com/snippets/css/a-guide-to-flexbox/

## Timeline Suggestion (8-week project)

**Week 1-2**: Database design and setup
**Week 3-4**: Authentication and basic structure
**Week 5-6**: Core features (CRUD operations)
**Week 7**: UI/UX development
**Week 8**: Testing, debugging, documentation

## Support Strategy

### Week 1-2: Heavy guidance
- Review database designs
- Help with setup issues
- Explain authentication concepts

### Week 3-6: Moderate guidance
- Answer specific questions
- Review code for security issues
- Help debug complex problems

### Week 7-8: Minimal guidance
- Students should be independent
- Only help with critical blockers
- Focus on presentation prep

## Evaluation Tips

1. **Test the application yourself** - Try to break it
2. **Review the code** - Check for security issues
3. **Check database design** - Verify normalization
4. **Test edge cases** - Full classes, invalid input, etc.
5. **Evaluate UX** - Is it intuitive?

## Sample Demo Questions

Ask students to demonstrate:
1. "Show me how a student enrolls in a class"
2. "How did you prevent SQL injection?"
3. "Walk me through your database schema"
4. "What happens if a class is full?"
5. "Show me the attendance tracking feature"
6. "Explain your session management"

## Conclusion

This starter template provides enough structure to guide students while requiring substantial implementation work. It's designed to assess their full-stack development capabilities while teaching real-world web application development practices.

Good luck with your class!
