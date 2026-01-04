#!/bin/bash

# Function to process a domain
process_domain() {
    local domain_dir=$1
    local domain_name=$2
    
    echo "Processing $domain_name..."
    
    if [ -d "$domain_dir" ]; then
        cd "$domain_dir"
        git pull
        if [ $? -eq 0 ]; then
            echo "git pull $domain_dir success."
        else
            echo "git pull $domain_dir failed."
            return 1
        fi
        
        if [ -d "tmp" ]; then
            cd tmp
            rm -rf *.php *.htm
            echo "rm -rf $domain_name/tmp/*.php *.htm success."
            cd ..
        else
            echo "Warning: tmp directory not found in $domain_dir"
        fi
        cd ../..
    else
        echo "Warning: Directory $domain_dir does not exist."
        return 1
    fi
}

# Process all domains
process_domain "domains/luxdupes.com/public_html" "luxdupes.com"
process_domain "domains/preluxs.com/public_html" "preluxs.com"
process_domain "domains/bbkbags.com/public_html" "bbkbags.com"
process_domain "domains/amzrepo.com/public_html" "amzrepo.com"
process_domain "domains/bresbougieboutique.com/public_html" "bresbougieboutique.com"

echo "All operations completed."
echo "=============== ok finished!!!"