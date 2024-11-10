# Computer Networks Assignment 2

This project implements a UDP-based server-client system for file management, with support for multiple access levels. Clients can request permissions, and the server grants or denies them based on access level.

## Features

- **Client-Server Model**: Communicates over UDP
- **Access Levels**: Full access, edit access, and read-only access for clients
- **File Operations**: Read, write, execute, and delete (based on permission level)

## Setup

1. Clone the repository.
2. Navigate to the `src` folder.
3. Run the server and client scripts as instructed below.

## Running the Server

```bash
php src/server.php
