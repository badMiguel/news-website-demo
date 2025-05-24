<?php

use parallel\Runtime\Error;

class Model
{
    private PDO $db;

    public function __construct()
    {
        $dbPath = MODEL . "db.sqlite";
        try {
            $this->db = new PDO("sqlite:$dbPath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec('PRAGMA foreign_keys = ON');
        } catch (PDOException $err) {
            error_log("Database connection failed: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getTotalNewsCount(?string $category): int
    {
        try {
            if (!$category) {
                $statement = $this->db->query("SELECT COUNT(*) FROM news");
                return $statement->fetch(PDO::FETCH_ASSOC)["COUNT(*)"];
            }

            $statement = $this->db->prepare("
                SELECT COUNT(*)
                FROM news n
                JOIN news_category nc ON nc.news_id = n.news_id
                JOIN category c ON nc.category_id = c.category_id
                JOIN user u ON n.author_id = u.user_id
                WHERE c.category = :category
            ");
            $statement->execute(["category" => $category]);
            return $statement->fetch(PDO::FETCH_ASSOC)["COUNT(*)"];
        } catch (PDOException $err) {
            error_log("Error getting total news count: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    private function getNewsCategory(array &$newsList): void
    {
        try {
            for ($i = 0; $i < count($newsList); $i++) {
                $statement = $this->db->prepare("
                    SELECT c.category
                    FROM category c
                    JOIN news_category nc ON nc.category_id = c.category_id
                    WHERE nc.news_id = :newsId
                ");
                $statement->execute(["newsId" => $newsList[$i]["news_id"]]);
                $categoryList = $statement->fetchAll(PDO::FETCH_ASSOC);

                $categories = [];
                for ($j = 0; $j < count($categoryList); $j++) {
                    array_push($categories, $categoryList[$j]["category"]);
                }
                $newsList[$i]["category"] = $categories;
            }
        } catch (PDOException $err) {
            error_log("Error getting total news count: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getNewsList(int $start, int $end): array
    {
        try {
            $statement = $this->db->prepare("
                SELECT news.*,user.full_name AS author, user.privilege
                FROM news
                JOIN user ON news.author_id = user.user_id
                ORDER BY edited_date DESC
                LIMIT :end OFFSET :start
            ");
            $statement->execute(["start" => $start, "end" => $end]);
            $newsList = $statement->fetchAll(PDO::FETCH_ASSOC);

            $this->getNewsCategory($newsList);

            return $newsList;
        } catch (PDOException $err) {
            error_log("Error getting news list: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getNewsListByCategory(int $start, int $end, string $category): array
    {
        try {
            $statement = $this->db->prepare("
                SELECT n.*,u.full_name AS author, u.privilege
                FROM news n
                JOIN news_category nc ON nc.news_id = n.news_id
                JOIN category c ON nc.category_id = c.category_id
                JOIN user u ON n.author_id = u.user_id
                WHERE c.category = :category
                ORDER BY edited_date DESC
                LIMIT :end OFFSET :start
            ");
            $statement->execute(["start" => $start, "end" => $end, "category" => $category]);
            $newsList = $statement->fetchAll(PDO::FETCH_ASSOC);

            $this->getNewsCategory($newsList);

            return $newsList;
        } catch (PDOException $err) {
            error_log("Error getting news list by category: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getLatestNews(): ?array
    {
        try {
            $statement = $this->db->query("
                SELECT *,u.full_name as author, u.privilege
                FROM news n
                JOIN user u ON u.user_id = n.author_id
                ORDER BY edited_date DESC 
                LIMIT 1
            ");
            $latestNews = [$statement->fetch(PDO::FETCH_ASSOC)];

            if (!$latestNews[0]) {
                return null;
            }

            $this->getNewsCategory($latestNews);
            return $latestNews;
        } catch (PDOException $err) {
            error_log("Error getting latest news: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getUserByUsername(string $username): ?array
    {
        try {
            $statement = $this->db->prepare("SELECT * FROM user WHERE user_name = :username");
            $statement->execute(["username" => $username]);
            return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $err) {
            error_log("Error getting user by username: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getNewsDetails(int $id): ?array
    {
        try {
            $statement = $this->db->prepare("
                SELECT news.*,user.full_name AS author, user.privilege
                FROM news 
                JOIN user ON news.author_id = user.user_id
                WHERE news_id = :news_id
            ");
            $statement->execute(["news_id" => $id]);
            $newsDetails = [$statement->fetch(PDO::FETCH_ASSOC)];

            $this->getNewsCategory($newsDetails);

            if (!$newsDetails[0]) {
                return null;
            }

            $newsDetails['comments'] = $this->getCommentsForNews($id);
            return $newsDetails;
        } catch (PDOException $err) {
            error_log("Error getting news details: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    private function uploadImage(string $path): ?string
    {
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

        // // TODO - remove
        // error_log($targetPath);
        // foreach ($_FILES["image"] as $k => $v) {
        //     error_log($k . " - " . $v);
        // }

        // validate file
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            return "Uploaded file is not an image";
        }

        // check image size
        if ($_FILES["image"]["size"] > 2000000) {
            return "Image file is too large.";
        }

        $allowed = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed)) {
            return "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed";
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], IMAGE_DIR . $path)) {
            return null;
        } else {
            return "There was an error uploading the image";
        }
    }

    /** 
     * @return array{0: bool, 1: bool|string} Returns:
     *      - [0]: Whether an error occurred (true = error, false = no error)
     *      - [1]: Error message (string) or check indicator (bool)     
     */
    private function imageUsedByOthers(string $imagePath, int $newsId): array
    {
        try {
            $statement = $this->db->prepare("
                SELECT news_id 
                FROM news 
                WHERE image_path = :imagePath AND NOT news_id = :newsId
            ");
            $statement->execute(["imagePath" => $imagePath, "newsId" => $newsId]);
            if (!$statement->fetch(PDO::FETCH_ASSOC)) {
                return [true, false];
            }
            return [true, true];
        } catch (PDOException $err) {
            return [false, $err->getMessage()];
        }
    }

    private function deleteImage(string $imagePath, int $newsId): ?string
    {
        if ($this->imageUsedByOthers($imagePath, $newsId)) {
            return null;
        }

        $fullPath = IMAGE_DIR . $imagePath;
        if (file_exists($fullPath) && !unlink($fullPath)) {
            return "Error deleting image";
        }
        return null;
    }

    public function addNewsToDB(
        string $newsTitle,
        string $newsSummary,
        string $newsBody,
        array $categoryIdList,
    ): ?string {
        try {
            session_start();
            $authorId = $_SESSION['user_id'];
            session_write_close();

            $this->db->beginTransaction();

            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $counter = 0;
            do {
                $newImagePath = bin2hex(random_bytes(16)) . "." . $imageFileType;
                $counter++;
                if ($counter === 5) {
                    throw new Exception("Failed to generate a filename for image");
                }
            } while (file_exists(IMAGE_DIR . $newImagePath));

            error_log($newImagePath);

            $statement1 = $this->db->prepare("
                INSERT INTO news 
                    (news_title, news_subtitle, body, author_id, image_path) 
                VALUES 
                    (:title, :summary, :body, :authorId, :imagePath)
            ");
            $statement1->execute([
                "title" => $newsTitle,
                "summary" => $newsSummary,
                "body" => $newsBody,
                "authorId" => $authorId,
                "imagePath" => $newImagePath,
            ]);

            $statement2 = $this->db->prepare("SELECT news_id FROM news WHERE news_title = :title");
            $statement2->execute(["title" => $newsTitle]);
            $news = $statement2->fetch(PDO::FETCH_ASSOC);

            foreach ($categoryIdList as $categoryId) {
                $statement3 = $this->db->prepare("
                    INSERT INTO news_category (news_id, category_id) VALUES (:newsId, :categoryId)
                ");
                $statement3->execute(["newsId" => $news["news_id"], "categoryId" => $categoryId]);
            }

            $uploadHasError = $this->uploadImage($newImagePath);
            if ($uploadHasError) {
                throw new Exception("Failed to upload image: " . $uploadHasError);
            }

            $this->db->commit();
            return null;
        } catch (PDOException $err) {
            $this->db->rollBack();
            return $err->getMessage();
        }
    }

    public function updateNewsInDB(
        int $newsId,
        string $newsTitle,
        string $newsSummary,
        string $newsBody,
        array $categoryIdList,
    ): ?string {
        try {
            $this->db->beginTransaction();

            $getOldImagePath = $this->getImagePath($newsId);
            if (!$getOldImagePath[0]) {
                throw new Exception("Failed to check news' image path: " . $getOldImagePath[1]);
            }

            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $counter = 0;
            do {
                $newImagePath = bin2hex(random_bytes(16)) . "." . $imageFileType;
                $counter++;
                if ($counter === 5) {
                    throw new Exception("Failed to generate a filename for image");
                }
            } while (file_exists(IMAGE_DIR . $newImagePath));

            $statement1 = $this->db->prepare("
                UPDATE news 
                SET 
                    news_title = :title,
                    news_subtitle = :summary,
                    body = :body,
                    edited_date = CURRENT_TIMESTAMP,
                    image_path = :imagePath
                    WHERE news_id = :newsId
            ");
            $statement1->execute([
                "newsId" => $newsId,
                "title" => $newsTitle,
                "summary" => $newsSummary,
                "body" => $newsBody,
                "imagePath" => $newImagePath,
            ]);

            $statement2 = $this->db->prepare("DELETE FROM news_category WHERE news_id = :newsId");
            $statement2->execute(["newsId" => $newsId]);

            foreach ($categoryIdList as $category) {
                $statement3 = $this->db->prepare("
                    INSERT INTO news_category (news_id, category_id) VALUES (:newsId, :categoryId)
                ");
                $statement3->execute(["newsId" => $newsId, "categoryId" => $category]);
            }

            if ($newImagePath && $getOldImagePath[1] !== $newImagePath) {
                $uploadHasError = $this->uploadImage($newImagePath);
                if ($uploadHasError) {
                    throw new Exception("Failed to upload image: " . $uploadHasError);
                }
            }

            $this->db->commit();
            return null;
        } catch (PDOException $err) {
            $this->db->rollBack();
            return "Transaction failed while editing {$newsId}: " . $err->getMessage();
        }
    }

    public function deleteNewsFromDB(int $newsId): ?string
    {
        try {
            $this->db->beginTransaction();

            $imagePath = $this->getImagePath($newsId);
            if (!$imagePath[0]) {
                throw new Exception("Failed to get image path: " . $imagePath[1]);
            }

            $statement = $this->db->prepare("DELETE FROM news WHERE news_id = :newsId");
            $statement->execute(["newsId" => $newsId]);

            $deleteImgStatus = $this->deleteImage($imagePath[1], $newsId);
            if ($deleteImgStatus) {
                throw new Exception("Failed to delete image: " . $deleteImgStatus);
            }

            $this->db->commit();
            return null;
        } catch (PDOException $err) {
            $this->db->rollBack();
            return $err->getMessage();
        }
    }

    public function getImagePath(int $id): array
    {
        try {
            $statement = $this->db->prepare("
                SELECT image_path FROM news WHERE news_id = :newsId
            ");
            $statement->execute(["newsId" => $id]);
            $imagePath = $statement->fetch(PDO::FETCH_ASSOC)["image_path"];
            return [true, $imagePath];
        } catch (PDOException $err) {
            return [false, $err->getMessage()];
        }
    }

    public function getCommentsForNews(int $newsId): array
    {
        try {
            $statement = $this->db->prepare("
                SELECT c.*, u.user_name AS commentor_name 
                FROM comment c 
                LEFT JOIN user u ON c.commentor = u.user_id 
                WHERE c.news_id = :newsId
                ORDER BY c.created_date ASC 
            ");
            $statement->execute(["newsId" => $newsId]);
            $comments = $statement->fetchAll(PDO::FETCH_ASSOC);

            //reply of a comment
            $commentTree = [];
            $commentMap = [];
            foreach ($comments as $comment) {
                $comment['replies'] = [];
                $commentMap[$comment['comment_id']] = $comment;
            }
            foreach ($commentMap as $commentId => &$comment) {
                if ($comment['parent_comment_id']) {
                    $commentMap[$comment['parent_comment_id']]['replies'][] = &$comment;
                } else {
                    $commentTree[] = &$comment;
                }
            }
            return $commentTree;
        } catch (PDOException $err) {
            error_log("Error getting comments for news: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function addCommentToDB(int $newsId, int $commentorId, string $comment, ?int $parentCommentId = null): void
    {
        try {
            error_log("Adding comment to DB: news_id: $newsId, commentor_id: $commentorId, comment: $comment, parent_comment_id: " . ($parentCommentId ?: 'NULL'));

            $statement = $this->db->prepare("
                INSERT INTO comment (comment, commentor, news_id, parent_comment_id)
                VALUES (:comment, :commentor, :newsId, :parentCommentId)
            ");
            $statement->execute([
                "comment" => $comment,
                "commentor" => $commentorId,
                "newsId" => $newsId,
                "parentCommentId" => $parentCommentId,
            ]);
        } catch (PDOException $err) {
            error_log("Error adding comment to DB: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function toggleComments(int $newsId, bool $enable): void
    {
        try {
            $statement = $this->db->prepare("
                UPDATE news 
                SET comments_enabled = :enabled
                WHERE news_id = :newsId
            ");
            $statement->execute([
                'enabled' => $enable ? 1 : 0,
                'newsId' => $newsId,
            ]);
        } catch (PDOException $err) {
            error_log("Error toggling comments: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getCommentorId(int $commentId): int
    {
        try {
            $statement = $this->db->prepare("
                SELECT commentor AS commentorId
                FROM comment
                WHERE comment_id = :commentId
            ");
            $statement->execute(["commentId" => $commentId]);
            return $statement->fetch(PDO::FETCH_ASSOC)["commentorId"];
        } catch (PDOException $err) {
            error_log("Error getting commentor ID: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function commentExists(int $commentId): bool
    {
        try {
            $statement = $this->db->prepare("SELECT COUNT(*) FROM comment WHERE comment_id = :commentId");
            $statement->execute(['commentId' => $commentId]);
            return $statement->fetchColumn() > 0;
        } catch (PDOException $err) {
            error_log("Error checking comment existence: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function deleteComment(int $commentId): void
    {
        try {
            $statement = $this->db->prepare("DELETE FROM comment WHERE comment_id = :commentId");
            $statement->execute(["commentId" => $commentId]);
        } catch (PDOException $err) {
            error_log("Error deleting comment: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    // edit 
    public function updateComment(int $commentId, string $newComment): void
    {
        try {
            $statement = $this->db->prepare("
                UPDATE comment 
                SET comment = :newComment, created_date = CURRENT_TIMESTAMP
                WHERE comment_id = :commentId
            ");
            $statement->execute([
                "newComment" => $newComment,
                "commentId" => $commentId,
            ]);
        } catch (PDOException $err) {
            error_log("Error updating comment: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }
    public function getCategoryList(): array
    {
        try {
            $statement = $this->db->query("SELECT * FROM category");
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $err) {
            error_log("Error getting category list: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }
}
