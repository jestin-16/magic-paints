<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "projectm");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all messages along with the count of responses and response_given status
$message_sql = "
    SELECT m.id, m.message, c.cust_name, m.response_given,
           (SELECT COUNT(*) FROM message_responses WHERE message_id = m.id) AS response_count
    FROM messages m
    LEFT JOIN customer c ON m.user_id = c.cust_id";
$message_result = mysqli_query($con, $message_sql);

// Handle message response (AJAX handling)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['respond_message'])) {
    $message_id = $_POST['message_id'];
    $response = $_POST['response'];

    // Insert the new response into message_responses table
    $response_sql = "INSERT INTO message_responses (message_id, response) VALUES (?, ?)";
    $response_stmt = $con->prepare($response_sql);
    $response_stmt->bind_param("is", $message_id, $response);
    $response_stmt->execute();

    // Update the response_given column to TRUE
    $update_response_sql = "UPDATE messages SET response_given = TRUE WHERE id = ?";
    $update_stmt = $con->prepare($update_response_sql);
    $update_stmt->bind_param("i", $message_id);
    $update_stmt->execute();

    // Get the updated response count
    $response_count_sql = "SELECT COUNT(*) AS response_count FROM message_responses WHERE message_id = ?";
    $count_stmt = $con->prepare($response_count_sql);
    $count_stmt->bind_param("i", $message_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $response_count = $count_result->fetch_assoc()['response_count'];

    // Respond with success message and updated response count
    echo json_encode([
        'status' => 'success',
        'message' => 'Response sent successfully!',
        'response_count' => $response_count,
        'button_text' => 'Response Given'
    ]);
    exit;
}

// Fetch all responses for each message
$responses_sql = "SELECT * FROM message_responses WHERE message_id = ?";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .response {
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .response-form {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>All Messages</h2>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Message</th>
                    <th>Responses</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="message-table-body">
                <?php if (mysqli_num_rows($message_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($message_result)): ?>
                        <tr id="message-row-<?php echo $row['id']; ?>">
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['cust_name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                            <td>
                                <!-- Display the count of responses -->
                                <span id="response-count-<?php echo $row['id']; ?>">
                                    <?php echo htmlspecialchars($row['response_count']); ?> Responses
                                </span>

                                <!-- Fetch and display all responses for this message -->
                                <?php
                                $response_stmt = $con->prepare($responses_sql);
                                $response_stmt->bind_param("i", $row['id']);
                                $response_stmt->execute();
                                $response_result = $response_stmt->get_result();
                                if (mysqli_num_rows($response_result) > 0):
                                    while ($response_row = mysqli_fetch_assoc($response_result)):
                                ?>
                                    <div class="response">
                                        <p><strong>Response:</strong> <?php echo nl2br(htmlspecialchars($response_row['response'])); ?></p>
                                        <p><small><?php echo htmlspecialchars($response_row['response_date']); ?></small></p>
                                    </div>
                                <?php endwhile; endif; ?>

                                <!-- Response form -->
                                <form class="response-form" data-message-id="<?php echo $row['id']; ?>" method="POST" action="">
                                    <div class="mb-2">
                                        <textarea class="form-control" name="response" rows="3" placeholder="Type your response here..." required></textarea>
                                    </div>
                                    <button type="submit" name="respond_message" class="btn btn-primary">Send Response</button>
                                </form>
                            </td>
                            <td>
                                <button id="status-button-<?php echo $row['id']; ?>" 
                                        class="btn <?php echo ($row['response_given'] ? 'btn-success' : 'btn-warning'); ?>"
                                        <?php echo ($row['response_given'] ? 'disabled' : ''); ?>>
                                    <?php echo ($row['response_given'] ? 'Response Given' : 'Respond to Messages'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No messages found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('.response-form').on('submit', function(e) {
                e.preventDefault();

                var messageId = $(this).data('message-id');
                var response = $(this).find('textarea[name="response"]').val();
                
                $.ajax({
                    url: 'messages.php',
                    method: 'POST',
                    data: {
                        respond_message: true,
                        message_id: messageId,
                        response: response
                    },
                    success: function(data) {
                        var responseData = JSON.parse(data);
                        if (responseData.status === 'success') {
                            // Update the response count
                            $('#response-count-' + messageId).text(responseData.response_count + ' Responses');

                            // Update the button
                            $('#status-button-' + messageId)
                                .removeClass('btn-warning')
                                .addClass('btn-success')
                                .text('Response Given')
                                .prop('disabled', true);

                            // Append the new response
                            var newResponseHtml = '<div class="response"><p><strong>Response:</strong> ' + response + '</p><p><small>' + new Date().toLocaleString() + '</small></p></div>';
                            $('#message-row-' + messageId + ' td:nth-child(4)').prepend(newResponseHtml);
                            

                            // Clear the textarea
                            $(e.target).find('textarea').val('');
                            
                            alert(responseData.message);
                        }
                    },
                    error: function() {
                        alert('Error occurred while sending response!');
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>
