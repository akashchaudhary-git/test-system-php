<?php
// Retrieve the PDO object from the connection file
require_once('dbconn.php');

// Fetch all subjects
    try {
        $stmt = $pdo->query("SELECT * FROM ac_subjects");
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error occurred while fetching subjects: " . $e->getMessage());
    }

   
// Fetch all topics
try {
    $stmt = $pdo->query("SELECT * FROM ac_topics");
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error occurred while fetching topics: " . $e->getMessage());
}


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    



    // Retrieve form data
    $qstn = $_POST['qstn'];
    $qstn_exam = $_POST['qstn_exam'];
    $qstn_subject = $_POST['qstn_subject'];
    $qstn_topic = $_POST['qstn_topic'];
    $qstn_year = $_POST['qstn_year'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $answer = $_POST['answer'];
    $description = $_POST['description'];

    try {
        // Prepare the INSERT statement
        $stmt = $pdo->prepare("INSERT INTO ac_questiondb (qstn, qstn_exam, qstn_subject, qstn_topic, qstn_year, option1, option2, option3, option4, answer, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters to the statement
        $stmt->bindParam(1, $qstn);
        $stmt->bindParam(2, $qstn_exam);
        $stmt->bindParam(3, $qstn_subject);
        $stmt->bindParam(4, $qstn_topic);
        $stmt->bindParam(5, $qstn_year);
        $stmt->bindParam(6, $option1);
        $stmt->bindParam(7, $option2);
        $stmt->bindParam(8, $option3);
        $stmt->bindParam(9, $option4);
        $stmt->bindParam(10, $answer);
        $stmt->bindParam(11, $description);

        // Execute the statement
        $stmt->execute();


        // Redirect to a success page or do something else
        //header("Location: success.php");
        //exit();
    } catch (PDOException $e) {
        die("Error occurred during data insertion: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AC Question Form</title>

    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.0/lux/bootstrap.min.css' />
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> -->
    <style type="text/css">
        body{
            background-color: cornflowerblue;
        }
    </style>
</head>
<body >
    <div class="container">
        <div class="border my-3 rounded-3 shadow-sm p-3 bg-white">
            <p class="display-6 text-center">AC Question Form</p>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="mb-3 col-9">
                        <label for="qstn_exam" class="form-label">Exam:</label>
                        <input type="text" class="form-control bg-primary text-white" id="qstn_exam" name="qstn_exam" required>
                    </div>
                    <div class="mb-3 col-3">
                        <label for="qstn_year" class="form-label">Year:</label>
                        <input type="number" class="form-control bg-primary text-white" id="qstn_year" name="qstn_year" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="qstn" class="form-label">Question:</label>
                    <textarea class="form-control" id="qstn" name="qstn" rows="4" required></textarea>
                </div>
                <div class="row">
                    <div class="mb-3 col-4">
                        <label for="qstn_subject" class="form-label">Subject:</label>
                        <select class="form-select bg-success text-white" id="qstn_subject" name="qstn_subject" required>
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['subject_id']; ?>"><?php echo $subject['subject']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 col-8">
                        <label for="qstn_topic" class="form-label">Topic:</label>
                        <select class="form-select bg-success text-white" id="qstn_topic" name="qstn_topic" required>
                            <option value="">Select Topic</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col">
                        <label for="option1" class="form-label">Option 1:</label>
                        <input type="text" class="form-control" id="option1" name="option1" required>
                    </div>
                    <div class="mb-3 col">
                        <label for="option2" class="form-label">Option 2:</label>
                        <input type="text" class="form-control" id="option2" name="option2" required>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col">
                        <label for="option3" class="form-label">Option 3:</label>
                        <input type="text" class="form-control" id="option3" name="option3" required>
                    </div>
                    <div class="mb-3 col">
                        <label for="option4" class="form-label">Option 4:</label>
                        <input type="text" class="form-control" id="option4" name="option4" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="answer" class="form-label">Answer:</label>
                    <select type="text" class="form-select bg-warning" id="answer" name="answer" required>
                        <option value=""></option>
                        <option value="Option 1">A</option>
                        <option value="Option 2">B</option>
                        <option value="Option 3">C</option>
                        <option value="Option 4">D</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <textarea type="text" class="form-control" id="description" name="description" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Store the initial list of topics
        var allTopics = <?php echo json_encode($topics); ?>;
        
        // Update the topic dropdown options when the subject dropdown value changes
        $('#qstn_subject').change(function() {
            var selectedSubject = $(this).val();
            
            // Clear the current options
            $('#qstn_topic').empty();
            
            if (selectedSubject !== '') {
                // Filter topics based on the selected subject
                var filteredTopics = allTopics.filter(function(topic) {
                    return topic.subject_id.toString() === selectedSubject;
                });
                
                // Check if any filtered topics exist
                if (filteredTopics.length > 0) {
                    // Add the filtered topics to the dropdown
                    filteredTopics.forEach(function(topic) {
                        $('#qstn_topic').append('<option value="' + topic.topic_id + '">' + topic.topic + '</option>');
                    });
                } else {
                    // If no filtered topics exist, display a default option
                    $('#qstn_topic').append('<option value="">No topics available</option>');
                }
            } else {
                // If no subject is selected, display a default option
                $('#qstn_topic').append('<option value="">Select Topic</option>');
            }
        });
    });
</script>

</body>
</html>
