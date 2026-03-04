#!/bin/bash

# IONOS Production Load Test Script
# Domain: thethinkingmind.net
# Usage: ./ionos_load_test.sh [quick|normal|stress]

DOMAIN="https://thethinkingmind.net"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
RESULTS_DIR="load_test_results"

# Create results directory
mkdir -p "$RESULTS_DIR"

# Test configurations
QUICK_REQUESTS=100
QUICK_CONCURRENCY=10

NORMAL_REQUESTS=300
NORMAL_CONCURRENCY=20

STRESS_REQUESTS=1000
STRESS_CONCURRENCY=50

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Functions
print_header() {
    echo -e "${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

run_load_test() {
    local endpoint=$1
    local requests=$2
    local concurrency=$3
    local test_name=$4
    
    print_header "Testing: $test_name"
    echo "Endpoint: $endpoint"
    echo "Requests: $requests | Concurrency: $concurrency"
    echo ""
    
    local output_file="$RESULTS_DIR/${TIMESTAMP}_${test_name// /_}.txt"
    
    # Run Apache Bench
    ab -n $requests -c $concurrency -t 60 "$DOMAIN$endpoint" > "$output_file" 2>&1
    
    # Parse and display results
    if [ $? -eq 0 ]; then
        local response_time=$(grep "Time per request:" "$output_file" | head -1 | awk '{print $4}')
        local requests_per_sec=$(grep "Requests per second:" "$output_file" | awk '{print $4}')
        local failed=$(grep "Failed requests:" "$output_file" | awk '{print $3}')
        
        print_success "Test completed"
        echo "  Response time (avg): ${response_time}ms"
        echo "  Requests/sec: $requests_per_sec"
        echo "  Failed requests: ${failed:-0}"
        
        # Check for failures
        if [ "$failed" != "0" ] && [ ! -z "$failed" ]; then
            print_warning "Some requests failed - check $output_file for details"
        fi
    else
        print_error "Test failed - check $output_file for details"
    fi
    echo ""
}

# Check if Apache Bench is installed
if ! command -v ab &> /dev/null; then
    print_error "Apache Bench (ab) not found. Install with: brew install httpd (macOS)"
    exit 1
fi

# Determine test level
TEST_LEVEL=${1:-normal}

case $TEST_LEVEL in
    quick)
        print_header "QUICK LOAD TEST"
        echo "Domain: $DOMAIN"
        echo "Test Level: Quick (Light Load)"
        echo ""
        
        run_load_test "/index.php" $QUICK_REQUESTS $QUICK_CONCURRENCY "Homepage"
        run_load_test "/categories.php" $QUICK_REQUESTS $QUICK_CONCURRENCY "Categories Page"
        run_load_test "/login.php" $QUICK_REQUESTS $QUICK_CONCURRENCY "Login Page"
        ;;
        
    normal)
        print_header "NORMAL LOAD TEST"
        echo "Domain: $DOMAIN"
        echo "Test Level: Normal (Moderate Load)"
        echo ""
        
        run_load_test "/index.php" $NORMAL_REQUESTS $NORMAL_CONCURRENCY "Homepage"
        run_load_test "/categories.php" $NORMAL_REQUESTS $NORMAL_CONCURRENCY "Categories Page"
        run_load_test "/login.php" $NORMAL_REQUESTS $NORMAL_CONCURRENCY "Login Page"
        run_load_test "/register.php" $NORMAL_REQUESTS $NORMAL_CONCURRENCY "Register Page"
        ;;
        
    stress)
        print_header "STRESS LOAD TEST"
        echo "Domain: $DOMAIN"
        echo "Test Level: Stress (Heavy Load)"
        echo ""
        
        print_warning "Running stress test - this will put significant load on the server"
        sleep 3
        
        run_load_test "/index.php" $STRESS_REQUESTS $STRESS_CONCURRENCY "Homepage (Stress)"
        run_load_test "/categories.php" $STRESS_REQUESTS $STRESS_CONCURRENCY "Categories Page (Stress)"
        run_load_test "/login.php" $STRESS_REQUESTS $STRESS_CONCURRENCY "Login Page (Stress)"
        run_load_test "/register.php" $STRESS_REQUESTS $STRESS_CONCURRENCY "Register Page (Stress)"
        ;;
        
    *)
        echo "Usage: ./ionos_load_test.sh [quick|normal|stress]"
        echo ""
        echo "Test Levels:"
        echo "  quick   - Light load test (100 requests, 10 concurrent) - ~30 seconds"
        echo "  normal  - Moderate load test (300 requests, 20 concurrent) - ~2 minutes"
        echo "  stress  - Heavy stress test (1000 requests, 50 concurrent) - ~5 minutes"
        echo ""
        echo "Results saved to: $RESULTS_DIR/"
        exit 0
        ;;
esac

print_header "LOAD TEST SUMMARY"
echo "Test Level: $TEST_LEVEL"
echo "Results saved to: $RESULTS_DIR/"
echo ""
print_success "Load testing complete"
echo ""
echo "Performance Guidelines:"
echo "  ✓ Response Time: < 500ms per request"
echo "  ✓ Requests/sec: > 10 req/sec"
echo "  ✓ Success Rate: 100% (no failed requests)"
echo "  ✓ Server Error Rate: < 5%"
