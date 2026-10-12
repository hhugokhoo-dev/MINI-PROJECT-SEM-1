# Mini Project Journal

**Student Name:**
**Project Title:**
**Start Date:**
**End Date:**

---

Student Name:KHOO YONG JIAN
Project Title:DRIVORA
Start Date:29/9/2026
End Date: -




## Project Overview

> Briefly describe your project idea, what problem it solves, and what you aim to build.

---
Traditional car rental processes often rely on phone calls, walk-ins, or manual paperwork, which can be slow and error-prone for both customers and rental staff. Customers have no easy way to check vehicle availability or pricing in advance, and staff have to manually track which vehicles are rented out, available, or under maintenance.

This system solves that problem by providing an online platform where customers can browse available vehicles, view pricing and details, and submit rental applications without needing to visit or call the rental office. Staff manage the vehicle fleet and process rental applications, while admins oversee user accounts and vehicle categories — reducing manual work and minimizing booking conflicts.

## Tech Stack

> List the technologies, frameworks, and tools you plan to use.

- **Frontend**
- **Backend**
- **Database**
- **Other**

---
- **Frontend:CSS,HTML,BOOTSTRAP
- **Backend:PHP
- **Database:MySQL
- **Other:pending

## Day 1 — Date:29/9/2026

### What I planned to do today
The design of main page of the 'DRIVORA' and the system include register ,login and logout include the backend (database).

### What I actually did
Done the main page and system but the backend havent completely done.

### Blockers / Challenges
Lack of idea about the design of main page and the backend still not complete.

### What I learned
Think before designing and classification the div of the  main page and the others.

---

## Day 2 — Date: 30/9/2026

### What I planned to do today
The design of browse.php,showing the type of cars that customer can book.

### What I actually did
Code out the page of showing the car.

### Blockers / Challenges
Still thiking need to insert how many and which type of data needed into table.

### What I learned
Using css to style the product card.

---

## Day 3 — Date: 1/10/2026

### What I planned to do today
The UI design of after clicking the rent now button and adding the dashboard.

### What I actually did
Done for a basic structure of the page

### Blockers / Challenges
Struggled with passing vehicle ID parameters between pages.

### What I learned
How URL query parameters work in PHP ($_GET['id']).

---

## Day 4 — Date: 2/10/2026

### What I planned to do today
Adjust the cliking page 

### What I actually did
Attempted to fix database connection and setup issues.

### Blockers / Challenges
mysql occur problem 

### What I learned
How database connections work in PHP using PDO.

---

## Day 5 — Date:3/10/2026

### What I planned to do today
Try to solving theb problem of  mysql databse

### What I actually did
Solve the problem 

### Blockers / Challenges
Troubleshooting database ports and PDO error settings.

### What I learned
Proper PDO exception handling and verifying database host settings.

---

## Day 6 — Date: 4/10/2026

### What I planned to do today
The pending page and the staff access

### What I actually did
The customer rental request pending page completely done but still need some minor adjust

### Blockers / Challenges
Calculating total rental price dynamically based on pick-up and return dates.

### What I learned
Date handling in PHP using strtotime() and date validation.

---

## Day 7 — Date: 5/10/2026

### What I planned to do today
Done the staff system 

### What I actually did
Doing half way of the staff system 

### Blockers / Challenges
Linking rental data with customer and vehicle details in one table view.

### What I learned
Writing relational SQL JOIN queries.

---

## Day 8 — Date: 6/10/2026

### What I planned to do today
Done the staff system and also the admin system

### What I actually did
Staff system done but the admin system still on going

### Blockers / Challenges
The difficulties of php

### What I learned
Managing role authentication (customer, staff, admin) using PHP sessions.

---

## Day 9 — Date: 7/10/2026

### What I planned to do today
Done the admin system

### What I actually did
Half way done of admin system

### Blockers / Challenges
The sql data and the admin systme didnt update simultaneously

### What I learned
Using header redirects (header('Location: ...')) after form POST actions to refresh data.

---

## Day 10 — Date: 8/10/2026

### What I planned to do today
Finish the admin system by adding the users php and categories php

### What I actually did
Done the users php

### Blockers / Challenges
Preventing admins from accidentally deleting users who have active rental records.

### What I learned
Checking foreign key relationships with SQL COUNT(*) before deletion.

---

## Day 11 — Date: 9/10/2026

### What I planned to do today
Finish the admin categories php and also overall all the role php file

### What I actually did
Admin page finished 

### Blockers / Challenges
Managing form values for both adding new cars and editing existing ones.

### What I learned
Combining Add and Edit features into a single form (vehicle-form.php).

---

## Day 12 — Date: 10/10/2026

### What I planned to do today
Checking all the php code and also testing the admin page

### What I actually did
PHP code done checking

### Blockers / Challenges
Cleaning up messy inline comments and standardizing them into English.

### What I learned
Code refactoring and maintaining clear academic code structure.

---

## Day 13 — Date: 11/10/2026

### What I planned to do today
Planned to made the code shorter and simple

### What I actually did
The code been shoted and simpled

### Blockers / Challenges
Simplifying code structure without breaking session security or database logic.

### What I learned
Writing clean, beginner-friendly procedural PHP code.

---

## Day 14 — Date: 12/10/2026

### What I planned to do today
Overall seeing the entire project code and funtional

### What I actually did
Testing the entire system

### Blockers / Challenges
Minor UI bugs and verifying workflow across all three user roles.

### What I learned
Importance of end-to-end testing before final submission.

---

## Final Reflection

### What went well?
Successfully built a fully functional multi-role car rental system (Customer, Staff, Admin) with secure PDO database handling and a unified "Dark Luxury" UI theme.

### What would I do differently?
Plan the database structure and foreign key constraints more thoroughly at the beginning to avoid SQL connection and deletion issues during development.

### Key takeaways from this project
Gained hands-on experience in procedural PHP, SQL queries (JOIN, COUNT), session-based role authorization, and writing clean, maintainable code.

