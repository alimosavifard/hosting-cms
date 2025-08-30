
Hosting CMS

Hosting CMS is a lightweight, modular, and extensible Content Management System (CMS) built with PHP, designed specifically for hosting providers, domain registrars, and VPS management platforms. It offers a simple yet powerful framework for managing products, users, and carts with a focus on performance, security, and ease of customization. The CMS is ideal for small to medium-sized hosting businesses looking to create a professional web presence with minimal setup.

Features





Modular Architecture: Organized into core components (Config, Database, Router, Theme, etc.) for easy maintenance and scalability.



Theme Support: Supports multiple themes (e.g., default and modern) with automatic asset management (CSS/JS minification and copying to public directory).



Product Management: Easily manage hosting plans, domains, and VPS products with a clean and intuitive interface.



Cart System: Basic shopping cart functionality for adding products and processing orders.



User Authentication: Secure user registration and login system with CSRF protection.



Database Migrations and Seeders: Automated database setup with migrations and seeders for quick deployment.



Logging: Robust logging system using Monolog for debugging and error tracking.



Lightweight and Dependency-Free: Built with minimal dependencies, relying primarily on PHP and MySQL.



RTL Support: Fully compatible with right-to-left languages like Persian (Farsi).

Requirements





PHP >= 7.4



MySQL or MariaDB



Composer (for dependency management)



Web server (Apache/Nginx) with URL rewriting enabled



Write permissions for storage/ and public/assets/ directories

Installation

Follow these steps to set up Hosting CMS on your server:





Clone the Repository:

<code>
git clone https://github.com/alimosavifard/hosting-cms.git
cd hosting-cms
</code>



Install Dependencies: Install the required PHP dependencies using Composer:

<code>
composer install
</code>


Configure Environment:



Copy the .env.example file to .env:

copy .env.example .env



Edit the .env file to configure your database and application settings:

APP_URL=http://localhost:8000
DB_HOST=localhost
DB_NAME=hosting_cms
DB_USER=your_db_user
DB_PASS=your_db_password
THEME=default



Set Directory Permissions: Ensure the storage/ and public/assets/ directories are writable:

<code>
chmod -R 775 storage
chmod -R 775 public/assets
</code>

On Windows:
<code>
icacls storage /grant Everyone:F
icacls public/assets /grant Everyone:F
</code>


Run Installation: Access the installation script via command line:

</code>
php scripts/install.php
</code>

This will execute database migrations, seed initial data, and copy theme assets to the public/assets/ directory.



Start the Development Server: Use PHP's built-in server to test the application:

php -S localhost:8000 -t public

Then visit http://localhost:8000/?controller=product&action=index to view the product listing page.



Usage

Viewing Products



Navigate to http://your-domain.com/?controller=product&action=index to view the list of products.



The CMS uses a query-string-based routing system (e.g., ?controller=product&action=index).

Adding Products to Cart





Each product has an "Add to Cart" button that submits a POST request to ?controller=cart&action=add.



CSRF protection is enabled to ensure secure form submissions.

Switching Themes





Edit the THEME variable in .env (e.g., THEME=modern) to switch themes.



Run the installation script again to copy the new theme's assets:

<code>
rm storage/installed.lock
php scripts/install.php
</code>

Debugging





Check logs in storage/logs/app.log for detailed information about migrations, asset copying, and errors.

Customization

Adding New Themes


Create a new theme directory under themes/ (e.g., themes/mytheme/).



Add templates/ for PHP templates and assets/ for CSS/JS files.



Update the THEME variable in .env to mytheme.


Run the installation script to copy assets:

php scripts/install.php

Extending Functionality



Add new controllers in app/Controllers/ for custom routes and logic.


Create new models in app/Models/ for database interactions.

Define migrations and seeders in migrations/ and seeders/ for database schema changes.

Contributing

Contributions are welcome! To contribute to Hosting CMS:





Fork the repository.


Create a new branch (git checkout -b feature/your-feature).

Make your changes and commit (git commit -m "Add your feature").



Push to the branch (git push origin feature/your-feature).



Create a Pull Request on GitHub.

Please ensure your code follows the PSR-12 coding standard and includes appropriate tests.

License

Hosting CMS is open-source software licensed under the MIT License.

Support

For issues, questions, or feature requests, please open an issue on the GitHub repository.



Built with 💻 by Ali Mousavi Fard. Contributions and feedback are greatly appreciated!