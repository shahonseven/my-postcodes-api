# Malaysia Postcodes API

RESTful API for Malaysian postcode lookup and search with token-based authentication.

## Base URL

```
http://localhost
```

## Authentication

All postcode API endpoints require a Bearer token. Include the token in the Authorization header:

```bash
curl -H "Authorization: Bearer YOUR_API_TOKEN" http://localhost/api/states
```

### Register New User

```
POST /api/register
```

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abc123def456..."
  }
}
```

---

### Login

```
POST /api/login
```

**Request:**
```json
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
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abc123def456..."
  }
}
```

---

### Get Current User

```
GET /api/user
```

**Headers:** `Authorization: Bearer YOUR_API_TOKEN`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

---

### Logout

```
POST /api/logout
```

**Headers:** `Authorization: Bearer YOUR_API_TOKEN`

**Response:**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

### List User Tokens

```
GET /api/tokens
```

**Headers:** `Authorization: Bearer YOUR_API_TOKEN`

---

### Revoke Specific Token

```
DELETE /api/tokens/{tokenId}
```

**Headers:** `Authorization: Bearer YOUR_API_TOKEN`

---

### Revoke All Tokens

```
DELETE /api/tokens
```

**Headers:** `Authorization: Bearer YOUR_API_TOKEN`

---

## Postcode Endpoints

All postcode endpoints require authentication.

### 1. List All States

### 1. List All States

Get all Malaysian states and federal territories with their codes.

```
GET /api/states
```

**Response:**
```json
{
  "success": true,
  "data": [
    { "name": "Johor", "code": "JHR" },
    { "name": "Selangor", "code": "SGR" },
    { "name": "Wp Kuala Lumpur", "code": "KUL" }
  ]
}
```

---

### 2. List Cities

Get all cities, optionally filtered by state or search term.

```
GET /api/cities
```

**Query Parameters:**

| Parameter | Type   | Description                      |
|-----------|--------|----------------------------------|
| `state`   | string | Filter by state code (e.g., SGR) |
| `search`  | string | Search cities by name            |

**Examples:**

```bash
# All cities
curl http://localhost/api/cities

# Cities in Selangor
curl http://localhost/api/cities?state=SGR

# Search for "Shah"
curl http://localhost/api/cities?search=Shah
```

---

### 3. Lookup Postcode

Get city and state information for a specific postcode.

```
GET /api/postcode/{postcode}
```

**Example:**

```bash
curl http://localhost/api/postcode/50000
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "postcode": "50000",
      "city": "Kuala Lumpur",
      "state": "Wp Kuala Lumpur",
      "state_code": "KUL"
    }
  ]
}
```

**404 Response (postcode not found):**
```json
{
  "success": false,
  "message": "Postcode not found"
}
```

---

### 4. Search Postcodes

Search for postcodes by city or state name.

```
GET /api/search?q={query}
```

**Query Parameters:**

| Parameter | Type   | Description                    |
|-----------|--------|--------------------------------|
| `q`       | string | Search query (required)        |

**Example:**

```bash
# Search for "Shah Alam"
curl http://localhost/api/search?q=Shah%20Alam

# Search by state
curl http://localhost/api/search?q=Selangor
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "postcode": "40000",
      "city": "Shah Alam",
      "state": "Selangor",
      "state_code": "SGR"
    }
  ],
  "meta": {
    "total": 1,
    "limit": 50
  }
}
```

**400 Response (missing query):**
```json
{
  "success": false,
  "message": "Search query is required"
}
```

---

## State Codes Reference

| Code | State/Territory            |
|------|----------------------------|
| JHR  | Johor                      |
| KDH  | Kedah                      |
| KTN  | Kelantan                   |
| MLK  | Melaka                     |
| NSN  | Negeri Sembilan            |
| PHG  | Pahang                     |
| PRK  | Perak                      |
| PLS  | Perlis                     |
| PNG  | Pulau Pinang               |
| SBH  | Sabah                      |
| SGR  | Selangor                   |
| SRW  | Sarawak                    |
| TRG  | Terengganu                 |
| KUL  | WP Kuala Lumpur            |
| LBN  | WP Labuan                  |
| PJY  | WP Putrajaya               |

---

## Database Statistics

- **Total Postcodes:** 2,932
- **Total Cities/Areas:** 443
- **States/Territories:** 16

---

## Running Tests

```bash
php artisan test --filter=PostcodeApiTest
```

---

## Environment Setup

Make sure your `.env` file has the Sanctum stateful domains configured:

```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,my-postcodes.devbox.aplikasi.cc
```

---

## Quick Start Example

```bash
# 1. Register a new user
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# 2. Use the returned token to access postcode endpoints
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  http://localhost/api/postcode/50000
```
