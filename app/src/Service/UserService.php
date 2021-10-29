<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function createUser(array $data) : array {
        $user = $this->em->getRepository(User::class)->findByEmail($data['email']);
        if($user instanceof User) {
            return [
                'statusCode' => 400,
                'message' => 'A user with this email exists!',
            ];
        }
        $user = $this->em->getRepository(User::class)->findByUsername($data['username']);
        if($user instanceof User) {
            return [
                'statusCode' => 400,
                'message' => 'A user with this name exists!',
            ];
        }
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $this->em->persist($user);
        $this->em->flush();
        return [
            'statusCode' => 200,
            'message' => 'The user was created successfully!',
            'user' => $user,
        ];
    }


    public function deleteUser(array $data, int $id) : array {
        $user = $this->em->getRepository(User::class)->find($id);
        if(!$user instanceof User) {
            return[
                'statusCode' => 400,
                'message' => 'No user with this id found!'
            ];
        }
        $this->em->remove($user);
        $this->em->flush();
        return [
            'statusCode' => 200,
            'message' => 'The user has been successfully deleted!',
            'user' => $user,
        ];
    }


    public function updateUser(array $data, int $id) : array {
        $user = $this->em->getRepository(User::class)->find($id);
        if(!$user instanceof User) {
            return [
                'statusCode' => 400,
                'message' => 'No user with this id found!',
            ];
        }
        if(array_key_exists('email', $data)) {
            $userN = $this->em->getRepository(User::class)->findByEmail($data['email']);
            if($userN instanceof User) {
                return [
                    'statusCode' => 400,
                    'message' => 'A user with this email exists!',
                ];
            }
            $user->setEmail($data['email']);
        }
        if(array_key_exists('username', $data)) {
            $userN = $this->em->getRepository(User::class)->findByUsername($data['username']);
            if($userN instanceof User) {
                return [
                    'statusCode' => 400,
                    'message' => 'A user with this name exists!',
                ];
            }
            $user->setUsername($data['username']);
        }
        $this->em->flush();
        return [
            'statusCode' => 200,
            'message' => 'User changed successfully!',
            'user' => $user,
        ];
    }

}