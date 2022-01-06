<?php

require_once "DAO.php";

require_once '../Model/AccountModel.php';

class AccountDAO extends DAO
{
    private AccountModel $account;

    public function __construct($account = null)
    {
        parent::__construct();

        if($account == null) $account = new AccountModel(
            0,
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            new RoleModel(0, "", [])
        );
        $this->account = $account;
    }

    public function getAccount(): AccountModel
    {
        return $this->account;
    }

    public function setAccount(AccountModel $account)
    {
        $this->account = $account;
    }

    /**
     * @return array
     */
    public function create() : bool {

        try {

            $this->connect();

            $query  = "INSERT INTO account(username, mail, password) VALUES (:username, :mail, :password)";
            $sth    = $this->connection->prepare($query);
            $result = $sth->execute([
                ":username" => $this->account->getUsername(),
                ":mail"     => $this->account->getMail(),
                ":password" => $this->account->getPassword()
            ]);

            $query  = "SELECT id FROM account WHERE mail = :mail";
            $stmt    = $this->connection->prepare($query);
            $result = $stmt->execute([
                ":mail" => $this->account->getMail()
            ]);

            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->account->setId($account['id']);

            $this->connection = null;

            return $result;

        } catch (PDOException $e) {

            die('<div class="alert alert-danger" role="alert">[Erreur]: ' . $e->getMessage() . '<div/>');

        }
    }

    public function verifyPassword() {

        try {

            $this->connect();

            $query  = "SELECT password FROM account WHERE mail = :mail";
            $stmt    = $this->connection->prepare($query);
            $result = $stmt->execute([
                ":mail" => $this->account->getMail()
            ]);

            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->connection = null;

            if($account === false) return false;

            return password_verify($this->account->getPassword(), $account['password']);


        } catch (PDOException $e) {

            print '<div class="alert alert-danger" role="alert">[Erreur]: ' . $e->getMessage() . '<div/>';
            die();

        }

    }

    public function updateLastConnection() {

        $this->connect();

        $query  = "UPDATE account SET lastConnection = :lastConnection WHERE id = :id";
        $stmt   = $this->connection->prepare($query);
        $stmt->execute([
            ":lastConnection"   => date('Y-m-d H:i:s'),
            ":id"               => $this->account->getId()
        ]);

        $this->connection = null;

    }

    public function getAccountByUsername() {
        try {

            $this->connect();

            $query  = "
                SELECT firstname, lastname, username, birthDate, mail, createdAt, lastConnection, pictureURL, role_id, role.name AS role_name
                FROM account 
                INNER JOIN role ON account.role_id = role.id
                WHERE account.username = :username";
            $stmt    = $this->connection->prepare($query);
            $result = $stmt->execute([
                ":username" => $this->account->getUsername()
            ]);

            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->connection = null;

            if($stmt->rowCount() == 0) return false;

            $this->account->setFirstname($account['firstname'] ?? "");
            $this->account->setLastname($account['lastname'] ?? "");
            $this->account->setUsername($account['username']);
            $this->account->setBirthDate($account['birthDate'] ?? "");
            $this->account->setMail($account['mail']);
            $this->account->setCreatedAt($account['createdAt']);
            $this->account->setLastConnection($account['lastConnection']);
            $this->account->setProfilePictureURL($account['pictureURL'] ?? "");
            $this->account->setRole(new RoleModel($account['role_id'], $account['role_name'], []));

            return true;

        } catch (PDOException $e) {

            print '<div class="alert alert-danger" role="alert">[Erreur]: ' . $e->getMessage() . '<div/>';
            die();

        }
    }

    public function getAccountByMail() {

        try {

            $this->connect();

            $query  = "
                SELECT firstname, lastname, username, birthDate, mail, createdAt, lastConnection, pictureURL, role_id, role.name AS role_name
                FROM account 
                INNER JOIN role ON account.role_id = role.id
                WHERE account.mail = :mail";
            $stmt    = $this->connection->prepare($query);
            $result = $stmt->execute([
                ":mail" => $this->account->getMail()
            ]);

            $account = $stmt->fetch();

            $this->connection = null;

            if($stmt->rowCount() == 0) return false;

            $this->account->setFirstname($account['firstname'] ?? "");
            $this->account->setLastname($account['lastname'] ?? "");
            $this->account->setUsername($account['username']);
            $this->account->setBirthDate($account['birthDate'] ?? "");
            $this->account->setMail($account['mail']);
            $this->account->setCreatedAt($account['createdAt']);
            $this->account->setLastConnection($account['lastConnection']);
            $this->account->setProfilePictureURL($account['pictureURL'] ?? "");
            $this->account->setRole(new RoleModel($account['role_id'], $account['role_name'], []));

            return true;

        } catch (PDOException $e) {

            print '<div class="alert alert-danger" role="alert">[Erreur]: ' . $e->getMessage() . '<div/>';
            die();

        }
    }

    public function getAccountById() {

        try {

            $this->connect();

            $query  = "
                SELECT firstname, lastname, username, birthDate, mail, createdAt, lastConnection, pictureURL, role_id, role.name AS role_name
                FROM account 
                INNER JOIN role ON account.role_id = role.id
                WHERE account.id = :id";
            $stmt    = $this->connection->prepare($query);
            $stmt->execute([
                ":id" => $this->account->getId()
            ]);

            $this->connection = null;

            $account = $stmt->fetch();

            if($stmt->rowCount() == 0) return false;

            $this->account->setFirstname($account['firstname'] ?? "");
            $this->account->setLastname($account['lastname'] ?? "");
            $this->account->setUsername($account['username']);
            $this->account->setBirthDate($account['birthDate'] ?? "");
            $this->account->setMail($account['mail']);
            $this->account->setCreatedAt($account['createdAt']);
            $this->account->setLastConnection($account['lastConnection']);
            $this->account->setProfilePictureURL($account['pictureURL'] ?? "");
            $this->account->setRole(new RoleModel($account['role_id'], $account['role_name'], []));

            return true;

        } catch (PDOException $e) {

            print '<div class="alert alert-danger" role="alert">[Erreur]: ' . $e->getMessage() . '<div/>';
            die();

        }

    }
}