<?php

namespace App\DataFixtures;

use App\Entity\{Organization, Permission, Role, User, Course, Lesson};
use App\Data\Enums\{Roles, Permissions};
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $permissions = $this->createPermissions($manager);
        
        $superAdminRole = $this->createGlobalRole($manager, Roles::SUPER_ADMIN, $permissions);
        $adminRole = $this->createGlobalRole($manager, Roles::ADMIN, $permissions);
        $instructorRole = $this->createGlobalRole($manager, Roles::INSTRUCTOR, $permissions);
        $studentRole = $this->createGlobalRole($manager, Roles::STUDENT, $permissions);

        $systemOrg = $this->createOrganization($manager, 'System', 'system', true);
        $this->createUser($manager, 'super@system.local', 'Super', 'Admin', 'SuperSecret123!', $systemOrg, $superAdminRole);

        $demoOrg = $this->createOrganization($manager, 'Demo University', 'demo-university');
        $demoRoles = [
            'ADMIN' => $adminRole,
            'INSTRUCTOR' => $instructorRole,
            'STUDENT' => $studentRole
        ];
        $this->createDemoUsers($manager, $demoOrg, $demoRoles);

        $manager->flush();
    }

    private function createPermissions(ObjectManager $manager): array
    {
        $permissions = [];
        foreach (Permissions::cases() as $permEnum) {
            $permission = new Permission();
            $permission
                ->setName($permEnum->value)
                ->setDescription($permEnum->description());
            $manager->persist($permission);
            $permissions[$permEnum->value] = $permission;
        }
        return $permissions;
    }

    private function createOrganization(ObjectManager $manager, string $name, string $slug, bool $isSystem = false): Organization
    {
        $org = new Organization();
        $org->setName($name);
        $org->setSlug($slug);
        $org->setIsActive(true);
        $org->setIsSystemOrganization($isSystem);
        $manager->persist($org);
        return $org;
    }

    private function createGlobalRole(ObjectManager $manager, Roles $roleEnum, array $permissions): Role
    {
        $role = new Role();
        $role->setName($roleEnum->value);
        $role->setDescription($roleEnum->description());
        $role->setIsSystemRole($roleEnum === Roles::SUPER_ADMIN);

        foreach ($roleEnum->defaultPermissions() as $permName) {
            if (isset($permissions[$permName])) {
                $role->addPermission($permissions[$permName]);
            }
        }

        $manager->persist($role);
        return $role;
    }

    private function createUser(ObjectManager $manager, string $email, string $first, string $last, string $password, Organization $org, Role $role): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($first);
        $user->setLastName($last);
        $user->setOrganization($org);
        $user->setIsActive(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRole($role);
        $manager->persist($user);
        return $user;
    }

    private function createDemoUsers(ObjectManager $manager, Organization $org, array $roles): void
    {
        $admin = $this->createUser($manager, 'admin@demo.com', 'John', 'Admin', 'admin123', $org, $roles['ADMIN']);
        $instructor = $this->createUser($manager, 'instructor@demo.com', 'Jane', 'Instructor', 'instructor123', $org, $roles['INSTRUCTOR']);
        $student = $this->createUser($manager, 'student@demo.com', 'Bob', 'Student', 'student123', $org, $roles['STUDENT']);
        $this->createDemoCourses($manager, $org, $instructor);
    }

    private function createDemoCourses(ObjectManager $manager, Organization $org, User $instructor): void
    {
        $course = new Course();
        $course->setTitle('Introduction to Leadership');
        $course->setDescription('Learn essential leadership principles.');
        $course->setOrganization($org);
        $course->setInstructor($instructor);
        $manager->persist($course);

        $lessons = [
            ['Welcome Video', 'video', 'Course overview and introduction.', 'https://example.com/welcome.mp4', 0],
            ['Leadership Fundamentals', 'pdf', 'Understanding leadership basics.', 'https://example.com/leadership.pdf', 1],
            ['Interactive Exercise', 'exercise', 'Apply your leadership skills.', null, 2],
        ];

        foreach ($lessons as [$title, $type, $desc, $url, $difficulty]) {
            $lesson = new Lesson();
            $lesson->setTitle($title)
                   ->setType($type)
                   ->setDescription($desc)
                   ->setResourceUrl($url)
                   ->setDifficulty($difficulty)
                   ->setCourse($course);
            $manager->persist($lesson);
            $course->addLesson($lesson);
        }
    }
}
