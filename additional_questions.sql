-- Additional sample questions for different categories
-- Run this to add more subject-based questions

USE exam_simulator;

-- More PHP Basics questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) VALUES
('Which PHP function is used to get the data type of a variable?', 'type()', 'gettype()', 'vartype()', 'datatype()', 'B', 'PHP Basics', 'Easy'),
('What is the output of echo 5 + "5 apples"; in PHP?', '10', '10 apples', '5 apples', 'Error', 'A', 'PHP Basics', 'Medium'),
('Which keyword is used to prevent a class from being inherited?', 'static', 'final', 'private', 'sealed', 'B', 'PHP Basics', 'Hard');

-- Array questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) VALUES
('Which function is used to count elements in an array?', 'length()', 'size()', 'count()', 'sizeof()', 'C', 'Arrays', 'Easy'),
('Which function adds an element to the end of an array?', 'array_add()', 'array_push()', 'array_append()', 'array_insert()', 'B', 'Arrays', 'Easy'),
('Which function is used to sort an array in ascending order?', 'sort()', 'asort()', 'ksort()', 'array_sort()', 'A', 'Arrays', 'Medium'),
('Which function merges two or more arrays?', 'array_combine()', 'array_merge()', 'array_join()', 'merge_arrays()', 'B', 'Arrays', 'Easy');

-- OOP questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) VALUES
('Which keyword is used to create a class in PHP?', 'class', 'object', 'define', 'struct', 'A', 'OOP', 'Easy'),
('Which visibility keyword makes a property accessible only within the class?', 'public', 'protected', 'private', 'internal', 'C', 'OOP', 'Easy'),
('Which method is automatically called when an object is created?', 'init()', '__construct()', 'create()', '__init__()', 'B', 'OOP', 'Medium'),
('What does the extends keyword do?', 'Creates a new class', 'Implements an interface', 'Inherits from a parent class', 'Defines a namespace', 'C', 'OOP', 'Easy');

-- Security questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) VALUES
('Which function is used to prevent SQL injection?', 'mysql_escape()', 'mysqli_real_escape_string()', 'sql_safe()', 'escape_sql()', 'B', 'Security', 'Medium'),
('Which function hashes passwords securely in PHP?', 'md5()', 'sha1()', 'password_hash()', 'crypt()', 'C', 'Security', 'Medium'),
('What does XSS stand for?', 'Cross-Site Scripting', 'External Site Security', 'Cross-Server Sync', 'Extra Site Safety', 'A', 'Security', 'Easy'),
('Which HTTP header helps prevent clickjacking?', 'X-Frame-Options', 'X-Security', 'Content-Security', 'Frame-Guard', 'A', 'Security', 'Hard');

-- More Database questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) VALUES
('Which SQL command is used to retrieve data from a database?', 'GET', 'SELECT', 'FETCH', 'RETRIEVE', 'B', 'Database', 'Easy'),
('Which SQL clause is used to filter records?', 'FILTER', 'WHERE', 'HAVING', 'IF', 'B', 'Database', 'Easy'),
('Which JOIN returns all records from both tables?', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN', 'D', 'Database', 'Medium');
