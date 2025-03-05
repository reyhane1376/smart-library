# Book Reservation System (smart library)

A sophisticated book reservation system with advanced features for library management, built with Laravel.

## Technologies Used
1. Laravel 12: PHP framework used for building and managing the application.
2. MySQL: Database used for storing system data.
3. SRP (Single Responsibility Principle): Implemented to improve code readability and maintainability by ensuring each class has one responsibility.
4. Dependency Inversion: Applied to reduce direct dependencies and increase system flexibility.
5. Event Sourcing: Used to log all changes to the book statuses as events, enabling precise history tracking and recovery.
6. Optimistic Locking: Implemented for managing concurrent reservations and preventing race conditions in book status updates.
7. Cache Tags: Used for efficient data caching and improved system performance.
8. Retry System: Implemented to retry operations in case of failures, ensuring reliability in time-sensitive systems.
9. Notification System: Utilized to notify users about changes or events within the system.


## Postman Collection

A Postman collection is available for testing the system's APIs.  
You can download it using the link below:

[Download Postman Collection](https://raw.githubusercontent.com/reyhane1376/smart-library/refs/heads/main/postman/smart_book.postman_collection.json)
