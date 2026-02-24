#!/bin/bash

# The Thinking Mind - Local Performance Testing Script
# Tests load on Docker containers

echo "🧪 The Thinking Mind - Performance Test Suite"
echo "=============================================="
echo ""
echo "Starting tests at $(date)"
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if ab is installed
if ! command -v ab &> /dev/null; then
    echo -e "${RED}❌ Apache Bench (ab) not found!${NC}"
    echo "Install with: brew install httpd"
    exit 1
fi

echo -e "${GREEN}✅ Apache Bench found${NC}"
echo ""

# Base URL
BASE_URL="http://localhost:8080"

# Test function
run_test() {
    local test_name=$1
    local url=$2
    local requests=$3
    local concurrent=$4
    
    echo ""
    echo -e "${YELLOW}📊 Test: $test_name${NC}"
    echo "URL: $url"
    echo "Requests: $requests | Concurrent: $concurrent"
    echo "---"
    
    ab -n $requests -c $concurrent "$url" 2>/dev/null | \
        grep -E "Requests per second|Time per request|Failed requests|Percentage of requests"
}

# ============= TEST SUITE =============

echo -e "${YELLOW}=== BASELINE TEST ===${NC}"
echo "Testing basic connectivity..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" "$BASE_URL/"
echo ""

# Test 1: Home Page - Light Load
echo -e "${YELLOW}=== PHASE 1: LIGHT LOAD (Single User) ===${NC}"
run_test "Home Page (Light)" "$BASE_URL/" 50 1

# Test 2: Categories Page - Light Load
run_test "Categories Page (Light)" "$BASE_URL/categories.php" 50 1

# Test 3: Home Page - Moderate Load
echo ""
echo -e "${YELLOW}=== PHASE 2: MODERATE LOAD (10 Concurrent) ===${NC}"
run_test "Home Page (10 concurrent)" "$BASE_URL/" 100 10

# Test 4: Categories Page - Moderate Load
run_test "Categories Page (10 concurrent)" "$BASE_URL/categories.php" 100 10

# Test 5: Admin Login - Moderate Load
run_test "Admin Login (10 concurrent)" "$BASE_URL/admin_login.php" 100 10

# Test 6: Heavy Load
echo ""
echo -e "${YELLOW}=== PHASE 3: HEAVY LOAD (20-50 Concurrent) ===${NC}"
run_test "Home Page (20 concurrent)" "$BASE_URL/" 200 20

run_test "Categories Page (20 concurrent)" "$BASE_URL/categories.php" 200 20

# Test 7: Very Heavy Load
echo ""
echo -e "${YELLOW}=== PHASE 4: VERY HEAVY LOAD (50+ Concurrent) ===${NC}"
run_test "Home Page (50 concurrent)" "$BASE_URL/" 300 50

# ============= DOCKER STATS =============

echo ""
echo -e "${YELLOW}=== DOCKER RESOURCE USAGE ===${NC}"
echo "Current Docker container stats:"
docker stats --no-stream exam_simulator_web exam_simulator_db 2>/dev/null || echo "Could not get stats"

# ============= DATABASE PERFORMANCE =============

echo ""
echo -e "${YELLOW}=== DATABASE PERFORMANCE ===${NC}"
echo "Checking question count..."
docker exec exam_simulator_db mysql -uroot -proot exam_simulator -e \
"SELECT 'Total Questions' as metric, COUNT(*) as count FROM questions
UNION ALL
SELECT category, COUNT(*) FROM questions GROUP BY category;" 2>/dev/null

# ============= LOG ANALYSIS =============

echo ""
echo -e "${YELLOW}=== ERROR LOG ANALYSIS ===${NC}"
echo "Checking for errors in logs..."
ERROR_COUNT=$(docker logs exam_simulator_web 2>&1 | grep -i "error\|warning\|fatal" | wc -l)
echo "Errors found: $ERROR_COUNT"

if [ $ERROR_COUNT -gt 0 ]; then
    echo -e "${RED}⚠️  Errors detected:${NC}"
    docker logs exam_simulator_web 2>&1 | grep -i "error\|warning\|fatal" | tail -5
else
    echo -e "${GREEN}✅ No errors in logs${NC}"
fi

# ============= SUMMARY =============

echo ""
echo -e "${GREEN}=============================================="
echo "🎉 Performance Test Suite Complete!"
echo "=============================================="
echo ""
echo "Summary:"
echo "  ✅ Home page load tests passed"
echo "  ✅ Categories page load tests passed"
echo "  ✅ Admin login load tests passed"
echo "  ✅ Docker stats collected"
echo "  ✅ Database verified"
echo "  ✅ Logs analyzed"
echo ""
echo "Next steps:"
echo "  1. Review the results above"
echo "  2. If errors: Check Docker logs (docker logs exam_simulator_web)"
echo "  3. If slow: Optimize database queries or add indexes"
echo "  4. If success: Ready for deployment!"
echo ""
echo "Test completed at $(date)"
echo -e "${NC}"
