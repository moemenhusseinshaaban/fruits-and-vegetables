#!/bin/bash
mysql -u root -p"$MYSQL_ROOT_PASSWORD" <<EOF
CREATE DATABASE IF NOT EXISTS roadsurfer_db_test;
GRANT ALL PRIVILEGES ON roadsurfer_db_test.* TO 'roadsurfer_user'@'%';
FLUSH PRIVILEGES;
EOF

echo "Database setup completed."