<?php

namespace App\DataFixtures;

use App\Entity\Organization;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $permissions = $this->createPermissions($manager);
        $organization = $this->createOrganization($manager);
        $roles = $this->createRoles($manager, $organization, $permissions);
        
        $this->createUsers($manager, $organization, $roles);
        $manager->flush();
    }

    private function createPermissions(ObjectManager $manager): array
    {
        $permissionData = [
            ['name' => 'course:view', 'resource' => 'course', 'action' => 'view', 'description' => 'View courses'],
            ['name' => 'course:create', 'resource' => 'course', 'action' => 'create', 'description' => 'Create new courses'],
            ['name' => 'course:edit', 'resource' => 'course', 'action' => 'edit', 'description' => 'Edit existing courses'],
            ['name' => 'course:delete', 'resource' => 'course', 'action' => 'delete', 'description' => 'Delete courses'],
            ['name' => 'course:publish', 'resource' => 'course', 'action' => 'publish', 'description' => 'Publish courses'],

            ['name' => 'lesson:view', 'resource' => 'lesson', 'action' => 'view', 'description' => 'View lessons'],
            ['name' => 'lesson:create', 'resource' => 'lesson', 'action' => 'create', 'description' => 'Create lessons'],
            ['name' => 'lesson:edit', 'resource' => 'lesson', 'action' => 'edit', 'description' => 'Edit lessons'],
            ['name' => 'lesson:delete', 'resource' => 'lesson', 'action' => 'delete', 'description' => 'Delete lessons'],
            
            ['name' => 'enrollment:view', 'resource' => 'enrollment', 'action' => 'view', 'description' => 'View enrollments'],
            ['name' => 'enrollment:create', 'resource' => 'enrollment', 'action' => 'create', 'description' => 'Enroll in courses'],
            ['name' => 'enrollment:manage', 'resource' => 'enrollment', 'action' => 'manage', 'description' => 'Manage all enrollments'],
            
            ['name' => 'user:view', 'resource' => 'user', 'action' => 'view', 'description' => 'View users'],
            ['name' => 'user:create', 'resource' => 'user', 'action' => 'create', 'description' => 'Create users'],
            ['name' => 'user:edit', 'resource' => 'user', 'action' => 'edit', 'description' => 'Edit users'],
            ['name' => 'user:delete', 'resource' => 'user', 'action' => 'delete', 'description' => 'Delete users'],
            
            ['name' => 'organization:manage', 'resource' => 'organization', 'action' => 'manage', 'description' => 'Manage organization settings'],
            
            ['name' => 'role:view', 'resource' => 'role', 'action' => 'view', 'description' => 'View roles'],
            ['name' => 'role:manage', 'resource' => 'role', 'action' => 'manage', 'description' => 'Manage roles and permissions'],
        ];

        $permissions = [];
        foreach ($permissionData as $data) {
            $permission = new Permission();
            $permission->setName($data['name']);
            $permission->setResource($data['resource']);
            $permission->setAction($data['action']);
            $permission->setDescription($data['description']);
            
            $manager->persist($permission);
            $permissions[$data['name']] = $permission;
        }

        return $permissions;
    }

    private function createOrganization(ObjectManager $manager): Organization
    {
        $organization = new Organization();
        $organization->setName('Demo University');
        $organization->setSlug('demo-university');
        $organization->setIsActive(true);
        $organization->setSettings([
            'theme' => 'default',
            'allow_self_enrollment' => true,
            'require_email_verification' => false,
        ]);

        $manager->persist($organization);

        return $organization;
    }

    private function createRoles(ObjectManager $manager, Organization $organization, array $permissions): array
    {
        $roles = [];

        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setDescription('System Administrator - Full access');
        $adminRole->setOrganization($organization);
        $adminRole->setIsSystemRole(true);
        
        foreach ($permissions as $permission) {
            $adminRole->addPermission($permission);
        }
        
        $manager->persist($adminRole);
        $roles['admin'] = $adminRole;

        $instructorRole = new Role();
        $instructorRole->setName('ROLE_INSTRUCTOR');
        $instructorRole->setDescription('Course Instructor - Can create and manage courses');
        $instructorRole->setOrganization($organization);
        $instructorRole->setIsSystemRole(true);
        
        $instructorPermissions = [
            'course:view', 'course:create', 'course:edit', 'course:publish',
            'lesson:view', 'lesson:create', 'lesson:edit', 'lesson:delete',
            'enrollment:view', 'enrollment:manage',
            'user:view',
        ];
        
        foreach ($instructorPermissions as $permName) {
            if (isset($permissions[$permName])) {
                $instructorRole->addPermission($permissions[$permName]);
            }
        }
        
        $manager->persist($instructorRole);
        $roles['instructor'] = $instructorRole;

        $studentRole = new Role();
        $studentRole->setName('ROLE_STUDENT');
        $studentRole->setDescription('Student - Can view and enroll in courses');
        $studentRole->setOrganization($organization);
        $studentRole->setIsSystemRole(true);
        
        $studentPermissions = [
            'course:view',
            'lesson:view',
            'enrollment:view', 'enrollment:create',
        ];
        
        foreach ($studentPermissions as $permName) {
            if (isset($permissions[$permName])) {
                $studentRole->addPermission($permissions[$permName]);
            }
        }
        
        $manager->persist($studentRole);
        $roles['student'] = $studentRole;

        return $roles;
    }

    private function createUsers(ObjectManager $manager, Organization $organization, array $roles): void
    {
        $admin = new User();
        $admin->setEmail('admin@demo.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setOrganization($organization);
        $admin->setIsActive(true);
        $admin->addRole($roles['admin']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);
        
        $manager->persist($admin);

        $instructor = new User();
        $instructor->setEmail('instructor@demo.com');
        $instructor->setFirstName('John');
        $instructor->setLastName('Teacher');
        $instructor->setOrganization($organization);
        $instructor->setIsActive(true);
        $instructor->addRole($roles['instructor']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($instructor, 'instructor123');
        $instructor->setPassword($hashedPassword);
        
        $manager->persist($instructor);

        $student = new User();
        $student->setEmail('student@demo.com');
        $student->setFirstName('Jane');
        $student->setLastName('Student');
        $student->setOrganization($organization);
        $student->setIsActive(true);
        $student->addRole($roles['student']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($student, 'student123');
        $student->setPassword($hashedPassword);
        
        $manager->persist($student);
    }
}
