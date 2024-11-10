# REQUIREMENTS

## Server

1. Define variables to hold the port number (the port number can be arbitrary) and the IP address (real IP).
2. Ensure the server is able to listen to all group members at a minimum.
3. Accept requests from devices sending requests (each group member should execute at least one request on the server).
4. The server should be able to read messages sent by clients.
5. Provide at least one client with full access to the folders/files on the server.

## Client

1. Establish a socket connection to the server.
2. One device (client) should have privileges for `write()`, `read()`, and `execute()`.
3. Other clients should have only `read()` permission.
4. The connection to the server should specify the correct port and server IP address.
5. Define server sockets and ensure connections do not fail.
6. Be able to read responses returned by the server.
7. Send messages to the server in text format.
8. Have full access to folders/files on the server.

## Additional Requirements

- Each group member should be able to explain the entire project and have both scripts (client and server) ready for execution on their device.
- The project must be fully functional and hosted on GitHub by all group members. Members who have not contributed will not receive full credit, and individual assessment points may be deducted.
- Work on the project and commit code to GitHub continuously, rather than uploading all the code at once. If the code is uploaded all at once, penalty points will be applied.
- The uploaded code on GitHub must be public.

## Protocol and Language

- The assignment requires using the **UDP protocol** and implementing the project in **PHP**.
