<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }

        .job-details {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            font-size: 24px;
            margin: 0 0 10px;
        }

        p {
            font-size: 16px;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="job-details">
        <?php
        require_once "db_connect.php";

        // Retrieve the job ID from the URL query parameter
        $job_id = isset($_GET["job_id"]) ? $_GET["job_id"] : null;

        if ($job_id) {
            // Fetch the job details from your database using the job ID
            $sql = "SELECT * FROM jobvacancy WHERE ID = :job_id";
            $query = $connection->prepare($sql);
            $query->bindParam(":job_id", $job_id, PDO::PARAM_INT);
            $query->execute();

            // Check if a matching job is found
            if ($query->rowCount() > 0) {
                $job = $query->fetch(PDO::FETCH_ASSOC);
                // Display the job details
                echo "JobTitle: <h2>" . $job["JobTitle"] . "</h2>";
                echo"";
                echo "Description: <p>" . $job["Description"] . "</p>";
                // Display other job details as needed
            } else {
                // Handle the case when no matching job is found
                echo "<div class='error'>Job not found!</div>";
            }
        } else {
            // Handle the case when no job ID is provided
            echo "<div class='error'>No job ID provided.</div>";
        }
        ?>

    </div>
    <a href="list-job-vacancies.php" class="add-new-job-button">Back</a>

</body>
</html>
