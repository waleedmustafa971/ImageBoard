# ImageBoard - 4chan Style Anonymous Imageboard

A modern, full-featured imageboard (inspired by 4chan) built with Laravel 12 and PHP 8.2.

## Features

- **Anonymous Posting** - Users can post without registration
- **Multiple Boards** - Create and manage different topic boards
- **Image Uploads** - Support for JPG, PNG, and GIF with automatic thumbnail generation
- **Thread System** - Create threads and reply to existing ones
- **Post Quoting** - Click post numbers to quote other posts
- **Admin Panel** - Complete board and content moderation system
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

- Create, edit, and delete boards
- Pin/unpin threads
- Lock/unlock threads
- Delete threads and posts
- View board statistics

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
├── Http/Controllers/
│   ├── AdminController.php
│   ├── BoardController.php
│   ├── PostController.php
│   └── ThreadController.php
├── Models/
│   ├── Admin.php
│   ├── Board.php
│   ├── Post.php
│   └── Thread.php
└── Services/
    └── ImageService.php
resources/views/
├── admin/
├── boards/
├── threads/
└── layouts/
```

## Security

- Admin authentication with separate guard
- CSRF protection on all forms
- Image upload validation
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the MIT license.

## Credits

Built by [Waleed Mustafa](https://github.com/waleedmustafa971)

Inspired by 4chan's classic imageboard design.

## Support

For issues and questions, please use the [GitHub Issues](https://github.com/waleedmustafa971/ImageBoard/issues) page.
