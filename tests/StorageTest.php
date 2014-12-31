<?php
/**
 * Created by PhpStorm.
 * User: seeb
 * Date: 12/30/14
 * Time: 11:21 PM
 */

class StorageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function getConfigIgnoresMissingFileAndReturnsNewConfig()
    {
        $storage = new \Bookmarks\Storage("any unkown file");

        /** @var \Bookmarks\Config $actual */
        $actual = $storage->getConfig();

        $this->assertInstanceOf('Bookmarks\Config', $actual);
    }

    /**
     * @test
     */
    public function getConfigParsesFileAndReturnsContent()
    {
        list($group, $storage) = $this->loadStorageFromFile();

        /** @var \Bookmarks\Config $actual */
        $actual = $storage->getConfig();

        $this->assertInstanceOf('Bookmarks\Config', $actual);

        $actual->issetGroup($group->getId());
    }

    /**
     * @return array
     */
    private function loadStorageFromFile()
    {
        $fileName = $this->getFilename();

        $group = new \Bookmarks\Group();
        $group->title = 'my group';

        $data = array(
            'groups' =>
                array(
                    $group->toArray()
                )
        );

        file_put_contents($fileName, json_encode($data));

        $storage = new \Bookmarks\Storage($fileName);

        return array($group, $storage);
    }

    /**
     * @test
     */
    public function saveConfigStoresChangesToFile()
    {
        /** @var \Bookmarks\Group $group */
        /** @var \Bookmarks\Storage $storage */
        list($group, $storage) = $this->loadStorageFromFile();

        $storedGroup = $storage->getConfig()->getGroup($group->getId());
        $storedGroup->title = 'my new title';

        $storage->saveConfig($storage->getConfig());

        $expected = array(
            'groups' =>
                array(
                    $storedGroup->toArray()
                )
        );

        $this->assertEquals(
            json_encode($expected, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),
            file_get_contents($this->getFilename())
        );
    }

    /**
     * @return string
     */
    private function getFilename()
    {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'storage.yaml';
        return $fileName;
    }
}
