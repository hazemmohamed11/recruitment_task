<?php
// Include the database connection file
require_once "../db_connect.php";
require_once "../../../../wp-load.php";


$sql = "SELECT * FROM jobvacancy "; // Limit the query to the specified count
$result = $connection->query($sql);

$counter = 0;
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/custom-style.css" />
  <script src="<?php echo get_stylesheet_directory_uri(); ?>/custom-script.js"></script>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 8px 12px;
      text-align: left;
    }
    th {
      background-color: #0073e6;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    .job-title {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Job Title</th>
        <th>Description</th>
        <th>Department</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Actions</th>
        <th>Actions</th>

      </tr>
    </thead>
    <tbody>
      <?php
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
       

        echo "<tr>";
        echo "<td>" . $row["ID"] . "</td>";
        echo "<td class='job-title'><a href='detail-page.php?job_id=" . $row["ID"] . "'>" . $row["JobTitle"] . "</a></td>";
        echo "<td>" . $row["Description"] . "</td>";
        echo "<td>" . $row["Department"] . "</td>";
        echo "<td>" . $row["StartDate"] . "</td>";
        echo "<td>" . $row["EndDate"] . "</td>";
        echo "<td><a href='delete-job-vacancy.php?id=" . $row["ID"] . "'>Delete</a></td>";
        echo "<td><a href='edit-job-vacancy.php?id=" . $row["ID"] . "'>Edit</a></td> ";


        echo "</tr>";

        $counter++;
      }
      ?>
    </tbody>
  </table>
  <a href="add-job-vacancy.php" class="add-new-job-button">Add New Job</a>

</body>
</html>

<?php


function db_connect() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recruitment_task";
  
    // Create a MySQLi connection
    $conn = new mysqli($servername, $username, $password, $dbname);
  
    // Check the connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
  
    return $conn;
  }
  
  function check_and_update_vacancies() {
    // Use your custom MySQLi database connection
    $custom_db = db_connect(); // Call your custom database connection function
  
    $current_date = date('Y-m-d H:i:s');
  
    // Define the table name without the WordPress prefix
    $table_name = 'jobvacancy';
  
    // Query the database for vacancies with an end date in the past
    $sql = "SELECT ID FROM $table_name WHERE EndDate < ? AND Status = 'published'";
  
    $stmt = $custom_db->prepare($sql);
  
    if ($stmt) {
        $stmt->bind_param("s", $current_date);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($vacancyID);
  
        while ($stmt->fetch()) {
            // Update the status to 'unpublished'
            $updateSql = "UPDATE $table_name SET Status = 'unpublished' WHERE ID = ?";
            $updateStmt = $custom_db->prepare($updateSql);
  
            if ($updateStmt) {
                $updateStmt->bind_param("i", $vacancyID);
                if ($updateStmt->execute()) {
                    echo "Vacancy ID: $vacancyID updated successfully.";
                } else {
                    echo "Error updating vacancy ID: $vacancyID. Error: " . $custom_db->error;
                }
                $updateStmt->close();
            }
        }
        $stmt->close();
    } else {
        echo "Error in SQL query: " . $custom_db->error;
    }
  }
  check_and_update_vacancies();
  
$connection = null;
?>
