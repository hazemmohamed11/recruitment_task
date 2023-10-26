<?php
// Include the database connection file
require_once "db_connect.php";
require_once "../../../../wp-load.php";

$job_titles_count = get_option('job_titles_count'); // Retrieve the job titles count from the options

$sql = "SELECT * FROM jobvacancy WHERE Status = 'Published' LIMIT $job_titles_count"; // Limit the query to the specified count and filter by status
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
        
      </tr>
    </thead>
    <tbody>
      <?php
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if ($counter >= $job_titles_count) {
          break;
        }

        echo "<tr>";
        echo "<td>" . $row["ID"] . "</td>";
        echo "<td class='job-title'><a href='detail-published.php?job_id=" . $row["ID"] . "'>" . $row["JobTitle"] . "</a></td>";
        echo "<td>" . $row["Description"] . "</td>";
        echo "<td>" . $row["Department"] . "</td>";
        echo "<td>" . $row["StartDate"] . "</td>";
        echo "<td>" . $row["EndDate"] . "</td>";
        echo "</tr>";

        $counter++;
      }
      ?>
    </tbody>
  </table>

</body>
</html>

<?php
$connection = null;
?>
