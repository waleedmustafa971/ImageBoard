# ImageBoard - 4chan Style Anonymous Imageboard

A modern, full-featured imageboard (inspired by 4chan) built with Laravel 12 and PHP 8.2.

## Features

- **Anonymous Posting** - Users can post without registration
- **Multiple Boards** - Create and manage different topic boards
- **Image Uploads** - Support for JPG, PNG, and GIF with automatic thumbnail generation
- **Thread System** - Create threads and reply to existing ones
- **Post Quoting** - Click post numbers to quote other posts
- **Admin Panel** - Complete board and content moderation system
- **Supervisor System** - Assign board-specific moderators with limited permissions
- **Activity Logging** - Track all moderation actions by admins and supervisors
- **Thread Management** - Pin and lock threads
- **Catalog View** - Visual overview of all threads in a board

## Tech Stack

- **Laravel 12** - PHP framework
- **MySQL** - Database
- **Intervention Image** - Image processing and thumbnails
- **Tailwind-inspired CSS** - Custom styling inspired by classic imageboards

## Installation

### Requirements

- PHP 8.2+
- MySQL 5.7+
- Composer
- GD Extension enabled

### Setup

1. Clone the repository:
```bash
git clone https://github.com/waleedmustafa971/ImageBoard.git
cd ImageBoard
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update `.env` with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=imageboard
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seed the database:
```bash
php artisan migrate:fresh --seed
```

6. Create storage symlink:
```bash
php artisan storage:link
```

7. Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` to see your imageboard!

## Default Boards

The seeder creates 5 default boards:
- **/b/** - Random
- **/tech/** - Technology
- **/g/** - Gaming
- **/a/** - Anime & Manga
- **/fit/** - Fitness

## Admin Access

Default admin credentials:
- **Username:** admin
- **Password:** password

**⚠️ Change these credentials immediately in production!**

Access the admin panel at: `http://localhost:8000/admin/login`

## Admin Features

- **Board Management**
  - Create, edit, and delete boards
  - View board statistics
- **Supervisor Management**
  - Create and manage board supervisors
  - Assign/remove supervisors to specific boards
  - Activate/deactivate supervisor accounts
- **Moderation Actions**
  - Pin/unpin threads
  - Lock/unlock threads
  - Delete threads and posts
- **Activity Monitoring**
  - View comprehensive moderation logs
  - Filter logs by board, action type, and moderator
  - Track all admin and supervisor actions

## Supervisor System

Supervisors are board-specific moderators with limited permissions compared to admins.

### Supervisor Features

- **Board-Specific Access** - Only moderate assigned boards
- **Thread Moderation**
  - Pin/unpin threads
  - Lock/unlock threads
  - Delete threads
- **Post Moderation**
  - Delete individual posts
- **Activity Dashboard**
  - View assigned boards
  - See personal moderation history

### Creating a Supervisor

1. Login as admin
2. Navigate to "Manage Supervisors"
3. Click "Create New Supervisor"
4. Fill in username, email, and password
5. Assign specific boards to moderate
6. Set active status

Access the supervisor panel at: `http://localhost:8000/supervisor/login`

## Usage

### Creating a Thread
1. Navigate to any board (e.g., `/b`)
2. Click "Start a New Thread"
3. Fill in the subject and content
4. Optionally upload an image (required for OP)
5. Submit

### Replying to a Thread
1. Open any thread
2. Scroll to the reply form
3. Fill in your comment
4. Optionally upload an image
5. Submit

### Quoting Posts
Click on any post number to automatically add a quote reference to your reply.

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── SupervisorController.php
│   │   ├── BoardController.php
│   │   ├── PostController.php
│   │   └── ThreadController.php
│   └── Middleware/
│       └── EnsureSupervisorCanModerate.php
├── Models/
│   ├── Admin.php
│   ├── Supervisor.php
│   ├── Board.php
│   ├── Post.php
│   ├── Thread.php
│   └── ModerationLog.php
└── Services/
    └── ImageService.php
resources/views/
├── admin/
│   ├── boards/
│   ├── supervisors/
│   ├── activity-logs.blade.php
│   └── dashboard.blade.php
├── supervisor/
│   ├── login.blade.php
│   └── dashboard.blade.php
├── boards/
├── threads/
└── layouts/
```

## Security

- **Authentication**
  - Separate authentication guards for admin and supervisor
  - Password hashing with bcrypt
  - IDOR vulnerability protection
- **Authorization**
  - Board-specific access control for supervisors
  - Middleware-based permission checking
- **Data Protection**
  - CSRF protection on all forms
  - Image upload validation and sanitization
  - SQL injection protection via Eloquent ORM
  - XSS protection via Blade templating
- **Audit Trail**
  - Comprehensive moderation action logging
  - Track all changes with moderator attribution

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the MIT license.

## Credits

Built by [Waleed Mustafa](https://github.com/waleedmustafa971)

Inspired by 4chan's classic imageboard design.

## Support

For issues and questions, please use the [GitHub Issues](https://github.com/waleedmustafa971/ImageBoard/issues) page.
