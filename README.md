# PHP_Laravel12_Native_PHP

## Project Overview

ProductHub is a simple and modern product management application built using Laravel 12.
It allows users to create, view, update, delete, and search products with image upload support.

This project is ideal for beginners learning Laravel CRUD, portfolio projects, college or MCA submissions, and general Laravel practice.

---

## Features

Full CRUD Operations (Create, Read, Update, Delete)
Product Image Upload with Preview
Search Products by Name or Description
Pagination Support
Flash Success and Error Messages
Modern Responsive UI using Tailwind CSS
Authentication Ready with Laravel Breeze
Clean MVC Architecture
Image Remove Feature
Seeder for Demo Data

---

## Technologies Used

Laravel 12
PHP 8 or Higher
MySQL
Tailwind CSS
Font Awesome
Laravel Breeze (Optional Authentication)
Vite and NPM

---

## Installation Guide

### 1. Clone Repository

```
git clone https://github.com/your-username/laravel-producthub.git
cd laravel-producthub
```

### 2. Install Dependencies

```
composer install
npm install
```

### 3. Environment Setup

```
cp .env.example .env
php artisan key:generate
```

Update database settings inside `.env` file:

```
DB_DATABASE=producthub_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Migration

```
php artisan migrate
```

### 5. Storage Link for Images

```
php artisan storage:link
```

### 6. Run Project

```
php artisan serve
npm run dev
```

Visit in browser:

```
http://localhost:8000
```

---

## Folder Structure

```
app/
 ├── Models/Product.php
 ├── Http/Controllers/ProductController.php
 ├── Http/Controllers/HomeController.php

resources/views/
 ├── layouts/app.blade.php
 ├── home.blade.php
 ├── products/
 │   ├── index.blade.php
 │   ├── create.blade.php
 │   ├── show.blade.php

 database/
 ├── migrations/
 ├── seeders/ProductSeeder.php
```

---

## Routes

| Route               | Method | Description    |
| ------------------- | ------ | -------------- |
| /                   | GET    | Home Page      |
| /products           | GET    | Product List   |
| /products/create    | GET    | Add Product    |
| /products/{id}      | GET    | View Product   |
| /products/{id}/edit | GET    | Edit Product   |
| /products/{id}      | DELETE | Delete Product |

---

## Seeder – Demo Data

Run the following command:

```
php artisan db:seed
```

This will automatically insert sample products into the database.

---

## Screenshots (Optional)

You can create a `screenshots` folder and add images such as:

/screenshots/home.png
<img width="1787" height="971" alt="image" src="https://github.com/user-attachments/assets/1db1bb0d-083a-4d8c-a4b2-277b68823152" />
/screenshots/products.png
<img width="1624" height="937" alt="image" src="https://github.com/user-attachments/assets/2565e795-850f-4395-b538-e13717330850" />
/screenshots/create.png
<img width="1576" height="950" alt="image" src="https://github.com/user-attachments/assets/a84eefcd-c83e-4c8c-a2a8-f766ce0e3c31" />


## Validation Rules

Name – Required
Price – Numeric
Quantity – Integer
Image – JPG, PNG, JPEG, GIF with maximum 2MB size

---

## Future Enhancements

Categories
Shopping Cart
Order Management
REST API
Excel and PDF Export
Wishlist
Multi‑Language Support
Reviews and Ratings

---

## Author

Mihir Mehta
PHP Laravel Developer

---

## License

This project is open‑source and free to use for learning and personal projects.


