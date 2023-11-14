# Commission Calculator Application

## Overview
Commission Calculator is a PHP command-line application designed to calculate commissions for transactions based on predefined rules. The application reads transaction data from a CSV file, processes each transaction, and outputs the commission fees into an output CSV file.

## Prerequisites
- Docker
- docker-compose

## Getting Started

These instructions will cover usage information and for the docker container

### Container Parameters

List the different parameters available to your container

| Parameter    | Description                                                                                                             |
|--------------|-------------------------------------------------------------------------------------------------------------------------|
| `script.php` | The PHP script that will be executed. Starting point of application.                                                    |
| `input.csv`  | The input CSV file containing transactions to process, You can change name in ClI to insert your data.                  |

## Build and start the Docker container, it will automatically display the results in CLI

```sh
docker-compose run -e INPUT_FILE=input.csv app
```

## Start Tests

```sh
docker-compose up phpunit
```

## Built With

- PHP 8.2
- Docker
- Composer

