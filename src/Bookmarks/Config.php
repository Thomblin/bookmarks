<?php

namespace Bookmarks;

class Config
{
    /**
     * @var Group[]
     */
    private $groups = array();

    /**
     * @param Group $group
     */
    public function addGroup(Group $group)
    {
        $group->assertValid();

        $this->groups[$group->getId()] = $group;
    }

    /**
     * @return bool
     */
    public function issetGroup($id)
    {
        return isset($this->groups[$id]);
    }

    /**
     * @return Group
     * @throws \InvalidArgumentException
     */
    public function getGroup($id)
    {
        if ( !$this->issetGroup($id) ) {
            throw new \InvalidArgumentException("group [$id] not found");
        }

        return $this->groups[$id];
    }

    /**
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $groups = array();

        foreach ( $this->groups as $group ) {
            $groups[] = $group->toArray();
        }

        return array(
            'groups' => $groups,
        );
    }

    /**
     * @param $data
     */
    public function fromArray(array $data)
    {
        if ( isset($data['groups']) && is_array($data['groups']) ) {
            foreach ( $data['groups'] as $groupData ) {
                $group = new Group();
                $group->fromArray($groupData);

                $this->addGroup($group);
            }
        }
    }

    /**
     * @param string $word
     *
     * @return array
     */
    public function search($word)
    {
        $ids = array();

        foreach ( $this->getGroups() as $group ) {
            $ids = array_merge($ids, $group->search($word));
        }

        return $ids;
    }
} 