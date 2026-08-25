# People Status Manager

A simple HTML/CSS/JavaScript + PHP + MySQL project for InfinityFree.

## Features

- One-line form for Name and Age.
- Saves submitted records into MySQL.
- Displays all records in a table.
- Toggle button changes Status from 0 to 1 or 1 to 0.
- Uses JavaScript `fetch()` so the status changes immediately without refreshing the page.
- Uses prepared SQL statements for inserting and updating data.


## InfinityFree setup

1. Create your InfinityFree hosting account and website.
2. Open the Control Panel and go to **MySQL Databases**.
3. Create a database.
4. Copy these values from the database section:
   - MySQL Host Name
   - MySQL User Name
   - MySQL DB Name


## How the project works


1. User enters name and age.
2. JavaScript sends a POST request to `api.php`.
3. PHP validates the values and inserts them into MySQL.
4. JavaScript requests the updated records and redraws the table.

### Toggling status

1. User clicks Toggle.
2. JavaScript sends the record ID to api.php.
3. PHP changes status using:
   0 -> 1 or 1 -> 0
4. PHP returns the new status as JSON.
5. JavaScript changes the status cell immediately without reloading the page.


