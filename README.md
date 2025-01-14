# MySQL Connections Performance Test

## Overview
This project evaluates the performance of different programming languages / drivers when interacting with a MySQL database. The goal is to determine the best language for a project where 80% of the workload involves database logic.

Languages tested:
- PHP
- NodeJS
- Rust
- Golang
- Python

Each language runs a simple web server connected to the database. All environments are Dockerized to ensure consistency, with all languages connecting to a single shared database.

---

## Prerequisites
1. Docker installed on your system.
2. A UNIX-based operating system for running the setup scripts (on windows based systems you can also run docker composer but no with up.sh script)

---

## Setup and Execution

### Requirements
- docker-desktop

### Step 1: Clone the Repository
```bash
git clone git@github.com:anton-panfilov/mysql-connections-performance-test.git
cd ProjectRoot/docker
```

### Step 2: Setup configuration
Copy the `.env.dist` file to `.env` and set up the `PASSPHRASE_BASE64` environment variable.
```bash
cp .env.dist .env

# Generate a base64 passphrase and update .env file
PASSPHRASE_BASE64=$(openssl rand -base64 32)
sed -i "s|^PASSPHRASE_BASE64=.*|PASSPHRASE_BASE64=$(openssl rand -base64 32)|" .env
```

Also, please review other settings on .env file

You can also add settings with yours ip addresses to the `hosts` file, for example by default:
```
127.0.0.2 percona.pt
127.0.0.3 rust.pt
127.0.0.4 php.pt
127.0.0.5 python.pt
127.0.0.6 go.pt
127.0.0.7 nest.pt
```

### Step 3: Start the Docker Environment
```bash
./up.sh
```

### Step 4: Install requirements
- php composer


### Step 5: Access Web Servers
Each language will expose its web server on a specific port

---

## Results
After running the tests, you can compare the performance of each language based on metrics like:
- Response time
- Resource usage (CPU, Memory)
- Throughput

Collect and analyze the data to identify the optimal language for your database-heavy project.
