<?php

namespace App\Controller;

use App\Entity\User;
use App\Message\Message;
use App\Service\SerializerService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
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
    public function create_user(Request $request, UserService $service): Response
    {
        $data = json_decode($request->getContent(), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            return $this->json([
                'message' => 'Неверный JSON!'
            ],400);
        }

        $result = $service->createUser($data);
        return $this->json($result, $result['statusCode']);
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

    #[Route('/sendMessage', name: 'send_message', methods: ['POST'])]
    public function ping(Request $request, MessageBusInterface $bus) : Response
    {
        $body = json_decode($request->getContent(), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            return $this->json([
                'statusCode' => 400,
                'message' => 'Неверный JSON!'
            ],400);
        }

        if(!array_key_exists('command', $body)) {
            return $this->json([
                'statusCode' => 400,
                'message' => 'Нет команды'
            ],400);
        }

        if(!array_key_exists('data', $body)) {
            return $this->json([
                'statusCode' => 400,
                'message' => 'Нет data'
            ],400);
        }

        $message = new Message($request->getContent());
        $bus->dispatch($message);

        return $this->json([
            'statusCode' => 200,
            'message'=>'Соощение отправлено'
        ]);
    }

}
