<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    #[Route('', name: 'user_list', methods: ['GET'])]
    public function list() : Response {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        return $this->json([
            'data' => ($users),
        ]);

    }


    #[Route('/{id}', name: 'get_user', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function fetchUser(int $id) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if(!$user instanceof User) {
            return $this->json([
                'message' => 'Пользователь с таким id не найден'
            ],404);
        }
        return $this->json($user);
    }


    #[Route('/{id}', name: 'delete_user', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteUser(int $id) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if(!$user instanceof User) {
            return $this->json([
                'message' => 'Пользователь с таким id не найден'
            ],404);
        }
        $em->remove($user);
        $em->flush();
        return $this->json([
            'message' => 'Пользователь успешно удалён',
        ]);
    }


    #[Route('', name: 'create_user', methods: ['POST'])]
    public function create_user(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            return $this->json([
                'message' => 'Неверный JSON!'
            ],400);
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findByEmail($data['email']);
        if($user instanceof User) {
            return $this->json([
               'message' => 'Пользователь с таким email существует',
            ],400);
        }
        $user = $em->getRepository(User::class)->findByUsername($data['username']);
        if($user instanceof User) {
            return $this->json([
                'message' => 'Пользователь с таким именем существует',
                'user' => $user
            ],400);
        }
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $em->persist($user);
        $em->flush();
        return $this->json([
           'message' => 'Пользователь создан успешно!',
            'user' => ($user),
        ]);
    }


    #[Route('/{id}', name: 'update_user', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(int $id,Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if(!$user instanceof User) {
            return $this->json([
                'message' => 'Пользователь с таким id не найден'
            ],404);
        }
        $data = json_decode($request->getContent(), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            return $this->json([
                'message' => 'Неверный JSON!'
            ],400);
        }
        if(array_key_exists('email', $data)) {
            $userN = $em->getRepository(User::class)->findByEmail($data['email']);
            if($userN instanceof User) {
                return $this->json([
                    'message' => 'Пользователь с таким email существует',
                ],400);
            }
            $user->setEmail($data['email']);
        }
        if(array_key_exists('username', $data)) {
            $userN = $em->getRepository(User::class)->findByUsername($data['username']);
            if($userN instanceof User) {
                return $this->json([
                    'message' => 'Пользователь с таким именем существует',
                ],400);
            }
            $user->setUsername($data['username']);
        }
        $em->flush();
        return $this->json([
            'message' => 'Пользователь успешно изменён!',
        ]);
    }
}
