<?php 

namespace App\Repository;

use App\Document\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class UserRepository extends ServiceDocumentRepository 
{

    public function __construct(ManagerRegistry $registery)
    {
        parent::__construct($registery, User::class);
    }

    /**
     * Description :
     * SAVE a User in the database
     *
     * @param User $user (user objet)
     * @return void
     */
    public function save(User $user): void
    {
            $this->getDocumentManager()->persist($user);
            $this->getDocumentManager()->flush();
    }

 
    /**
     * Description :
     * find all user from the database
     *
     * @return array
     */
    public function findAllFromBdd() : array 
    {
        return $this->findAll();
    }

    /**
     * Description :
     * find one user by ID
     *
     * @param [type] $userID
     * @return array (User)
     */
    public function findById($userID) : array 
    {
        return $this->findBy([ 'id' => $userID ]);
    }

    /**
     * Description :
     * Delete a User by User
     *
     * @param object $user
     * @return void
     */
    public function removeByDocument(object $user) : void
    {
        $this->getDocumentManager()->remove($user);
        $this->getDocumentManager()->flush();
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function setUserDocument($id) {
        
        $this->getDocumentManager()->getRepository(User::class)->find($id);

        $this->getDocumentManager()->flush();

    }

    /**
     * Description :
     * find a user by email
     *
     * @param string $email
     * @return object
     */
    public function findUserByEmail(string $email) : object
    {
        if($email){
            return $this->findOneBy([ 'email' => $email]);
        }
    }
    
    /**
     * Description :
     * find a user by username
     *
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername(string $username) : ?User
    {
        if($username){
            return $this->findOneBy([ 'username' => $username]);
        }
    }

    /**
     * Description :
     * find user by ID
     *
     * @param string $id
     * @return User
     */
    public function findUserById(string $id): User
    {
        if($id){
            return $this->findOneBy([ 'id' => $id]);
        }
    }

    /**
     * Description :
     * find user by Roles 
     *
     * @param string $roles
     * @return object
     */
    public function findUserByRoles(string $roles) : object
    {
        if($roles){
            return $this->findOneBy([ 'roles' => $roles]);
        }
    }

    /**
     * Description :
     * check if email already exists
     * if email already exists return true else false
     *
     * @param string $emailCheck
     * @return boolean
     */
    public function checkEmailAlreadyExists(string $emailCheck) : bool
    {
        $check = $this->findOneBy(['email'=>$emailCheck]);

        $check ? $check = true : $check = false ;

        return $check;
    }

} 