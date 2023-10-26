            <?php
            /*
            Plugin Name: Recruitment System
            Description: Recruitment system
            Version: 1.0
            Author: Hazem Elkady
            */

            // Add the configuration page to the admin menu
            require_once "db_connect.php";

            function add_recruitment_settings_page() {
                add_menu_page('Recruitment System Settings', 'Recruitment Settings', 'manage_options', 'recruitment_settings_page', 'render_configuration_page');
                register_setting('recruitment_settings_group', 'job_titles_count'); // Use the same group name here
            }
            add_action('admin_menu', 'add_recruitment_settings_page');

            // Render the configuration page
            function render_configuration_page() {
                if (isset($_POST['job_titles_count'])) {
                    $job_titles_count = sanitize_text_field($_POST['job_titles_count']);
                    update_option('job_titles_count', $job_titles_count);
                }
                ?>
                <div class="wrap">
                    <h2>Recruitment System Settings</h2>
                    <form method="post" action="options.php">
                        <?php settings_fields('recruitment_settings_group'); ?>
                        <?php do_settings_sections('recruitment_settings_page'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Count of Job Titles to Display</th>
                                <td>
                                    <?php
                                    $job_titles_count = get_option('job_titles_count');
                                    ?>
                                    <input type="number" name="job_titles_count" value="<?php echo esc_attr($job_titles_count); ?>" />
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>
                </div>
                <?php
            }
            add_action('rest_api_init', 'register_job_application_endpoint');

            function register_job_application_endpoint() {
                register_rest_route('recruitment-system/v1', '/apply', array(
                    'methods'  => 'POST',
                    'callback' => 'handle_job_application_request',
                    'permission_callback' => '__return_true', // Allow unauthenticated requests for testing purposes
                ));
            }


            function handle_job_application_request($request) {
                $parameters = $request->get_json_params();

                // Debugging: Log the received parameters to error log
                error_log('Received parameters: ' . print_r($parameters, true));

                // Extract data from the request
                $email = sanitize_email($parameters['email']);
                $job_id = intval($parameters['job_id']); // Assuming job_id is an integer

                // Debugging: Log the extracted data
                error_log('Email: ' . $email);
                error_log('Job ID: ' . $job_id);
                global $connection;

                // Save the application to the database
                $application_date = date('Y-m-d H:i:s');

                try {
                  
                    $table_name = 'applicants'; // Assuming 'applicants' is the table name

                    // Prepare the SQL statement
                    $statement = $connection->prepare("INSERT INTO $table_name (email,JobID,ApplicationDate) VALUES (:email, :job_id, :application_date)");

                    // Bind parameters
                    $statement->bindParam(':email', $email);
                    $statement->bindParam(':job_id', $job_id);
                    $statement->bindParam(':application_date', $application_date);

                    // Execute the statement
                    $result = $statement->execute();

                    if ($result) {
                        $response = array('message' => 'Application submitted successfully.');
                        return rest_ensure_response($response);
                    } else {
                        $response = array('error' => 'Failed to submit the application.');
                        return rest_ensure_response($response, 5003);
                    }
                } catch (PDOException $e) {
                    $response = array('error' => 'Database connection failed: ' . $e->getMessage());
                    return rest_ensure_response($response, 5001);
                }
            }

            function db_connect() {
                // Replace this with your database connection logic
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "recruitment_task";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection 
                if ($conn->connect_error) {
                    die("Database connection failed: " . $conn->connect_error);
                }

                return $conn;
            }

            function check_and_update_vacancies() {
                // Use your custom database connection
                $custom_db = db_connect(); // Call your custom database connection function

                $current_date = date('Y-m-d H:i:s');

                // Query the database for vacancies with an end date in the past
                // $sql = "SELECT ID FROM {$custom_db->real_escape_string($custom_db->prefix)}jobvacancy WHERE EndDate < ? AND Status = 'published'";

                global $wpdb;
            $table_name = $wpdb->prefix . 'jobvacancy';
            $sql = $wpdb->prepare("SELECT ID FROM $table_name WHERE EndDate < %s AND Status = 'published'", $current_date);

                $stmt = $custom_db->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("s", $current_date);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($vacancyID);

                    while ($stmt->fetch()) {
                        // Update the status to 'unpublished'
                        $updateSql = "UPDATE {$custom_db->real_escape_string($custom_db->prefix)}jobvacancy SET Status = 'unpublished' WHERE ID = ?";
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
                }
            }
            class LatestVacanciesWidget extends WP_Widget {
                private $connection; // Store the database connection

                public function __construct() {
                    parent::__construct(
                        'latest-vacancies-widget',
                        'Latest Vacancies Widget',
                        array('description' => 'Display the latest vacancies.')
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

                    echo $args['before_title'] . 'Latest Vacancies' . $args['after_title'];

                    $table_name = $this->connection->quote($this->connection->getAttribute(PDO::MYSQL_ATTR_PREFIX) . 'jobvacancy');
                    $query = "SELECT * FROM $table_name ORDER BY ID DESC LIMIT 5";
                    $result = $this->connection->query($query);

                    if ($result) {
                        echo '<ul>';

                        foreach ($result as $vacancy) {
                            echo '<li><a href="#">' . $vacancy['JobTitle'] . '</a></li>';
                        }

                        echo '</ul>';
                    } else {
                        echo 'No vacancies found.';
                    }

                    echo $args['after_widget'];
                }
            }


            // Define the activation hook function
            function my_plugin_activation() {
                // Create the custom tables when the plugin is activated
                global $wpdb;
                $charset_collate = $wpdb->get_charset_collate();
                
                // Create the applicants table
                $applicants_table_name = $wpdb->prefix . 'applicants';
                $sql_applicants = "CREATE TABLE $applicants_table_name (
                    ID int(11) NOT NULL AUTO_INCREMENT,
                    Email varchar(255) NOT NULL,
                    JobID int(11) DEFAULT NULL,
                    ApplicationDate date DEFAULT NULL,
                    PRIMARY KEY (ID),
                    CONSTRAINT applicants_ibfk_1 FOREIGN KEY (JobID) REFERENCES jobvacancy (ID) ON DELETE CASCADE
                ) $charset_collate;";
                dbDelta($sql_applicants);

            

                // Create the jobvacancy table
                $jobvacancy_table_name = $wpdb->prefix . 'jobvacancy';
                $sql_jobvacancy = "CREATE TABLE $jobvacancy_table_name (
                    ID int(11) NOT NULL AUTO_INCREMENT,
                    JobTitle varchar(255) NOT NULL,
                    Description varchar(500) DEFAULT NULL,
                    Department varchar(255) DEFAULT NULL,
                    StartDate datetime DEFAULT NULL,
                    EndDate datetime DEFAULT NULL,
                    Status varchar(20) DEFAULT 'published',
                    PRIMARY KEY (ID),
                ) $charset_collate;";
                dbDelta($sql_jobvacancy);
            }

            // Register the activation hook
            register_activation_hook(__FILE__, 'my_plugin_activation');
            check_and_update_vacancies();