<?php
class LatestVacanciesWidget extends WP_Widget {
    private $connection; // Store the database connection

    public function __construct() {
        parent::__construct(
            'latest_vacancies_widget',
            'Latest Vacancies Widget',
            array('description' => 'Display the latest 5 vacancies.')
        );

        // Establish the database connection in the constructor
        $this->connection = $this->db_connect();
    }

    // Establish the database connection using PDO
    private function db_connect() {
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $db_name = 'recruitment_task';

        try {
            $dsn = "mysql:host=$host;dbname=$db_name";
            $connection = new PDO($dsn, $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        // Query the database for the last 5 jobs
        $table_name = $this->connection->quote($this->connection->getAttribute(PDO::MYSQL_ATTR_PREFIX) . 'jobvacancy');
        $query = "SELECT * FROM $table_name ORDER BY StartDate DESC LIMIT 5";
        
        // Debugging: Output the SQL query
        echo '<p>SQL Query: ' . $query . '</p>';
        
        $result = $this->connection->query($query);

        if ($result) {
            echo '<h2>Latest Vacancies</h2>';
            echo '<ul>';
            foreach ($result as $job) {
                $job_title = esc_html($job['job_title']);
                $job_link = get_permalink($job['ID']);
                echo "<li><a href='$job_link'>$job_title</a></li>";
            }
            echo '</ul>';
        } else {
            echo '<p>No vacancies found.</p>';
            
            // Add debugging information
            echo '<p>Error Info: ';
            print_r($this->connection->errorInfo());
            echo '</p>';
        }

        echo $args['after_widget'];
    }


}

