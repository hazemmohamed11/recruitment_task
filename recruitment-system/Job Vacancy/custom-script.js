   // JavaScript for the job vacancies table
   document.addEventListener('DOMContentLoaded', function() {
    var jobTitleLinks = document.querySelectorAll('.job-title a');
    for (var i = 0; i < jobTitleLinks.length; i++) {
      jobTitleLinks[i].addEventListener('click', function(e) {
        e.preventDefault();
        var jobID = this.getAttribute('href').split('=')[1];
        window.location.href = 'detail-page.php?job_id=' + jobID; // Replace 'detail-page.php' with the URL of your detail page template
      });
    }
  });