# ImageBoard - 4chan Style Anonymous Imageboard

A modern, full-featured imageboard (inspired by 4chan) built with Laravel 12 and PHP 8.2.

## Features

### Core Functionality
- **Anonymous Posting** - Users can post without registration
- **Country Flags** - Display poster's country flag next to their name (just like 4chan)
  - Automatic IP-based geolocation
  - Cached for performance (24-hour cache)
  - Flag images via CDN
- **Multiple Boards** - Create and manage different topic boards
- **Thread System** - Create threads and reply to existing ones
- **Post Quoting** - Click post numbers to quote other posts
- **Catalog View** - Visual overview of all threads in a board

### Media & Images
- **Multiple Image Uploads** - Upload up to 4 images per post (5MB each)
- **Image Support** - JPG, PNG, GIF, and WEBP with automatic thumbnail generation
- **Image Gallery** - Multiple images displayed in a clean grid layout

### Moderation & Security
- **Ban System** - IP-based banning with board-specific or global scope
  - Flexible ban durations (1 hour, 1 day, 1 week, 1 month, permanent)
  - Ban from post action for quick moderation
  - Ban management interface for admins
- **Captcha & Anti-Spam** - Simple math captcha on all posts
  - Rate limiting (3 posts per minute per IP)
  - Protection against bot spam
- **Post Reporting** - Community-driven moderation
  - Report posts for spam, illegal content, harassment, off-topic, or other
  - Report review dashboard for admins and supervisors
  - Duplicate report prevention (24-hour cooldown per IP per post)
- **Admin Panel** - Complete board and content moderation system
- **Supervisor System** - Assign board-specific moderators with limited permissions
- **Activity Logging** - Track all moderation actions by admins and supervisors

### User Experience
- **Live Thread Updates** - Auto-refresh threads every 10 seconds
  - New posts appear automatically with "NEW" badge
  - No page reload required
  - Real-time discussion experience
- **Thread Management** - Pin and lock threads
- **Post Actions** - Delete posts and threads with proper authorization

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
- **Ban Management**
  - Create IP-based bans (global or board-specific)
  - Set ban duration (1 hour to permanent)
  - Ban users directly from posts
  - View and remove active/expired bans
  - Filter bans by status and board
- **Report Management**
  - Review user-submitted post reports
  - Mark reports as reviewed or dismissed
  - Filter reports by status
  - Quick access to reported posts
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
  - Ban users from posts (board-specific or global)
- **Report Management**
  - Review reports from assigned boards
  - Mark reports as reviewed or dismissed
- **Activity Dashboard**
  - View assigned boards
  - See personal moderation history
  - Access to report queue

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
4. Upload an image (required for OP)
5. Solve the math captcha
6. Submit

### Replying to a Thread
1. Open any thread
2. Scroll to the reply form
3. Fill in your comment
4. Optionally upload up to 4 images
5. Solve the math captcha
6. Submit
7. New replies will auto-load every 10 seconds

### Reporting a Post
1. Click the "Report" button on any post
2. Select a reason (spam, illegal content, harassment, off-topic, or other)
3. Submit report
4. Moderators will review the report

### Quoting Posts
Click on any post number to automatically add a quote reference to your reply.

## File Structure

```
app/
├── Helpers/
│   └── Captcha.php
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── SupervisorController.php
│   │   ├── BoardController.php
│   │   ├── PostController.php
│   │   ├── ThreadController.php
│   │   └── ReportController.php
│   ├── Middleware/
│   │   ├── EnsureSupervisorCanModerate.php
│   │   ├── CheckIfBanned.php
│   │   └── RateLimitPosts.php
│   └── Requests/
│       ├── StoreThreadRequest.php
│       └── StorePostRequest.php
├── Models/
│   ├── Admin.php
│   ├── Supervisor.php
│   ├── Board.php
│   ├── Post.php
│   ├── PostImage.php
│   ├── Thread.php
│   ├── ModerationLog.php
│   ├── Ban.php
│   └── Report.php
└── Services/
    ├── ImageService.php
    └── GeoIPService.php
resources/views/
├── admin/
│   ├── boards/
│   ├── supervisors/
│   ├── bans/
│   ├── reports/
│   ├── activity-logs.blade.php
│   └── dashboard.blade.php
├── supervisor/
│   ├── reports/
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
- **Spam Protection**
  - Math captcha on all thread and post submissions
  - Rate limiting (3 posts per minute per IP)
  - IP-based ban system with flexible durations
  - Ban enforcement middleware
- **Data Protection**
  - CSRF protection on all forms
  - Image upload validation and sanitization (5MB limit per image)
  - Multiple image upload support with validation
  - SQL injection protection via Eloquent ORM
  - XSS protection via Blade templating
  - IP address hashing for privacy
- **Audit Trail**
  - Comprehensive moderation action logging
  - Track all changes with moderator attribution
  - Ban action logging
  - Report submission tracking

## Development & Testing

### Testing Country Flags

The imageboard includes a testing command to verify the GeoIP functionality:

```bash
# Test GeoIP service with various countries
php artisan test:country-flags

# Test with a specific IP address and update latest post
php artisan test:country-flags --ip=8.8.8.8
```

**Note:** When testing on localhost, posts will show **[Local]** indicator instead of flags. Deploy to a production server to see actual country flags based on user IP addresses.

### Clearing Caches

After making changes to views or configuration:

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the MIT license.

## Credits

Built by [Waleed Mustafa](https://github.com/waleedmustafa971)

Inspired by 4chan's classic imageboard design.

## Support

For issues and questions, please use the [GitHub Issues](https://github.com/waleedmustafa971/ImageBoard/issues) page.
