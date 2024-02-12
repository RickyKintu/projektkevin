<?php
function generateRandomUsername()
{
    $randomNumber = mt_rand(1000, 9999);
    return "Anonymous" . $randomNumber;
}

$id = $_GET['id'];
?>

<?php
require_once "settings.php";

// Fetch comments from the database
$commentsQuery = "SELECT * FROM comments WHERE content_id = ? AND content_type = 'video' ORDER BY created_at DESC";
$stmt = mysqli_prepare($con, $commentsQuery);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$comments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $comments[] = $row;
}
mysqli_free_result($result);
?>


<link rel="stylesheet" href="css/comments.css">

<div class="comment-section">
    <!-- Comment Form -->
    <div class="comment-form">
        <h3>Leave a Comment</h3>
        <form id="commentForm" action="functions/post_comment.php" method="post">
            <input type="hidden" name="content_type" value="video" />
            <input type="hidden" name="content_id" value="<?php echo $id ?>" />
            <!-- Name Field -->
            <input type="text" name="name" placeholder="Anonymous" />
            <textarea name="text" placeholder="Your Comment" required></textarea>
            <button type="submit">Post Comment</button>
        </form>
    </div>
    <div id="thankYouMessage" style="display:none;">
        Thank you for your comment!
    </div>
    <div class="comments-list">
        <h3>Comments</h3>
        <?php foreach ($comments as $comment) : ?>
            <div class="comment" id="comment-<?php echo $comment['id']; ?>">
                <div class="comment-info">
                    Posted by <?php echo htmlspecialchars($comment['user_name']); ?> on <?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?>
                </div>
                <div class="comment-text">
                    <?php echo htmlspecialchars($comment['text']); ?>
                </div>
                <div class="comment-actions">
                    <!--  <button>Like</button>
                    <button class="dislike">Dislike</button>-->
                    <button onclick="showReplyForm(<?php echo $comment['id']; ?>)">Reply</button>
                </div>

                <!-- Reply Form -->
                <div class="reply-form" id="reply-form-<?php echo $comment['id']; ?>" style="display:none;">
                    <input type="text" name="reply_name" placeholder="Anonymous" required />
                    <textarea name="reply_text" placeholder="Write a reply..." required></textarea>
                    <button onclick="postReply(<?php echo $comment['id']; ?>)">Post Reply</button>
                </div>


                <!-- Container for replies -->
                <div class="replies" id="replies-<?php echo $comment['id']; ?>">
                    <?php
                    // Prepare a query to fetch replies for this comment
                    $repliesQuery = "SELECT * FROM comments WHERE parent_id = ? ORDER BY created_at ASC";
                    $stmt = mysqli_prepare($con, $repliesQuery);
                    if ($stmt === false) {
                        die("Failed to prepare statement: " . mysqli_error($con));
                    }
                    mysqli_stmt_bind_param($stmt, 'i', $comment['id']);
                    mysqli_stmt_execute($stmt);
                    $repliesResult = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($repliesResult) > 0) {
                        while ($reply = mysqli_fetch_assoc($repliesResult)) {
                            // Display each reply
                    ?>
                            <div class="reply">
                                <div class="reply-info">
                                    Replied by <?php echo htmlspecialchars($reply['user_name']); ?> on <?php echo date('Y-m-d H:i', strtotime($reply['created_at'])); ?>
                                </div>
                                <div class="reply-text">
                                    <?php echo htmlspecialchars($reply['text']); ?>
                                </div>
                                <!-- <button>Like</button>
                                <button class="dislike">Dislike</button> -->
                                <button onclick="showReplyForm(<?php echo $comment['id']; ?>)">Reply</button>
                            </div>
                    <?php
                        }
                    }

                    mysqli_free_result($repliesResult);


                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($comments)) : ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </div>

    <?php
    mysqli_close($con);
    ?>
    <script>
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var textArea = this.querySelector('textarea[name="text"]');
            var textValue = textArea.value.trim();

            // Check if the comment meets the length requirements
            if (textValue.length < 4 || textValue.length > 250) {
                alert("Comments must be between 4 and 250 characters.");
                return; // Stop the form submission
            }

            var formData = new FormData(this);

            fetch('functions/post_comment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('thankYouMessage').style.display = 'block';
                        document.getElementById('commentForm').reset();
                        // Optionally, add code to refresh the comment list
                    } else {
                        alert("Failed to submit comment.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        function showReplyForm(commentId) {
            document.getElementById('reply-form-' + commentId).style.display = 'block';
        }

        function postReply(commentId) {
            var replyText = document.querySelector('#reply-form-' + commentId + ' textarea').value.trim();
            if (replyText.length === 0) {
                alert("Please enter a reply.");
                return;
            }
            var formData = new FormData();
            formData.append('text', replyText);
            formData.append('parent_id', commentId);
            formData.append('content_id', <?php echo $id ?>);
            formData.append('content_type', 'video');

            fetch('functions/post_reply.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reply posted successfully');
                        location.reload();
                    } else {
                        alert("Failed to submit reply.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });


            function postReply(commentId) {
                var replyName = document.querySelector('#reply-form-' + commentId + ' input[name="reply_name"]').value.trim();
                var replyText = document.querySelector('#reply-form-' + commentId + ' textarea').value.trim();

                if (replyText.length === 0 || replyName.length === 0) {
                    alert("Please fill in all fields.");
                    return;
                }

                var formData = new FormData();
                formData.append('name', replyName);
                formData.append('text', replyText);
                formData.append('parent_id', commentId);
                // Append other necessary data as before

                fetch('functions/post_reply.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Reply posted successfully');
                        } else {
                            alert("Failed to submit reply.");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }


        }
    </script>