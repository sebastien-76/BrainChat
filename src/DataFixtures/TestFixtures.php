<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\Invitation;
use App\Entity\ChatMessage;
use App\Entity\Participant;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture
{
    private $faker;
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $user->setAuthor($this->faker->name());
            $manager->persist($user);
            $users[] = $user;
        }

        $rooms = [];
        for ($i = 0; $i < 10; $i++) {
            $room = new Room();
            $room->setTitle($this->faker->word(5));
            $room->setDescription($this->faker->paragraph());
            $room->setIsPrivate($this->faker->boolean(50)); // 50% de chances que la room soit privée
            $manager->persist($room);
            $rooms[] = $room;
        }

        for ($i = 0; $i < 20; $i++) {
            $invitation = new Invitation();
            $invitation->setSender($this->faker->randomElement($users));
            $invitation->setRecipient($this->faker->randomElement($users));
            $invitation->setRoom($this->faker->randomElement($rooms));
            $invitation->setToken($this->faker->uuid());
            $manager->persist($invitation);
        }

        for ($i = 0; $i < 100; $i++) {
            $chatMessage = new ChatMessage();
            $chatMessage->setContent($this->faker->sentence());
            $chatMessage->setCreatedAt($this->faker->dateTimeBetween('-6 months'));
            $chatMessage->setRoom($this->faker->randomElement($rooms));
            $chatMessage->setUser($this->faker->randomElement($users));
            $manager->persist($chatMessage);
        }

        for ($i = 0; $i < 50; $i++) {
            $participant = new Participant();
            $participant->setUser($this->faker->randomElement($users));
            $participant->setRoom($this->faker->randomElement($rooms));
            $participant->setRoles(['ROLE_USER']);
            $manager->persist($participant);
        }

        $manager->flush();
    }
}
