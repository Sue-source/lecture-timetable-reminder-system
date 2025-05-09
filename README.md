
📘 Lecture Timetable Reminder System
A web-based application designed to help students and lecturers stay informed about upcoming lectures through automatic timetable reminders and notifications.

📝 Table of Contents

About

Features

Installation

Usage

Technologies Used

Database Structure

Contributing

License

Contact

📖 About

This project allows students and lecturers to view their lecture timetables and receive automatic reminders before a class begins. Admins can manage user accounts and schedule updates. It aims to reduce missed classes and improve time management in academic environments.

✨ Features

Student and lecturer user accounts

Admin dashboard to manage users and timetables

Lecture timetable viewing by day

Automated email or in-system notifications

Reminder scheduling

User login & registration system

Activity tracking (login/logout)

🔧 Installation

Clone the repository:

bash
Copy
Edit
git clone https://github.com/yourusername/lecture-timetable-reminder.git
Move the project to your XAMPP htdocs folder (e.g., C:\xampp\htdocs\lecture-timetable-reminder)

Import the SQL database file into phpMyAdmin

Configure your connect.php file with your database credentials

Start Apache and MySQL from XAMPP control panel

Visit http://localhost/lecture-timetable-reminder in your browser

▶️ Usage

Students: View lectures, set reminders, and receive alerts.

Lecturers: View their class schedule and receive reminders.

Admin: Add/edit/delete users and timetable entries.

🛠 Technologies Used

Frontend: HTML, CSS, JavaScript

Backend: PHP (v8+)

Database: MySQL

Other Tools: PHPMailer (for sending email reminders), XAMPP

🗃 Database Structure

Main tables include:

users (id, name, email, role, password)

timetable (id, course_name, course_code, start_time, end_time, venue, department, faculty, day)

reminders (id, user_id, timetable_id, reminder_date, reminder_time)

notifications and user_notifications (for alerts)

🤝 Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Steps:

bash
Copy
Edit
1. Fork the repo
2. Create your feature branch (git checkout -b feature/your-feature)
3. Commit your changes (git commit -m 'Add your feature')
4. Push to the branch (git push origin feature/your-feature)
5. Open a Pull Request
📄 License
This project is licensed under the MIT License.

📬 Contact

Email: menensusan@gmail.com

GitHub: https://github.com/Sue-source/lecture-timetable-reminder-system
