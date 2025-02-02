# Restaurant Booking App

This is a simple restaurant booking application built with PHP 8, Twig templating, and JWT authentication. Guests can book a table for breakfast, lunch, or dinner, while staff can log in to manage bookings and daily services.

## Setup

1. Run `composer install` in the project root.
2. Configure your web server (e.g., Apache) to use the `/public` folder as the document root.
3. Create your MySQL database and run the provided SQL to create the tables.
4. Configure HTTPS in your Apache server.

## Files

- **/public**: Contains public PHP controllers.
- **/templates**: Contains Twig templates.
- **/includes**: Contains shared PHP files (DB connection, Twig initialization, JWT helper).
- **/api**: Contains API endpoints for bookings and (placeholder) staff/service actions.
