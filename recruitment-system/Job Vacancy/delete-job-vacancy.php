<?php
// Include the database connection file
require_once "../db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    try {
        // Retrieve the job vacancy ID
        $id = $_GET["id"];

        // Prepare and execute the SQL query to delete the job vacancy
        $sql = "DELETE FROM jobvacancy WHERE ID = :id";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            echo "Job vacancy has been deleted successfully!";
        } else {
            echo "Failed to delete the job vacancy.";
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
