# Rentify API Endpoints

**Base URL:** `http://localhost:8005/api`

## Authentication

### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "tenant",
  "phone": "1234567890"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Get Current User
```http
GET /api/auth/me
Authorization: Bearer {token}
```

### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

---

## Properties

### List Properties (Public - Works with/without authentication)
```http
GET /api/properties
Authorization: Bearer {token} (optional)

# Query Parameters (optional):
?location=Dhaka
&min_rent=5000
&max_rent=20000
&bedrooms=2
&rental_type=family
&property_type=apartment
&featured=true
&available=true
&search=apartment
&sort_by=rent
&sort_order=asc
&page=1
&per_page=15
```

**Note:** When authenticated as tenant, response includes `favourited` and `rented_by_me` fields.

**Example curl (without auth):**
```bash
curl -X GET "http://localhost:8005/api/properties" \
  -H "Accept: application/json"
```

**Example curl (with auth):**
```bash
curl -X GET "http://localhost:8005/api/properties" \
  -H "Authorization: Bearer 1|your-token-here" \
  -H "Accept: application/json"
```

### Get Property Details (Public - Works with/without authentication)
```http
GET /api/properties/{id}
Authorization: Bearer {token} (optional)
```

**Note:** When authenticated as tenant, response includes `favourited` and `rented_by_me` fields.

### Create Property (Landlord only)
```http
POST /api/properties
Authorization: Bearer {token}
Content-Type: multipart/form-data

property_name=Luxury Apartment
&location=Dhanmondi, Dhaka
&rent=15000
&bedrooms=3
&bathrooms=2
&property_type=apartment
&rental_type=family
&description=Spacious apartment
&size=1200
&parking=1
&furnished=1
&image=@path/to/image.jpg
```

### Update Property (Landlord only)
```http
PUT /api/properties/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

### Delete Property (Landlord only)
```http
DELETE /api/properties/{id}
Authorization: Bearer {token}
```

### Upload Gallery Images
```http
POST /api/properties/{id}/gallery
Authorization: Bearer {token}
Content-Type: multipart/form-data

images[]=@image1.jpg
images[]=@image2.jpg
```

### Delete Gallery Image
```http
DELETE /api/properties/gallery/{imageId}
Authorization: Bearer {token}
```

### Get Property Units
```http
GET /api/properties/{id}/units
Authorization: Bearer {token}
```

### Add Property Unit
```http
POST /api/properties/{id}/units
Authorization: Bearer {token}
Content-Type: application/json

{
  "unit_name": "Unit 1A",
  "floor_number": 1,
  "rent": 15000,
  "bedrooms": 3,
  "bathrooms": 2,
  "size": 1200
}
```

---

## Favourites

### Get Favourites (Tenant only)
```http
GET /api/favourites
Authorization: Bearer {token}
```

### Add to Favourites
```http
POST /api/properties/{id}/favourite
Authorization: Bearer {token}
```

### Remove from Favourites
```http
DELETE /api/properties/{id}/favourite
Authorization: Bearer {token}
```

---

## Rental Requests

### List Rental Requests
```http
GET /api/rental-requests
Authorization: Bearer {token}
```

### Get Rental Request Details
```http
GET /api/rental-requests/{id}
Authorization: Bearer {token}
```

### Create Rental Request (Tenant only)
```http
POST /api/rental-requests
Authorization: Bearer {token}
Content-Type: application/json

{
  "property_id": 1,
  "move_in_date": "2025-02-01",
  "message": "I am interested in this property"
}
```

### Approve Request (Landlord only)
```http
PUT /api/rental-requests/{id}/approve
Authorization: Bearer {token}
```

### Reject Request (Landlord only)
```http
PUT /api/rental-requests/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "Property already rented"
}
```

---

## Contracts

### List Contracts
```http
GET /api/contracts
Authorization: Bearer {token}
```

### Get Contract Details
```http
GET /api/contracts/{id}
Authorization: Bearer {token}
```

### Generate Contract (Landlord only)
```http
POST /api/contracts/request/{requestId}/generate
Authorization: Bearer {token}
Content-Type: application/json

{
  "start_date": "2025-02-01",
  "end_date": "2026-01-31",
  "rent_amount": 15000,
  "terms": "Payment due on 1st of every month"
}
```

### Download Contract PDF
```http
GET /api/contracts/{id}/pdf
Authorization: Bearer {token}
```

### Sign Contract
```http
POST /api/contracts/{id}/sign
Authorization: Bearer {token}
Content-Type: application/json

{
  "signature": "base64_encoded_signature"
}
```

### Verify Contract
```http
GET /api/contracts/{contractId}/verify
Authorization: Bearer {token}
```

### Get Contract QR Code
```http
GET /api/contracts/{contractId}/qr
Authorization: Bearer {token}
```

---

## Payments

### List Payments
```http
GET /api/payments
Authorization: Bearer {token}
```

### Get Payment Details
```http
GET /api/payments/{id}
Authorization: Bearer {token}
```

### Create Payment (Tenant only)
```http
POST /api/payments
Authorization: Bearer {token}
Content-Type: application/json

