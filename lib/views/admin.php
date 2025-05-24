 
<h2>Admin Dashboard - Manage News</h2>
<?php if (!isset($_SESSION['privilege']) || $_SESSION['privilege'] < 2): ?>
    <p>Access denied. Only editors can view this page.</p>
<?php else: ?>
    <?php if (empty($newsList)): ?>
        <p>No news available.</p>
    <?php else: ?>
        <?php require_once VIEWS . "partials/news_amount.php" ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($newsList as $news): ?>
                    <tr>
                        <td><?= htmlspecialchars($news['news_title']) ?></td>
                        <td><?= htmlspecialchars($news['author'] ?? 'Unknown') ?>
                        <td>
                            <?php
                            $role = 'Unknown';
                            if (isset($news['privilege'])) {
                                switch ($news['privilege']) {
                                    case 0:
                                        $role = 'User';
                                        break;
                                    case 1:
                                        $role = 'Journalist';
                                        break;
                                    case 2:
                                        $role = 'Editor';
                                        break;
                                }
                            }
                            echo htmlspecialchars($role);
                            ?>
                            <td>
                            <a href="/news/edit?id=<?= htmlspecialchars($news['news_id']) ?>">Edit</a> |
                            <a href="/news/delete?id=<?= htmlspecialchars($news['news_id']) ?>" onclick="return confirm('Are you sure you want to delete this news?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php require_once VIEWS . "partials/page_number.php" ?>
    <?php endif; ?>
<?php endif; ?>
