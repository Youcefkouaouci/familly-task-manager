<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\TaskAssignment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // 1. Création des Users
        $users = [];
        
        // Admin
        $admin = new User();
        $admin->setEmail('admin@familytask.com')
              ->setFirstName($faker->firstName)
              ->setLastName($faker->lastName)
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);
        $users[] = $admin;
        
        // Parents (5)
        for ($i = 0; $i < 5; $i++) {
            $parent = new User();
            $parent->setEmail($faker->email)
                  ->setFirstName($faker->firstName)
                  ->setLastName($faker->lastName)
                  ->setRoles(['ROLE_PARENT'])
                  ->setPassword($this->passwordHasher->hashPassword($parent, 'password'));
            $manager->persist($parent);
            $users[] = $parent;
        }
        
        // Enfants (10)
        $children = [];
        for ($i = 0; $i < 10; $i++) {
            $child = new User();
            $child->setEmail($faker->email)
                 ->setFirstName($faker->firstName)
                 ->setLastName($faker->lastName)
                 ->setRoles(['ROLE_CHILD'])
                 ->setPassword($this->passwordHasher->hashPassword($child, 'password'));
            $manager->persist($child);
            $children[] = $child;
        }
        
        $manager->flush();
        
        // 2. Création des Tasks (20)
        $tasks = [];
        $categories = ['Ménage', 'Devoirs', 'Jardinage', 'Cuisine', 'Courses'];
        
        for ($i = 0; $i < 20; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(3))
                 ->setDescription($faker->paragraph(3))
                 ->setCategory($faker->randomElement($categories))
                 ->setDueDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+1 month')))
                 ->setReward($faker->randomElement([null, '€5', 'Sortie', 'Temps d\'écran']))
                 ->setParent($users[$faker->numberBetween(1, 5)]) // Les parents sont aux index 1-5
                 ->setIsUrgent($faker->boolean(20));
            
            $manager->persist($task);
            $tasks[] = $task;
        }
        
        $manager->flush();
        
        // 3. Création des TaskAssignments (15)
        $statuses = [
            TaskAssignment::STATUS_PENDING,
            TaskAssignment::STATUS_ACCEPTED,
            TaskAssignment::STATUS_REFUSED,
            TaskAssignment::STATUS_NEGOTIATING,
            TaskAssignment::STATUS_COMPLETED
        ];
        
        for ($i = 0; $i < 15; $i++) {
            $assignment = new TaskAssignment();
            $assignment->setTask($tasks[$i])
                      ->setChild($children[$i % 10]) // Répartition sur les 10 enfants
                      ->setStatus($statuses[array_rand($statuses)])
                      ->setIsNotified(false)
                      ->setLastStatusChangedAt(new \DateTimeImmutable());
            
            if ($assignment->getStatus() === TaskAssignment::STATUS_COMPLETED) {
                $assignment->setCompletedAt(new \DateTimeImmutable());
            }
            
            $manager->persist($assignment);
        }
    $manager->flush();
    }
}
