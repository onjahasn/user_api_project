<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function index(UserRepository $repository): JsonResponse
    {
        // Récupère tous les utilisateurs depuis la base
        $users = $repository->findAll();

        // Transforme les utilisateurs en tableau de données
        $data = array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }, $users);

        // Retourne une réponse JSON
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    public function show(User $user): JsonResponse
    {
        // Prépare les données de l'utilisateur
        $data = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        // Retourne une réponse JSON
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    public function showMe(Security $security): JsonResponse
    {
        // Récupère l'utilisateur connecté
        $user = $security->getUser();

        if (!$user instanceof User) {
            // Si aucun utilisateur connecté, retourner une erreur 401
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Prépare les données de l'utilisateur connecté
        $data = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        // Retourne une réponse JSON
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // Récupère les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Crée un nouvel utilisateur
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Enregistre l'utilisateur dans la base de données
        $em->persist($user);
        $em->flush();

        // Retourne une confirmation de création
        return new JsonResponse(['status' => 'User created'], JsonResponse::HTTP_CREATED);
    }

    public function update(Request $request, User $user, EntityManagerInterface $em): JsonResponse
    {
        // Récupère les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Met à jour les propriétés de l'utilisateur si elles sont fournies
        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }

        // Sauvegarde les modifications
        $em->flush();

        // Retourne une confirmation de mise à jour
        return new JsonResponse(['status' => 'User updated'], JsonResponse::HTTP_OK);
    }

    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        // Supprime l'utilisateur de la base
        $em->remove($user);
        $em->flush();

        // Retourne une confirmation de suppression
        return new JsonResponse(['status' => 'User deleted'], JsonResponse::HTTP_OK);
    }
}
