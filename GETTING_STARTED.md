# Getting Started with DCMA

Welcome to your capstone project! This guide will help you get started building the Dynamic Class Management Application.

## 🎯 Your Goal

Build a fully functional web application that allows:
- **Lecturers** to create and manage classes, track attendance, and assign grades
- **Students** to enroll in classes, view schedules, and track their progress

## 📋 Before You Start

### 1. Read the Documentation
- [x] Read `README.md` - Understand project requirements
- [x] Read `SETUP.md` - Set up your development environment
- [x] Review `FEATURES_CHECKLIST.md` - See what you need to build
- [x] Bookmark `QUICK_REFERENCE.md` - Keep it handy while coding

### 2. Set Up Your Environment
- [x] Install XAMPP/WAMP
- [x] Place project in web server directory
- [x] Start Apache and MySQL
- [x] Test that you can access `http://localhost/dcma-starter`

### 3. Plan Your Approach
- [x] Review all TODO comments in the code
- [x] Sketch out your database schema on paper
- [x] Plan your work in phases (see below)

## 🗺️ Recommended Development Path

### Week 1: Database Foundation
**Goal:** Create a solid database schema

1. Open `database/dcma_template.sql`
2. Design your tables:
   - `users` (students and lecturers)
   - `classes` (course information)
   - `enrollments` (student-class relationships)
   - `attendance` (attendance records)
3. Add primary keys, foreign keys, and constraints
4. Insert sample data for testing
5. Test your schema in phpMyAdmin

**Checkpoint:** Can you query your database and see sample data?

### Week 2: Authentication System
**Goal:** Users can log in and log out

1. Complete `config/config.php`:
   - Database connection
   - Helper functions (isLoggedIn, requireLogin, etc.)
2. Build `login.php`:
   - Create login form
   - Handle form submission
   - Verify credentials
   - Set session variables
3. Complete `logout.php`:
   - Destroy session
   - Redirect to login
4. Update `index.php`:
   - Route users based on login status

**Checkpoint:** Can you log in and log out successfully?

### Week 3: Lecturer Dashboard
**Goal:** Lecturers can create and manage classes

1. Build `views/dashboard_lecturer.php`:
   - Display lecturer's classes
   - Create "Add Class" form
   - Handle class creation
   - Implement delete functionality
2. Start `views/class_details.php`:
   - Display class information
   - Show enrolled students

**Checkpoint:** Can a lecturer create, view, and delete classes?

### Week 4: Student Dashboard
**Goal:** Students can enroll in classes

1. Build `views/dashboard_student.php`:
   - Display available classes
   - Show enrolled classes
   - Implement enrollment
   - Implement drop class
2. Start `views/class_view.php`:
   - Display class details
   - Show student's grade

**Checkpoint:** Can a student enroll in and drop classes?

### Week 5: Advanced Features
**Goal:** Add grade and attendance management

1. Complete `views/class_details.php`:
   - Add grade update functionality
   - Build attendance marking system
   - Create attendance modal
2. Complete `views/class_view.php`:
   - Display attendance history
   - Show attendance statistics

**Checkpoint:** Can lecturers manage grades and attendance?

### Week 6: UI/UX Polish
**Goal:** Make it look professional

1. Style `login.php`:
   - Center login box
   - Add colors and spacing
2. Style dashboards:
   - Create grid layouts
   - Style class cards
   - Add hover effects
3. Style forms and tables:
   - Make inputs look good
   - Style buttons
   - Add responsive design
4. Add error/success messages styling

**Checkpoint:** Does your app look professional and user-friendly?

### Week 7: Testing & Security
**Goal:** Ensure everything works and is secure

1. Test all features:
   - Try to break things
   - Test edge cases
   - Test with different users
2. Add security measures:
   - Verify all inputs are sanitized
   - Check password hashing
   - Test role-based access
3. Fix any bugs you find

**Checkpoint:** Does everything work correctly and securely?

### Week 8: Documentation & Demo
**Goal:** Prepare for presentation

1. Update README with:
   - Setup instructions
   - Features you implemented
   - Any additional features
2. Add code comments where needed
3. Prepare demo presentation:
   - Show key features
   - Explain your code
   - Discuss challenges and solutions

**Checkpoint:** Are you ready to present?

## 💡 Tips for Success

### Do's ✅
- **Start small** - Build one feature at a time
- **Test frequently** - Test after each feature
- **Use the reference** - Check QUICK_REFERENCE.md for code patterns
- **Comment your code** - Explain complex logic
- **Ask for help** - When you're stuck, ask your instructor
- **Save often** - Commit your work regularly
- **Plan before coding** - Think through the logic first

### Don'ts ❌
- **Don't skip the database** - Get it right first
- **Don't copy blindly** - Understand what you're writing
- **Don't ignore errors** - Fix them as they appear
- **Don't skip testing** - Test each feature thoroughly
- **Don't leave it to the last minute** - Start early
- **Don't forget security** - Sanitize inputs, hash passwords
- **Don't make it too complex** - Keep it simple and functional

## 🐛 When You Get Stuck

### Debugging Process
1. **Read the error message** - It usually tells you what's wrong
2. **Check your syntax** - Missing semicolons, brackets?
3. **Echo your variables** - See what values you're working with
4. **Test your SQL** - Run queries in phpMyAdmin
5. **Check the reference** - Look for similar code patterns
6. **Google the error** - Someone has probably solved it
7. **Ask for help** - Your instructor is there to help

### Common Issues
- **"Connection failed"** → Check database credentials
- **"Undefined index"** → Use `isset()` before accessing array
- **"Headers already sent"** → No output before `header()`
- **Blank page** → Enable error reporting
- **SQL error** → Echo the query and test in phpMyAdmin

## 📚 Resources

### Essential Reading
- [PHP Manual](https://www.php.net/manual/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [W3Schools PHP](https://www.w3schools.com/php/)
- [MDN Web Docs](https://developer.mozilla.org/)

### Video Tutorials
- Search YouTube for "PHP MySQL CRUD tutorial"
- Look for "PHP authentication system tutorial"
- Watch "Responsive web design CSS tutorial"

### Tools
- **phpMyAdmin** - Database management
- **Browser DevTools** - Debug JavaScript and CSS
- **VS Code** - Code editor with PHP extensions

## ✅ Daily Checklist

Each day you work on the project:
- [x] Review what you accomplished yesterday
- [x] Set a specific goal for today
- [x] Write code for 1-2 hours
- [x] Test what you built
- [x] Fix any bugs
- [x] Update FEATURES_CHECKLIST.md
- [x] Commit your work
- [x] Plan tomorrow's goal

## 🎓 Learning Mindset

Remember:
- **It's okay to struggle** - That's how you learn
- **Mistakes are normal** - Everyone makes them
- **Progress over perfection** - Get it working first, then improve
- **Ask questions** - There are no stupid questions
- **Celebrate small wins** - Each feature completed is progress
- **Learn from errors** - They teach you what not to do

## 🚀 Ready to Start?

1. Complete the setup (SETUP.md)
2. Open `database/dcma_template.sql`
3. Start designing your database schema
4. Check off items in FEATURES_CHECKLIST.md as you go

**You've got this! Good luck with your capstone project!** 💪

---

**Need Help?**
- Check QUICK_REFERENCE.md for code snippets
- Review FEATURES_CHECKLIST.md for requirements
- Consult your instructor when stuck
- Remember: Building this project is a learning journey!
