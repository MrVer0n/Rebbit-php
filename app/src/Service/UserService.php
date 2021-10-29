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
                'message' => 'Пользователь с таким email существует',
            ];
        }
        $user = $this->em->getRepository(User::class)->findByUsername($data['username']);
        if($user instanceof User) {
            return [
                'statusCode' => 400,
                'message' => 'Пользователь с таким именем существует',
                'user' => $user
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
            'message' => 'Пользователь создан успешно!',
            'user' => $user,
        ];
    }

}