# OOP-server-backend
This is a web application with backend built with PHP, HTML, and javascript using object-oriented programming. It is designed to work with an NGINX server and is made for use as part of a homeserver.


Features

- User File Storage: Supports file uploads with a user directory structure. Files are stored in an "archive" directory by default, with individual user directories at archive/{user}.
- IP Address Logging: Logs the IP addresses of users accessing the application, stored in ../logs/ip_addresses.txt.
- User Account Management: Maintains a list of user accounts for the file archive in ../logs/users.txt.
- Comment System: Allows users to submit comments, which are saved in ../logs/comments.txt. Comments are time-stamped and displayed on the homepage.
- Comment Mechanism: Implements a cooldown period between comments to prevent spam.
- User Count: Implements a user count so developers and users can gauge site activity


Requirements
- PHP 7.0+
- NGINX
`
/var/www/html/

├── archive/                # user file storage

│   └── {user}/             # user directories

│

├── logs/                   # log files

│   ├── ip_addresses.txt    # logged IP addresses 

│   ├── users.txt           # user names for the archive

│   └── comments.txt        # comments file

│

├── index.php               # main application file

└── upload.php              # file upload handling
`
