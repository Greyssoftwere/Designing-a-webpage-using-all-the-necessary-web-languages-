<?php
// The page itself is HTML. PHP is used here only to load the page.
// All database operations are handled through api.php.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People Status Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <section class="card">
            <h1>People Status Manager</h1>
            <p class="subtitle">Add a person and manage their status.</p>

            <form id="personForm" class="person-form">
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Name"
                    maxlength="100"
                    required
                >

                <input
                    type="number"
                    id="age"
                    name="age"
                    placeholder="Age"
                    min="1"
                    max="120"
                    required
                >

                <button type="submit">Submit</button>
            </form>

            <p id="message" class="message" aria-live="polite"></p>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="peopleTableBody">
                        <!-- Records are inserted here by JavaScript -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
