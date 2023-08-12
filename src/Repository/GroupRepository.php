<?php 

namespace App\Repository;

use App\Document\Group;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use MongoDB\BSON\ObjectId;

class GroupRepository extends ServiceDocumentRepository 
{

    public function __construct(ManagerRegistry $registery)
    {
        parent::__construct($registery, Group::class);
    }

    /**
     * GET query and execute query
     *
     * @param Group $group
     * @return void
     */
    public function save(Group $group): void 
    {
        $this->getDocumentManager()->persist($group);
        $this->getDocumentManager()->flush();
    }

    /**
     * Find a group by ID
     *
     * @param string $id
     * @return array return the group
     */
    public function findGroupsById(string $id):array
    {

        return $this->findBy([ 'id' => $id ]);
    }

    /**
     * find the group collection
     *
     * @return array return all groups
     */
    public function findAllGrpFromBdd() : array 
    {
        return $this->findAll();
    }

    /**
     * Delete a group by group
     *
     * @param object $group
     * @return void
     */
    public function removeByDocument(object $group) : void
    {
        $this->getDocumentManager()->remove($group);
        $this->getDocumentManager()->flush();
    }

    /**
     * Description :
     * find each invitation with 
     *
     * @param string $idSession is the ID of connected user 
     * @param string $response is the response to invitation
     * @return array return the guest
     */
    public function findGuestById(string $idSession, string $response) : array 
    {
        return $this->findBy([
            'status' => "waiting",
            'guests' => [
                '$elemMatch' => [
                    'invitation' => "$response",
                    'guest.$id' => new ObjectId($idSession),
                ]
            ]
        ]);
    }

    /**
     * find the ready group
     *
     * @param string $id connected user ID
     * @return array each ready groups 
     */
    public function findReadyGroup(string $idSession): array
    {
        return $this->findBy([
            'status' => "waiting",
            'guests' => [
                '$elemMatch' => [
                    'guest.$id' => new ObjectId($idSession),
                    'invitation' => "accept",
                ],
                '$not' => [
                    '$elemMatch' => [
                        'invitation' => "waiting",
                    ],
                ],
            ],
        ]);
    }
}