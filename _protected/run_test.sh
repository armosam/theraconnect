#!/usr/bin/env bash

base_path="/vagrant/www/public_html/_protected"

function info {
  echo " "
  echo "--> $1"
  echo " "
}

main(){

    clear

    php -S localhost:8080 -t /vagrant/www/public_html/ &
    pid=$!

    case "$1" in
        api)
            run_api_test
            ;;
        common)
            run_common_test
            ;;
        backend)
            run_backend_test
            ;;
        frontend)
            run_frontend_test
            ;;
        *)
            run_api_test
            run_common_test
            run_backend_test
            run_frontend_test
    esac

    kill -9 ${pid}
}

run_test(){
    ${base_path}/vendor/bin/codecept build
    ${base_path}/vendor/bin/codecept run --coverage --coverage-text
}

run_api_test(){
    cd ${base_path}/tests/codeception/api/
    run_test
}

run_common_test(){
    cd ${base_path}/tests/codeception/common/
    run_test
}

run_frontend_test(){
    cd ${base_path}/tests/codeception/frontend/
    run_test
}

run_backend_test(){
    cd ${base_path}/tests/codeception/backend/
    run_test
}

test_env=$1
main "${test_env}"

exit 0