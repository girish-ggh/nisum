# Nisum Migration Module

The **Nisum Migration** module is a custom Drupal 10 module designed to facilitate the migration of data from a JSON source (`https://jsonplaceholder.typicode.com/users`) to Drupal content types and user entities. The module leverages the Drupal Migrate API, providing migration configurations through YAML files for structured data handling.

## Features

- Migrate data to a custom content type **Company**.
- Migrate user data to the **User** entity.
- Custom Drush command for executing migrations.

## Table of Contents

- [Installation](#installation)
- [Migration Configuration](#migration-configuration)
  - [Companies Migration](#companies-migration)
  - [Users Migration](#users-migration)
- [Drush Command Usage](#drush-command-usage)
- [Running the Migration](#running-the-migration)
- [Conclusion](#conclusion)

## Installation

1. **Download the module**:
   Clone or download the `nisum_migration` module into the `modules/custom` directory of your Drupal installation.

   ```bash
   git clone <repository-url> modules/custom/nisum_migration
   ```

2. **Enable the module**: 
   Use Drush or the Drupal admin interface to enable the module.**:
   Clone or download the `nisum_migration` module into the `modules/custom` directory of your Drupal installation.
   ```bash
   drush en nisum_migration
   ```
3. **Install dependencies** (if required): 
   Ensure that Drush and the Migrate module are installed in your Drupal environment. You can install Drush using Composer:
   ```bash
   composer require drush/drush
   ```

## Migration Configuration

The migrations are defined using YAML configuration files located in the `config/install` directory of the module.

### Companies Migration

- **ID**: `migrate_companies`
- **Label**: 'Migrate Companies from JSON'
- **Source**: JSON data fetched from `https://jsonplaceholder.typicode.com/users`.
- **Destination**: Content type **Company**.

**YAML Configuration**:
```yaml
id: migrate_companies
label: 'Migrate Companies from JSON'
source:
  plugin: url
  data_fetcher_plugin: http
  data_parser_plugin: json
  urls:
    - https://jsonplaceholder.typicode.com/users
  fields:
    - name: comp_id
      label: "User Id"
      selector: id
    - name: company_details
      label: "Comp Details"
      selector: company
  ids:
    comp_id:
      type: string
process:
  type:
    plugin: default_value
    default_value: company
  uid:
    plugin: default_value
    default_value: 1
  title: 
    plugin: extract
    source: company_details
    index:
      - name
  field_catchphrase: 
    plugin: extract
    source: company_details
    index:
      - catchPhrase
  field_description: 
    plugin: extract
    source: company_details
    index:
      - bs
destination:
  plugin: entity:node
  bundle: company
migration_dependencies: {}
```

### Users Migration

The Users Migration handles the import of user data from a JSON source. This migration is designed to fetch user information from the endpoint `https://jsonplaceholder.typicode.com/users` and create user entities in the Drupal system.

**Migration Configuration**:

- **ID**: `migrate_users`
- **Label**: 'Migrate Users from JSON'
- **Source**: JSON data fetched from `https://jsonplaceholder.typicode.com/users`.
- **Destination**: User entity.

**YAML Configuration**:

```yaml
id: migrate_users
label: 'Migrate Users from JSON'
source:
  plugin: url
  data_fetcher_plugin: http
  data_parser_plugin: json
  urls:
    - https://jsonplaceholder.typicode.com/users
  fields:
    - name: userid
      label: 'User ID'
      selector: id
    - name: field_full_name
      label: 'Full Name'
      selector: name
    - name: username
      label: 'Username'
      selector: username
    - name: email
      label: 'Email'
      selector: email
    - name: field_phone
      label: 'Phone Number'
      selector: phone
    - name: field_website
      label: 'Website'
      selector: website
    - name: company_details
      label: "Comp Details"
      selector: company
    - name: add_details
      label: "Add Details"
      selector: address 
    - name: add_geo_details
      label: "Add Geo Details"
      selector: address/geo  
  ids:
    userid:
      type: integer
process:
  name: username
  mail: email
  pass: "user_password" # Optionally, set a default password.
  roles:
    plugin: default_value
    default_value:
      - authenticated
      - nisum_migrated
  status:
    plugin: default_value
    default_value: 1
  field_phone: field_phone
  field_website: field_website
  field_full_name: field_full_name
  field_address_suite: 
    plugin: extract
    source: add_details
    index:
      - suite
  field_address_street: 
    plugin: extract
    source: add_details
    index:
      - street
  field_address_city: 
    plugin: extract
    source: add_details
    index:
      - city
  field_address_zipcode: 
    plugin: extract
    source: add_details
    index:
      - zipcode
  field_address_lat: 
    plugin: extract
    source: add_geo_details
    index:
      - lat
  field_address_lng: 
    plugin: extract
    source: add_geo_details
    index:
      - lng
  field_user_company: 
    plugin: extract
    source: company_details
    index:
      - name
  field_company:
    plugin: entity_lookup
    entity_type: node
    bundle: company
    source: company_details
    index:
      - name
    value_key: title

destination:
  plugin: entity:user
migration_dependencies:
  required:
    - migrate_companies
    
```

### Explanation of the Migration Configuration

- **ID**: The migration is identified by a unique ID `migrate_users`, which is used when executing the migration.

- **Label**: The label 'Migrate Users from JSON' provides a human-readable name for the migration, making it easier to identify in the system.

- **Source**: 
  - The source plugin is defined as `url`, which indicates that the data will be fetched from a specified URL.
  - The `data_fetcher_plugin` is set to `http`, specifying that the data will be retrieved via an HTTP request.
  - The `data_parser_plugin` is set to `json`, indicating that the retrieved data is in JSON format.
  - The `urls` array contains the endpoint `https://jsonplaceholder.typicode.com/users`, from which user data will be fetched.

- **Fields**: 
  - Each field to be imported is specified here. The `name` field contains two properties: `name`, which represents the field key, and `label`, which is a human-readable description of the field.
  - The `selector` defines how to extract the value from the JSON object. For example, `selector: id` maps to the `userid` field, allowing the system to pull the correct data from

## Drush Command Usage

The custom Drush command for migrating users and companies is defined in the `nisum_migration` module. This command allows you to easily execute the migrations from the command line.

### Command Overview

- **Command Name**: `nisum-migration:run`
- **Alias**: `nmigrate`

### How to Use

1. **Navigate to Your Drupal Project**: Open a terminal and navigate to the root directory of your Drupal project.

2. **Run the Migration Command**:
   You can execute the migration command by running the following command in your terminal:

   ```bash
   drush nisum-migration:run
   ```
## Running the Migration

To execute the migrations defined in the `nisum_migration` module, follow these steps:

#### Prerequisites

1. **Ensure Drupal is Installed**: Make sure your Drupal site is set up and running.
2. **Install Dependencies**: Ensure that Drush and any other required modules for migrations are installed.
3. **Enable the Module**: Confirm that the `nisum_migration` module is enabled. You can enable it using Drush:

   ```bash
   drush en nisum_migration
   ```
4. Monitor the output for success or error messages indicating the status of each migration.

## Rollback Migration

To rollback the migrations, follow these steps:
1. **Rollback Users**:

   ```bash
   drush mr migrate_users
   ```
2. **Rollback Companies**:

   ```bash
   drush mr migrate_companies
   ```
   
## Conclusion

### Explanation of the `README.md` Structure:
- **Module Title and Description**: Clear overview of what the module does.
- **Features**: Lists the main functionalities.
- **Installation**: Detailed steps for downloading and enabling the module.
- **Migration Configuration**: Breaks down the YAML configurations for both companies and users, explaining their purpose.
- **Drush Command Usage**: Instructions for using the custom Drush command, including aliases for convenience.
- **Running the Migration**: Step-by-step guide to executing the migrations.
- **Conclusion**: Encouragement for feedback and issue reporting.

Feel free to modify any sections to better fit your module's specifics or organizational standards!


