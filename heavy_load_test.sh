#!/bin/bash

# The Thinking Mind - Heavy Load Performance Test
# Tests with 100+ concurrent users

echo "🔥 The Thinking Mind - HEAVY LOAD Performance Test"
echo "==================================================="
echo ""
echo "Starting HEAVY LOAD tests at $(date)"
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
run_heavy_test() {
    local test_name=$1
    local url=$2
    local requests=$3
    local concurrent=$4
    
    echo ""
    echo -e "${YELLOW}📊 Test: $test_name${NC}"
    echo "URL: $url"
    echo "Requests: $requests | Concurrent Users: $concurrent"
    echo "---"
    
    ab -n $requests -c $concurrent "$url" 2>/dev/null | \
        grep -E "Requests per second|Time per request|Failed requests|Percentage of requests|Longest request"
}

# ============= BASELINE =============

echo -e "${YELLOW}=== SYSTEM BASELINE CHECK ===${NC}"
echo "Docker status before test:"
docker stats --no-stream exam_simulator_web exam_simulator_db 2>/dev/null | head -3

echo ""
echo "Database health:"
docker exec exam_simulator_db mysql -uroot -proot exam_simulator -e \
"SELECT 'Total Questions' as metric, COUNT(*) as value FROM questions;" 2>/dev/null

# ============= HEAVY LOAD TESTS =============

echo ""
echo -e "${YELLOW}=== TEST 1: 50 CONCURRENT USERS ===${NC}"
run_heavy_test "Home Page (50 users)" "$BASE_URL/" 500 50

echo ""
echo -e "${YELLOW}=== TEST 2: 75 CONCURRENT USERS ===${NC}"
run_heavy_test "Categories Page (75 users)" "$BASE_URL/categories.php" 750 75

echo ""
echo -e "${YELLOW}=== TEST 3: 100 CONCURRENT USERS ===${NC}"
run_heavy_test "Home Page (100 users)" "$BASE_URL/" 1000 100

echo ""
echo -e "${YELLOW}=== TEST 4: 100 CONCURRENT USERS (Categories) ===${NC}"
run_heavy_test "Categories Page (100 users)" "$BASE_URL/categories.php" 1000 100

echo ""
echo -e "${YELLOW}=== TEST 5: 100 CONCURRENT USERS (Admin) ===${NC}"
run_heavy_test "Admin Login (100 users)" "$BASE_URL/admin_login.php" 1000 100

echo ""
echo -e "${YELLOW}=== TEST 6: EXTREME LOAD - 150 CONCURRENT USERS ===${NC}"
run_heavy_test "Home Page (150 users)" "$BASE_URL/" 1500 150

# ============= RESOURCE MONITORING =============

echo ""
echo -e "${YELLOW}=== DOCKER RESOURCE USAGE (After Tests) ===${NC}"
echo "Current Docker container stats:"
docker stats --no-stream exam_simulator_web exam_simulator_db 2>/dev/null

# ============= ERROR ANALYSIS =============

echo ""
echo -e "${YELLOW}=== ERROR LOG ANALYSIS ===${NC}"
echo "Checking for application errors (excluding build logs)..."
ERROR_COUNT=$(docker logs exam_simulator_web 2>&1 | grep -v "libtool\|cc -I\|mysqli\|DZEND\|DHAVE_CONFIG" | grep -i "error\|fatal\|exception" | wc -l)
echo "Application errors found: $ERROR_COUNT"

if [ $ERROR_COUNT -gt 0 ]; then
    echo -e "${RED}⚠️  Errors detected:${NC}"
    docker logs exam_simulator_web 2>&1 | grep -v "libtool\|cc -I\|mysqli\|DZEND\|DHAVE_CONFIG" | grep -i "error\|fatal\|exception" | tail -10
else
    echo -e "${GREEN}✅ No application errors in logs${NC}"
fi

# ============= PERFORMANCE INTERPRETATION =============

echo ""
echo -e "${GREEN}=============================================="
echo "📊 PERFORMANCE TEST RESULTS ANALYSIS"
echo "=============================================="
echo ""
echo "🎯 What to look for:"
echo ""
echo "  REQUESTS PER SECOND (higher is better):"
echo "    ✅ 100+ req/sec = Excellent"
echo "    ✅ 50-100 req/sec = Good"
echo "    ⚠️  20-50 req/sec = Acceptable"
echo "    ❌ < 20 req/sec = Needs optimization"
echo ""
echo "  TIME PER REQUEST (lower is better):"
echo "    ✅ < 100ms = Excellent"
echo "    ✅ 100-500ms = Good"
echo "    ⚠️  500-1000ms = Acceptable"
echo "    ❌ > 1000ms = Needs optimization"
echo ""
echo "  FAILED REQUESTS (should be 0):"
echo "    ✅ 0 failed = Perfect"
echo "    ⚠️  1-5% = Minor issues"
echo "    ❌ > 5% = Stability problems"
echo ""
echo "  DOCKER RESOURCE USAGE:"
echo "    ✅ CPU < 50% = Good"
echo "    ✅ Memory < 60% = Good"
echo "    ⚠️  CPU 50-80% = High load"
echo "    ❌ CPU > 80% = Overloaded"
echo ""
echo "=============================================="
echo "🎉 Heavy Load Performance Test Complete!"
echo "=============================================="
echo ""
echo "Next steps:"
echo "  1. Review the results above"
echo "  2. Check if you need database optimization"
echo "  3. Consider caching for high-traffic pages"
echo "  4. Monitor Docker stats during peak usage"
echo ""
echo "Test completed at $(date)"
echo -e "${NC}"
