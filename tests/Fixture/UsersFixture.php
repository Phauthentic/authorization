<?php
declare(strict_types=1);

namespace Phauthentic\Authentication\Test\Fixture;

use Phauthentic\Authentication\Test\Schema\UsersSchema;
use PDO;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

class UsersFixture implements FixtureInterface
{
    protected $yamlFile;

    /**
     * {@inheritDoc}
     */
    public function createSchema(PDO $pdo): void
    {
        UsersSchema::create($pdo);
    }

    /**
     * Returns a path to the file with data set.
     *
     * @param string $name Filename.
     * @return string
     */
    protected function getFile(string $name): string
    {
        if (empty($this->yamlFile)) {
            return dirname(dirname(__FILE__)) . '/data_set/' . $name;
        }

        return $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataSet(): IDataSet
    {
        $yamlFile = $this->getFile('users.yml');

        if (!file_exists()) {
            throw new \RuntimeException(sprintf(
                'Could not load data for fixture `%s `from `%s`',
                get_class($this),
                $yamlFile
            ));
        }

        return new YamlDataSet($yamlFile);
    }
}
