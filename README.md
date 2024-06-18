# Delivery Service API

This project provides an API for managing orders and deliveries for a delivery service application. It includes functionalities for clients to create and cancel orders, and for couriers to view, accept, and update the status of deliveries.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [API Endpoints](#api-endpoints)
  - [Order Endpoints](#order-endpoints)
  - [Delivery Endpoints](#delivery-endpoints)
- [Running Tests](#running-tests)
- [Models and Observers](#models-and-observers)
- [Comments](#comments)

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/coderahh/MartinDeliver.git
    cd delivery-service-api
    ```

2. Install dependencies:

    ```bash
    composer install
    ```

3. Copy the example environment file and configure your environment variables:

    ```bash
    cp .env.example .env
    ```
4. Generate an application key:

    ```bash
    php artisan key:generate
    ```

5. Run the database migrations and seeders:

    ```bash
    php artisan migrate --seed
    ```

## Configuration

Ensure that you have configured the necessary environment variables in your `.env` file, such as database connection details and other relevant settings.

## API Endpoints

### Order Endpoints
## Create a New Order

**URL:** `/api/order`

**Method:** `POST`

**Request Body:**

```json
{
  "token": "client_token",
  "pickup_name": "John Doe",
  "pickup_mobile": "1234567890",
  "pickup_address": "123 Main St",
  "pickup_lat": 40.7128,
  "pickup_long": -74.0060,
  "delivery_name": "Jane Doe",
  "delivery_mobile": "0987654321",
  "delivery_address": "456 Elm St",
  "delivery_lat": 40.7128,
  "delivery_long": -74.0060
}       
```
**Response:**

```json
{
  "service_request_id": 1
}
```
## Cancel an Order

**URL:** `/api/order/{id}/cancel`

**Method:** `PUT`

**Request Body:**

```json
{
  "token": "client_token"
}
```
**Response:**

```json
{
  "message": "Order cancelled."
}
```
## Delivery Endpoints

### Get Available Orders

**URL:** `/api/delivery/orders`

**Method:** `POST`

**Request Body:**

```json
{
  "token": "courier_token"
}
```
**Response:**

```json
{
  "orders": [
    {
      "id": 1,
      "pickup_name": "John Doe",
      "pickup_mobile": "1234567890",
      "pickup_address": "123 Main St",
      "pickup_lat": 40.7128,
      "pickup_long": -74.0060,
      "delivery_name": "Jane Doe",
      "delivery_mobile": "0987654321",
      "delivery_address": "456 Elm St",
      "delivery_lat": 40.7128,
      "delivery_long": -74.0060,
      "status": 0,
      "client_id": 1
    }
    // More orders...
  ]
}
```
## Accept an Order

**URL:** `/api/delivery/{id}/accept`

**Method:** `POST`

**Request Body:**

```json
{
  "token": "courier_token",
  "lat": 40.7128,
  "long": -74.0060
}
```
**Response:**

```json
{
  "delivery": {
    "order_id": 1,
    "courier_id": 1,
    "status": 0,
    "lat": 40.7128,
    "long": -74.0060,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

## Update Delivery Status

**URL:** `/api/delivery/{id}/status`

**Method:** `PUT`

**Request Body:**

```json
{
  "token": "courier_token",
  "status": 2, // STATUS_PICKED_UP
  "lat": 40.7128,
  "long": -74.0060
}
```
**Response:**

```json
{
   "delivery_id": 1
}
```

## Running Tests
To run the tests for the application, use the following command:

```bash
php artisan test
```
The test suite includes feature tests for the OrderController and DeliveryController to ensure the correct functionality of order creation, cancellation, and delivery management.


## Models and Observers

## Models
 ### User
 The User model represents a user in the system, which can be either a client or a courier. It includes relationships to the Order and Delivery models and handles authentication.

### Order
 The Order model represents an order placed by a client. It includes attributes such as pickup and delivery details and the order status. It has a relationship with the User model (client).

### Delivery
 The Delivery model represents a delivery task assigned to a courier. It includes attributes such as the order ID, courier ID, status, and location. It has relationships with the Order and User models.

## Observers
### DeliveryObserver
 The DeliveryObserver class handles events related to the Delivery model, such as the creation and updating of deliveries. It sends HTTP POST requests to the client's webhook URL with delivery details.

## Comments
The codebase includes comments for classes, methods, and specific sections of code to provide clarity on their purpose and functionality. These comments help in understanding the flow of the application and the roles of different components.

