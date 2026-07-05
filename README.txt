FIFA Predictor - Simple PHP + MySQL Site

1. Put this folder inside XAMPP htdocs, for example:
   C:\xampp\htdocs\fifa_predictor

2. Start Apache and MySQL in XAMPP.

3. Open phpMyAdmin:
   http://localhost/phpmyadmin

4. Import install.sql.
   This creates the database, tables, rounds, and sample matches.

5. Open the site:
   http://localhost/fifa_predictor/index.php

6. Admin page:
   http://localhost/fifa_predictor/admin.php
   Password: admin2026

7. To change database settings or admin password, edit db.php.

How it works:
- Each round has one form.
- Name, roll, and department are required.
- One roll number can submit only once per round.
- Admin can open or close each form.
- After closing a form, vote percentages are shown as pie charts.
- Clicking a chart or button shows who selected that team.
- Admin can set the correct answer for each match.
- Leaderboard calculates points automatically.
- Correct answer: full points.
- Wrong answer: 50% point deduction.
