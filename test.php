<?php
// Retrieve the PDO object from the connection file
require_once('dbconn.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Screen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
        }

        .main-content {
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-4 sidebar bg-light">
                <h2>Question List</h2>
                <ul class="list-group">
                    <?php
                    // Fetch all questions from ac_questiondb table
                    $stmt = $pdo->query("SELECT * FROM ac_questiondb");
                    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($questions as $question) {
                        echo '<li class="list-group-item"><a href="?question_id=' . $question['qstn_id'] . '">' . $question['qstn'] . '</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <div class="col-8 main-content">
                <?php
                    // Check if a specific question is selected
                    if (isset($_GET['question_id'])) {
                        $questionId = $_GET['question_id'];

                        // Retrieve the question details from ac_questiondb table
                        $stmt = $pdo->prepare("SELECT * FROM ac_questiondb WHERE qstn_id = ?");
                        $stmt->execute([$questionId]);
                        $question = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($question) {
                            // Display the question details
                            echo '<h2>' . $question['qstn'] . '</h2>';
                            echo '<p>Exam: ' . $question['qstn_exam'] . '</p>';
                            echo '<p>Year: ' . $question['qstn_year'] . '</p>';
                            echo '<p>Options:</p>';
                            echo '<form action="" method="POST">';

                            echo '
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answer" id="option1" value="Option 1">
                                <label class="form-check-label" for="option1">' . $question['option1'] . '</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answer" id="option2" value="Option 2">
                                <label class="form-check-label" for="option2">' . $question['option2'] . '</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answer" id="option3" value="Option 3">
                                <label class="form-check-label" for="option3">' . $question['option3'] . '</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answer" id="option4" value="Option 4">
                                <label class="form-check-label" for="option4">' . $question['option4'] . '</label>
                            </div>
                            ';

                            echo '<button type="submit" class="btn btn-primary">Check Answer</button>';

                            // Check if the form is submitted
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $selectedOption = $_POST['answer'];
                                $correctAnswer = $question['answer'];

                                // Compare the selected option with the correct answer
                                if ($selectedOption === $correctAnswer) {
                                    echo '<p class="text-success">Correct Answer!</p>';
                                } else {
                                    echo '<p class="text-danger">Incorrect Answer!</p>';
                                }

                                // Display the "Show Description" button if the question has been answered
                                echo '<button type="button" class="btn btn-primary" id="show-description">Show Description</button>';

                                // Display the description if the "Show Description" button is clicked
                                echo '
                                <div id="description" style="display: none;">
                                    <h3>Description:</h3>
                                    <p>' . $question['description'] . '</p>
                                </div>
                                ';

                                // Script to toggle the display of the description
                                echo '
                                <script>
                                    document.getElementById("show-description").addEventListener("click", function() {
                                        document.getElementById("description").style.display = "block";
                                    });
                                </script>
                                ';
                            }

                            echo '</form>';
                        } else {
                            echo '<p>Question not found.</p>';
                        }
                    } else {
                        echo '<p>Select a question from the sidebar.</p>';
                    }

                ?>
            </div>
        </div>
    </div>
</body>
</html>
