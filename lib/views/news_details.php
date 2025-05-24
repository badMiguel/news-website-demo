<?php
function add_S(int $time): string
{
    if ($time > 1) {
        return "s";
    }
    return "";
};


if (!isset($newsDetails["news_id"])) {
    echo "<p>Sorry news does not exist</p>";
    exit;
}
?>

<h1 class="news-title"><?php echo htmlspecialchars($newsDetails['news_title']); ?></h1>
<p class="news-author-time">
    <?php
    $created = new DateTime($newsDetails["created_date"]);
    $edited = new DateTime($newsDetails["edited_date"]);
    $now = new DateTime();
    $createdDiff = $created->diff($now);
    $editedDiff = $edited->diff($now);

    $news = $newsDetails;
    require VIEWS . "partials/time_ago_display.php";

    if ($created != $edited) {
        echo "<span style='margin: 0 0.3rem;'>|</span>";
        echo "Edited last ";
        if ($editedDiff->y > 0) {
            echo htmlspecialchars($editedDiff->y . " year" . add_S($editedDiff->y) . " ago");
        } else if ($editedDiff->m > 0) {
            echo htmlspecialchars($editedDiff->m . " month" . add_S($editedDiff->m) . " ago");
        } else if ($editedDiff->d > 0) {
            echo htmlspecialchars($editedDiff->d . " day" . add_S($editedDiff->d) . " ago");
        } else if ($editedDiff->h > 0) {
            echo htmlspecialchars($editedDiff->h . " hour" . add_S($editedDiff->h) . " ago");
        } else if ($editedDiff->i > 0) {
            echo htmlspecialchars($editedDiff->i . " minute" . add_S($editedDiff->i) . " ago");
        } else if ($editedDiff->s > 0) {
            echo htmlspecialchars($editedDiff->s . " second" . add_S($editedDiff->s) . " ago");
        }
    }
    ?>

    <span style='margin: 0 0.3rem;'>|</span>
    <?php echo htmlspecialchars($newsDetails['author']); ?>
<p>

<p class="news-subtitle"><?php echo htmlspecialchars($newsDetails['news_subtitle']); ?></p>

<?php if (
    htmlspecialchars($newsDetails["image_path"]) &&
    file_exists(IMAGE_DIR . $newsDetails["image_path"])
): ?>
    <img class="news-image" src="/../images/<?= htmlspecialchars($newsDetails["image_path"]) ?>" />
<?php endif ?>

<p class="body"><?php echo nl2br(htmlspecialchars($newsDetails['body'])); ?></p>

<?php require VIEWS . "partials/category_display.php" ?>

<?php if (isset($_SESSION['privilege']) && $_SESSION['privilege'] >= EDITOR): ?>
    <div class="edit-delete--container">
        <p class="edit--button">
            <a href="/news/edit?id=<?php echo htmlspecialchars($newsDetails['news_id']); ?> ">
                Edit
            </a>
        </p>
        <p class="delete--button">
            <a
                href="/news/delete?id=<?php echo htmlspecialchars($newsDetails['news_id']); ?>"
                onclick="return confirm('Are you sure you want to delete this news?');">
                Delete
            </a>
        </p>

        <?php if ($newsDetails['comments_enabled']): ?>
            <p>
                <a href="/news/comments/disable?id=<?= htmlspecialchars($newsDetails['news_id']) ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    Disable Comments
                </a>
            </p>
        <?php else: ?>
            <p>
                <a href="/news/comments/enable?id=<?= htmlspecialchars($newsDetails['news_id']) ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    Enable Comments
                </a>
            </p>
        <?php endif; ?>
    </div>
<?php endif; ?>


<h3>Comments</h3>
<?php if (empty($newsDetails['comments'])): ?>
    <p>No comments yet.</p>
<?php else: ?>
    <div class="comments">
        <?php
        function displayComments($comments, $level = 0, $newsId, $commentsEnabled)
        {
            foreach ($comments as $comment):
        ?>
                <div class="comment" style="margin-left: <?= $level * 20 ?>px; border-left: 2px solid #ccc; padding-left: 10px; margin-bottom: 10px;">

                    <p>
                        <strong><?= htmlspecialchars($comment['commentor_name'] ?? 'Anonymous') ?></strong>
                        <small>(<?= htmlspecialchars($comment['created_date']) ?>)</small>
                        <!-- delete and edit -->
                        <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['commentor'] || $_SESSION['privilege'] >= EDITOR)): ?>
                            <span>
                                <a href="/news/comment/delete?id=<?= htmlspecialchars($comment['comment_id']) ?>&news_id=<?= htmlspecialchars($newsId) ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</a>
                                | <a href="#" onclick="document.getElementById('edit-form-<?= $comment['comment_id'] ?>').style.display='block'; return false;">Edit</a>
                            </span>
                        <?php endif; ?>
                    </p>

                    <p><?= htmlspecialchars($comment['comment']) ?></p>

                    <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['commentor'] || $_SESSION['privilege'] >= EDITOR)): ?>
                        <form id="edit-form-<?= $comment['comment_id'] ?>" action="/news/comment/edit" method="POST" style="display: none; margin-top: 5px;">
                            <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['comment_id']) ?>">
                            <input type="hidden" name="news_id" value="<?= htmlspecialchars($newsId) ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <textarea name="new_comment" required style="width: 100%; height: 50px;"><?= htmlspecialchars($comment['comment']) ?></textarea>
                            <button type="submit">Update Comment</button>
                        </form>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id']) && $commentsEnabled): ?>
                        <form action="/news/comment/add" method="POST" style="margin-top: 5px;">
                            <input type="hidden" name="news_id" value="<?= htmlspecialchars($newsId) ?>">
                            <input type="hidden" name="parent_comment_id" value="<?= htmlspecialchars($comment['comment_id']) ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <textarea name="comment" placeholder="Reply to this comment..." required style="width: 100%; height: 50px;"></textarea>
                            <button type="submit">Reply</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($comment['replies'])): ?>
                        <?php displayComments($comment['replies'], $level + 1, $newsId, $commentsEnabled); ?>
                    <?php endif; ?>

                </div>
        <?php
            endforeach;
        }
        displayComments($newsDetails['comments'], 0, $newsDetails['news_id'], $newsDetails['comments_enabled']);
        ?>
    </div>
<?php endif; ?>

<?php if ($newsDetails['comments_enabled']): ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <h4>Add a Comment</h4>
        <form method="POST" action="/news/comment/add">
            <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($newsDetails['news_id']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <label>Comment: <textarea name="comment" required style="width: 100%; height: 100px;"></textarea></label><br>
            <button type="submit">Add Comment</button>
        </form>
    <?php else: ?>
        <p>Please <a href="/login">login</a> to add a comment.</p>
    <?php endif; ?>
<?php else: ?>
    <p>Comments are disabled for this article.</p>
<?php endif; ?>
