<?php

namespace src\Db;
class connexionFactory {
    private static $config;

    public static function setConfig($file) {
        self::$config = parse_ini_file($file);
    }

    public static function makeConnection() {
        if (!self::$config) {
            die("Erreur : la configuration n'est pas chargée.");
        }

        $dsn = self::$config['db_driver'] . ':host=' . self::$config['host'] . ';dbname=' . self::$config['dbname'] . ';charset=' . self::$config['charset'];
        $username = self::$config['username'];
        $password = self::$config['password'];

        try {
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);
            return $pdo;
        } catch (\PDOException $e) {
            var_dump(self::$config);
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
}

connexionFactory::setConfig('db.config.ini');

$db = connexionFactory::makeConnection();
