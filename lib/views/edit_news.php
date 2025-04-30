<h2>Edit News</h2>
<?php if (isset($_SESSION['newsEditStatus'])): ?>
    <?php if ($_SESSION['newsEditStatus']): ?>
        <p style="color: green;">News updated successfully!</p>
    <?php else: ?>
        <p style="color: red;">Please fill in.</p>
    <?php endif; ?>
    <?php unset($_SESSION['newsEditStatus']); ?>
<?php endif; ?>

<form method="POST" action="/news/edit/submit">
    <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($newsDetails['news_id']); ?>">
    <label>Title: <input type="text" name="news_title" value="<?php echo htmlspecialchars($newsDetails['news_title']); ?>" required></label><br>
    <label>Summary: <textarea name="news_summary" required><?php echo htmlspecialchars($newsDetails['news_summary']); ?></textarea></label><br>
    <label>Body: <textarea name="body" required><?php echo htmlspecialchars($newsDetails['body']); ?></textarea></label><br>
    <button type="submit">Update News</button>
</form>