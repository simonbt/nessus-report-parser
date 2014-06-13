<?php
/**
 * nessus-report-parser -- Users.php
 * User: Simon Beattie
 * Date: 11/06/2014
 * Time: 16:50
 */

namespace Library;


class Users extends ReportsAbstract
{


    public function checkUser($email, $password)
    {
        $userDetails = $this->getUserDetails($email);

        if ($userDetails[0]['email'] == $email && $userDetails[0]['password'] == $password) {
            return $userDetails[0];
        }
        return NULL;
    }

    public function createUser($name, $email, $password, $privilege)
    {

        $usernameCheck = $this->getUserDetails($email);
        if ($usernameCheck) {
            return 'exists';
        }
        $insertUser = $this->getPdo()->prepare('INSERT INTO users (email, password, privilege, name, pass_length, last_updated) VALUES(?, ?, ?, ?, ?, ?)');
        $insertedUser = $insertUser->execute(array($email, $this->createHash($password), $privilege, $name, strlen($password), date('Y-m-d H:i:s')));
        if (!$insertedUser) {
            die('Sorry, we couldn\'t add the user: ' . print_r($insertUser->errorInfo()) . PHP_EOL);
        }

        $userId = $this->getPdo()->lastInsertId();
        $nessusDirectory = __DIR__ . '/uploads/nessus/' . $userId;
        $openDlpDirectory = __DIR__ . '/uploads/opendlp/' . $userId;

        $nessusCreate = mkdir($nessusDirectory);
        $OpenDlpCreate = mkdir($openDlpDirectory);

        if ((!$nessusCreate) || (!$OpenDlpCreate))
        {
            die('Unable to create directories: ' . $nessusDirectory . ' :AND: ' . $openDlpDirectory);
        }
        return $userId;
    }

    public function removeUser($userId)
    {
        $userQuery = $this->getPdo()->prepare('DELETE FROM users WHERE id =?');
        $userQuery->execute(array($userId));
        if (!$userQuery) {
            die('Failed to remove user' . print_r($userQuery->errorInfo()));
        }
    }

    public function changeUserPass($email, $userId, $password, $newPass, $repeatPass)
    {

        $userDetails = $this->getUserDetails($email);

        if ($userDetails[0]['password'] == $this->createHash($password)) {

            if ($newPass == $repeatPass) {
                $sql = "UPDATE users SET password = :password, last_updated = :updatetime, pass_length = :pass_length WHERE id = :id;";
                $query = $this->getPdo()->prepare($sql);
                $query->bindParam(':password', $this->createHash($newPass), \PDO::PARAM_STR);
                $query->bindParam(':updatetime', date('Y-m-d H:i:s'), \PDO::PARAM_STR);
                $query->bindParam(':pass_length', strlen($newPass), \PDO::PARAM_INT);
                $query->bindParam(':id', $userId, \PDO::PARAM_INT);
                $query->execute();

                if (!$query) {
                    return 'failed';
                } else {
                    return 'success';
                }

            } else {
                return 'match';
            }
        } else {
            return 'wrongPass';
        }


    }

    public function setSeverity($userId, $severity)
    {
        $sql = "UPDATE users SET severity = :severity WHERE id = :id;";
        $query = $this->getPdo()->prepare($sql);
        $query->bindParam(':severity', $severity, \PDO::PARAM_STR);
        $query->bindParam(':id', $userId, \PDO::PARAM_INT);
        $query->execute();

        if (!$query)
        {
            return 'failed';
        }
        return 'success';
    }

    private function createHash($password)
    {
        $hash = hash('sha512', $password);
        return $hash;
    }

    public function getUserDetails($email)
    {
        $userQuery = $this->getPdo()->prepare('SELECT * FROM users WHERE email =?');
        $userQuery->execute(array($email));
        $userDetails = $userQuery->fetchAll(\PDO::FETCH_ASSOC);
        if (!$userDetails) {
            return FALSE;
        }
        return $userDetails;
    }

}