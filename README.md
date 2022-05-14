
## Installation
1. Get project and install

```
git clone
composer install
```

2. Configure .env file for DB (clean DB is best)
3. Run migration and seeders

```
php artisan migrate:fresh --seed
```

## Usage
All API request require JSON request header
```http request
Accept: application/json
```

API Tokens for Salesmen and Codelist are sent as Bearer token in request header

```http request
Authorization: Bearer 1|KgFkUI2urZDAd8GdxgeE9FAy6itmS8RTNY1ioeuU
```
### 1. Generate API Tokens

Different permission tokens are available to generate. These tokens can be use for testing permissions. No Bearer token is required for generating a user.

#### Token with ALL permissions
This multi-purpose API token allows following actions:

- get Salesmen info salesmen-get
- get single Salesman info salesmen-get
- create a new Salesman salesmen-store
- delete an existing Salesman salesmen-delete
- update an existing Salesman salesmen-update
- get Codelist codelist-get

Request
```http request
GET /api/generateUserAllAbilities
```
Sample Response
```yaml
{
    "token": "22|KsAhZzLugMQAzuMih0Wsvib88uOzAejMSWunOyp5"
}
```
#### Token with Salesmen ONLY permissions
This API token allows following actions:

- get Salesmen info salesmen-get
- get single Salesman info salesmen-get
- create a new Salesman salesmen-store
- delete an existing Salesman salesmen-delete
- update an existing Salesman salesmen-update
  
Request
```http request
GET /api/generateUserSalesmenOnly
```
Sample Response
```yaml
{
    "token": "22|KsAhZzLugMQAzuMih0Wsvib88uOzAejMSWunOyp5"
}
```

#### Token with Codelist ONLY permissions
This API token allows following actions:

- get Codelist codelist-get

Request
```http request
GET /api/generateUserCodelistOnly
```
Sample Response
```yaml
{
    "token": "22|KsAhZzLugMQAzuMih0Wsvib88uOzAejMSWunOyp5"
}
```

#### Token with Salesmen GET ONLY permissions
This API token allows following actions:

- get Salesmen info salesmen-get
- get single Salesman info salesmen-get

Request
```http request
GET /api/generateUserSalesmenGetOnly
```
Sample Response
```yaml
{
    "token": "22|KsAhZzLugMQAzuMih0Wsvib88uOzAejMSWunOyp5"
}
```

### 2. Generate API requests

#### Get single Salesman info
Requires token with salesmen-get
```http request
GET /api/salesmen/{salesman_uuid}
```
- salesman_uuid - required, Salesman UUID
#### Get multiple Salesmen info
Requires token with salesmen-get
```http request
GET /api/salesmen?{page=?}{per_page=?}{sort=?}
```
- page - optional, int
- per_page - optional, int
- sort - optional, string, column name
#### Delete a Salesman
Requires token with salesmen-delete
```http request
DELETE /api/salesmen/{salesman_uuid}
```
- salesman_uuid - required, Salesman UUID
#### Update a Salesman
Requires token with salesmen-update
```http request
PUT /api/salesmen/{salesman_uuid}
```
- salesman_uuid - required, Salesman UUID
#### Create a new Salesman
Requires token with salesmen-store
```http request
POST /api/salesmen
```
#### Get Codelist
Requires token with codelist-get
```http request
GET /api/codelist
```
