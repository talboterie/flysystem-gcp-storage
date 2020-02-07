#!/usr/bin/env sh
until [[ $yn =~ ^[Yy]$ ]]; do
    printf "\033c"
    echo ""

    read -p "What is the vendor name? " vendor
    read -p "What is the package name? " package_name
    read -p "What is the author name? " author_name
    read -p "What is the author username? " author_username
    read -p "What is the author email? " author_email
    read -p "What is the author website? " author_website
    read -p "What is the description of the package? " package_description

    printf "\033c"

    echo "Vendor name: $vendor"
    echo "Package name: $package_name"
    echo "Author name: $author_name"
    echo "Author username: $author_username"
    echo "Author email: $author_email"
    echo "Author website: $author_website"
    echo "Package description: $package_description"

    read -p "Are the information above correct? " yn
done

kebab () {
    kebab=$(echo "$1" | sed 's/\(.\)\([A-Z\s]\)/\1-\2/g' | tr '[:upper:]' '[:lower:]')
    eval "$2='$kebab'"
}

nospace () {
    space=$(echo "$1" | sed 's/ //g')
    eval "$2='$space'"
}

keys=( "package_name" "vendor" "author_name" "author_email" "author_username" "author_website" "package_description" )
for i in "${keys[@]}"; do
    for file in `find $PWD -type f -not -path '*.git/*'`; do
        nospace "${!i}" nospaced
        kebab "$nospaced" kebabed
        sed -i '' -a -e "s#{$i:kebab}#$kebabed#g" "$file"
        sed -i '' -a -e "s#{$i:no-space}#$nospaced#g" "$file"
        sed -i '' -a -e "s#{$i}#${!i}#g" "$file"
    done
done

[ -f "prefill.sh" ] && rm "prefill.sh"
