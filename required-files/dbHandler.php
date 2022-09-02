<?php
    class dbHandler
    {
        public static function query(string $query) : mysqli_result | bool
        {
            $conn = self::getDbConnection();
            return mysqli_query($conn, $query);
        }
    
        private static function getDbConnection() : mysqli | null
        {
            $server = "localhost";
            $userName = "root";
            $password = "";
            $dbName = "positionappDB";
    
            $conn = mysqli_connect($server, $userName, $password, $dbName);
    
            if (!$conn) {
                throw new Exception("Couldn't connect to the database. " . mysqli_connect_error());            
            }
            return $conn;
        }
    }
?>