# Truck Ordering Application

This application allows users to submit truck shipping requests and provides an admin interface for managing orders.

## Features

- **User Authentication:**
  - Secure user registration and login using Laravel Sanctum.
- **RESTful API:**
  - Provides data exchange between the backend and frontend.
- **Truck Request Form:**
  - Users can submit details like pickup location, delivery destination, cargo size, and weight.
- **User Dashboard:**
  - A personalized dashboard where users can monitor the status of their truck shipping requests in real-time.
- **Admin Interface:**
  - A comprehensive admin panel for viewing, updating, and managing all submitted orders.
- **Order Status Updates:**
  - Admins can update the status of each order, such as pending, in progress, or delivered.
- **Email Notifications:**
  - Automatic email notifications sent to admins when a new order is submitted.
- **Communication with Users:**
  - Admins can communicate with users via email directly from the admin interface.
- **Responsive Design:**
  - The application is optimized for mobile and desktop views.
- **Scalable Architecture:**
  - Built with scalability in mind to handle a growing number of users and orders.

## Installation

1. **Clone the Repository:**
   - Clone this repository to your local machine:
     ```bash
     git clone https://github.com/Fadwahigga/Truck-Ordering-Application-.git
     ```
   
2. **Backend Setup (Laravel):**
   - Navigate to the backend directory:
     ```bash
     cd Laravel
     ```
   - Install the necessary dependencies:
     ```bash
     composer install
     ```
   - Set up your environment configuration:
     - Copy the `.env.example` file to `.env`:
       ```bash
       cp .env.example .env
       ```
     - Configure your database settings in the `.env` file.
   
3. **Database Setup:**
   - Run the database migrations to set up the required tables:
     ```bash
     php artisan migrate
     ```
   - Optionally, seed the database with initial data:
     ```bash
     php artisan db:seed
     ```

4. **API and Authentication Configuration:**
   - Generate an application key:
     ```bash
     php artisan key:generate
     ```
   - If using Laravel Sanctum, publish the Sanctum configuration:
     ```bash
     php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
     ```
   - Run Sanctum migrations:
     ```bash
     php artisan migrate
     ```

5. **Run the Development Server:**
   - Start the Laravel development server:
     ```bash
     php artisan serve
     ```

## Frontend Setup (Flutter)

1. **Install Flutter:**
   - Ensure Flutter SDK is installed on your machine. Follow the instructions at the [Flutter website](https://flutter.dev/docs/get-started/install).

2. **Navigate to the Frontend Directory:**
   - Go to the frontend directory:
     ```bash
     cd Flutter
     ```

3. **Install Dependencies:**
   - Get all the required packages:
     ```bash
     flutter pub get
     ```

4. **Run the Flutter App:**
   - Launch the application on an emulator or physical device:
     ```bash
     flutter run
     ```

## Usage

1. **User Registration and Login:**
   - Register a new user account or log in with existing credentials.

2. **Submit a Truck Request:**
   - Fill out the truck request form with shipping details, including pickup and delivery locations, cargo specifications, and preferred times.

3. **Monitor Order Status:**
   - Users can track the progress of their orders via the dashboard.

4. **Admin Notifications:**
   - Admins receive email notifications whenever a new order is placed.

5. **Manage Orders:**
   - Admins can view all orders in the admin interface, update the order status, and communicate with users regarding their shipments.