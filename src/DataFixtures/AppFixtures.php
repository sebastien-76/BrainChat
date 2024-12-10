<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
    $this->hasher = $hasher;
}

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadAdmins();

    }

    public function loadAdmins(): void
    {
        //Défini les valeurs de user qui vont être stockées en bdd
        $datas = [
            [
                'email' => 'admin@example.com',
                'password' => '123',
                'roles' => ['ROLE_ADMIN'],
                'author' => 'Admin',
            ],
            [
                'email' => 'groq@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'author' => 'Groq',
            ],

        ];

        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setRoles($data['roles']);
            $user->setAuthor($data['author']);

            //Permet de pousser les données en base de données
            //indique que la variable user doit être stockée en bdd
            $this->manager->persist($user);
        }
        //génére et execute le code sql qui va stocker les données en bdd
        $this->manager->flush();
    }

}