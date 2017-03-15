grant all privileges on *.* to 'root'@'localhost' identified by '';
CREATE DATABASE the_peak_beyond;
CREATE USER 'tpb'@'localhost' identified by 'tpb2017';
GRANT ALL PRIVILEGES ON the_peak_beyond.* TO 'tpb'@'localhost';
FLUSH PRIVILEGES;


