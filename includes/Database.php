<?php
/**
 * Database helper class for common database operations
 * Provides a cleaner interface for database queries
 */

class Database {
    private $conn;
    
    /**
     * Constructor
     * @param mysqli $connection Database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get the raw mysqli connection
     * @return mysqli
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Execute a query and return the result
     * @param string $sql SQL query
     * @return mysqli_result|bool
     */
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    /**
     * Get all categories with question counts
     * @param string|null $type Optional type filter (IT, Academic)
     * @return array Array of categories
     */
    public function getCategories($type = null) {
        $sql = "SELECT category, COUNT(*) as question_count, 
                MIN(difficulty) as min_difficulty,
                MAX(difficulty) as max_difficulty
                FROM questions ";
        
        if ($type) {
            $type = $this->conn->real_escape_string($type);
            // Add type filtering logic if needed
        }
        
        $sql .= "GROUP BY category ORDER BY category";
        
        $result = $this->query($sql);
        $categories = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        
        return $categories;
    }
    
    /**
     * Get questions for exam
     * @param string|null $category Category filter
     * @param string|null $difficulty Difficulty filter
     * @param int $limit Number of questions
     * @return array Array of questions
     */
    public function getExamQuestions($category = null, $difficulty = null, $limit = 10) {
        $sql = "SELECT * FROM questions WHERE 1=1";
        
        if ($category) {
            $category = $this->conn->real_escape_string($category);
            $sql .= " AND category = '$category'";
        }
        
        if ($difficulty) {
            $difficulty = $this->conn->real_escape_string($difficulty);
            $sql .= " AND difficulty = '$difficulty'";
        }
        
        $sql .= " ORDER BY RAND() LIMIT " . intval($limit);
        
        $result = $this->query($sql);
        $questions = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $questions[] = $row;
            }
        }
        
        return $questions;
    }
    
    /**
     * Get a single question by ID
     * @param int $id Question ID
     * @return array|null Question data or null
     */
    public function getQuestion($id) {
        $id = intval($id);
        $result = $this->query("SELECT * FROM questions WHERE id = $id");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Add a new question
     * @param array $data Question data
     * @return bool|int Question ID on success, false on failure
     */
    public function addQuestion($data) {
        $question = $this->conn->real_escape_string($data['question']);
        $option_a = $this->conn->real_escape_string($data['option_a']);
        $option_b = $this->conn->real_escape_string($data['option_b']);
        $option_c = $this->conn->real_escape_string($data['option_c']);
        $option_d = $this->conn->real_escape_string($data['option_d']);
        $correct = $this->conn->real_escape_string($data['correct_answer']);
        $category = $this->conn->real_escape_string($data['category']);
        $difficulty = $this->conn->real_escape_string($data['difficulty']);
        
        $sql = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) 
                VALUES ('$question', '$option_a', '$option_b', '$option_c', '$option_d', '$correct', '$category', '$difficulty')";
        
        if ($this->query($sql)) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update existing question
     * @param int $id Question ID
     * @param array $data Question data
     * @return bool Success status
     */
    public function updateQuestion($id, $data) {
        $id = intval($id);
        $question = $this->conn->real_escape_string($data['question']);
        $option_a = $this->conn->real_escape_string($data['option_a']);
        $option_b = $this->conn->real_escape_string($data['option_b']);
        $option_c = $this->conn->real_escape_string($data['option_c']);
        $option_d = $this->conn->real_escape_string($data['option_d']);
        $correct = $this->conn->real_escape_string($data['correct_answer']);
        $category = $this->conn->real_escape_string($data['category']);
        $difficulty = $this->conn->real_escape_string($data['difficulty']);
        
        $sql = "UPDATE questions SET 
                question_text = '$question',
                option_a = '$option_a',
                option_b = '$option_b',
                option_c = '$option_c',
                option_d = '$option_d',
                correct_answer = '$correct',
                category = '$category',
                difficulty = '$difficulty'
                WHERE id = $id";
        
        return $this->query($sql);
    }
    
    /**
     * Delete a question
     * @param int $id Question ID
     * @return bool Success status
     */
    public function deleteQuestion($id) {
        $id = intval($id);
        return $this->query("DELETE FROM questions WHERE id = $id");
    }
    
    /**
     * Count total questions
     * @param string|null $category Category filter
     * @param string|null $difficulty Difficulty filter
     * @return int Question count
     */
    public function countQuestions($category = null, $difficulty = null) {
        $sql = "SELECT COUNT(*) as count FROM questions WHERE 1=1";
        
        if ($category) {
            $category = $this->conn->real_escape_string($category);
            $sql .= " AND category = '$category'";
        }
        
        if ($difficulty) {
            $difficulty = $this->conn->real_escape_string($difficulty);
            $sql .= " AND difficulty = '$difficulty'";
        }
        
        $result = $this->query($sql);
        
        if ($result) {
            $row = $result->fetch_assoc();
            return intval($row['count']);
        }
        
        return 0;
    }
    
    /**
     * Search questions by keyword
     * @param string $keyword Search keyword
     * @param string|null $category Category filter
     * @param int $limit Result limit
     * @return array Array of questions
     */
    public function searchQuestions($keyword, $category = null, $limit = 50) {
        $keyword = $this->conn->real_escape_string($keyword);
        $sql = "SELECT * FROM questions WHERE question_text LIKE '%$keyword%'";
        
        if ($category) {
            $category = $this->conn->real_escape_string($category);
            $sql .= " AND category = '$category'";
        }
        
        $sql .= " LIMIT " . intval($limit);
        
        $result = $this->query($sql);
        $questions = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $questions[] = $row;
            }
        }
        
        return $questions;
    }
    
    /**
     * Get user by username
     * @param string $username Username
     * @return array|null User data or null
     */
    public function getUserByUsername($username) {
        $username = $this->conn->real_escape_string($username);
        $result = $this->query("SELECT * FROM users WHERE username = '$username'");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Create a new user
     * @param string $username Username
     * @param string $password Password (will be hashed)
     * @param string $role User role (default: student)
     * @return bool|int User ID on success, false on failure
     */
    public function createUser($username, $password, $role = 'student') {
        $username = $this->conn->real_escape_string($username);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $role = $this->conn->real_escape_string($role);
        
        $sql = "INSERT INTO users (username, password_hash, role) VALUES ('$username', '$password_hash', '$role')";
        
        if ($this->query($sql)) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get statistics
     * @return array Statistics array
     */
    public function getStatistics() {
        $stats = [
            'total_questions' => 0,
            'total_categories' => 0,
            'total_users' => 0
        ];
        
        // Count questions
        $result = $this->query("SELECT COUNT(*) as count FROM questions");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_questions'] = intval($row['count']);
        }
        
        // Count categories
        $result = $this->query("SELECT COUNT(DISTINCT category) as count FROM questions");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_categories'] = intval($row['count']);
        }
        
        // Count users
        $result = $this->query("SELECT COUNT(*) as count FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_users'] = intval($row['count']);
        }
        
        return $stats;
    }
}
