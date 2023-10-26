# WordPress Recruitment System Plugin

The WordPress Recruitment System plugin provides a feature-rich recruitment management system for your WordPress website. It enables administrators to manage job vacancies and allows users to apply for job positions. This README file provides an overview of the plugin's features, installation instructions, and usage guidelines.

## Features

### Back-end Features
- **List, Add, Edit, and Delete Job Titles**: Admin users can easily manage job titles, including listing, adding, editing, and deleting them.
You can manage all of them through this link
http://localhost/wordpress/wp-content/plugins/recruitment-system/Job%20Vacancy/list-job-vacancies.php
And you'll see actions for Edit & Delete

- **Job Title Details**:
  - **Job Title**: Define job titles.
  - **Description**: Describe the job position with a maximum length of 500 characters.
  - **Department**: Assign job positions to specific departments using a single select option.
  - **Start Date**: Specify the job's start date, allowing today or future dates.
  - **End Date**: Set the job's end date, ensuring it's greater than the start date.

- **Configuration Page listing Count**: The plugin features a configuration page in the WordPress admin area. Administrators can configure the number of job titles to display on the listing page.
You will find it called "Recruitment Setting"
And can check it on this link after select the needed count
http://localhost/wordpress/wp-content/plugins/recruitment-system/Job%20Vacancy/list-allpublished.php
It depends on Using (get_option) to get count entered in the page

- **Custom Database Tables**: Upon installation, the plugin creates custom database tables to store user email addresses who have applied for vacancies.
It depends on(register_activation_hook)

- **Vacancy API**: The plugin provides a REST API endpoint for users to apply for job vacancies.
You can test it through this endpoint 
http://localhost/wordpress/wp-json/recruitment-system/v1/apply

With this example
{
    "email": "hazem@gmail.com",
    "job_id": 1
}

- **Email Notifications**: Users receive email notifications when administrators create new job vacancies.
This Part it's done after Some Steps:
1- installing WP Mail SMTP
2-choose gmail 
3-Open Gmail Console Account
4-Create a new Project
5-Within Api& Services
6-Choose Gmail API
7-It will create for you CLIENT ID,Client Secret Key
8-Put them in WP Mail SMTP 
9-Verify & Allow using this plugin your mail to send a mail through it
- **Vacancy Status**: Vacancies are automatically unpublished once they reach their end date 
once you visit this page 
http://localhost/wordpress/wp-content/plugins/recruitment-system/Job%20Vacancy/list-job-vacancies.php
The (Published) jobs will be (Unpublished) if they reach end date

### Front-end Features
- **Listing Page**: A front-end page displays published job vacancies, allowing users to explore available job positions.
http://localhost/wordpress/wp-content/plugins/recruitment-system/Job%20Vacancy/list-allpublished.php

You can check it with this link
- **Job Details Page**: Users can click on a job title to access a dedicated job details page with comprehensive information about the vacancy.
After clicking on job_title you'll open a detailed page

- **Sidebar Widget**: A sidebar widget lists the latest 5 vacancies, providing quick access to recent job opportunities.

## Installation

To install the WordPress Recruitment System plugin, follow these steps:

1. Download the plugin ZIP file from  this repository.

2. Go to your WordPress admin dashboard.

3. Navigate to the "Plugins" section.

4. Click "Add New" and then "Upload Plugin."

5. Upload the plugin ZIP file.

6. Activate the plugin.

## Usage

### Managing Job Titles


### Applying for Job Vacancies


### Viewing Latest Vacancies (Widget)
Users can find the latest job vacancies listed in the sidebar widget.
It depends on the theme used so please double check if your theme exist widgets or not
If not please change it and please double check if (function.php) in your theme contains These line:

 function register_latest_vacancies_widget() {
       register_widget('LatestVacanciesWidget');
   }
   add_action('widgets_init', 'register_latest_vacancies_widget');


## License

This project is licensed under the [Hazem ELkady] 