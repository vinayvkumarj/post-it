# 📝 Post It - Notes Application

A modern, responsive web-based notes application built with PHP, MySQL, and vanilla JavaScript. Features include creating, editing, pinning, archiving, and deleting notes with a clean, Google Keep-inspired interface.

![Post It Notes App]

## ✨ Features

- **📌 Pin Notes** - Keep important notes at the top
- **📦 Archive** - Move notes to archive for later reference
- **🗑️ Delete** - Permanently remove unwanted notes
- **🎨 Color Coding** - Organize notes with 6 different colors
- **🌓 Dark Mode** - Easy on the eyes with dark theme support
- **📱 Responsive Design** - Works on desktop, tablet, and mobile
- **🔒 Secure Authentication** - User registration and login with password hashing
- **👤 User Profiles** - Upload profile pictures and manage account settings
- **⚡ Fast & Lightweight** - Built with vanilla JavaScript, no frameworks

## 🚀 Tech Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Icons**: [Lucide Icons](https://lucide.dev/)
- **Design**: Custom CSS with CSS Grid and Flexbox

## 📁 Project Structure
notes-app/
├── assets/
│ ├── css/
│ │ └── style.css # Main stylesheet
│ ├── images/
│ │ └── default.png # Default profile picture
│ └── uploads/
│ └── profiles/ # User profile pictures
├── config/
│ ├── config.php # App configuration & helper functions
│ └── database.php # Database connection (not in Git)
├── includes/
│ ├── header.php # Common header with navigation
│ └── footer.php # Common footer with scripts
├── index.php # Main notes page
├── archive.php # Archived notes page
├── profile.php # User profile management
├── help.php # Help/FAQ page
├── login.php # Login page
├── register.php # Registration page
├── logout.php # Logout handler
├── save_note.php # API: Create/update notes
├── delete_note.php # API: Delete notes
├── toggle_pin.php # API: Pin/unpin notes
├── toggle_archive.php # API: Archive/unarchive notes
├── update_profile.php # API: Update profile info
├── app.js # Main JavaScript file
├── .gitignore # Git ignore rules
└── README.md # This file


## 🛠️ Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher / MariaDB
- Composer (optional, for dependencies)
- Git

### Step 1: Clone the Repository

```bash
git clone [https://github.com/yourusername/notes-app.git](https://github.com/yourusername/notes-app.git)
cd notes-app
```

### Step 2: Create Database

1. Open phpMyAdmin or your MySQL client
2. Create a new database named `notes_app`
3. Import the database schema from `database.sql` (if provided)

### Step 3: Configure Database

1. Copy the database template:
   ```bash
   cp config/database.php.example config/database.php
   ```

2. Edit `config/database.php` with your credentials:
   ```php
   $host = "localhost";
   $db_name = "notes_app";
   $username = "root";
   $password = "your_password";
   ```

### Step 4: Set Up File Permissions

Make sure the uploads directory is writable:

```bash
chmod -R 755 assets/uploads
```

### Step 5: Configure Site URL (Optional)

If you're not using `localhost/notes-app/`, update `config/config.php`:

```php
define('SITE_URL', 'http://your-domain.com/');
```

### Step 6: Run the Application

Open your browser and navigate to:
http://localhost/notes-app/



### Step 7: Create Your First Account

1. Click "Register" on the login page
2. Fill in your details
3. Start creating notes!

## 🎨 Usage

### Creating Notes

- Click the **+** button (bottom right) or click "Create new note"
- Add a title and description
- Choose a color
- Click "Save"

### Organizing Notes

- **Pin**: Click the 📌 icon to pin important notes to the top
- **Archive**: Click the 📦 icon to move notes to archive
- **Delete**: Click the 🗑️ icon to permanently delete a note
- **Edit**: Click on any note to open and edit it

### Dark Mode

- Click your profile picture (top right)
- Select "Dark Mode" from the dropdown

## 🔐 Security Features

- **Password Hashing**: All passwords are hashed using `password_hash()` (bcrypt)
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: HTML escaping with `htmlspecialchars()`
- **Session Management**: Secure session handling
- **CSRF Protection**: (Recommended to add)

## 🎯 Key Features Explained

### Note Cards

Notes are displayed in a responsive grid with:
- Color-coded backgrounds
- Pin/Archive/Delete actions
- Last updated timestamp
- Hover effects and animations

### Modal Editor

- Inline editing with real-time preview
- Color picker with 6 preset colors
- Pin/Archive/Delete actions
- Created/Updated timestamps

### Archive System

- Archived notes are moved to a separate page
- Can be unarchived or deleted permanently
- Maintains all note metadata

### Profile Management

- Upload profile pictures
- Update username and email
- View account statistics (total notes, archived notes)
- Delete profile picture

## 📱 Responsive Design

The app is fully responsive with breakpoints at:
- **Desktop**: 5 columns
- **Laptop**: 3 columns (max-width: 1200px)
- **Tablet**: 2 columns (max-width: 900px)
- **Mobile**: 1 column (max-width: 600px)

## 🐛 Troubleshooting

### Database Connection Failed

- Check `config/database.php` credentials
- Ensure MySQL server is running
- Verify database name exists

### Profile Pictures Not Uploading

- Check `assets/uploads/profiles/` permissions
- Verify `MAX_FILE_SIZE` in `config/config.php`
- Check PHP `upload_max_filesize` in `php.ini`

### Notes Not Saving

- Check browser console for JavaScript errors
- Verify `save_note.php` is accessible
- Check database connection

### Dark Mode Not Persisting

- Check browser localStorage is enabled
- Clear browser cache and reload

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- Design inspired by [Google Keep](https://keep.google.com/)
- Icons from [Lucide](https://lucide.dev/)
- Fonts from [Google Fonts](https://fonts.google.com/)

## 📧 Contact

For questions or support, please open an issue on GitHub.

---

**Built with ❤️ using PHP and Vanilla JavaScript**
