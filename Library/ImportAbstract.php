<?php
/**
 * slim -- ImportAbstract.php
 * User: Simon Beattie
 * Date: 10/06/2014
 * Time: 20:51
 */

namespace Library;


class ImportAbstract
{

    /**
     * @var PDO $pdo
     */
    protected $pdo;


    public function __construct($pdo)
    {
        $this->setPdo($pdo);
    }


    /**
     * setPdo sets the pdo property in object storage
     *
     * @param \PDO $pdo
     * @throws InvalidArgumentException
     * @return StatusAbstract
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