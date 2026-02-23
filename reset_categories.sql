-- Clear existing questions
DELETE FROM exam_answers;
DELETE FROM exam_results;
DELETE FROM questions;

-- Add KCSA (Kubernetes Certified Security Associate) questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty, created_at) VALUES
('Which Kubernetes component is responsible for authentication and authorization?', 'kube-scheduler', 'kube-apiserver', 'kubelet', 'etcd', 'B', 'KCSA', 'Medium', NOW()),
('What is the purpose of Pod Security Policy in Kubernetes?', 'To manage resource allocation', 'To define security standards for pods', 'To control network traffic', 'To monitor pod performance', 'B', 'KCSA', 'Medium', NOW()),
('Which field in a PodSecurityPolicy controls privileged container execution?', 'allowPrivilegeEscalation', 'privileged', 'hostNetwork', 'hostPID', 'B', 'KCSA', 'Hard', NOW()),
('What is the purpose of RBAC (Role-Based Access Control) in Kubernetes?', 'To allocate computational resources', 'To define who can perform what actions on which resources', 'To manage container networking', 'To schedule pods across nodes', 'B', 'KCSA', 'Medium', NOW()),
('How can you ensure that a pod cannot escalate privileges?', 'Set allowPrivilegeEscalation to false in SecurityContext', 'Use NetworkPolicy', 'Configure resource limits', 'Set nodeName field', 'A', 'KCSA', 'Medium', NOW()),
('Which Kubernetes resource defines network access policies?', 'SecurityPolicy', 'NetworkPolicy', 'IngressPolicy', 'AccessPolicy', 'B', 'KCSA', 'Medium', NOW()),
('What does the principle of least privilege mean in Kubernetes security?', 'Grant all permissions to users', 'Grant only the minimum necessary permissions to perform a task', 'Grant permissions only to administrators', 'Grant permissions temporarily', 'B', 'KCSA', 'Easy', NOW()),
('Which parameter in kubelet controls the read-only port?', 'read-only-port', 'port', 'secure-port', 'healthz-port', 'A', 'KCSA', 'Hard', NOW()),
('What is the purpose of PodSecurityStandards in Kubernetes 1.23+?', 'To manage pod storage', 'To replace Pod Security Policy with a built-in standard', 'To control container images', 'To manage service discovery', 'B', 'KCSA', 'Hard', NOW()),
('Which API version should you use for modern RBAC definitions?', 'v1alpha1', 'v1beta1', 'rbac.authorization.k8s.io/v1', 'v1', 'C', 'KCSA', 'Medium', NOW()),

-- Add Python questions
('What is the output of print(type(5))?', '<class ''int''>', '<class ''float''>', '<class ''str''>', 'integer', 'A', 'Python', 'Easy', NOW()),
('Which of these is a mutable data type in Python?', 'tuple', 'string', 'list', 'frozenset', 'C', 'Python', 'Easy', NOW()),
('What does list.append() do?', 'Returns the last element', 'Adds an element to the end of the list', 'Removes the last element', 'Sorts the list', 'B', 'Python', 'Easy', NOW()),
('What is the result of 10 // 3 in Python?', '3.33', '3', '4', '3.0', 'B', 'Python', 'Easy', NOW()),
('Which keyword is used to create a function in Python?', 'function', 'def', 'define', 'func', 'B', 'Python', 'Easy', NOW()),
('What is the correct syntax for a dictionary in Python?', '{key: value}', '(key: value)', '[key: value]', '(key, value)', 'A', 'Python', 'Easy', NOW()),
('What does the enumerate() function do?', 'Counts elements', 'Returns pairs of index and value', 'Sorts a list', 'Filters elements', 'B', 'Python', 'Medium', NOW()),
('Which method removes and returns the last element from a list?', 'remove()', 'pop()', 'delete()', 'drop()', 'B', 'Python', 'Medium', NOW()),
('What is the purpose of *args in a function definition?', 'To accept variable number of arguments', 'To accept only one argument', 'To specify argument types', 'To make arguments optional', 'A', 'Python', 'Medium', NOW()),
('Which module provides date and time functionality?', 'time', 'datetime', 'dateutil', 'calendar', 'B', 'Python', 'Medium', NOW()),
('What is a lambda function in Python?', 'A named function', 'An anonymous function', 'A recursive function', 'A built-in function', 'B', 'Python', 'Hard', NOW()),
('What does list comprehension do?', 'Compresses a list', 'Creates a new list by applying an operation to each element', 'Combines multiple lists', 'Sorts a list', 'B', 'Python', 'Hard', NOW());