{
  "contract_id": 1,
  "amount": 15000,
  "payment_method": "wallet",
  "month": "February 2025"
}
```

### Confirm Payment (Landlord only)
```http
PUT /api/payments/{id}/confirm
Authorization: Bearer {token}
```

### Reject Payment (Landlord only)
```http
PUT /api/payments/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "Incorrect amount"
}
```

### Download Payment Receipt
```http
GET /api/payments/{id}/receipt
Authorization: Bearer {token}
```

---

## Messages

### Get Conversations
```http
GET /api/messages/conversations
Authorization: Bearer {token}
```

### Get Messages with User
```http
GET /api/messages/conversation/{userId}
Authorization: Bearer {token}
```

### Send Message
```http
POST /api/messages/{userId}
Authorization: Bearer {token}
Content-Type: application/json

{
  "message": "Hello, I'm interested in your property"
}
```

### Mark Messages as Read
```http
POST /api/messages/{userId}/read
Authorization: Bearer {token}
```

### Get Unread Count
```http
GET /api/messages/unread-count
Authorization: Bearer {token}
```

---

## Notifications

### Get Notifications
```http
GET /api/notifications
Authorization: Bearer {token}
```

### Mark as Read
```http
PUT /api/notifications/{id}/read
Authorization: Bearer {token}
```

### Mark All as Read
```http
PUT /api/notifications/read-all
Authorization: Bearer {token}
```

---

## Support Tickets

### List Tickets
```http
GET /api/tickets
Authorization: Bearer {token}
```

### Create Ticket
```http
POST /api/tickets
Authorization: Bearer {token}
Content-Type: application/json

{
  "subject": "Issue with payment",
  "category": "billing",
  "priority": "high",
  "message": "I'm having trouble processing my payment"
}
```

### Get Ticket Details
```http
GET /api/tickets/{id}
Authorization: Bearer {token}
```

### Reply to Ticket
```http
POST /api/tickets/{id}/reply
Authorization: Bearer {token}
Content-Type: application/json

{
  "message": "This is an update on my ticket"
}
```

### Update Ticket Status
```http
PUT /api/tickets/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "resolved"
}
```

---

## Reviews

### Create Property Review (Tenant only)
```http
POST /api/properties/{id}/review
Authorization: Bearer {token}
Content-Type: application/json

{
  "rating": 4,
  "comment": "Great property with good amenities"
}
```

### Get Property Reviews
```http
GET /api/properties/{id}/reviews
```

### Create Tenant Review (Landlord only)
```http
POST /api/reviews/tenants/{tenantId}
Authorization: Bearer {token}
Content-Type: application/json

{
  "rating": 5,
  "comment": "Excellent tenant, always pays on time"
}
```

### Get Tenant Reviews
```http
GET /api/reviews/tenants/{tenantId}
```

---

## Wallet

### Get Wallet Balance
```http
GET /api/wallet/balance
Authorization: Bearer {token}
```

### Add Money to Wallet
```http
POST /api/wallet/add-money
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 50000,
  "payment_method": "card",
  "transaction_id": "TXN123456"
}
```

### Get Wallet Transactions
```http
GET /api/wallet/transactions
Authorization: Bearer {token}
```

---

## Admin

### List Users
```http
GET /api/admin/users
Authorization: Bearer {token}
```

### Get User Details
```http
GET /api/admin/users/{id}
Authorization: Bearer {token}
```

### Verify User
```http
PUT /api/admin/users/{id}/verify
Authorization: Bearer {token}
```

### Block User
```http
PUT /api/admin/users/{id}/block
Authorization: Bearer {token}
Content-Type: application/json

{
  "reason": "Violation of terms"
}
```

### List Properties
```http
GET /api/admin/properties
Authorization: Bearer {token}
```

### Verify Property
```http
PUT /api/admin/properties/{id}/verify
Authorization: Bearer {token}
```

### List Reports
```http
GET /api/admin/reports
Authorization: Bearer {token}
```

### Get Report Details
```http
GET /api/admin/reports/{type}/{id}
Authorization: Bearer {token}

# type: property or user
```

### Resolve Report
```http
PUT /api/admin/reports/{type}/{id}/resolve
Authorization: Bearer {token}
Content-Type: application/json

{
  "resolution_notes": "Issue has been resolved"
}
```

### Get Activity Logs
```http
GET /api/admin/activity-logs
Authorization: Bearer {token}
```

---

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Common Response Format

**Success:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

**Pagination:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7
  }
}
```

## Authentication vs No Authentication

### Property Endpoints Differences

**Unauthenticated Request:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "property_name": "Luxury Apartment",
    "location": "Dhanmondi, Dhaka",
    "rent": 15000,
    ...
  }
}
```

**Authenticated Request (Tenant):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "property_name": "Luxury Apartment",
    "location": "Dhanmondi, Dhaka",
    "rent": 15000,
    ...
    "favourited": true,
    "rented_by_me": false
  }
}
```

**Additional Fields for Authenticated Tenants:**
- `favourited` (boolean) - Whether the property is in user's favourites
- `rented_by_me` (boolean) - Whether the user has an active rental for this property
