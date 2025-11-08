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
        
        $systemOrg = $this->createSystemOrganization($manager);
        $superAdminRole = $this->createSuperAdminRole($manager, $systemOrg, $permissions);
        $this->createSuperAdminUser($manager, $systemOrg, $superAdminRole);
        
        $demoOrg = $this->createDemoOrganization($manager);
        $demoRoles = $this->createStandardRoles($manager, $demoOrg, $permissions);
        $this->createDemoUsers($manager, $demoOrg, $demoRoles);
        
        $manager->flush();
    }

    private function createPermissions(ObjectManager $manager): array
    {
        $permissionData = [
            ['name' => 'organization:view', 'resource' => 'organization', 'action' => 'view', 'description' => 'View organizations'],
            ['name' => 'organization:create', 'resource' => 'organization', 'action' => 'create', 'description' => 'Create organizations'],
            ['name' => 'organization:edit', 'resource' => 'organization', 'action' => 'edit', 'description' => 'Edit organizations'],
            ['name' => 'organization:delete', 'resource' => 'organization', 'action' => 'delete', 'description' => 'Delete organizations'],
            
            ['name' => 'course:view', 'resource' => 'course', 'action' => 'view', 'description' => 'View courses'],
            ['name' => 'course:create', 'resource' => 'course', 'action' => 'create', 'description' => 'Create courses'],
            ['name' => 'course:edit', 'resource' => 'course', 'action' => 'edit', 'description' => 'Edit courses'],
            ['name' => 'course:delete', 'resource' => 'course', 'action' => 'delete', 'description' => 'Delete courses'],
            ['name' => 'course:publish', 'resource' => 'course', 'action' => 'publish', 'description' => 'Publish courses'],

            ['name' => 'lesson:view', 'resource' => 'lesson', 'action' => 'view', 'description' => 'View lessons'],
            ['name' => 'lesson:create', 'resource' => 'lesson', 'action' => 'create', 'description' => 'Create lessons'],
            ['name' => 'lesson:edit', 'resource' => 'lesson', 'action' => 'edit', 'description' => 'Edit lessons'],
            ['name' => 'lesson:delete', 'resource' => 'lesson', 'action' => 'delete', 'description' => 'Delete lessons'],
            
            ['name' => 'enrollment:view', 'resource' => 'enrollment', 'action' => 'view', 'description' => 'View enrollments'],
            ['name' => 'enrollment:create', 'resource' => 'enrollment', 'action' => 'create', 'description' => 'Enroll in courses'],
            ['name' => 'enrollment:manage', 'resource' => 'enrollment', 'action' => 'manage', 'description' => 'Manage enrollments'],
            
            ['name' => 'user:view', 'resource' => 'user', 'action' => 'view', 'description' => 'View users'],
            ['name' => 'user:create', 'resource' => 'user', 'action' => 'create', 'description' => 'Create users'],
            ['name' => 'user:edit', 'resource' => 'user', 'action' => 'edit', 'description' => 'Edit users'],
            ['name' => 'user:delete', 'resource' => 'user', 'action' => 'delete', 'description' => 'Delete users'],
            
            ['name' => 'role:view', 'resource' => 'role', 'action' => 'view', 'description' => 'View roles'],
            ['name' => 'role:manage', 'resource' => 'role', 'action' => 'manage', 'description' => 'Manage roles'],
        ];

        $permissions = [];
        foreach ($permissionData as $data) {
            $permission = new Permission();
            $permission->setName($data['name']);
            $permission->setDescription($data['description']);
            
            $manager->persist($permission);
            $permissions[$data['name']] = $permission;
        }

        return $permissions;
    }

    private function createSystemOrganization(ObjectManager $manager): Organization
    {
        $systemOrg = new Organization();
        $systemOrg->setName('System');
        $systemOrg->setSlug('system');
        $systemOrg->setIsActive(true);
        $systemOrg->setIsSystemOrganization(true);
        
        $manager->persist($systemOrg);

        return $systemOrg;
    }

    private function createSuperAdminRole(ObjectManager $manager, Organization $systemOrg, array $permissions): Role
    {
        $superAdminRole = new Role();
        $superAdminRole->setName('ROLE_SUPER_ADMIN');
        $superAdminRole->setDescription('System Super Administrator - Full system access');
        $superAdminRole->setOrganization($systemOrg);
        $superAdminRole->setIsSystemRole(true);
        
        foreach ($permissions as $permission) {
            $superAdminRole->addPermission($permission);
        }
        
        $manager->persist($superAdminRole);

        return $superAdminRole;
    }

    private function createSuperAdminUser(ObjectManager $manager, Organization $systemOrg, Role $superAdminRole): void
    {
        $superAdmin = new User();
        $superAdmin->setEmail('super@system.local');
        $superAdmin->setFirstName('Super');
        $superAdmin->setLastName('Admin');
        $superAdmin->setOrganization($systemOrg);
        $superAdmin->setIsActive(true);
        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'SuperSecret123!'));
        $superAdmin->addRole($superAdminRole);
        
        $manager->persist($superAdmin);
    }

    private function createDemoOrganization(ObjectManager $manager): Organization
    {
        $demoOrg = new Organization();
        $demoOrg->setName('Demo University');
        $demoOrg->setSlug('demo-university');
        $demoOrg->setIsActive(true);
        
        $manager->persist($demoOrg);

        return $demoOrg;
    }

    private function createStandardRoles(ObjectManager $manager, Organization $organization, array $permissions): array
    {
        $roles = [];

        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $adminRole->setDescription('Organization Administrator - Full org access');
        $adminRole->setOrganization($organization);
        
        foreach ($permissions as $name => $permission) {
            if (!str_starts_with($name, 'organization:')) {
                $adminRole->addPermission($permission);
            }
        }
        
        $manager->persist($adminRole);
        $roles['admin'] = $adminRole;

        $instructorRole = new Role();
        $instructorRole->setName('ROLE_INSTRUCTOR');
        $instructorRole->setDescription('Course Instructor');
        $instructorRole->setOrganization($organization);
        
        $instructorPermissions = [
            'course:view', 'course:create', 'course:edit', 'course:publish',
            'lesson:view', 'lesson:create', 'lesson:edit', 'lesson:delete',
            'enrollment:view',
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
        $studentRole->setDescription('Student User');
        $studentRole->setOrganization($organization);
        
        $studentPermissions = [
            'course:view',
            'lesson:view',
            'enrollment:view', 
            'enrollment:create',
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

    private function createDemoUsers(ObjectManager $manager, Organization $organization, array $roles): void
    {
        $admin = new User();
        $admin->setEmail('admin@demo.com');
        $admin->setFirstName('John');
        $admin->setLastName('Admin');
        $admin->setOrganization($organization);
        $admin->setIsActive(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->addRole($roles['admin']);
        $manager->persist($admin);

        $instructor = new User();
        $instructor->setEmail('instructor@demo.com');
        $instructor->setFirstName('Jane');
        $instructor->setLastName('Instructor');
        $instructor->setOrganization($organization);
        $instructor->setIsActive(true);
        $instructor->setPassword($this->passwordHasher->hashPassword($instructor, 'instructor123'));
        $instructor->addRole($roles['instructor']);
        $manager->persist($instructor);

        $student = new User();
        $student->setEmail('student@demo.com');
        $student->setFirstName('Bob');
        $student->setLastName('Student');
        $student->setOrganization($organization);
        $student->setIsActive(true);
        $student->setPassword($this->passwordHasher->hashPassword($student, 'student123'));
        $student->addRole($roles['student']);
        $manager->persist($student);
    }
}