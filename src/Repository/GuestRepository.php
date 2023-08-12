<?php 

namespace App\Repository;

use App\Document\Guest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class GuestRepository extends ServiceDocumentRepository 
{

    // bool $flush = false, DocumentManager $dm

    public function __construct(ManagerRegistry $registery)
    {
        parent::__construct($registery, Guest::class);
    }

    public function save(Guest $guest): void 
    {
            $this->getDocumentManager()->persist($guest);
            $this->getDocumentManager()->flush();
    }


}