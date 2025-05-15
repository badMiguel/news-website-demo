<?php
if (!$newsDetails) {
    echo "
        <p>Sorry news does not exist</p>
    ";
    exit;
}
?>

<h2><?php echo htmlspecialchars($newsDetails[0]['news_title']); ?></h2>
<p><strong>Summary:</strong> <?php echo htmlspecialchars($newsDetails[0]['news_subtitle']); ?></p>
<p><strong>Body:</strong> <?php echo nl2br(htmlspecialchars($newsDetails[0]['body'])); ?></p>
<p><strong>Author:</strong> <?php echo htmlspecialchars($newsDetails[0]['author'] ?? 'Unknown'); ?></p>
<div class="category--container">
    <p><strong>Category:</strong></p>
    <?php foreach ($newsDetails[0]['category'] as $category): ?>
        <p><?php echo htmlspecialchars($category); ?></p>
    <?php endforeach ?>
</div>
<p><strong>Created:</strong> <?php echo htmlspecialchars($newsDetails[0]['created_date']); ?></p>
<p><strong>Edited:</strong> <?php echo htmlspecialchars($newsDetails[0]['edited_date']); ?></p>

<?php if (isset($_SESSION['privilege']) && $_SESSION['privilege'] >= EDITOR): ?>
    <a href="/news/edit?id=<?php echo htmlspecialchars($newsDetails[0]['news_id']); ?>">Edit</a>
    <a href="/news/delete?id=<?php echo htmlspecialchars($newsDetails[0]['news_id']); ?>" onclick="return confirm('Are you sure you want to delete this news?');">Delete</a>
<?php endif; ?>

<!-- add comment function -->
<h3>Comments</h3>
<?php if (isset($newsDetails[0]['comments']) && !empty($newsDetails[0]['comments'])): ?>
    <ul>
        <?php foreach ($newsDetails[0]['comments'] as $comment): ?>
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
        <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($newsDetails[0]['news_id']); ?>">
        <label>Comment: <textarea name="comment" required></textarea></label><br>
        <button type="submit">Add Comment</button>
    </form>
<?php else: ?>
    <p>Please <a href="/login">login</a> to add a comment.</p>
<?php endif; ?>
