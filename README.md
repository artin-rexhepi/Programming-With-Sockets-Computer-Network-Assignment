# Computer Networks Assignment 2

This project implements a UDP-based server-client system for file management, designed to handle multiple clients with different access levels. The system enables secure file operations based on granted permissions.

## Features

### Access Levels:
- **Full Access**: Read, write, create, delete, and open files.
- **Edit Access**: Read, write, and open files.
- **Read-Only Access**: List, read, and open files in read-only mode.

### Commands:
- `list`: List all files in the server directory.
- `read <file>`: View the content of a file.
- `write <file> <content>`: Modify or append content to a file.
- `create <file>`: Create a new file (full access required).
- `delete <file>`: Remove a file (full access required).
- `open <file>`: Open a file (read-only or full mode based on access).

  
## Setup

1. Clone the repository.
2. Navigate to the `src` folder.
3. Run the server and client scripts as instructed below.

## Running the Server

```bash
php src/server/server.php
```

## Running the Client

```bash
php src/server/client.php
```

### Upon connecting, the client will:
- Enter a password (admin2024, editor2024) or proceed as a guest.
- Use the displayed menu to execute commands based on access level.

## Requirements
For detailed requirements, refer to the `docs/requirements.md`.
