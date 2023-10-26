<?php
// Include the database connection file
require_once "../db_connect.php";
require_once "../../../../wp-load.php";
if (function_exists('wp_mail')) {
    echo "Mail will be sent after filling these data.";
} else {
    echo "Mail Won't be sent after filling these data.";
}
// Function to send an email notification to all applicants
function sendEmailToApplicants($jobTitle, $description) {
    global $connection;

    // Retrieve all applicant email addresses from the database
    $sql = "SELECT Email FROM applicants";
    $stmt = $connection->query($sql);
    $recipients = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Define the email subject and message
    $subject = 'New Job Vacancy: ' . $jobTitle;
    $message = "A new job vacancy has been created:\n\n";
    $message .= "Job Title: $jobTitle\n";
    $message .= "Description:\n$description";

    // Send the email to each applicant
    foreach ($recipients as $recipient) {
        // You may want to add a debugger to check each recipient
        error_log("Sending email to: $recipient");
        if (wp_mail($recipient, $subject, $message)) {
            echo "Email sent successfully.";
        } else {
            echo "Email sending failed.";
        }    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $jobTitle = $_POST["job_title"];
    $description = $_POST["description"];
    $department = $_POST["department"];
    $startDate = $_POST["start_date"];
    $endDate = $_POST["end_date"];
// Validate the End Date
if (strtotime($endDate) <= strtotime($startDate)) {
    echo "End Date must be greater than Start Date.";
} else {
    // Prepare and execute the SQL query to insert a new job vacancy
    $sql = "INSERT INTO jobvacancy (JobTitle, Description, Department, StartDate, EndDate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->execute([$jobTitle, $description, $department, $startDate, $endDate]);

    // Check if the insertion was successful
    if ($stmt->rowCount() > 0) {
        // Send an email notification to all applicants
        sendEmailToApplicants($jobTitle, $description);

        echo "New job vacancy has been added successfully!";
    } else {
        // You may want to log an error message if the insertion failed
        error_log("Failed to add the job vacancy.");
        echo "Failed to add the job vacancy.";
    }
}
}

// Close the statement
$stmt = null;
?>
<!-- HTML Form -->
<form method="POST" action="add-job-vacancy.php" style="max-width: 400px; margin: 0 auto; text-align: left;">
    <label for="job_title" style="display: block; margin-top: 10px;">Job Title:</label>
    <input type="text" name="job_title" required maxlength="500" style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <label for="description" style="display: block;">Description:</label>
    <textarea name="description" required maxlength="500" style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>

    <label for="department" style="display: block;">Department:</label>
    <select name="department" style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <option value="HR">HR</option>
        <option value="IT">IT</option>
        <option value="Sales">Sales</option>
        <!-- Add more department options as needed -->
    </select>

    <label for="start_date" style="display: block;">Start Date:</label>
    <input type="date" name="start_date" required style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <label for="end_date" style="display: block;">End Date:</label>
    <input type="date" name="end_date" required style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">

    <input type="submit" value="Add Job Vacancy" style="background-color: #0073e6; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
</form>

<a href="list-job-vacancies.php" class="add-new-job-button">Back</a>
