<?php
// Include the database connection file
require_once "../db_connect.php";
// Check if the "id" parameter is present in the URL
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $id = $_POST["id"];
    $jobTitle = $_POST["job_title"];
    $description = $_POST["description"];
    $department = $_POST["department"];
    $startDate = $_POST["start_date"];
    $endDate = $_POST["end_date"];

    try {
        // Prepare and execute the SQL query to update the job vacancy
        $sql = "UPDATE jobvacancy SET JobTitle = :jobTitle, Description = :description, Department = :department, StartDate = :startDate, EndDate = :endDate WHERE ID = :id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':jobTitle', $jobTitle, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            echo "Job vacancy has been updated successfully!";
        } else {
            echo "Failed to update the job vacancy.";
        }

        // Close the statement
        $stmt = null;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Close the database connection
$connection = null;
?>

<!-- HTML Form -->
<form method="POST" action="edit-job-vacancy.php" style="max-width: 400px; margin: 0 auto; text-align: left;">
<label for="id" style="display: block; margin-top: 10px;">Job ID:</label>
<input type="number" name="id" required value="<?php echo $id; ?>" style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
<label for="job_title" style="display: block; margin-top: 10px;">Job Title:</label>
    <input type="text" name="job_title" required maxlength="500" style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <label for="description" style="display: block;">Description:</label>
    <textarea name="description" required maxlength="500" style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>

    <label for="department" style="display: block;">Department:</label>
    <select name="department" style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <option value="HR">HR</option>
        <option value="IT">IT</option>
        <option value="Sales">Sales</option>
    </select>

    <label for="start_date" style="display: block;">Start Date:</label>
    <input type="date" name="start_date" required style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <label for="end_date" style="display: block;">End Date:</label>
    <input type="date" name="end_date" required style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <input type="submit" value="Update Job Vacancy" style="background-color: #0073e6; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
</form>
