<?php
function add_S(int $time): string
{
    if ($time > 1) {
        return "s";
    }
    return "";
};

if (!$newsDetails) {
    echo "<p>Sorry news does not exist</p>";
    exit;
}
?>

<h1 class="news-title"><?php echo htmlspecialchars($newsDetails['news_title']); ?></h1>
<p><strong>By:</strong> <?php echo htmlspecialchars($newsDetails['author']); ?></p>

<?php
$created = new DateTime($newsDetails["created_date"]);
$edited = new DateTime($newsDetails["edited_date"]);
$now = new DateTime();
$createdDiff = $created->diff($now);
$editedDiff = $edited->diff($now);

$news = $newsDetails;
require VIEWS . "time_ago_display.php";

if ($created != $edited) {
    echo "<p>Edited last ";
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
    echo "</p>";
}

?>

<?php require VIEWS . "category_display.php"?>

<p class="news-subtitle"><?php echo htmlspecialchars($newsDetails['news_subtitle']); ?></p>
<p class="body"><?php echo nl2br(htmlspecialchars($newsDetails['body'])); ?></p>

<?php if (isset($_SESSION['privilege']) && $_SESSION['privilege'] >= EDITOR): ?>
    <a href="/news/edit?id=<?php echo htmlspecialchars($newsDetails['news_id']); ?>">Edit</a>
    <a href="/news/delete?id=<?php echo htmlspecialchars($newsDetails['news_id']); ?>" onclick="return confirm('Are you sure you want to delete this news?');">Delete</a>
<?php endif; ?>

<!-- add comment function -->
<h3>Comments</h3>
<?php if (isset($newsDetails['comments']) && !empty($newsDetails['comments'])): ?>
    <ul>
        <?php foreach ($newsDetails['comments'] as $comment): ?>
            <li>
                <strong><?php echo htmlspecialchars($comment['commentor_name'] ?? 'Anonymous'); ?>:</strong>
                <?php echo htmlspecialchars($comment['comment']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No comments yet.</p>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <h4>Add a Comment</h4>
    <form method="POST" action="/news/comment/add">
        <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($newsDetails['news_id']); ?>">
        <label>Comment: <textarea name="comment" required></textarea></label><br>
        <button type="submit">Add Comment</button>
    </form>
<?php else: ?>
    <p>Please <a href="/login">login</a> to add a comment.</p>
<?php endif; ?>
