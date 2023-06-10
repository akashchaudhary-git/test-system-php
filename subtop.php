<?php
// Retrieve the PDO object from the connection file
require_once('dbconn.php');

// Function to fetch all subjects
function getAllSubjects()
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM ac_subjects");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error occurred while fetching subjects: " . $e->getMessage());
    }
}

// Function to fetch all topics
function getAllTopics()
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM ac_topics");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error occurred while fetching topics: " . $e->getMessage());
    }
}

// Function to fetch all topics by subject
function getAllTopicsBySubject($subjectId)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM ac_topics INNER JOIN ac_subjects ON ac_topics.subject_id = ac_subjects.subject_id WHERE ac_subjects.subject_id = ?");
        $stmt->bindParam(1, $subjectId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error occurred while fetching topics by subject: " . $e->getMessage());
    }
}


// Check if the subject form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitSubject'])) {
    $subject = $_POST['subject'];

    try {
        // Prepare the INSERT statement for subjects
        $stmt = $pdo->prepare("INSERT INTO ac_subjects (subject) VALUES (?)");
        $stmt->bindParam(1, $subject);
        $stmt->execute();

        // Redirect or display success message
        //header("Location: success.php");
        
    } catch (PDOException $e) {
        die("Error occurred during subject creation: " . $e->getMessage());
    }
}

// Check if the topic form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitTopic'])) {
    $topic = $_POST['topic'];
    $subject = $_POST['subject_id'];

    try {
        // Prepare the INSERT statement for topics
        $stmt = $pdo->prepare("INSERT INTO ac_topics (subject, topic) VALUES (?, ?)");
        $stmt->bindParam(1, $subject);
        $stmt->bindParam(2, $topic);
        $stmt->execute();

        // Redirect or display success message
        //header("Location: success.php");
        //exit();
    } catch (PDOException $e) {
        die("Error occurred during topic creation: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Subject and Topic</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Create Subject</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject:</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submitSubject">Submit</button>
                </form>
            </div>
            <div class="col-md-6">
                <h2>Create Topic</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject:</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <?php
                            // Fetch all subjects from ac_subjects table
                            $subjects = getAllSubjects();
                            foreach ($subjects as $subject) {
                                echo "<option value='{$subject['subject_id']}'>{$subject['subject']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="topic" class="form-label">Topic:</label>
                        <input type="text" class="form-control" id="topic" name="topic" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submitTopic">Submit</button>
                </form>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <h2>Subjects</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subject ID</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display all subjects
                        foreach ($subjects as $subject) {
                            echo "<tr>";
                            echo "<td>{$subject['subject_id']}</td>";
                            echo "<td>{$subject['subject']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
             <!-- 
             =============================   
                    Topics
             =============================
             -->
            <div class="col-md-6">
                <h2>Topics</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="filter_subject" class="form-label">Filter by Subject:</label>
                        <select class="form-select" id="filter_subject" name="filter_subject" onchange="this.form.submit()">
                            <option value="">-- Select --</option>
                            <?php
                            // Fetch all subjects from ac_subjects table
                            $subjects = getAllSubjects();
                            foreach ($subjects as $subject) {
                                $selected = ($_POST['filter_subject'] == $subject['subject_id']) ? 'selected' : '';
                                echo "<option value='{$subject['subject_id']}' $selected>{$subject['subject']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Topic ID</th>
                            <th>Subject</th>
                            <th>Topic</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
            $selectedSubject = isset($_POST['filter_subject']) ? $_POST['filter_subject'] : '';

            if ($selectedSubject && $selectedSubject !== '') {
                // Fetch filtered topics based on the selected subject
                $filteredTopics = getAllTopicsBySubject($selectedSubject);

                if (empty($filteredTopics)) {
                    echo "<tr><td colspan='3'>No topics found for the selected subject.</td></tr>";
                } else {
                    foreach ($filteredTopics as $topic) {
                        echo "<tr>";
                        echo "<td>{$topic['topic_id']}</td>";
                        echo "<td>{$topic['subject']}</td>";
                        echo "<td>{$topic['topic']}</td>";
                        echo "</tr>";
                    }
                }
            } else {
                // Display all topics
                if (isset($topics)) {
                    foreach ($topics as $topic) {
                        echo "<tr>";
                        echo "<td>{$topic['topic_id']}</td>";
                        echo "<td>{$topic['subject']}</td>";
                        echo "<td>{$topic['topic']}</td>";
                        echo "</tr>";
                    }
                }
            }
            ?>
                    </tbody>
                </table>
            </div>



            <!-- <div class="col-md-6">
                <h2>Topics</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="filter_subject" class="form-label">Filter by Subject:</label>
                        <select class="form-select" id="filter_subject" name="filter_subject" onchange="this.form.submit()">
                            <option value="">All Subjects</option>
                            <?php
                            // Fetch all subjects from ac_subjects table
                            $subjects = getAllSubjects();
                            foreach ($subjects as $subject) {
                                $selected = ($_POST['filter_subject'] == $subject['subject_id']) ? 'selected' : '';
                                echo "<option value='{$subject['subject_id']}' $selected>{$subject['subject']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Topic ID</th>
                            <th>Subject</th>
                            <th>Topic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch filtered topics based on the selected subject
                        $selectedSubject = $_POST['filter_subject'];
                        if ($selectedSubject) {
                            $filteredTopics = getAllTopicsBySubject($selectedSubject);
                            foreach ($filteredTopics as $topic) {
                                echo "<tr>";
                                echo "<td>{$topic['topic_id']}</td>";
                                echo "<td>{$topic['subject']}</td>";
                                echo "<td>{$topic['topic']}</td>";
                                echo "</tr>";
                            }
                        } else {
                            foreach ($topics as $topic) {
                                echo "<tr>";
                                echo "<td>{$topic['topic_id']}</td>";
                                echo "<td>{$topic['subject']}</td>";
                                echo "<td>{$topic['topic']}</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
 -->

            <!-- <div class="col-md-6">
                <h2>Topics</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Topic ID</th>
                            <th>Subject</th>
                            <th>Topic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all topics
                        // $topics = getAllTopics();
                        // foreach ($topics as $topic) {
                        //     echo "<tr>";
                        //     echo "<td>{$topic['topic_id']}</td>";
                        //     echo "<td>{$topic['subject']}</td>";
                        //     echo "<td>{$topic['topic']}</td>";
                        //     echo "</tr>";
                        // }
                        ?>
                    </tbody>
                </table>
            </div> -->
        </div>
    </div>
</body>
</html>
