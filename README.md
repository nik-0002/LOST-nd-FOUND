# Intelligent Lost & Found (PHP + MySQL + HTML/CSS/JS)
1. Install XAMPP (Windows); start Apache + MySQL.
2. Copy the `lostfound` folder to C:\xampp\htdocs\
3. Open http://localhost/phpmyadmin -> Import -> database/schema.sql
4. Edit config/db.php (DB password, MAPS_KEY).
5. Open http://localhost/lostfound/
Make an admin: register normally, then in phpMyAdmin run:
   UPDATE users SET role='admin' WHERE email='you@example.com';
