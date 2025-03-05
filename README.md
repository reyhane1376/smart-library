# Book Reservation System (smart library)

A sophisticated book reservation system with advanced features for library management, built with Laravel.

## Features

- **Book Version Management**:
  - Multiple copies of books with different physical conditions
  - Repair history and physical condition tracking
  - Copy replacement in case of damage

- **Smart Reservation System**:
  - User priority-based reservations
  - Chain reservations (automatic notification to next person in queue)
  - Reservation limitations based on user history (delays, damages)

- **Complex Rules**:
  - Special copy reservations for VIP members
  - Dynamic penalty system (increasing penalty rate for repeated violations)
  - Book exchange between different branches

- **Sophisticated Scoring System Based On**:
  - Number of delay days
  - Book condition upon return
  - Number of canceled reservations
  - Extension request history

## Technologies & Patterns Used

### Backend Framework
- **Laravel**: PHP framework used for developing the API with elegant syntax and robust features

### Architecture & Design Patterns
- **SOLID Principles**: Followed throughout the codebase for maintainable and scalable software
- **Dependency Injection**: Used for loose coupling between components
- **Repository Pattern**: Implemented for data access abstraction
- **Service Layer Pattern**: Used to encapsulate business logic
- **Event Sourcing**: Implemented for tracking all book status changes
- **Optimistic Locking**: Used for managing concurrent reservations

### Database
- **MySQL/PostgreSQL**: For persistent data storage
- **Eloquent ORM**: Laravel's ORM for database interactions
- **Migrations**: For version control of database schema

### Caching
- **Cache Tags**: Implemented for related data cache management
- **Redis**: Used for caching and queue management

### Queue & Jobs
- **Laravel Queue**: For handling time-consuming operations
- **Queue Workers**: For processing jobs in the background

### Notification System
- **Notification Channels**: Email, SMS, etc.
- **Retry Mechanism**: For failed notifications

## Installation & Setup

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL
- Redis (for queue and cache)

### Installation Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/book-reservation-system.git
   cd book-reservation-system
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up environment variables:
   ```bash
   cp .env.example .env
   ```
   Edit the `.env` file with your database and Redis configurations.

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Run seeders (optional, for test data):
   ```bash
   php artisan db:seed
   ```

7. Start queue workers:
   ```bash
   php artisan queue:work
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

## API Documentation

A Postman collection is included in the repository for API testing and documentation.

1. Import the collection from `postman/BookReservationSystem.json` into Postman
2. Set up environment variables in Postman (base URL, token)
3. Use the provided requests to interact with the API

## Key API Endpoints

### Books
- `GET /api/books` - List all books
- `GET /api/books/{id}` - Get book details
- `POST /api/books` - Create a new book
- `PUT /api/books/{id}` - Update a book
- `DELETE /api/books/{id}` - Delete a book

### Book Copies
- `GET /api/books/{book_id}/copies` - List all copies of a book
- `POST /api/books/{book_id}/copies` - Add a new copy
- `PUT /api/copies/{id}` - Update copy details
- `DELETE /api/copies/{id}` - Remove a copy

### Reservations
- `GET /api/reservations` - List all reservations
- `POST /api/books/{book_id}/copies/{copy_id}/reserve` - Reserve a book copy
- `PUT /api/reservations/{id}/cancel` - Cancel a reservation
- `PUT /api/reservations/{id}/extend` - Extend a reservation

### Users
- `GET /api/users/{id}` - Get user details
- `GET /api/users/{id}/score` - Get user score
- `GET /api/users/{id}/reservations` - Get user reservations

## Development Workflow

This project follows a Git workflow with incremental commits:

1. Create feature branches for new features/fixes
2. Make small, focused commits with clear messages
3. Create pull requests for review
4. Merge to main branch after review

## Testing

Run the test suite with:

```bash
php artisan test
```

## License

[MIT License](LICENSE)
