<?php
/**
 * slim -- ReportsAbstract.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 16:15
 */

namespace Library;


class ReportsAbstract
{

    /**
     * @var \PDO $pdo
     */
    protected $pdo;

    /**
     * @var array $config
     */
    protected $config;

    public function __construct($pdo)
    {
        $this->setPdo($pdo);
    }

    /**
     * setPdo sets the pdo property in object storage
     *
     * @param \PDO $pdo
     * @throws \InvalidArgumentException
     */
    public function setPdo($pdo)
    {
        if (empty($pdo)) {
            throw new \InvalidArgumentException(__METHOD__ . ' cannot accept an empty pdo');
        }
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * getPdo returns the pdo from the object
     *
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

} 