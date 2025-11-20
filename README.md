Carpooling DBMS Application  
CPSC 2221 – Database Systems Project  

This web application allows students to register as **Riders** or **Providers** and find carpool matches based on **zone/section** and **address**.  
The system uses a relational database with proper foreign keys, subtype tables, and 1-to-many relationships.

---

## Project Structure



DB-carpool/
- index.html
- rider.html
- provider.html
- match.html
-  match.php
- insertRider.php
- insertProvider.php
- db.php
- database.sql  (import this file into phpMyAdmin)
- index.css


---

## Database Setup

1. Open **phpMyAdmin**
2. Create a new database: `DB_carpool`
3. Click **Import**
4. Select `database.sql` from this repository
5. Click **Go**

This will automatically create all required tables:

- `StudentUser`
- `Address`
- `Area`
- `Providers`
- `Riders`
- `Vehicle`
- `Schedules`
- `Has_an`
- `IsRidingWith`
- `PickUp`

All foreign keys, cascades, and subtypes will also be created.

---

## Configuring the Database Connection

Open **db.php** and ensure the credentials match your local environment:

```php
$conn = new mysqli("localhost", "root", "", "DB_carpool");


If your MySQL uses a password, update it here.

Running the Application

After placing the project folder inside the server’s htdocs directory:

http://localhost/DB-carpool/index.html

Pages Available
Page	Description
index.html	Homepage
rider.html	Register a Rider
provider.html	Register a Provider
match.html	Search for matches
match.php	Backend page that displays results
Features
Register Rider

The system collects:

Name

Student ID

Email

Phone

Address (Street, Number, City, Postal Code)

Zone/Section

Inserts into:

StudentUser

Address

Riders

✔ Register Provider

Collectors all Rider fields plus:

Vehicle Info (plate, model, owner ID)

Seat availability

Schedules

Inserts into:

StudentUser

Address

Providers

Vehicle (linked via OwnerStudentID)

✔ Find Matches

A user selects:

Role (Rider/Provider)

Section (A–D)

Optional: arrival time

match.php returns matches from:

Providers → for Riders

Riders → for Providers

All results include:

Name

Address

Car details (for providers)

Times

Days

Presented in an HTML table.
