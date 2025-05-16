 
<h2>Admin Dashboard - Manage News</h2>
<?php if (!isset($_SESSION['privilege']) || $_SESSION['privilege'] < 2): ?>
    <p>Access denied. Only editors can view this page.</p>
<?php else: ?>
    <?php if (empty($newsList)): ?>
        <p>No news available.</p>
    <?php else: ?>
        <?php require_once VIEWS . "news_amount.php" ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Author</th>
                    <th>Categories</th>
                    <th>Created</th>
                    <th>Edited</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newsList as $news): ?>
                    <tr>
                        <td><?= htmlspecialchars($news['news_id']) ?></td>
                        <td><?= htmlspecialchars($news['news_title']) ?></td>
                        <td><?= htmlspecialchars($news['news_subtitle']) ?></td>
                        <td><?= htmlspecialchars($news['author'] ?? 'Unknown') ?></td>
                        <td>
                            <?php foreach ($news['category'] as $c): ?>
                                <?= htmlspecialchars($c) ?>,
                            <?php endforeach; ?>
                        </td>
                        <td><?= htmlspecialchars($news['created_date']) ?></td>
                        <td><?= htmlspecialchars($news['edited_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php require_once VIEWS . "page_number.php" ?>
    <?php endif; ?>
<?php endif; ?>